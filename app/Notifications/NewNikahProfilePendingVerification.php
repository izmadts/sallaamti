<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewNikahProfilePendingVerification extends Notification
{
    use Queueable;

    public function __construct(public NikahProfile $profile) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nikah Profile Awaiting CNIC Verification — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A Nikah profile has a confirmed payment and is now awaiting CNIC verification.')
            ->line('**Member:** ' . $this->profile->user->name)
            ->line('**City:** ' . $this->profile->city)
            ->action('Review Profile', route('admin.nikah.verifications'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🪪 ' . $this->profile->user->name . '\'s Nikah profile is awaiting CNIC verification.',
            'url' => route('admin.nikah.verifications'),
        ];
    }
}
