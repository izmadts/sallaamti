<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeMail extends Notification implements ShouldQueue
{
    use Queueable;

    // $subject/$intro default to the sign-in wording this started as. Password
    // reset overrides them (see AuthController::passwordForgot) so a member who
    // didn't ask for a reset can tell from the email alone what was attempted,
    // rather than getting the same anonymous "verification code" either way.
    public function __construct(
        public string $code,
        public string $subject = 'Your Sallaamti Verification Code',
        public string $intro = 'Your verification code is:',
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Assalamu Alaikum!')
            ->line($this->intro)
            ->line("## {$this->code}")
            ->line('This code expires in 10 minutes.')
            ->line("If you didn't request this, you can safely ignore this email.")
            ->salutation('— The Sallaamti Team');
    }
}
