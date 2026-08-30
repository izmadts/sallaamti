<?php

namespace App\Notifications;

use App\Models\NikahInterest;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahInterestAccepted extends Notification implements ShouldQueue
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
            'title' => '✅ Interest accepted!',
            'body' => 'Your interest request was accepted — contact details are now visible.',
            'data' => ['type' => 'interest_accepted', 'interest_id' => (string) $this->interest->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Interest Accepted — Sallaamti Nikah')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Good news — your interest request was accepted.')
            ->line('Contact details are now visible to both sides, and you can start a guardian-mediated conversation.')
            ->action('View Interest', route('nikah.interests'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Your interest request was accepted! Contact details are now visible.',
            'interest_id' => $this->interest->id,
            'url' => route('nikah.interests'),
        ];
    }
}
