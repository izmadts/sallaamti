<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeMail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Sallaamti Verification Code')
            ->greeting('Assalamu Alaikum!')
            ->line('Your verification code is:')
            ->line("## {$this->code}")
            ->line('This code expires in 10 minutes.')
            ->line("If you didn't request this, you can safely ignore this email.")
            ->salutation('— The Sallaamti Team');
    }
}
