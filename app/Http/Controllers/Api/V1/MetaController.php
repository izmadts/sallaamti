<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\CountryStates;
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
}
