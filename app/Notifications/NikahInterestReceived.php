<?php

namespace App\Notifications;

use App\Models\NikahInterest;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahInterestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public NikahInterest $interest) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💌 New interest request',
            'body' => 'Someone has sent you an interest request on Sallaamti Nikah.',
            'data' => ['type' => 'interest_received', 'interest_id' => (string) $this->interest->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Interest Request — Sallaamti Nikah')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Someone has sent you an interest request on Sallaamti Nikah.')
            ->action('View Interest', route('nikah.interests'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'You received a new interest request.',
            'interest_id' => $this->interest->id,
            'url' => route('nikah.interests'),
        ];
    }
}
