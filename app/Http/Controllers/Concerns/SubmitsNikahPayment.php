<?php

namespace App\Http\Controllers\Concerns;

use App\Models\NikahProfile;
use App\Models\User;
use App\Notifications\NewNikahPaymentSubmitted;

// Shared by the walk-in wizard's payment step and the standalone
// matchmaker payment-submission action (Matchmaker\NikahBrowseController)
// — same outcome as a member submitting their own payment.blade.php form:
// status flips to 'submitted' and lands in the normal admin review queue
// (Admin\NikahPaymentAdminController), never auto-confirmed. A matchmaker
// relaying a receipt is no more (and no less) trusted than a member
// submitting their own — admin reviews both identically.
trait SubmitsNikahPayment
{
    private function recordNikahPaymentSubmission(NikahProfile $profile, string $method, ?string $reference, string $screenshotPath): void
    {
        $profile->update([
            'payment_method' => $method,
            'payment_reference' => $reference,
            'payment_screenshot' => $screenshotPath,
            'payment_amount' => $profile->applicableVerificationFee(),
            'payment_status' => 'submitted',
            'payment_rejection_reason' => null,
        ]);

        User::role('admin')->each(function ($admin) use ($profile) {
            try {
                $admin->notify(new NewNikahPaymentSubmitted($profile));
            } catch (\Throwable $e) {
                \Log::error('NewNikahPaymentSubmitted notification failed: ' . $e->getMessage());
            }
        });
    }
}
