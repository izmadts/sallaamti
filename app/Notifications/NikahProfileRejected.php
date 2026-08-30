<?php

namespace App\Notifications;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahProfileRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $reason) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => 'Your profile needs a few changes',
            'body' => $this->reason,
            'data' => ['type' => 'profile_rejected'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nikah Profile Update — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your Nikah profile verification was not approved.')
            ->line('Reason: ' . $this->reason)
            ->action('Update Profile', route('nikah.edit'))
            ->line('Please update your profile and resubmit.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '❌ Nikah profile rejected: ' . $this->reason,
            'url' => route('nikah.edit'),
        ];
    }
}
