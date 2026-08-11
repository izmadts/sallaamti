<?php

namespace App\Notifications;

use App\Models\NikahInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahInterestReceived extends Notification
{
    use Queueable;

    public function __construct(public NikahInterest $interest) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
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
