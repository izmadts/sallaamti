<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Guest-accessible mobile counterpart to the web NikahCounselorApplicationController
// — same validation, same MatchmakerApplication::create() call, same
// "no account required to apply" rule (a real account + 'matchmaker' role
// only get attached once Admin\MatchmakerApplicationController advances an
// application all the way to 'certified'). No honeypot field here since a
// mobile app has no bot-fillable HTML form to protect; throttled instead.
class NikahCounselorApplicationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:30', 'unique:matchmaker_applications,mobile_number'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'in:male,female'],
            'age' => ['required', 'integer', 'min:21', 'max:70'],
            'marital_status' => ['required', 'in:never_married,divorced,widowed,married,separated'],
            'qualification' => ['required', 'in:' . implode(',', array_keys(MatchmakerApplication::QUALIFICATIONS))],
            'qualification_other' => ['required_if:qualification,other', 'nullable', 'string', 'max:100'],
            'selfie_photo' => ['required', 'image', 'max:4096'],
            'cnic_number' => ['required', 'string', 'max:20', 'unique:matchmaker_applications,cnic_number'],
            'cnic_front_image' => ['required', 'image', 'max:4096'],
            'cnic_back_image' => ['required', 'image', 'max:4096'],
            'area' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'payout_method' => ['nullable', 'in:bank_transfer,jazzcash,easypaisa'],
            'payout_account_title' => ['nullable', 'required_with:payout_method', 'string', 'max:255'],
            'payout_account_number' => ['nullable', 'required_with:payout_method', 'string', 'max:50'],
            'payout_bank_name' => ['nullable', 'required_if:payout_method,bank_transfer', 'string', 'max:100'],
            'consent_accepted' => ['accepted'],
            'terms_accepted' => ['accepted'],
        ]);

        $validated['selfie_photo'] = ImageOptimizer::store($request->file('selfie_photo'), 'matchmaker-applications/selfies', 'private');
        $validated['cnic_front_image'] = ImageOptimizer::store($request->file('cnic_front_image'), 'matchmaker-applications/cnic', 'private');
        $validated['cnic_back_image'] = ImageOptimizer::store($request->file('cnic_back_image'), 'matchmaker-applications/cnic', 'private');

        $validated['user_id'] = null;
        $validated['consent_accepted'] = true;
        $validated['terms_accepted'] = true;
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();
        $validated['device_city'] = $this->lookupCity($request->ip());

        MatchmakerApplication::create($validated);

        return response()->json([
            'message' => __('db.Thank you! Your application has been received — our team will review it and be in touch soon.'),
        ], 201);
    }

    private function lookupCity(?string $ip): ?string
    {
        if (!$ip || in_array($ip, ['127.0.0.1', '::1'])) {
            return null;
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,city,regionName']);

            if ($response->ok() && $response->json('status') === 'success') {
                return trim(($response->json('city') ?: '') . ', ' . ($response->json('regionName') ?: ''), ', ');
            }
        } catch (\Throwable $e) {
            // Best-effort — never blocks the actual application.
        }

        return null;
    }
}
