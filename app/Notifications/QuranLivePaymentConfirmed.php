<?php

namespace App\Notifications;

use App\Models\QuranLiveCourse;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class QuranLivePaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuranLiveCourse $course, public string $month) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Monthly Fee Confirmed — ' . $this->course->title)
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your monthly fee for ' . $this->course->title . ' (' . $this->month . ') has been confirmed.')
            ->line("You can now access today's class link.")
            ->action('Join My Class', route('quran-live.my-class'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quran_payment_confirmed',
            'message' => '✅ Monthly fee confirmed for ' . $this->course->title . '. Join your class!',
            'url' => route('quran-live.my-class'),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'Payment confirmed',
            'body' => "Your fee for {$this->course->title} ({$this->month}) is confirmed — you can join today's class.",
            'data' => ['type' => 'quran_payment_confirmed', 'course_id' => (string) $this->course->id],
            'app' => 'sallaamti_app',
        ];
    }
}
