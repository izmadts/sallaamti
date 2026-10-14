<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\MatchmakerApplication;
use App\Support\CountryStates;
use App\Support\PakistanCities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Static reference data the app needs for form dropdowns (Country/State on
// the Nikah wizard, matching the web's <x-country-state-fields> exactly) —
// no auth required, same as /faqs, since none of it is user-specific.
class MetaController extends Controller
{
    public function countryStates(): JsonResponse
    {
        return response()->json([
            'countries' => CountryStates::countries(),
            'states' => CountryStates::map(),
        ]);
    }

    // Live-search, not a preloaded list like countryStates() above — the
    // cities table has ~150k rows, too many to ship in one response. Same
    // pick-or-type combo UX as Education's <x-searchable-select> on the web
    // wizard, just backed by a real query instead of a fixed short list.
    public function cities(Request $request): JsonResponse
    {
        $query = City::query();

        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }
        if ($request->filled('state')) {
            $query->where('state', $request->string('state'));
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', $request->string('search') . '%');
        }

        return response()->json([
            'cities' => $query->orderBy('name')->limit(20)->pluck('name'),
        ]);
    }

    // Drives the guest "Apply to become a Nikah Counselor" form's pickers
    // from the same source-of-truth consts the admin panel and web
    // application form already use.
    public function nikahCounselorApplicationEnums(): JsonResponse
    {
        return response()->json([
            'qualifications' => MatchmakerApplication::QUALIFICATIONS,
            'marital_statuses' => [
                'never_married' => 'Never Married',
                'divorced' => 'Divorced',
                'widowed' => 'Widowed',
                'married' => 'Married',
                'separated' => 'Separated',
            ],
            'payout_methods' => [
                'bank_transfer' => 'Bank Transfer',
                'jazzcash' => 'JazzCash',
                'easypaisa' => 'EasyPaisa',
            ],
            'cities' => PakistanCities::all(),
            'default_country' => 'Pakistan',
        ]);
    }
}
