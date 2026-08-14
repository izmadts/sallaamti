<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahProfileSuspended extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $reason) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Nikah Profile Has Been Suspended — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your Nikah profile has been suspended by our moderation team and is now hidden from search.')
            ->line('Reason: ' . $this->reason)
            ->line('If you believe this is a mistake, please contact support.')
            ->action('View Profile', route('nikah.show'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '⛔ Your Nikah profile has been suspended. Reason: ' . $this->reason,
            'url' => route('nikah.show'),
        ];
    }
}
