<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:send-reminders')->hourly();
Schedule::command('users:purge-deactivated')->daily();
Schedule::command('quran:send-class-reminders')->dailyAt('08:00');
Schedule::command('quran:send-fee-reminders')->dailyAt('08:30');

// Admin-configurable via Settings (Admin > Integrations) — read fresh on
// every schedule:run tick, so a changed batch size/time takes effect
// within a minute without a deploy.
Schedule::command('wall:publish-scheduled')
    ->dailyAt(\App\Models\Setting::get('scheduled_batch_time', '09:00'));
