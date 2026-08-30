<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\MatchmakingConsent;
use App\Models\NikahPackage;
use App\Models\Setting;
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

    // Same account details resources/views/nikah/payment.blade.php already
    // shows a paying client — a counselor relaying these over WhatsApp/
    // phone/in person to a client who's paying by JazzCash/EasyPaisa/bank
    // transfer needs the exact same numbers, not a second hand-typed copy
    // that can drift out of sync with what admin has actually configured
    // in Settings. Authenticated (matchmaker.*) since these are real
    // account numbers, unlike the enums above.
    public function paymentAccounts(): JsonResponse
    {
        return response()->json([
            'jazzcash_number' => Setting::get('jazzcash_number'),
            'jazzcash_account_title' => Setting::get('jazzcash_account_title'),
            'easypaisa_number' => Setting::get('easypaisa_number'),
            'bank_name' => Setting::get('bank_name'),
            'bank_account_title' => Setting::get('bank_account_title'),
            'bank_account_number' => Setting::get('bank_account_number'),
            'bank_account_iban' => Setting::get('bank_account_iban'),
        ]);
    }

    // The same admin-managed catalog resources/views/public/matchmaking's
    // package-selection step already reads — a counselor discussing
    // options with a client (in person, over WhatsApp) needs to see the
    // exact same names/prices/limits, not remember them or guess.
    public function packages(): JsonResponse
    {
        return response()->json([
            'packages' => NikahPackage::active()->ordered()->get()->map(fn (NikahPackage $package) => [
                'id' => $package->id,
                'name' => $package->localizedName(),
                'tagline' => $package->localizedTagline(),
                'description' => $package->localizedDescription(),
                'features' => $package->localizedFeatures(),
                'price' => $package->price,
                'currency' => $package->currency,
                'duration_days' => $package->duration_days,
                'proposal_limit' => $package->proposal_limit,
                'consultant_level' => $package->consultant_level,
                'is_one_time' => $package->isOneTime(),
                'color' => $package->color,
                'icon' => $package->icon,
            ]),
        ]);
    }
}
