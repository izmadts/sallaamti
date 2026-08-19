<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesNikahProfile;
use App\Models\NikahProfile;
use App\Models\NikahSavedProfile;
use App\Services\ImageOptimizer;
use App\Support\CountryStates;
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
        $validated['language'] = $this->resolveLanguage($request);
        $validated['height'] = $this->resolveHeight($request);
        $validated['user_id'] = Auth::id();

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
            return back()->withInput()->withErrors(['photo' => 'Sorry, we could not save your uploaded photo(s) — please try again in a moment. If this keeps happening, contact support.']);
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

        // Nothing on this page needs action until the fee is paid — land the
        // member straight on the payment screen (the actual final step) each
        // time they come back to their profile, instead of a "Pay Now" link
        // they have to notice and click themselves.
        if ($profile->payment_status !== 'confirmed') {
            return redirect()->route('nikah.payment');
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

        return view('nikah.edit', [
            'profile' => $profile,
            'countries' => CountryStates::countries(),
            'countryStates' => CountryStates::map(),
        ]);
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->nikahProfile;
        $validated = $this->validateProfile($request, $profile);
        $validated['sect'] = $this->resolveSect($request);
        $validated['language'] = $this->resolveLanguage($request);
        $validated['height'] = $this->resolveHeight($request);
        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        $validated['open_to_polygamy'] = $request->has('open_to_polygamy');

        // Gender isn't a NikahProfile column — it lives on the account and
        // is never duplicated — but this form shows it too since matching
        // depends on it. Write it straight through to the account instead.
        $gender = $request->validate(['gender' => ['required', 'in:male,female']])['gender'];
        if ($gender !== Auth::user()->gender) {
            Auth::user()->update(['gender' => $gender]);
        }

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
            return back()->withInput()->withErrors(['photo' => 'Sorry, we could not save your uploaded photo(s) — please try again in a moment. If this keeps happening, contact support.']);
        }

        $profile->update($validated);

        if (!empty($validated['city']) && !Auth::user()->city) {
            Auth::user()->update(['city' => $validated['city']]);
        }

        return redirect()->route('nikah.show')->with('status', 'Profile updated successfully.');
    }

    // A profile that hasn't paid its verification fee or hasn't been approved
    // yet can still see and reach out to fully verified members — one-sided
    // and exactly the kind of unvetted access the verification fee and admin
    // review exist to prevent. Gate every browse/view entry point behind
    // "your own profile is paid and verified" instead, with a redirect that
    // sends the member straight to whichever step is actually blocking them.
    protected function browsingBlockedRedirect(?NikahProfile $myProfile)
    {
        if (!$myProfile) {
            return redirect()->route('nikah.create')->with('status', 'Create your Nikah profile first to explore matches.');
        }

        if ($myProfile->payment_status !== 'confirmed') {
            return redirect()->route('nikah.payment')->with('status', 'Please complete your verification fee payment before you can explore other profiles.');
        }

        if ($myProfile->verification_status !== 'verified') {
            return redirect()->route('nikah.show')->with('status', "Your profile is still awaiting verification by our team. You'll be able to explore matches once it's approved.");
        }

        return null;
    }

    public function browse(Request $request)
    {
        // Redirect guest to register with friendly message
        if (!Auth::check()) {
            return redirect()->route('register')
                ->with('status', 'Please create a free account to browse Nikah profiles.');
        }
        
        // Matching is opposite-gender and reads straight from the account —
        // if it's ever blank (legacy data, or the wizard's own guard was
        // bypassed), `gender != NULL` matches nothing in SQL, so this would
        // otherwise silently show an empty list with no explanation.
        if (!Auth::user()->gender) {
            return redirect()->route('profile.edit')
                ->with('status', 'Please select your gender on your profile first — it\'s needed for Nikah matching.');
        }

        $myProfile = Auth::user()->nikahProfile;

        if ($blocked = $this->browsingBlockedRedirect($myProfile)) {
            return $blocked;
        }

        // Get blocked IDs BEFORE running the query
        $blockedIds = $myProfile->blockedProfiles()->pluck('blocked_profile_id')->toArray();

        // Deliberately not filtering on verification_status here — profiles
        // still awaiting admin review show up too (with a "Verification
        // Pending" badge on the card instead of the earned trust badges),
        // otherwise the vast majority of real profiles are invisible until
        // an admin gets to them. is_active/suspended/public are different,
        // genuine safety/privacy concerns and stay enforced.
        $query = NikahProfile::where('is_active', true)
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
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }
        if ($request->filled('country')) {
            $query->where('country', $request->country);
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
        if ($request->filled('family_type')) {
            $query->where('family_type', 'like', '%' . $request->family_type . '%');
        }
        if ($request->filled('min_height') || $request->filled('max_height')) {
            // Height is stored as free text (e.g. 5'8"), not a number, so a
            // min/max range can't be a simple DB comparison — instead, build
            // the fixed list of selectable heights with their inch value,
            // narrow it to the requested range, and match against those
            // exact strings. Any "Other" free-text height falls outside this
            // range filter, same as it always has for the exact-match version.
            $heightInches = [];
            for ($ft = 4; $ft <= 7; $ft++) {
                for ($in = 0; $in <= 11; $in++) {
                    if ($ft === 7 && $in > 0) break;
                    $heightInches[$ft . "'" . $in . '"'] = $ft * 12 + $in;
                }
            }
            $minInches = $heightInches[$request->min_height] ?? min($heightInches);
            $maxInches = $heightInches[$request->max_height] ?? max($heightInches);
            $matchingHeights = array_keys(array_filter($heightInches, fn($inches) => $inches >= $minInches && $inches <= $maxInches));
            $query->whereIn('height', $matchingHeights);
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

        // Infinite scroll: the page's JS re-requests this same URL with
        // page=2, 3, ... and an AJAX header once the viewer nears the
        // bottom, and just wants the next batch of cards back, not a full
        // page reload.
        if ($request->ajax()) {
            return response()->json([
                'html' => view('nikah.partials.profile-cards', compact('paginated', 'sentInterestIds', 'savedProfileIds'))->render(),
                'has_more' => $paginated->hasMorePages(),
            ]);
        }

        $countries = CountryStates::countries();
        $countryStates = CountryStates::map();

        return view('nikah.browse', compact('paginated', 'sentInterestIds', 'savedProfileIds', 'myProfile', 'countries', 'countryStates'));
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
        $myProfile = Auth::user()->nikahProfile;

        if ($blocked = $this->browsingBlockedRedirect($myProfile)) {
            return $blocked;
        }

        // Only show verified, active, non-suspended, public profiles
        abort_unless($profile->isSearchable(), 404);

        abort_if(\App\Models\NikahBlock::existsBetween($myProfile->id, $profile->id), 404);

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
