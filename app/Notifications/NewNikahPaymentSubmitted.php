<?php

namespace App\Notifications;

use App\Models\NikahProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewNikahPaymentSubmitted extends Notification implements ShouldQueue
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
            ->subject('New Nikah Payment Proof Submitted — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A Nikah verification fee payment proof was submitted and needs confirmation.')
            ->line('**Member:** ' . $this->profile->user->name)
            ->line('**Amount:** Rs. ' . number_format($this->profile->payment_amount))
            ->line('**Method:** ' . ucfirst(str_replace('_', ' ', $this->profile->payment_method)))
            ->action('Review Payment', route('admin.nikah.payments'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💳 New Nikah payment proof from ' . $this->profile->user->name . ' — needs confirmation.',
            'url' => route('admin.nikah.payments'),
        ];
    }
}
