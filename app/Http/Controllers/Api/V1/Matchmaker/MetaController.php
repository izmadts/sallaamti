<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\MatchmakingConsent;
use Illuminate\Http\JsonResponse;

// Every enumerable field in the app (consent type/method, lead status/source,
// proposal response) is driven from these backend source-of-truth arrays
// rather than a second hardcoded Dart copy, so a picker never drifts from
// what the backend actually accepts.
class MetaController extends Controller
{
    public function enums(): JsonResponse
    {
        return response()->json([
            'consent_types' => MatchmakingConsent::TYPES,
            'consent_methods' => MatchmakingConsent::METHODS,
            'lead_statuses' => [
                'new' => 'New',
                'contacted' => 'Contacted',
                'interested' => 'Interested',
                'registered' => 'Registered',
                'not_interested' => 'Not Interested',
                'closed' => 'Closed',
            ],
            'lead_sources' => [
                'facebook' => 'Facebook',
                'instagram' => 'Instagram',
                'whatsapp' => 'WhatsApp',
                'website' => 'Website',
                'phone' => 'Phone',
                'referral' => 'Referral',
                'manual' => 'Manual',
                'other' => 'Other',
            ],
            'looking_for' => [
                'self' => 'For Myself',
                'family_member' => 'For a Family Member',
            ],
            'requirement_priorities' => [
                'must_have' => 'Must Have',
                'preferred' => 'Preferred',
                'flexible' => 'Flexible',
            ],
        ]);
    }
}
