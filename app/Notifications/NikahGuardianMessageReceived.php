<?php

namespace App\Notifications;

use App\Models\NikahGuardianMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahGuardianMessageReceived extends Notification
{
    use Queueable;

    public function __construct(public NikahGuardianMessage $message) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Guardian Message — Sallaamti Nikah')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('You have a new message in your guardian conversation.')
            ->line('"' . \Illuminate\Support\Str::limit($this->message->message, 150) . '"')
            ->action('View Conversation', route('nikah.messages.show', $this->message->nikah_interest_id));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💬 New guardian message received.',
            'url' => route('nikah.messages.show', $this->message->nikah_interest_id),
        ];
    }
}
