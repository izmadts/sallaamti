<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahAdminMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $message) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A message from the Sallaamti team about your Nikah profile')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Our team reviewed your Nikah profile and has a note for you:')
            ->line($this->message)
            ->action('Update My Profile', route('nikah.edit'))
            ->line('If anything is unclear, just reply to this email and we\'ll help you sort it out.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '📩 The Sallaamti team sent you a note about your Nikah profile: ' . str($this->message)->limit(80),
            'url' => route('nikah.edit'),
        ];
    }
}
