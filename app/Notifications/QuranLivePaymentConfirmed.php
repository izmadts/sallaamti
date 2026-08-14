<?php

namespace App\Notifications;

use App\Models\QuranLiveCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class QuranLivePaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuranLiveCourse $course, public string $month) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
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
            'message' => '✅ Monthly fee confirmed for ' . $this->course->title . '. Join your class!',
            'url' => route('quran-live.my-class'),
        ];
    }
}
