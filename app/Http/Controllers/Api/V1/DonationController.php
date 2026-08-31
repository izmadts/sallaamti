<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\DonationReceived;
use App\Notifications\NewDonationReceived;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// The mobile-app side of DonationController (public web form) and
// DonationAdminController (admin review) — an authenticated member never
// needs to retype their name/email/phone (unlike the guest-oriented web
// form), and gets a history list the web version only exposes via a
// separate session-authenticated page.
class DonationController extends Controller
{
    private const PURPOSES = [
        ['value' => 'general', 'label' => 'General Fund'],
        ['value' => 'quran_education', 'label' => 'Quran Education'],
        ['value' => 'orphan_support', 'label' => 'Orphan Support'],
        ['value' => 'nikah_fund', 'label' => 'Nikah Fund'],
    ];

    private function payload(Donation $donation): array
    {
        return [
            'id' => $donation->id,
            'donation_number' => $donation->donation_number,
            'amount' => $donation->amount,
            'purpose' => $donation->purpose,
            'message' => $donation->message,
            'payment_method' => $donation->payment_method,
            'payment_status' => $donation->payment_status,
            'payment_rejection_reason' => $donation->payment_rejection_reason,
            'is_anonymous' => (bool) $donation->is_anonymous,
            'created_at' => $donation->created_at,
        ];
    }

    public function meta(): JsonResponse
    {
        return response()->json([
            'tiers' => [500, 1000, 3000, 5000, 10000],
            'purposes' => self::PURPOSES,
            'payment_instructions' => [
                'bank_name' => setting('bank_name'),
                'bank_account_number' => setting('bank_account_number'),
                'bank_account_iban' => setting('bank_account_iban'),
                'account_title' => setting('site_name'),
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $donations = $request->user()->donations()->latest()->get();

        return response()->json(['donations' => $donations->map(fn (Donation $d) => $this->payload($d))]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'payment_method' => ['required', 'in:bank_transfer,international'],
            'payment_screenshot' => ['nullable', 'image', 'max:4096'],
            'purpose' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        $fields = [
            'user_id' => $user->id,
            'donor_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'] ?? 'general',
            'message' => $validated['message'] ?? null,
            'payment_method' => $validated['payment_method'],
            // Matches the public web form: no free-text reference is
            // actually collected, admin verifies via the screenshot alone.
            'payment_reference' => 'pending-admin-verify',
            'payment_status' => 'submitted',
            'is_anonymous' => $request->boolean('is_anonymous'),
            'donation_number' => Donation::generateNumber(),
        ];

        if ($request->hasFile('payment_screenshot')) {
            $fields['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'donations/screenshots', 'private');
        }

        $donation = Donation::create($fields);

        try {
            $user->notify(new DonationReceived($donation));
        } catch (\Throwable $e) {
            \Log::error('DonationReceived notification failed: ' . $e->getMessage());
        }

        User::role('admin')->each(function (User $admin) use ($donation) {
            try {
                $admin->notify(new NewDonationReceived($donation));
            } catch (\Throwable $e) {
                \Log::error('NewDonationReceived notification failed: ' . $e->getMessage());
            }
        });

        return response()->json(['donation' => $this->payload($donation)], 201);
    }
}
