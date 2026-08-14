<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PurgeDeactivatedAccounts extends Command
{
    protected $signature = 'users:purge-deactivated';

    protected $description = 'Permanently delete accounts that have been deactivated for 30+ days';

    public const GRACE_PERIOD_DAYS = 30;

    public function handle(): void
    {
        $users = User::whereNotNull('deactivated_at')
            ->where('deactivated_at', '<=', now()->subDays(self::GRACE_PERIOD_DAYS))
            ->get();

        foreach ($users as $user) {
            $user->delete();
        }

        $this->info("Permanently deleted {$users->count()} account(s) past the 30-day deactivation grace period.");
    }
}
