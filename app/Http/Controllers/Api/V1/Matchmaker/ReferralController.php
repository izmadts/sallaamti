<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use App\Models\MatchmakerReferral;
use Illuminate\Http\JsonResponse;

// Read-only: counselor code, referral link, QR (base64 PNG), referral
// count. Native share sheet on the Flutter side hands the link off.
class ReferralController extends Controller
{
    public function show(): JsonResponse
    {
        $application = MatchmakerApplication::where('user_id', auth()->id())->where('status', 'certified')->first();

        return response()->json([
            'counselor_code' => $application?->counselor_code,
            'referral_link' => $application ? url('/register?ref=' . $application->counselor_code) : null,
            'qr_code_data_uri' => $application?->referralQrCodeBase64(),
            'referral_count' => MatchmakerReferral::where('counselor_user_id', auth()->id())->count(),
        ]);
    }
}
