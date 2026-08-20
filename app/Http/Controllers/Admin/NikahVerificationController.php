<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Http\Controllers\Controller;
use App\Models\NikahContactRequest;
use App\Models\NikahProfile;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Services\ImageOptimizer;
use App\Support\CountryStates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NikahVerificationController extends Controller
{
    use ValidatesNikahProfile, RegistersMinimalUsers;

    // Deliberately checked in-controller rather than route middleware —
    // nikah.create-profile is narrower than nikah.manage (matchmaker has
    // only the former), and Laravel's `can:` route middleware has no
    // built-in OR between two separate abilities.
    private function authorizeProfileCreation(): void
    {
        abort_unless(auth()->user()->can('nikah.manage') || auth()->user()->can('nikah.create-profile'), 403);
    }

    // Admin/matchmaker data-entry for a walk-in registrant who can't create
    // their own account — creates the User and NikahProfile together in one
    // step. Held to the exact same validation (incl. required CNIC photos)
    // as a member creating their own profile — this is in-person, consented
    // data entry, not the same thing as a matchmaker browsing an existing
    // member's CNIC/photo without their knowledge (see the Matchmaker
    // browse controller, which redacts both).
    public function create()
    {
        $this->authorizeProfileCreation();

        return view('admin.nikah.create', [
            'countries' => CountryStates::countries(),
            'countryStates' => CountryStates::map(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeProfileCreation();

        $accountValidated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
        ]);

        $identifier = trim($request->identifier);
        $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);
        if (!$isEmail) {
            $identifier = preg_replace('/[\s\-]/', '', $identifier);
        }

        Validator::make(['identifier' => $identifier], [
            'identifier' => [
                $isEmail ? 'email' : new ValidPhoneNumber(),
                Rule::unique(User::class, $isEmail ? 'email' : 'phone'),
            ],
        ])->validate();

        $validated = $this->validateProfile($request);
        $validated['sect'] = $this->resolveSect($request);
        $validated['language'] = $this->resolveLanguage($request);
        $validated['height'] = $this->resolveHeight($request);

        try {
            if ($request->hasFile('cnic_front_image')) {
                $validated['cnic_front_image'] = ImageOptimizer::store($request->file('cnic_front_image'), 'nikah/cnic', 'private', maxDimension: 1600, quality: 85);
            }
            if ($request->hasFile('cnic_back_image')) {
                $validated['cnic_back_image'] = ImageOptimizer::store($request->file('cnic_back_image'), 'nikah/cnic', 'private', maxDimension: 1600, quality: 85);
            }
            if ($request->hasFile('photo')) {
                $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'nikah/photos', 'private', maxDimension: 1200);
            }
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['photo' => 'Sorry, we could not save the uploaded photo(s) — please try again in a moment.']);
        }

        $user = $this->createMinimalUser(
            $accountValidated['name'],
            $isEmail ? strtolower($identifier) : null,
            $isEmail ? null : $identifier,
        );
        $user->update(['gender' => $accountValidated['gender'], 'city' => $validated['city'] ?? null]);

        $validated['user_id'] = $user->id;
        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        $validated['open_to_polygamy'] = $request->has('open_to_polygamy');
        $validated['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));
        $validated['public_token'] = Str::random(32);

        $profile = NikahProfile::create($validated);

        $status = "Profile created for {$user->name}.";

        if ($isEmail) {
            Password::sendResetLink(['email' => $user->email]);
            $status .= ' A password-setup link was emailed to them so they can log in.';
        } else {
            $status .= ' No email was provided, so no login link could be sent — add one to their account later via Users, then use "Send Password Reset Link" there.';
        }

        return redirect()->route('admin.nikah.show', $profile)->with('status', $status);
    }

    // Real-admin-only (route middleware is `admin.only`, not a permission a
    // matchmaker could ever hold) — a matchmaker requests, only admin decides.
    public function contactRequests()
    {
        $requests = NikahContactRequest::with(['profile.user', 'requester'])
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->paginate(20);

        return view('admin.nikah.contact-requests', compact('requests'));
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
