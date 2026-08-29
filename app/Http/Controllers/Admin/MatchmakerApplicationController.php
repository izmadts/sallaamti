<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\MatchmakerApplication;
use App\Models\User;
use App\Notifications\NikahCounselorCertified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MatchmakerApplicationController extends Controller
{
    use RegistersMinimalUsers;

    public function index(Request $request)
    {
        $query = MatchmakerApplication::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Separate from pipeline `status` — a certified counselor asking
        // for their physical card mailed is an ongoing fulfillment task,
        // not a stage in the certification pipeline, so it gets its own
        // filter rather than being folded into the Stage dropdown.
        if ($request->filled('card_status')) {
            match ($request->card_status) {
                'requested' => $query->whereNotNull('card_requested_at')->whereNull('card_dispatched_at'),
                'dispatched' => $query->whereNotNull('card_dispatched_at'),
                default => null,
            };
        }

        $applications = $query->paginate(20)->withQueryString();
        $pendingCardRequests = MatchmakerApplication::whereNotNull('card_requested_at')->whereNull('card_dispatched_at')->count();

        // A matchmaker account doesn't have to come through this pipeline
        // at all — admin can grant the role directly from Users > Roles.
        // Those accounts were previously invisible on this page entirely
        // (this page only ever queried MatchmakerApplication rows), even
        // though they're real, currently-working counselors. Shown as
        // their own section, always, regardless of the stage filter,
        // since they have no stage to filter by.
        $directRoleUsers = User::role('matchmaker')
            ->whereNotIn('id', MatchmakerApplication::whereNotNull('user_id')->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return view('admin.matchmaker-applications.index', compact('applications', 'directRoleUsers', 'pendingCardRequests'));
    }

    public function show(MatchmakerApplication $matchmakerApplication, \App\Services\Matchmaking\CounselorPerformanceCalculator $calculator)
    {
        $matchmakerApplication->load('user', 'reviewedBy');

        // Same numbers the counselor's own Performance page shows them —
        // admin previously had no visibility into this at all when
        // overriding a level manually, or judging whether an auto-promotion
        // (Console\Commands\PromoteEligibleCounselors) made sense.
        $performance = $matchmakerApplication->isCertified()
            ? $calculator->calculate($matchmakerApplication->user_id)
            : null;
        $levelProgress = $performance ? $matchmakerApplication->nextLevelProgress($performance['score'], $performance['stats']) : null;

        return view('admin.matchmaker-applications.show', ['application' => $matchmakerApplication, 'performance' => $performance, 'levelProgress' => $levelProgress]);
    }

    public function updateStatus(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        if ($matchmakerApplication->isTerminal()) {
            return back()->with('error', 'This application was already rejected or withdrawn.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(MatchmakerApplication::STEPS))],
        ]);

        // The one hard gate in an otherwise-flexible pipeline — certifying
        // someone who never actually accepted the Agreement/NDA themselves
        // is exactly the gap this module exists to close. A friendly
        // redirect-back, not abort_if(), so this reaches the page's own
        // flash-message convention instead of Laravel's generic 422 page.
        if ($validated['status'] === 'certified' && !$matchmakerApplication->hasAcceptedAgreementAndNda()) {
            return back()->with('error', 'This applicant hasn\'t accepted the Nikah Counselor Agreement and NDA yet — send them the agreement link first.');
        }

        $wasCertified = $matchmakerApplication->isCertified();

        $matchmakerApplication->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
        ]);

        if (!$wasCertified && $validated['status'] === 'certified') {
            $this->certify($matchmakerApplication);
        }

        return back()->with('status', 'Stage updated to "' . MatchmakerApplication::STEPS[$validated['status']] . '".');
    }

    public function updateLevel(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        abort_unless($matchmakerApplication->isCertified(), 422, 'Only certified counselors have a public level.');

        $validated = $request->validate([
            'level' => ['required', 'in:' . implode(',', array_keys(MatchmakerApplication::LEVELS))],
        ]);

        $matchmakerApplication->update($validated);

        return back()->with('status', 'Level updated to "' . MatchmakerApplication::LEVELS[$validated['level']] . '".');
    }

    // Sensitive identity documents — never exposed via a plain public
    // Storage::url() (the disk is 'private'), streamed through this
    // permission-gated route instead, same spirit as NikahFileController.
    public function file(MatchmakerApplication $matchmakerApplication, string $type)
    {
        abort_unless(in_array($type, ['selfie_photo', 'cnic_front_image', 'cnic_back_image'], true), 404);

        $path = $matchmakerApplication->{$type};
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path);
    }

    public function reject(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        abort_if($matchmakerApplication->isTerminal(), 422, 'This application was already rejected or withdrawn.');

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $matchmakerApplication->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'reviewed_by' => auth()->id(),
            'notes' => $validated['notes'] ?? $matchmakerApplication->notes,
        ]);

        return back()->with('status', 'Application rejected.');
    }

    // 'withdrawn' has been in STATUSES/TERMINAL_EXIT_STATUSES since this
    // pipeline was built, but was never actually reachable — updateStatus()
    // only accepts STEPS values and reject() hard-codes 'rejected'. This is
    // the applicant's own choice to leave (they said so, on a call or in
    // writing), distinct from reject() being Sallaamti's decision that
    // they're not suitable — same terminal outcome, different reason on
    // record.
    public function withdraw(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        abort_if($matchmakerApplication->isTerminal(), 422, 'This application was already rejected or withdrawn.');

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $matchmakerApplication->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
            'reviewed_by' => auth()->id(),
            'notes' => $validated['notes'] ?? $matchmakerApplication->notes,
        ]);

        return back()->with('status', 'Application marked as withdrawn.');
    }

    // Marks a counselor's physical ID card as printed and mailed — set
    // from the app's "Request Card Dispatch" button (card_requested_at),
    // this is admin confirming the physical follow-through actually
    // happened. No further automation past this point (no courier
    // integration) — it's a manual record of a manual task.
    public function markCardDispatched(MatchmakerApplication $matchmakerApplication)
    {
        $matchmakerApplication->update(['card_dispatched_at' => now()]);

        return back()->with('status', 'Card marked as dispatched.');
    }

    // Generates (or regenerates, if suspected leaked) the signed link the
    // applicant uses to actually read and accept the Nikah Counselor
    // Agreement + NDA themselves — see Public\MatchmakerAgreementController.
    public function sendAgreementLink(MatchmakerApplication $matchmakerApplication)
    {
        // 24 random alnum chars (~142 bits of entropy) is already far more
        // than enough for a single-use, database-checked secret — kept
        // shorter than the old 40 chars so the shareable link stays clean
        // for WhatsApp/social use instead of looking like a spam link.
        $matchmakerApplication->update(['agreement_link_token' => Str::random(24)]);

        return back()->with('status', 'Agreement link ready — copy it below and send it to ' . $matchmakerApplication->full_name . '. They\'ll need the last 7 digits of their mobile number to open it, every time.');
    }

    public static function agreementLink(MatchmakerApplication $application): ?string
    {
        if (!$application->agreement_link_token) {
            return null;
        }

        return route('public.matchmaker-agreement.show', ['matchmakerApplication' => $application->id, 't' => $application->agreement_link_token]);
    }

    // Creates the account (if the applicant didn't already have one),
    // grants the real 'matchmaker' role for the first time (see hiring
    // document's "don't allow instant → dashboard" rule — this is the one
    // moment that actually happens), mints their MM-PK-###### counselor
    // code, and issues the certificate through the exact same
    // Certificate/DomPDF/verify() pipeline the volunteer ID card already
    // uses (VolunteerAdminController::approve()).
    private function certify(MatchmakerApplication $application): void
    {
        $user = $application->user;

        if (!$user) {
            $user = $this->createMinimalUser($application->full_name, null, $application->mobile_number);
            $application->update(['user_id' => $user->id]);
        }

        // Same both-directions-if-empty sync ProfileController uses for city —
        // gender is collected on the application but lives only on User.
        if (!$user->gender && $application->gender) {
            $user->update(['gender' => $application->gender]);
        }

        if (!$user->hasRole('matchmaker')) {
            $user->assignRole('matchmaker');
        }

        if (!$application->counselor_code) {
            $application->update([
                'counselor_code' => $this->generateCounselorCode(),
                'certified_at' => now(),
            ]);
        }

        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'type' => 'nikah_counselor_id'],
            [
                'course_id' => null,
                'title' => 'Sallaamti Certified Nikah Counselor',
                'certificate_number' => $application->counselor_code,
                'issued_by' => auth()->id(),
                'issued_at' => now(),
            ]
        );

        try {
            $user->notify(new NikahCounselorCertified($application, $certificate));
        } catch (\Throwable $e) {
            \Log::error('NikahCounselorCertified notification failed: ' . $e->getMessage());
        }
    }

    private function generateCounselorCode(): string
    {
        $lastNumber = MatchmakerApplication::whereNotNull('counselor_code')
            ->get()
            ->map(fn ($a) => (int) substr($a->counselor_code, -6))
            ->max() ?? 0;

        return 'MM-PK-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}
