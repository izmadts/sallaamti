<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\User;
use App\Notifications\NikahCounselorCertified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        // Shown on the permanent-delete confirmation so admin isn't
        // guessing what "everything tied to this counselor" actually means.
        $leadCount = $matchmakerApplication->user_id
            ? Lead::where('assigned_to', $matchmakerApplication->user_id)->orWhere('created_by', $matchmakerApplication->user_id)->count()
            : 0;
        $commissionCount = $matchmakerApplication->user_id
            ? \App\Models\CommissionLedgerEntry::where('matchmaker_id', $matchmakerApplication->user_id)->count()
            : 0;

        return view('admin.matchmaker-applications.show', [
            'application' => $matchmakerApplication,
            'performance' => $performance,
            'levelProgress' => $levelProgress,
            'leadCount' => $leadCount,
            'commissionCount' => $commissionCount,
        ]);
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

    // Certifying a counselor creates their account with an unguessable
    // random password nobody ever sees (RegistersMinimalUsers::
    // createMinimalUser()'s ?? Str::random(40) fallback) — unlike a client
    // registered through the walk-in wizard, nothing here ever sent the new
    // counselor a password-reset link, so there was no way for them to log
    // in until admin used this. Mirrors Matchmaker\ClientController::
    // setLoginPassword() exactly (same must_change_password prompt on
    // first login); the form's own "Generate" button fills in a random
    // password client-side so admin doesn't have to think one up.
    public function setPassword(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        $user = $matchmakerApplication->user;
        abort_unless($user, 422, 'This application has no linked Sallaamti account yet — certify it first.');

        $validated = $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
        ]);

        return back()->with('status', 'Login password set — share it with the counselor now.');
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

    // Undoes an accidental/mistaken reject() — deliberately admin-only
    // (not the broader matchmaker-applications.manage permission the rest
    // of this controller uses), since reversing a rejection is a rarer,
    // more sensitive call than the routine stage/reject/withdraw actions.
    // reject() has no side effects beyond the row itself (no role granted/
    // revoked, no notification sent - see reject()'s own comment), so
    // there's nothing else to undo: just clear the rejection and drop the
    // application back to the first pipeline stage for admin to
    // re-advance manually via the existing "Move to Stage" control.
    public function reactivate(MatchmakerApplication $matchmakerApplication)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Only an admin can reactivate a rejected application.');
        abort_unless($matchmakerApplication->status === 'rejected', 422, 'Only a rejected application can be reactivated.');

        $matchmakerApplication->update([
            'status' => 'applied',
            'rejected_at' => null,
            'reviewed_by' => auth()->id(),
            'notes' => trim(($matchmakerApplication->notes ? $matchmakerApplication->notes . "\n\n" : '')
                . 'Reactivated by ' . auth()->user()->name . ' on ' . now()->format('d M Y') . ' — moved back to "Application Received."'),
        ]);

        return back()->with('status', 'Application reactivated — back at "Application Received." Move it forward through the pipeline as needed.');
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

    // Admin-only, permanent, unrecoverable — meant for cleaning up a
    // mistaken/test entry, not for offboarding a real counselor (that
    // permanently erases their CommissionLedgerEntry payout history along
    // with everything else). Deletes every Lead they created or were
    // assigned (which cascades to that lead's shortlist items, timeline,
    // requirements, consents, proposal batches, and messages via existing
    // DB foreign keys), then the application row itself, then the User
    // account (which separately cascades to their Certificate,
    // CommissionLedgerEntry, ProposalBatch, and MatchmakerReferral rows).
    // Client accounts/NikahProfiles those leads pointed at are untouched -
    // deleting a Lead only removes the counselor's own tracking of that
    // client relationship, never the client's own record.
    public function destroy(MatchmakerApplication $matchmakerApplication)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Only an admin can permanently delete a Nikah Counselor.');

        $user = $matchmakerApplication->user;

        DB::transaction(function () use ($matchmakerApplication, $user) {
            if ($user) {
                Lead::where('assigned_to', $user->id)->orWhere('created_by', $user->id)->delete();
            }

            $matchmakerApplication->delete();

            $user?->delete();
        });

        return redirect()->route('admin.matchmaker-applications.index')->with('status', 'Nikah Counselor permanently deleted.');
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
