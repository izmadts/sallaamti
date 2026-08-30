<?php

namespace App\Notifications;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NikahPaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💳 Payment confirmed',
            'body' => 'Your Nikah verification fee payment has been confirmed. Our team will now review your profile.',
            'data' => ['type' => 'payment_confirmed'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Confirmed — Sallaamti Nikah')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your Nikah verification fee payment has been confirmed.')
            ->line('Our team will now review your CNIC and profile details.')
            ->action('View Profile', route('nikah.show'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💳 Nikah payment confirmed. Profile is now under verification.',
            'url' => route('nikah.show'),
        ];
    }
}
