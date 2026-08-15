<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Models\NikahProfile;
use App\Models\NikahSavedProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NikahProfileController extends Controller
{
    use ValidatesNikahProfile;

    public function store(Request $request)
    {
        $validated = $this->validateProfile($request);
        $validated['sect'] = $this->resolveSect($request);
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('cnic_front_image')) {
            $validated['cnic_front_image'] = $request->file('cnic_front_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('cnic_back_image')) {
            $validated['cnic_back_image'] = $request->file('cnic_back_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('nikah/photos', 'private');
        }

        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        $validated['open_to_polygamy'] = $request->has('open_to_polygamy');
        $validated['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));
        $validated['public_token'] = Str::random(32);

        NikahProfile::create($validated);

        session()->flash('conversion_event', 'nikah_profile_created');

        return redirect()->route('nikah.payment')->with('status', 'Profile submitted! Please complete the verification fee payment to proceed.');
    }

    public function show()
    {
        $profile = Auth::user()->nikahProfile;

        if (!$profile) {
            return redirect()->route('nikah.create');
        }

        if (!$profile->public_token) {
            $profile->update(['public_token' => Str::random(32)]);
        }

        return view('nikah.show', compact('profile'));
    }

    public function edit()
    {
        $profile = Auth::user()->nikahProfile;

        if (!$profile) {
            return redirect()->route('nikah.create');
        }

        return view('nikah.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->nikahProfile;
        $validated = $this->validateProfile($request, $profile);
        $validated['sect'] = $this->resolveSect($request);
        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        $validated['open_to_polygamy'] = $request->has('open_to_polygamy');

        if ($request->hasFile('cnic_front_image')) {
            $validated['cnic_front_image'] = $request->file('cnic_front_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('cnic_back_image')) {
            $validated['cnic_back_image'] = $request->file('cnic_back_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('nikah/photos', 'private');
        }

        $profile->update($validated);

        if (!empty($validated['city']) && !Auth::user()->city) {
            Auth::user()->update(['city' => $validated['city']]);
        }

        return redirect()->route('nikah.show')->with('status', 'Profile updated successfully.');
    }

    public function browse(Request $request)
    {
        // Redirect guest to register with friendly message
        if (!Auth::check()) {
            return redirect()->route('register')
                ->with('status', 'Please create a free account to browse Nikah profiles.');
        }
        
        $myProfile = Auth::user()->nikahProfile;
      
        if (!$myProfile) {
            return redirect()->route('nikah.create')->with('status', 'Create your profile first to browse matches.');
        }

        // Get blocked IDs BEFORE running the query
        $blockedIds = $myProfile->blockedProfiles()->pluck('blocked_profile_id')->toArray();

        $query = NikahProfile::where('verification_status', 'verified')
            ->where('is_active', true)
            ->whereNull('suspended_at')
            ->where('visibility', 'public')
            ->where('user_id', '!=', Auth::id())
            ->whereNotIn('id', $blockedIds) // ← correct position: inside the query
            ->whereHas('user', function ($q) {
                $q->where('gender', '!=', Auth::user()->gender);
            })->with(['user', 'photos' => function ($q) {
                $q->where('is_primary', true);
            }]);

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('min_age')) {
            $query->where('age', '>=', $request->min_age);
        }
        if ($request->filled('max_age')) {
            $query->where('age', '<=', $request->max_age);
        }
        if ($request->filled('sect')) {
            $query->where('sect', 'like', '%' . $request->sect . '%');
        }
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }
        if ($request->filled('education')) {
            $query->where('education', 'like', '%' . $request->education . '%');
        }
        if ($request->filled('ethnicity')) {
            $query->where('ethnicity', 'like', '%' . $request->ethnicity . '%');
        }
        if ($request->filled('language')) {
            $query->where('language', 'like', '%' . $request->language . '%');
        }
        if ($request->filled('prayer_frequency')) {
            $query->where('prayer_frequency', $request->prayer_frequency);
        }
        if ($request->boolean('open_to_polygamy')) {
            $query->where('open_to_polygamy', true);
        }

        // Match % depends on comparing every candidate against the viewer's
        // preferences in PHP, so we cap the working set instead of loading
        // the entire filtered table into memory as the profile count grows.
        $profiles = $query->orderByDesc('created_at')->limit(300)->get();

        // Calculate match % and sort descending
        $profiles = $profiles->map(function ($profile) use ($myProfile) {
            $breakdown = $profile->matchBreakdownWith($myProfile);
            $profile->match_percentage = $breakdown['percentage'];
            $profile->match_criteria = $breakdown['criteria'];
            return $profile;
        })->sortByDesc('match_percentage');

        // Manual pagination after sorting
        $page = $request->get('page', 1);
        $perPage = 9;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $profiles->forPage($page, $perPage),
            $profiles->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $sentInterestIds = $myProfile->sentInterests()->pluck('receiver_profile_id')->toArray();
        $savedProfileIds = $myProfile->savedProfiles()->pluck('saved_profile_id')->toArray();

        return view('nikah.browse', compact('paginated', 'sentInterestIds', 'savedProfileIds', 'myProfile'));
    }

    public function toggleSave(NikahProfile $profile)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);
        abort_if(\App\Models\NikahBlock::existsBetween($myProfile->id, $profile->id), 403);

        $existing = NikahSavedProfile::where('nikah_profile_id', $myProfile->id)
            ->where('saved_profile_id', $profile->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('status', 'Profile removed from saved.');
        }

        NikahSavedProfile::create([
            'nikah_profile_id' => $myProfile->id,
            'saved_profile_id' => $profile->id,
        ]);

        return back()->with('status', 'Profile saved!');
    }

    public function saved()
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 404);

        $saved = $myProfile->savedProfiles()->with('savedProfile.user')->get();
        $sentInterestIds = $myProfile->sentInterests()->pluck('receiver_profile_id')->toArray();

        return view('nikah.saved', compact('saved', 'sentInterestIds'));
    }

    public function view(NikahProfile $profile)
    {
        // Only show verified, active, non-suspended, public profiles
        abort_unless($profile->isSearchable(), 404);

        $myProfile = Auth::user()->nikahProfile;

        if ($myProfile) {
            abort_if(\App\Models\NikahBlock::existsBetween($myProfile->id, $profile->id), 404);
        }

        // Check if there's a mutual accepted interest
        $hasAcceptedInterest = false;
        $interest = null;

        if ($myProfile) {
            $interest = \App\Models\NikahInterest::where(function ($q) use ($myProfile, $profile) {
                $q->where('sender_profile_id', $myProfile->id)
                    ->where('receiver_profile_id', $profile->id);
            })->orWhere(function ($q) use ($myProfile, $profile) {
                $q->where('receiver_profile_id', $myProfile->id)
                    ->where('sender_profile_id', $profile->id);
            })->first();

            $hasAcceptedInterest = $interest && $interest->status === 'accepted';
        }

        $sentInterest = $myProfile
            ? \App\Models\NikahInterest::where('sender_profile_id', $myProfile->id)
            ->where('receiver_profile_id', $profile->id)
            ->first()
            : null;

        $isSaved = $myProfile
            ? \App\Models\NikahSavedProfile::where('nikah_profile_id', $myProfile->id)
            ->where('saved_profile_id', $profile->id)
            ->exists()
            : false;

        $matchBreakdown = $myProfile ? $profile->matchBreakdownWith($myProfile) : ['percentage' => 0, 'criteria' => []];
        $matchPercentage = $matchBreakdown['percentage'];
        $matchCriteria = $matchBreakdown['criteria'];

        return view('nikah.profile-view', compact(
            'profile',
            'hasAcceptedInterest',
            'interest',
            'sentInterest',
            'isSaved',
            'matchPercentage',
            'matchCriteria'
        ));
    }

    public function publicView(string $token)
    {
        $profile = NikahProfile::where('public_token', $token)->firstOrFail();

        // Possession of the (unguessable, 32-char random) token is the access
        // control here, so this deliberately ignores `visibility` — a Private
        // profile is hidden from search/browse but must still open via the
        // personal link its owner explicitly shared, otherwise "Private" is a
        // one-way trap with no way to ever be seen by anyone. Suspension still
        // applies though — a moderation action must hide the profile everywhere.
        abort_unless(
            $profile->verification_status === 'verified' && $profile->is_active && !$profile->isSuspended(),
            404
        );

        // Remember this page so that if a visitor registers or logs in from
        // here (e.g. to show interest), they land back on this exact profile
        // instead of the generic dashboard.
        if (!Auth::check()) {
            session(['url.intended' => url()->current()]);
        }

        return view('nikah.public-profile', compact('profile'));
    }
}
