<?php

namespace App\Notifications;

use App\Models\QuranClassGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class QuranProgressReportReady extends Notification
{
    use Queueable;

    public function __construct(public QuranClassGroup $group, public string $month) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Progress Report is Ready — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your teacher has submitted your progress report for ' . $this->month . '.')
            ->line('Course: ' . $this->group->course->title . ' — ' . $this->group->group_name)
            ->action('View Progress', route('quran-live.my-progress'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '📊 Your progress report for ' . $this->month . ' is ready.',
            'url' => route('quran-live.my-progress'),
        ];
    }
}
