<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahProfileVerified extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Nikah Profile is Verified — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Congratulations! Your Nikah profile has been verified by our team.')
            ->line('Your profile is now visible to potential matches.')
            ->action('Browse Matches', route('nikah.browse'))
            ->line('JazakAllah Khair for trusting Sallaamti. May Allah bless your search and grant you a righteous spouse — best of luck finding your match, InshaAllah!');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '✅ Your Nikah profile has been verified! You can now appear in searches.',
            'url' => route('nikah.show'),
        ];
    }
}
