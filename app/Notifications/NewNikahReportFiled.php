<?php

namespace App\Notifications;

use App\Models\NikahReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewNikahReportFiled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public NikahReport $report) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Nikah Profile Report — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A member reported a Nikah profile and it needs review.')
            ->line('**Reporter:** ' . $this->report->reporter->user->name)
            ->line('**Reported:** ' . $this->report->reported->user->name)
            ->line('**Reason:** ' . $this->report->reason)
            ->action('Review Report', route('admin.nikah.reports'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🚩 ' . $this->report->reporter->user->name . ' reported ' . $this->report->reported->user->name . '.',
            'url' => route('admin.nikah.reports'),
        ];
    }
}
