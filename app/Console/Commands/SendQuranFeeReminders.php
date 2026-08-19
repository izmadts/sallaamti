<?php

namespace App\Console\Commands;

use App\Models\QuranGroupStudent;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Notifications\QuranFeeReminder;
use App\Services\WhatsappNotifier;
use Illuminate\Console\Command;

class SendQuranFeeReminders extends Command
{
    protected $signature = 'quran:send-fee-reminders';

    protected $description = 'Remind parents whose child\'s Quran Live Class fee is unpaid for the current month';

    public function handle(WhatsappNotifier $notifier): void
    {
        $currentMonth = now()->format('Y-m');

        $students = QuranGroupStudent::where('status', 'active')
            ->where(function ($q) use ($currentMonth) {
                $q->whereNull('last_fee_reminder_month')->orWhere('last_fee_reminder_month', '!=', $currentMonth);
            })
            ->with(['group.course', 'admission', 'user'])
            ->get()
            ->filter(function ($student) use ($currentMonth) {
                $subscription = $student->group->course->subscriptionFor($student->admission, $currentMonth);
                return !$subscription || $subscription->payment_status === 'unpaid' || $subscription->payment_status === 'rejected';
            });

        $whatsappReady = Setting::get('whatsapp_quran_reminders_enabled', '0') === '1'
            && SocialAccount::active('whatsapp')
            && Setting::get('whatsapp_template_name_quran_fee_reminder');

        foreach ($students as $student) {
            $student->user?->notify(new QuranFeeReminder($student->group->course, $student->admission));

            if ($whatsappReady) {
                $this->sendWhatsappReminder($notifier, $student);
            }

            $student->update(['last_fee_reminder_month' => $currentMonth]);
        }

        $this->info("Sent fee reminders for {$students->count()} student(s).");
    }

    private function sendWhatsappReminder(WhatsappNotifier $notifier, QuranGroupStudent $student): void
    {
        $recipient = $student->user;
        if (!$recipient || !$recipient->whatsapp_notify_opt_in || !$recipient->phone) {
            return;
        }

        $account = SocialAccount::active('whatsapp');
        $templateName = Setting::get('whatsapp_template_name_quran_fee_reminder');

        try {
            $result = $notifier->sendTemplate($account, $recipient->phone, $templateName, [
                $student->admission->student_name,
                $student->group->course->title,
            ]);

            if (!$result['success']) {
                \Log::warning("SendQuranFeeReminders: WhatsApp send to {$recipient->phone} failed — {$result['error']}");
            }
        } catch (\Throwable $e) {
            \Log::warning('SendQuranFeeReminders: WhatsApp send to ' . $recipient->phone . ' threw — ' . $e->getMessage());
        }
    }
}
