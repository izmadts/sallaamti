<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:send-reminders')->hourly();
Schedule::command('users:purge-deactivated')->daily();
Schedule::command('notifications:prune')->daily();
Schedule::command('quran:send-class-reminders')->dailyAt('08:00');
Schedule::command('quran:send-fee-reminders')->dailyAt('08:30');
Schedule::command('matchmaker:promote-eligible-counselors')->dailyAt('02:00');
// Temporarily disabled for tonight's 03:00 run (15 Nov 2026) as a safety
// net — the command had a real bug (only checked who CREATED a lead, never
// who it's ASSIGNED to) that would have unassigned all 848 leads currently
// sitting on the owner's own admin account. Fixed in ReleaseStaleLeads.php;
// re-enable this line once that fix is deployed and `php artisan
// leads:release-stale --dry-run` on the server shows a sane count.
// Schedule::command('leads:release-stale')->dailyAt('03:00');
Schedule::command('nikah:send-payment-deadline-reminders')->dailyAt('09:00');

// Admin-configurable via Settings (Admin > Integrations) — read fresh on
// every schedule:run tick, so a changed batch size/time takes effect
// within a minute without a deploy.
//
// This file is loaded on every console boot — including before the settings
// table exists (fresh-install migrate, the SQLite test suite) — so a failed
// read falls back to the default instead of breaking every command/test.
$wallBatchTime = rescue(fn () => \App\Models\Setting::get('scheduled_batch_time', '09:00'), '09:00', report: false);
Schedule::command('wall:publish-scheduled')
    ->dailyAt($wallBatchTime ?: '09:00');
