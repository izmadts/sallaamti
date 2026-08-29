<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\MatchmakerApplication;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NikahCounselorCertified extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchmakerApplication $application, public Certificate $certificate) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '🎉 You\'re certified!',
            'body' => 'Your application has been approved — you are now a Certified Sallaamti Nikah Counselor (' . $this->application->counselor_code . ').',
            'data' => ['type' => 'counselor_certified'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulations — You\'re a Certified Sallaamti Nikah Counselor!')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your application has been approved — you are now a Certified Sallaamti Nikah Counselor.')
            ->line('Your Counselor ID: **' . $this->application->counselor_code . '**')
            ->line('Log in to your Sallaamti account to find the Match Maker Desk, your referral link, and your certificate.')
            ->action('Go to Dashboard', route('dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🎉 You are now a Certified Sallaamti Nikah Counselor (' . $this->application->counselor_code . ')!',
            'url' => route('dashboard'),
        ];
    }
}
