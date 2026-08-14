<?php

namespace App\Console\Commands;

use App\Models\CounselingBooking;
use App\Notifications\CounselingBookingReminder;
use Illuminate\Console\Command;

class SendCounselingBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send a reminder for counseling sessions happening in the next 24 hours';

    public function handle(): void
    {
        $bookings = CounselingBooking::where('status', 'confirmed')
            ->whereNull('reminded_at')
            ->whereBetween('scheduled_at', [now(), now()->addHours(24)])
            ->with(['member', 'counselor'])
            ->get();

        foreach ($bookings as $booking) {
            $booking->member?->notify(new CounselingBookingReminder($booking));
            $booking->counselor?->notify(new CounselingBookingReminder($booking));
            $booking->update(['reminded_at' => now()]);
        }

        $this->info("Sent reminders for {$bookings->count()} booking(s).");
    }
}
