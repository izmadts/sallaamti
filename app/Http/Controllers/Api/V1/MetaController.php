<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use App\Support\CountryStates;
use App\Support\PakistanCities;
use Illuminate\Http\JsonResponse;

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
