<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use App\Models\NikahContactRequest;
use App\Models\NikahProfile;
use App\Services\ImageOptimizer;
use App\Support\CountryStates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NikahVerificationController extends Controller
{
    use ValidatesNikahProfile;

    // Walk-in profile creation moved to NikahProfileWizardController (step
    // wizard, same session-per-step pattern as the member-facing Nikah
    // wizard) — see routes/web.php's admin.nikah.profiles.create.* group.

    // Real-admin-only (route middleware is `admin.only`, not a permission a
    // matchmaker could ever hold) — a matchmaker requests, only admin decides.
    // General "things a counselor asked admin to act on" hub — one URL, a
    // tab per request type (?type=contact|card), rather than a separate
    // page per type. Contact requests were here first; card-dispatch
    // requests (from the app's "Request Card Dispatch" button) joined it
    // as the second tab instead of getting their own standalone page.
    public function contactRequests(Request $request)
    {
        $type = $request->query('type', 'contact');

        $pendingContactCount = NikahContactRequest::where('status', 'pending')->count();
        $pendingCardCount = MatchmakerApplication::whereNotNull('card_requested_at')->whereNull('card_dispatched_at')->count();

        if ($type === 'card') {
            $cardRequests = MatchmakerApplication::whereNotNull('card_requested_at')
                ->orderByRaw('card_dispatched_at is not null')
                ->orderByDesc('card_requested_at')
                ->paginate(20);

            return view('admin.nikah.contact-requests', [
                'type' => 'card',
                'cardRequests' => $cardRequests,
                'pendingContactCount' => $pendingContactCount,
                'pendingCardCount' => $pendingCardCount,
            ]);
        }

        $requests = NikahContactRequest::with(['profile.user', 'requester'])
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->paginate(20);

        return view('admin.nikah.contact-requests', [
            'type' => 'contact',
            'requests' => $requests,
            'pendingContactCount' => $pendingContactCount,
            'pendingCardCount' => $pendingCardCount,
        ]);
    }

    public function approveContactRequest(NikahContactRequest $contactRequest)
    {
        $contactRequest->update([
            'status' => 'approved',
            'decided_by' => auth()->id(),
            'decided_at' => now(),
        ]);

        return back()->with('status', 'Contact request approved — the matchmaker can now see this profile\'s guardian contact.');
    }

    public function denyContactRequest(Request $request, NikahContactRequest $contactRequest)
    {
        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:500']]);

        $contactRequest->update([
            'status' => 'denied',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'decided_by' => auth()->id(),
            'decided_at' => now(),
        ]);

        return back()->with('status', 'Contact request denied.');
    }

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

        // Lets an admin jump straight to "payment confirmed, not yet
        // approved" (or still-pending/rejected payments) right after
        // confirming one — otherwise the just-paid profile is just
        // somewhere in the full latest-first list with no way to narrow
        // down to it quickly.
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
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

        // Payment confirmed + verification still pending is exactly the
        // "ready to approve right now" queue — surface it ahead of
        // everything else (newest first within that) instead of leaving it
        // mixed into the plain latest-first list, where it can be pages
        // behind brand-new unpaid signups.
        $profiles = $query
            ->orderByRaw("(payment_status = 'confirmed' and verification_status = 'pending') desc")
            ->latest()
            ->paginate(10)
            ->withQueryString();

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
            // Accepts a bare or "#"-prefixed profile ID (as it now appears
            // on the profile's own share summary — see NikahProfile::
            // shareSummary()) alongside the existing name/email/phone/city
            // search, so pasting one straight from a shared WhatsApp
            // message finds the exact profile instead of a same-city list.
            $search = trim($request->search);
            $possibleId = ltrim($search, '#');

            $query->where(function ($q) use ($search, $possibleId) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });

                if (ctype_digit($possibleId)) {
                    $q->orWhere('id', $possibleId);
                }
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

        // Match-finding filters — on top of the moderation-focused ones
        // above, so an admin or Nikah Counselor can narrow down to
        // actual candidates for a specific client, not just manage queue
        // state.
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('sect')) {
            $query->where('sect', $request->sect);
        }
        if ($request->filled('education')) {
            $query->where('education', $request->education);
        }
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }
        if ($request->filled('min_age')) {
            $query->where('age', '>=', $request->min_age);
        }
        if ($request->filled('max_age')) {
            $query->where('age', '<=', $request->max_age);
        }
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('state')) {
            $query->where('state', 'like', '%' . $request->state . '%');
        }

        // Only a fixed set of page sizes, not whatever's in the query string
        // — an admin could otherwise request per_page=999999 and force one
        // request to load the entire table.
        $perPage = in_array((int) $request->get('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->per_page
            : 25;

        $profiles = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => NikahProfile::count(),
            'active' => NikahProfile::where('is_active', true)->count(),
            'suspended' => NikahProfile::whereNotNull('suspended_at')->count(),
        ];

        $countries = CountryStates::countries();
        $educationLevels = ['Matric / O-Levels', 'Intermediate / A-Levels', "Bachelor's", "Master's", 'MPhil / MS', 'PhD', 'Madrassah / Islamic Education'];

        return view('admin.nikah.all-profiles', compact('profiles', 'stats', 'countries', 'educationLevels', 'perPage'));
    }

    public function show(NikahProfile $profile)
    {
        $profile->load(['user', 'moderationNotes.admin', 'shares.sharedBy']);

        // Guardian-phone reuse is a soft fraud signal (fake matchmakers
        // running several "client" profiles off one contact number), not a
        // hard block — siblings can legitimately share one guardian's
        // number, so this is surfaced for admin judgment, not validated
        // away. See NikahProfile::guardianContactSharedCount().
        $guardianSharedCount = $profile->guardian_contact
            ? NikahProfile::where('guardian_contact', $profile->guardian_contact)->where('id', '!=', $profile->id)->count()
            : 0;

        return view('admin.nikah.show', compact('profile', 'guardianSharedCount'));
    }

    // All OTHER profiles sharing one guardian contact number — the
    // drill-down an admin reaches by clicking the "N profiles share this
    // contact" flag, so they can compare names/cities/ages side by side
    // before deciding whether it's a real family or a fake-matchmaker ring
    // running several "client" profiles off one number.
    public function guardianGroup(string $contact)
    {
        $profiles = NikahProfile::with('user')
            ->where('guardian_contact', $contact)
            ->latest()
            ->get();

        abort_if($profiles->isEmpty(), 404);

        // All created within the same calendar day is a much stronger
        // red flag than the count alone — a real family adds its children's
        // profiles over months/years as each becomes marriage-age, not all
        // in one sitting.
        $allSameDay = $profiles->pluck('created_at')->map->toDateString()->unique()->count() === 1;

        return view('admin.nikah.guardian-group', compact('profiles', 'contact', 'allSameDay'));
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

        $memberName = $profile->user?->name ?? 'this profile';
        $profile->delete();

        return redirect()->route('admin.nikah.profiles')
            ->with('status', "Nikah profile and all associated data for {$memberName} permanently deleted.");
    }

    public function contact(Request $request, NikahProfile $profile)
    {
        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        if (!$profile->user) {
            return back()->with('error', 'This profile\'s linked account no longer exists — nothing to message.');
        }

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

        return back()->with('status', 'Reminder email sent to ' . ($profile->user?->name ?? 'the member') . '.');
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

    // Logs that staff posted this profile's share card to an external
    // channel — see NikahProfileShare's own comment for why this exists.
    // Doesn't touch the profile itself, purely a record for staff visibility.
    public function recordShare(Request $request, NikahProfile $profile)
    {
        $validated = $request->validate([
            'channel' => ['required', 'string', Rule::in(array_keys(\App\Models\NikahProfileShare::CHANNELS))],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $profile->shares()->create([
            'shared_by' => auth()->id(),
            'channel' => $validated['channel'],
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('status', 'Logged — profile #' . $profile->id . ' marked as shared on ' . \App\Models\NikahProfileShare::CHANNELS[$validated['channel']] . '.');
    }

    // Lets staff toggle a client's "visible in search" status on their
    // behalf — previously only the member themselves could do this (see
    // NikahSafetyController::toggleActive()). A separate lever from
    // Suspend: suspension is a one-way moderation action tied to a report,
    // this is the same reversible on/off switch a member has for
    // themselves, just usable by staff for a client who asked them to
    // pause/resume their own visibility.
    public function toggleActive(NikahProfile $profile)
    {
        abort_if($profile->isSuspended(), 422, 'This profile is suspended — unsuspend it first.');

        $profile->update(['is_active' => !$profile->is_active]);

        return back()->with('status', 'Profile is now ' . ($profile->is_active ? 'active and visible in search.' : 'hidden from search.'));
    }

    // Lets staff edit a client's Nikah profile on their behalf — e.g. the
    // client dictated changes over a call/WhatsApp, or a Nikah Counselor is
    // correcting something for a profile they created. Shares the exact
    // same validation rules/field-resolution helpers as the member's own
    // self-service edit (NikahProfileController::edit()/update()) via
    // ValidatesNikahProfile, just scoped to an arbitrary {profile} instead
    // of the authenticated user's own.
    public function edit(NikahProfile $profile)
    {
        return view('admin.nikah.edit', [
            'profile' => $profile,
            'countries' => CountryStates::countries(),
            'countryStates' => CountryStates::map(),
        ]);
    }

    public function update(Request $request, NikahProfile $profile)
    {
        $validated = $this->validateProfile($request, $profile);
        $validated['sect'] = $this->resolveSect($request);
        $validated['language'] = $this->resolveLanguage($request);
        $validated['height'] = $this->resolveHeight($request);
        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        $validated['open_to_polygamy'] = $request->has('open_to_polygamy');

        // Gender isn't a NikahProfile column — it lives on the client's
        // account — so this writes it there instead, same as the member's
        // own edit form does for themselves.
        $gender = $request->validate(['gender' => ['required', 'in:male,female']])['gender'];
        if ($profile->user && $gender !== $profile->user->gender) {
            $profile->user->update(['gender' => $gender]);
        }

        try {
            if ($request->hasFile('photo')) {
                $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'nikah/photos', 'private', maxDimension: 1200);
            }
        } catch (\Throwable $e) {
            report($e);
            $message = 'Sorry, we could not save the uploaded photo — please try again in a moment.';

            if ($request->wantsJson()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['photo' => $message]);
            }

            return back()->withInput()->withErrors(['photo' => $message]);
        }

        $profile->update($validated);

        // See nikah/edit.blade.php's script (reused as-is for this admin
        // view) for why this form submits via fetch() and expects JSON back.
        if ($request->wantsJson()) {
            return response()->json(['redirect' => route('admin.nikah.show', $profile)]);
        }

        return redirect()->route('admin.nikah.show', $profile)->with('status', 'Profile updated.');
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
