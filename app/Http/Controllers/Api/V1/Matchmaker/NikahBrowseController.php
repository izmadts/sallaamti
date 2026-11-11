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
        // Only set when browsing on behalf of a specific client (the
        // Shortlist/Batches "pick a candidate" flow) — a plain browse has
        // no client to score candidates against or narrow by gender for.
        $lead = $request->filled('lead_id') ? Lead::with('requirement.items')->find($request->integer('lead_id')) : null;

        // requirement_type => requirement_value, keyed the same as
        // MatchmakingRequirementItem::TYPES — used below only as a fallback
        // for whichever of these the counselor hasn't explicitly filtered
        // by, so picking a candidate for a client starts already narrowed
        // to what's on file instead of a counselor having to re-type it.
        $requirementValues = $lead?->requirement?->items->pluck('requirement_value', 'requirement_type') ?? collect();
        $appliedFilters = [];

        $query = NikahProfile::with('user')->matchmakerVisible();

        if ($request->filled('gender')) {
            $query->whereHas('user', fn ($q) => $q->where('gender', $request->gender));
        } elseif ($lead?->gender) {
            // Picking a candidate for a client of known gender: default to
            // the opposite gender rather than showing everyone and relying
            // on the counselor to remember to filter — still overridable
            // via the Gender filter above if they explicitly set one.
            $query->whereHas('user', fn ($q) => $q->where('gender', $lead->gender === 'male' ? 'female' : 'male'));
        }

        $city = $request->filled('city') ? $request->city : $requirementValues->get('city');
        if ($city) {
            $query->where('city', 'like', '%' . $city . '%');
            $appliedFilters['city'] = $city;
        }

        $sect = $request->filled('sect') ? $request->sect : $requirementValues->get('sect');
        if ($sect) {
            $query->where('sect', 'like', '%' . $sect . '%');
            $appliedFilters['sect'] = $sect;
        }

        $maritalStatus = $request->filled('marital_status') ? $request->marital_status : $requirementValues->get('marital_status');
        if ($maritalStatus) {
            $query->where('marital_status', $maritalStatus);
            $appliedFilters['marital_status'] = $maritalStatus;
        }

        // "25-30" / "25 to 30" / a single "25+"-style minimum — same parsing
        // MatchmakingRequirementItem::ageMatches() uses, so a stored
        // age_range requirement narrows the list the same way it scores it.
        $minAge = $request->filled('min_age') ? $request->min_age : null;
        $maxAge = $request->filled('max_age') ? $request->max_age : null;
        if ($minAge === null && $maxAge === null && $requirementValues->get('age_range')) {
            preg_match_all('/\d+/', $requirementValues->get('age_range'), $matches);
            $numbers = array_map('intval', $matches[0] ?? []);
            if (count($numbers) === 1) {
                $minAge = $numbers[0];
            } elseif (count($numbers) >= 2) {
                [$minAge, $maxAge] = [min($numbers[0], $numbers[1]), max($numbers[0], $numbers[1])];
            }
        }
        if ($minAge !== null) {
            $query->where('age', '>=', $minAge);
            $appliedFilters['min_age'] = $minAge;
        }
        if ($maxAge !== null) {
            $query->where('age', '<=', $maxAge);
            $appliedFilters['max_age'] = $maxAge;
        }

        // Mirrors the web Matchmaker\NikahBrowseController's own education/
        // country/state filters — this API version only ever had
        // gender/city/sect/marital_status/age, so a counselor using the
        // mobile app had less to narrow by than the same role on the web
        // panel for no real reason.
        $education = $request->filled('education') ? $request->education : $requirementValues->get('education');
        if ($education) {
            $query->where('education', 'like', '%' . $education . '%');
            $appliedFilters['education'] = $education;
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
            $appliedFilters['country'] = $request->country;
        }
        if ($request->filled('state')) {
            $query->where('state', 'like', '%' . $request->state . '%');
            $appliedFilters['state'] = $request->state;
        }

        $profiles = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return response()->json([
            'profiles' => collect($profiles->items())->map(fn ($p) => $this->cardPayload($p, lead: $lead)),
            'current_page' => $profiles->currentPage(),
            'last_page' => $profiles->lastPage(),
            'total' => $profiles->total(),
            // What the server actually filtered by, including anything
            // defaulted from the lead's own requirements rather than typed
            // by the counselor — so the picker's filter fields can show it
            // instead of looking empty while the list is already narrowed.
            'applied_filters' => $appliedFilters,
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
