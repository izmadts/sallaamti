<?php

namespace App\Console\Commands;

use App\Models\NikahProfile;
use App\Notifications\NikahProfileCompletionReminder;
use App\Notifications\NikahProfilePausedForNonPayment;
use Illuminate\Console\Command;

// Daily: nudges a self-service member who hasn't paid the Nikah
// verification fee yet, then auto-pauses their profile once the 7-day
// window (set at profile creation — see NikahProfile::startsPaymentDeadline())
// actually runs out. Counselor/admin-created walk-in profiles are never
// touched here — see that same method's own comment on why.
class SendNikahPaymentDeadlineReminders extends Command
{
    protected $signature = 'nikah:send-payment-deadline-reminders';

    protected $description = 'Remind members with an unpaid/rejected Nikah verification fee, then auto-pause once their 7-day deadline passes';

    public function handle(): void
    {
        // 'submitted' is deliberately excluded — that member already acted
        // within the window and is just waiting on admin review now, not
        // stalling; nothing to remind them about or pause them over.
        $profiles = NikahProfile::whereIn('payment_status', ['unpaid', 'rejected'])
            ->whereNotNull('payment_deadline_at')
            ->where('is_active', true)
            ->whereNull('suspended_at')
            ->with('user')
            ->get();

        $reminded = 0;
        $paused = 0;

        foreach ($profiles as $profile) {
            if (!$profile->user || $profile->applicableVerificationFee() <= 0) {
                continue;
            }

            if ($profile->payment_deadline_at->isPast()) {
                $profile->update(['is_active' => false, 'payment_deadline_passed_at' => now()]);

                try {
                    $profile->user->notify(new NikahProfilePausedForNonPayment($profile));
                } catch (\Throwable $e) {
                    \Log::error('NikahProfilePausedForNonPayment notification failed: ' . $e->getMessage());
                }

                $paused++;
                continue;
            }

            $daysRemaining = max(1, (int) ceil(now()->diffInHours($profile->payment_deadline_at) / 24));

            try {
                $profile->user->notify(new NikahProfileCompletionReminder($profile, daysRemaining: $daysRemaining));
            } catch (\Throwable $e) {
                \Log::error('NikahProfileCompletionReminder notification failed: ' . $e->getMessage());
            }

            $reminded++;
        }

        $this->info("Sent {$reminded} reminder(s), paused {$paused} profile(s) past their deadline.");
    }
}
