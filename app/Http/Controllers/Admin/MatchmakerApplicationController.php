<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\MatchmakerApplication;
use App\Notifications\NikahCounselorCertified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchmakerApplicationController extends Controller
{
    use RegistersMinimalUsers;

    public function index(Request $request)
    {
        $query = MatchmakerApplication::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(20)->withQueryString();

        return view('admin.matchmaker-applications.index', compact('applications'));
    }

    public function show(MatchmakerApplication $matchmakerApplication)
    {
        $matchmakerApplication->load('user', 'reviewedBy');

        return view('admin.matchmaker-applications.show', ['application' => $matchmakerApplication]);
    }

    public function updateStatus(Request $request, MatchmakerApplication $matchmakerApplication)
    {
        abort_if($matchmakerApplication->isTerminal(), 422, 'This application was already rejected or withdrawn.');

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(MatchmakerApplication::STEPS))],
        ]);

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
