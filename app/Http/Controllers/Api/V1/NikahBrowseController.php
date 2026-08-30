<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NikahBlock;
use App\Models\NikahProfile;
use App\Models\NikahSavedProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NikahBrowseController extends Controller
{
    // Same three gates as the web's browsingBlockedRedirect(), translated
    // to a JSON error code the app branches on (redirects don't mean
    // anything to an API client) instead of a redirect response.
    private function browsingBlockedResponse(?NikahProfile $myProfile): ?JsonResponse
    {
        if (!$myProfile) {
            return response()->json(['code' => 'no_profile', 'message' => __('db.Create your Nikah profile first to explore matches.')], 409);
        }
        if ($myProfile->payment_status !== 'confirmed') {
            return response()->json(['code' => 'payment_required', 'message' => __('db.Please complete your verification fee payment before you can explore other profiles.')], 402);
        }
        if ($myProfile->verification_status !== 'verified') {
            return response()->json(['code' => 'pending_verification', 'message' => __('db.Your profile is still awaiting verification by our team.')], 403);
        }
        return null;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->gender) {
            return response()->json(['code' => 'gender_required', 'message' => __('db.Please select your gender on your profile first.')], 409);
        }

        $myProfile = $user->nikahProfile;

        if ($blocked = $this->browsingBlockedResponse($myProfile)) {
            return $blocked;
        }

        $blockedIds = $myProfile->blockedProfiles()->pluck('blocked_profile_id')->toArray();

        $query = NikahProfile::memberSearchable()
            ->where('user_id', '!=', $user->id)
            ->whereNotIn('id', $blockedIds)
            ->whereHas('user', fn ($q) => $q->where('gender', '!=', $user->gender))
            ->with(['user', 'photos' => fn ($q) => $q->where('is_primary', true)]);

        foreach (['city', 'sect', 'education', 'ethnicity', 'language', 'family_type'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, 'like', '%' . $request->$field . '%');
            }
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
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }
        if ($request->filled('prayer_frequency')) {
            $query->where('prayer_frequency', $request->prayer_frequency);
        }
        if ($request->boolean('open_to_polygamy')) {
            $query->where('open_to_polygamy', true);
        }

        $profiles = $query->orderByDesc('created_at')->limit(300)->get();

        $profiles = $profiles->map(function ($profile) use ($myProfile) {
            $breakdown = $profile->matchBreakdownWith($myProfile);
            $profile->match_percentage = $breakdown['percentage'];
            return $profile;
        })->sortByDesc('match_percentage')->values();

        $page = max(1, (int) $request->input('page', 1));
        $perPage = 9;
        $pageItems = $profiles->forPage($page, $perPage)->values();

        $sentInterestIds = $myProfile->sentInterests()->pluck('receiver_profile_id')->toArray();
        $savedProfileIds = $myProfile->savedProfiles()->pluck('saved_profile_id')->toArray();

        return response()->json([
            'profiles' => $pageItems->map(fn ($p) => $this->cardPayload($p, $sentInterestIds, $savedProfileIds)),
            'current_page' => $page,
            'has_more' => ($page * $perPage) < $profiles->count(),
            'total' => $profiles->count(),
        ]);
    }

    public function show(Request $request, NikahProfile $profile): JsonResponse
    {
        $user = $request->user();
        $myProfile = $user->nikahProfile;

        if ($blocked = $this->browsingBlockedResponse($myProfile)) {
            return $blocked;
        }

        abort_unless($profile->isSearchable(), 404);
        abort_if(NikahBlock::existsBetween($myProfile->id, $profile->id), 404);

        $breakdown = $profile->matchBreakdownWith($myProfile);

        $interest = \App\Models\NikahInterest::where(function ($q) use ($myProfile, $profile) {
            $q->where('sender_profile_id', $myProfile->id)->where('receiver_profile_id', $profile->id);
        })->orWhere(function ($q) use ($myProfile, $profile) {
            $q->where('receiver_profile_id', $myProfile->id)->where('sender_profile_id', $profile->id);
        })->first();

        $isSaved = NikahSavedProfile::where('nikah_profile_id', $myProfile->id)->where('saved_profile_id', $profile->id)->exists();

        return response()->json([
            'profile' => $this->cardPayload($profile, [], [], detailed: true),
            'match_percentage' => $breakdown['percentage'],
            'match_criteria' => $breakdown['criteria'],
            'interest_status' => $interest?->status,
            'is_mine_sent' => $interest && (int) $interest->sender_profile_id === (int) $myProfile->id,
            'is_saved' => $isSaved,
        ]);
    }

    public function toggleSave(Request $request, NikahProfile $profile): JsonResponse
    {
        $myProfile = $request->user()->nikahProfile;
        abort_unless($myProfile, 403);
        abort_if(NikahBlock::existsBetween($myProfile->id, $profile->id), 403);

        $existing = NikahSavedProfile::where('nikah_profile_id', $myProfile->id)->where('saved_profile_id', $profile->id)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        NikahSavedProfile::create(['nikah_profile_id' => $myProfile->id, 'saved_profile_id' => $profile->id]);
        return response()->json(['saved' => true]);
    }

    private function cardPayload(NikahProfile $profile, array $sentInterestIds, array $savedProfileIds, bool $detailed = false): array
    {
        $data = [
            'id' => $profile->id,
            'age' => $profile->age,
            'height' => $profile->height,
            'marital_status' => $profile->marital_status,
            'sect' => $profile->sect,
            'education' => $profile->education,
            'profession' => $profile->profession,
            'city' => $profile->city,
            'country' => $profile->country,
            'about' => $profile->about,
            'ethnicity' => $profile->ethnicity,
            'gender' => $profile->user?->gender,
            'trust_badges' => $profile->trustBadges(),
            'match_percentage' => $profile->match_percentage ?? 0,
            'has_sent_interest' => in_array($profile->id, $sentInterestIds),
            'is_saved' => in_array($profile->id, $savedProfileIds),
            'photo_url' => $profile->photo ? route('api.v1.nikah.file', [$profile, 'photo']) : null,
            'created_at' => $profile->created_at?->toIso8601String(),
            'last_active_at' => $profile->last_active_at?->toIso8601String(),
        ];

        if ($detailed) {
            $data['expectations'] = $profile->expectations;
            $data['family_type'] = $profile->family_type;
            $data['ethnicity'] = $profile->ethnicity;
            $data['language'] = $profile->language;
            $data['prayer_frequency'] = $profile->prayer_frequency;
            $data['hijab_or_beard'] = $profile->hijab_or_beard;
            $data['diet'] = $profile->diet;
        }

        return $data;
    }
}
