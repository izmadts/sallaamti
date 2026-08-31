<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class PruneOldNotifications extends Command
{
    protected $signature = 'notifications:prune';

    protected $description = 'Delete read notifications older than the retention window';

    // Only read ones - an unread notification stays forever until someone
    // actually sees it, no matter how old. This mirrors the read/unread
    // distinction the notification bell itself already shows.
    public const RETENTION_DAYS = 90;

    public function handle(): void
    {
        $deleted = DatabaseNotification::whereNotNull('read_at')
            ->where('read_at', '<=', now()->subDays(self::RETENTION_DAYS))
            ->delete();

        $this->info("Deleted {$deleted} read notification(s) older than " . self::RETENTION_DAYS . ' days.');
    }
}
