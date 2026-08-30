<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\NikahContactRequest;
use App\Models\NikahProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// A matchmaker can browse every profile to help arrange matches, but never
// sees identity-revealing fields (CNIC, photo) — mirrors the web
// Matchmaker\NikahBrowseController's index/show/requestContact exactly,
// same NikahProfile::matchmakerVisible() scope, same redaction.
class NikahBrowseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = NikahProfile::with('user')->matchmakerVisible();

        if ($request->filled('gender')) {
            $query->whereHas('user', fn ($q) => $q->where('gender', $request->gender));
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('sect')) {
            $query->where('sect', 'like', '%' . $request->sect . '%');
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

        $profiles = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Only set when browsing on behalf of a specific client (the
        // Shortlist/Batches "pick a candidate" flow) — a plain browse has
        // no client to score candidates against.
        $lead = $request->filled('lead_id') ? Lead::with('requirement.items')->find($request->integer('lead_id')) : null;

        return response()->json([
            'profiles' => collect($profiles->items())->map(fn ($p) => $this->cardPayload($p, lead: $lead)),
            'current_page' => $profiles->currentPage(),
            'last_page' => $profiles->lastPage(),
            'total' => $profiles->total(),
        ]);
    }

    public function show(NikahProfile $profile): JsonResponse
    {
        $existingRequest = NikahContactRequest::where('nikah_profile_id', $profile->id)
            ->where('requested_by', auth()->id())
            ->latest()
            ->first();

        return response()->json([
            'profile' => $this->cardPayload($profile, detailed: true),
            'existing_request_status' => $existingRequest?->status,
        ]);
    }

    public function requestContact(NikahProfile $profile): JsonResponse
    {
        $pending = NikahContactRequest::where('nikah_profile_id', $profile->id)
            ->where('requested_by', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($pending) {
            return response()->json(['message' => 'You already have a request on file for this profile.'], 409);
        }

        NikahContactRequest::create([
            'nikah_profile_id' => $profile->id,
            'requested_by' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json(['message' => __('db.Contact request sent to admin for review.')], 201);
    }

    private function cardPayload(NikahProfile $profile, bool $detailed = false, ?Lead $lead = null): array
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
            'gender' => $profile->user?->gender,
        ];

        if ($lead) {
            $data['match_score'] = $lead->matchScoreWith($profile);
        }

        if ($detailed) {
            $data['about'] = $profile->about;
            $data['expectations'] = $profile->expectations;
            $data['family_type'] = $profile->family_type;
            $data['ethnicity'] = $profile->ethnicity;
            $data['language'] = $profile->language;
            $data['prayer_frequency'] = $profile->prayer_frequency;
            $data['is_linked_to_lead'] = Lead::where('nikah_profile_id', $profile->id)->exists();
        }

        return $data;
    }
}
