<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DonationConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Donation $donation) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Donation Confirmed — JazakAllah Khair | Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your donation has been **confirmed and accepted**. May Allah multiply your reward!')
            ->line('---')
            ->line('**Confirmed Donation Receipt**')
            ->line('Amount: PKR ' . number_format($this->donation->amount))
            ->line('Cause: ' . ucfirst(str_replace('_', ' ', $this->donation->cause ?? 'General Fund')))
            ->line('Receipt #: SLLM-' . str_pad($this->donation->id, 6, '0', STR_PAD_LEFT))
            ->line('Confirmed: ' . now()->format('d M Y, h:i A'))
            ->line('---')
            ->line('Your sadaqah is now being put to work for the Ummah, in sha Allah.')
            ->action('View My Donations', route('donate.my'))
            ->salutation('JazakAllah Khair, The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '✅ Your donation of PKR ' . number_format($this->donation->amount) . ' has been confirmed! JazakAllah Khair.',
            'url'     => route('donate.my'),
        ];
    }
}
