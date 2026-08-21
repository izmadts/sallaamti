<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewNikahPaymentSubmitted;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NikahPaymentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $profile = $request->user()->nikahProfile;
        abort_unless($profile, 404);

        if ($profile->payment_status === 'confirmed') {
            return response()->json(['message' => __('db.Payment already confirmed.'), 'payment_status' => 'confirmed']);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:jazzcash,bank_transfer'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $validated['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');
        $validated['payment_status'] = 'submitted';
        $validated['payment_rejection_reason'] = null;
        $validated['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));

        $profile->update($validated);

        User::role('admin')->each(function ($admin) use ($profile) {
            try {
                $admin->notify(new NewNikahPaymentSubmitted($profile));
            } catch (\Throwable $e) {
                Log::error('NewNikahPaymentSubmitted notification failed: ' . $e->getMessage());
            }
        });

        return response()->json(['message' => __('db.Payment proof submitted! Our team will confirm it shortly.'), 'payment_status' => 'submitted']);
    }
}
