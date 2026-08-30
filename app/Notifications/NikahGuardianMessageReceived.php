<?php

namespace App\Notifications;

use App\Models\NikahGuardianMessage;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahGuardianMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public NikahGuardianMessage $message) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💬 New message',
            'body' => \Illuminate\Support\Str::limit($this->message->message, 100),
            'data' => ['type' => 'guardian_message', 'interest_id' => (string) $this->message->nikah_interest_id],
        ];
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
