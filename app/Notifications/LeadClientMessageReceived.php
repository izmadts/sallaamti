<?php

namespace App\Notifications;

use App\Models\LeadClientMessage;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

// Mirrors NikahGuardianMessageReceived exactly, for the counselor<->client
// channel instead of the member-to-member one. Sent to whichever side
// (client or counselor) didn't send the message.
class LeadClientMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeadClientMessage $message) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💬 New message',
            'body' => Str::limit($this->message->message, 100),
            'data' => ['type' => 'lead_client_message', 'lead_id' => (string) $this->message->lead_id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Message — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('You have a new message about your Nikah counseling.')
            ->line('"' . Str::limit($this->message->message, 150) . '"');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💬 New message received.',
            'lead_id' => $this->message->lead_id,
        ];
    }
}
