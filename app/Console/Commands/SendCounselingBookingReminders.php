<?php

namespace App\Console\Commands;

use App\Models\CounselingBooking;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Notifications\CounselingBookingReminder;
use App\Services\WhatsappNotifier;
use Illuminate\Console\Command;

class SendCounselingBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send a reminder for counseling sessions happening in the next 24 hours';

    public function handle(WhatsappNotifier $notifier): void
    {
        $bookings = CounselingBooking::where('status', 'confirmed')
            ->whereNull('reminded_at')
            ->whereBetween('scheduled_at', [now(), now()->addHours(24)])
            ->with(['member', 'counselor'])
            ->get();

        // Same guarded, opt-in-only pattern as SendWhatsappBroadcastJob — a
        // no-op unless an admin has connected WhatsApp, turned this on, and
        // configured a separate approved template (this is a session
        // reminder, not the new-post-announcement template).
        $whatsappReady = Setting::get('whatsapp_counseling_reminders_enabled', '0') === '1'
            && SocialAccount::active('whatsapp')
            && Setting::get('whatsapp_template_name_counseling_reminder');

        foreach ($bookings as $booking) {
            $booking->member?->notify(new CounselingBookingReminder($booking));
            $booking->counselor?->notify(new CounselingBookingReminder($booking));

            if ($whatsappReady) {
                $this->sendWhatsappReminder($notifier, $booking);
            }

            $booking->update(['reminded_at' => now()]);
        }

        $this->info("Sent reminders for {$bookings->count()} booking(s).");
    }

    private function sendWhatsappReminder(WhatsappNotifier $notifier, CounselingBooking $booking): void
    {
        $account = SocialAccount::active('whatsapp');
        $templateName = Setting::get('whatsapp_template_name_counseling_reminder');
        $when = $booking->scheduled_at->format('d M, h:i A');

        foreach ([$booking->member, $booking->counselor] as $recipient) {
            if (!$recipient || !$recipient->whatsapp_notify_opt_in || !$recipient->phone) {
                continue;
            }

            try {
                $result = $notifier->sendTemplate($account, $recipient->phone, $templateName, [$when]);

                if (!$result['success']) {
                    \Log::warning("SendCounselingBookingReminders: WhatsApp send to {$recipient->phone} failed — {$result['error']}");
                }
            } catch (\Throwable $e) {
                \Log::warning("SendCounselingBookingReminders: WhatsApp send to {$recipient->phone} threw — " . $e->getMessage());
            }
        }
    }
}
