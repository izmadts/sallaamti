<?php

namespace App\Console\Commands;

use App\Exceptions\UserDeletionBlockedException;
use App\Models\User;
use App\Services\UserDeletionService;
use Illuminate\Console\Command;

class PurgeDeactivatedAccounts extends Command
{
    protected $signature = 'users:purge-deactivated';

    protected $description = 'Permanently delete accounts that have been deactivated for 30+ days';

    public const GRACE_PERIOD_DAYS = 30;

    public function handle(UserDeletionService $deletionService): void
    {
        $users = User::whereNotNull('deactivated_at')
            ->where('deactivated_at', '<=', now()->subDays(self::GRACE_PERIOD_DAYS))
            ->get();

        $deleted = 0;

        foreach ($users as $user) {
            try {
                $deletionService->delete($user);
                $deleted++;
            } catch (UserDeletionBlockedException $e) {
                $this->warn("Skipped {$user->name} (#{$user->id}): {$e->getMessage()}");
            }
        }

        $this->info("Permanently deleted {$deleted} of {$users->count()} account(s) past the 30-day deactivation grace period.");
    }
}
