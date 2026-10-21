<?php

namespace App\Console\Commands;

use App\Models\NikahProfile;
use Illuminate\Console\Command;

// One-off, run-once command (not scheduled) — the payment-deadline feature
// only set payment_deadline_at going forward at profile creation, which
// would've left every pre-existing self-service unpaid/rejected profile
// never subject to it at all. The business wants the deadline (and the
// pressure to actually pay) to apply to every member, old and new — this
// gives every qualifying existing profile a fresh 7 days starting from
// today, not retroactively from whenever they originally signed up, so
// nobody gets deactivated with zero warning the moment this ships.
// Safe to re-run — only ever touches profiles with no deadline set yet.
class BackfillNikahPaymentDeadlines extends Command
{
    protected $signature = 'nikah:backfill-payment-deadlines';

    protected $description = 'Give every existing self-service, unpaid/rejected Nikah profile a fresh 7-day payment deadline starting today';

    public function handle(): void
    {
        $profiles = NikahProfile::whereNull('created_by')
            ->whereIn('payment_status', ['unpaid', 'rejected'])
            ->whereNull('payment_deadline_at')
            ->get()
            ->filter(fn (NikahProfile $p) => $p->applicableVerificationFee() > 0);

        foreach ($profiles as $profile) {
            $profile->update(['payment_deadline_at' => now()->addDays(7)]);
        }

        $this->info("Backfilled a 7-day payment deadline for {$profiles->count()} existing profile(s). They'll start getting daily reminders from tomorrow's scheduled run.");
    }
}
