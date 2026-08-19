<?php

namespace App\Console\Commands;

use App\Models\QuranGroupStudent;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Notifications\QuranClassReminder;
use App\Services\WhatsappNotifier;
use Illuminate\Console\Command;

class SendQuranClassReminders extends Command
{
    protected $signature = 'quran:send-class-reminders';

    protected $description = "Remind parents whose child has a Quran Live Class scheduled today";

    public function handle(WhatsappNotifier $notifier): void
    {
        // class_time is a free-text field admins type (e.g. "6:00 PM -
        // 6:45 PM", but not enforced), so it can't be reliably parsed down
        // to the minute — a same-day reminder naming the class_time
        // verbatim is the honest thing this data actually supports, not a
        // precise "starts in 15 minutes" ping.
        $today = now()->format('D');
        $todayDate = now()->toDateString();

        $students = QuranGroupStudent::where('status', 'active')
            ->with(['group.course', 'admission', 'user'])
            ->get()
            ->filter(function ($student) use ($today, $todayDate) {
                if ($student->last_class_reminder_date?->toDateString() === $todayDate) {
                    return false;
                }
                $group = $student->group;
                if (!$group || !$group->is_active || !in_array($today, (array) $group->class_days, true)) {
                    return false;
                }
                $subscription = $group->course->subscriptionFor($student->admission);
                return $subscription && $subscription->payment_status === 'confirmed';
            });

        $whatsappReady = Setting::get('whatsapp_quran_reminders_enabled', '0') === '1'
            && SocialAccount::active('whatsapp')
            && Setting::get('whatsapp_template_name_quran_class_reminder');

        foreach ($students as $student) {
            $student->user?->notify(new QuranClassReminder($student->group, $student->admission));

            if ($whatsappReady) {
                $this->sendWhatsappReminder($notifier, $student);
            }

            $student->update(['last_class_reminder_date' => $todayDate]);
        }

        $this->info("Sent class-day reminders for {$students->count()} student(s).");
    }

    private function sendWhatsappReminder(WhatsappNotifier $notifier, QuranGroupStudent $student): void
    {
        $recipient = $student->user;
        if (!$recipient || !$recipient->whatsapp_notify_opt_in || !$recipient->phone) {
            return;
        }

        $account = SocialAccount::active('whatsapp');
        $templateName = Setting::get('whatsapp_template_name_quran_class_reminder');

        try {
            $result = $notifier->sendTemplate($account, $recipient->phone, $templateName, [
                $student->admission->student_name,
                $student->group->class_time,
            ]);

            if (!$result['success']) {
                \Log::warning("SendQuranClassReminders: WhatsApp send to {$recipient->phone} failed — {$result['error']}");
            }
        } catch (\Throwable $e) {
            \Log::warning('SendQuranClassReminders: WhatsApp send to ' . $recipient->phone . ' threw — ' . $e->getMessage());
        }
    }
}
