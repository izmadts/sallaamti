<?php

namespace App\Notifications;

use App\Models\QuranAdmission;
use App\Models\QuranLiveCourse;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuranFeeReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuranLiveCourse $course, public QuranAdmission $admission) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Monthly Fee Due — {$this->admission->student_name}")
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("This month's fee for {$this->admission->student_name}'s Quran class ({$this->course->title}) hasn't been submitted yet.")
            ->line('Rs. ' . number_format($this->course->monthly_fee) . ' — submitting on time keeps their class link uninterrupted.')
            ->action('Pay Now', route('quran-live.subscribe', [$this->course, $this->admission]));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quran_fee_due',
            'message' => "💳 This month's fee for {$this->admission->student_name} ({$this->course->title}) is still due.",
            'url' => route('quran-live.subscribe', [$this->course, $this->admission]),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'Monthly fee due',
            'body' => "This month's fee for {$this->admission->student_name} ({$this->course->title}) is still due.",
            'data' => ['type' => 'quran_fee_due', 'admission_id' => (string) $this->admission->id],
            'app' => 'sallaamti_app',
        ];
    }
}
