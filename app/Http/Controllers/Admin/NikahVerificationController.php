<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NikahVerificationController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => NikahProfile::count(),
            'verified' => NikahProfile::where('verification_status', 'verified')->count(),
            'pending' => NikahProfile::where('verification_status', 'pending')->count(),
            'rejected' => NikahProfile::where('verification_status', 'rejected')->count(),
            'pending_payments' => NikahProfile::where('payment_status', 'submitted')->count(),
            'male' => NikahProfile::whereHas('user', fn($q) => $q->where('gender', 'male'))->count(),
            'female' => NikahProfile::whereHas('user', fn($q) => $q->where('gender', 'female'))->count(),
            'new_this_week' => NikahProfile::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $query = NikahProfile::with(['user', 'moderationNotes.admin']);

        // Filter by verification status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('verification_status', $request->status);
        }

        // Filter by gender (via related user)
        if ($request->filled('gender')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        // Search by name, email, city, or CNIC number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('cnic_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $profiles = $query->latest()->paginate(10)->withQueryString();

        // Guardian-phone reuse is a soft fraud signal, not a hard block —
        // siblings can legitimately share one guardian's contact number, so
        // this is surfaced for admin judgment rather than validated away.
        $guardianContactCounts = NikahProfile::whereIn('guardian_contact', $profiles->pluck('guardian_contact')->filter())
            ->selectRaw('guardian_contact, count(*) as total')
            ->groupBy('guardian_contact')
            ->having('total', '>', 1)
            ->pluck('total', 'guardian_contact');

        return view('admin.nikah.nikah-verifications', compact('profiles', 'stats', 'guardianContactCounts'));
    }

    // A lightweight, browsable roster of every Nikah profile — separate from
    // the moderation-queue-style `index()` above, which is cluttered with
    // approve/reject/CNIC-review actions and isn't meant for just looking
    // someone up.
    public function directory(Request $request)
    {
        $query = NikahProfile::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('gender')) {
            $query->whereHas('user', fn ($q) => $q->where('gender', $request->gender));
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->active === 'active');
        }

        $profiles = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total' => NikahProfile::count(),
            'active' => NikahProfile::where('is_active', true)->count(),
            'suspended' => NikahProfile::whereNotNull('suspended_at')->count(),
        ];

        return view('admin.nikah.all-profiles', compact('profiles', 'stats'));
    }

    public function show(NikahProfile $profile)
    {
        $profile->load(['user', 'moderationNotes.admin']);

        return view('admin.nikah.show', compact('profile'));
    }

    // Complete, irreversible wipe — the user account itself stays intact,
    // only the Nikah profile and everything tied to it (interests, saved
    // profiles, blocks, reports, moderation notes, photos) disappears. The
    // DB rows cascade automatically (all `cascadeOnDelete()`), but the
    // actual files on disk don't, so those must be removed explicitly
    // first — otherwise they'd become permanently orphaned dead weight.
    public function destroy(NikahProfile $profile)
    {
        $profile->load('photos');

        foreach (['photo', 'cnic_front_image', 'cnic_back_image', 'payment_screenshot'] as $field) {
            if ($profile->$field) {
                Storage::disk('private')->delete($profile->$field);
            }
        }

        foreach ($profile->photos as $galleryPhoto) {
            Storage::disk('private')->delete($galleryPhoto->path);
        }

        $memberName = $profile->user->name;
        $profile->delete();

        return redirect()->route('admin.nikah.profiles')
            ->with('status', "Nikah profile and all associated data for {$memberName} permanently deleted.");
    }

    public function contact(Request $request, NikahProfile $profile)
    {
        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        try {
            $profile->user->notify(new \App\Notifications\NikahAdminMessage($request->message));
        } catch (\Throwable $e) {
            \Log::error('NikahAdminMessage notification failed: ' . $e->getMessage());
            return back()->with('error', 'Could not send the message — check the mail log.');
        }

        $profile->moderationNotes()->create([
            'admin_id' => auth()->id(),
            'note' => "Contacted member: \"{$request->message}\"",
        ]);

        return back()->with('status', 'Message sent to ' . $profile->user->name . '.');
    }

    public function verifyGuardian(NikahProfile $profile)
    {
        $profile->update(['guardian_verified_at' => now()]);

        return back()->with('status', 'Guardian contact marked as verified.');
    }

    public function sendReminder(NikahProfile $profile)
    {
        abort_unless(in_array($profile->payment_status, ['unpaid', 'rejected']), 422, 'This profile is already past the payment step — nothing to remind them about.');

        try {
            $profile->user->notify(new \App\Notifications\NikahProfileCompletionReminder($profile));
        } catch (\Throwable $e) {
            \Log::error('NikahProfileCompletionReminder notification failed: ' . $e->getMessage());
            return back()->with('status', 'Could not send reminder — check the mail log.');
        }

        return back()->with('status', 'Reminder email sent to ' . $profile->user->name . '.');
    }

    public function bulkRemind(Request $request)
    {
        $request->validate(['profile_ids' => ['required', 'array'], 'profile_ids.*' => ['integer', 'exists:nikah_profiles,id']]);

        $profiles = NikahProfile::whereIn('id', $request->profile_ids)
            ->whereIn('payment_status', ['unpaid', 'rejected'])
            ->get();

        foreach ($profiles as $profile) {
            try {
                $profile->user->notify(new \App\Notifications\NikahProfileCompletionReminder($profile));
            } catch (\Throwable $e) {
                \Log::error('NikahProfileCompletionReminder notification failed: ' . $e->getMessage());
            }
        }

        return back()->with('status', "Reminder sent to {$profiles->count()} profile(s).");
    }

    public function addNote(Request $request, NikahProfile $profile)
    {
        $request->validate(['note' => ['required', 'string', 'max:1000']]);

        $profile->moderationNotes()->create([
            'admin_id' => auth()->id(),
            'note' => $request->note,
        ]);

        return back()->with('status', 'Note added.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate(['profile_ids' => ['required', 'array'], 'profile_ids.*' => ['integer', 'exists:nikah_profiles,id']]);

        $profiles = NikahProfile::whereIn('id', $request->profile_ids)
            ->where('payment_status', 'confirmed')
            ->get();

        foreach ($profiles as $profile) {
            $profile->update(['verification_status' => 'verified', 'rejection_reason' => null]);
            try {
                $profile->user->notify(new \App\Notifications\NikahProfileVerified());
            } catch (\Throwable $e) {
                \Log::error('NikahProfileVerified notification failed: ' . $e->getMessage());
            }
        }

        $skipped = count($request->profile_ids) - $profiles->count();
        $status = "{$profiles->count()} profile(s) approved.";
        if ($skipped > 0) {
            $status .= " {$skipped} skipped — payment not confirmed yet.";
        }

        return back()->with('status', $status);
    }

    public function approve(NikahProfile $profile)
    {
        abort_unless($profile->payment_status === 'confirmed', 403, 'Payment must be confirmed first.');

        $profile->update(['verification_status' => 'verified', 'rejection_reason' => null]);

        try {
            $profile->user->notify(new \App\Notifications\NikahProfileVerified());
        } catch (\Throwable $e) {
            \Log::error('NikahProfileVerified notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Profile approved.');
    }

    public function reject(Request $request, NikahProfile $profile)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $profile->update(['verification_status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);

        try {
            $profile->user->notify(new \App\Notifications\NikahProfileRejected($request->rejection_reason));
        } catch (\Throwable $e) {
            \Log::error('NikahProfileRejected notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Profile rejected.');
    }
}
