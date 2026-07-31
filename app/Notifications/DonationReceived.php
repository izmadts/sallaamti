<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DonationReceived extends Notification
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
            ->subject('JazakAllah Khair — Donation Received | Sallaamti')
            ->greeting('Assalamu Alaikum ' . ($this->donation->donor_name ?? $notifiable->name) . '!')
            ->line('**JazakAllah Khair for your generous donation!** May Allah accept it as Sadaqah Jariyah.')
            ->line('---')
            ->line('**Donation Summary**')
            ->line('Amount: PKR ' . number_format($this->donation->amount))
            ->line('Cause: ' . ucfirst(str_replace('_', ' ', $this->donation->cause ?? 'General Fund')))
            ->line('Reference: #' . str_pad($this->donation->id, 6, '0', STR_PAD_LEFT))
            ->line('Date: ' . $this->donation->created_at->format('d M Y, h:i A'))
            ->line('---')
            ->line('Our admin will verify your payment and send a final confirmation within **24 hours**, in sha Allah.')
            ->action('View My Donations', route('donate.my'))
            ->line('"The believer\'s shade on the Day of Resurrection will be his charity." — Tirmidhi')
            ->salutation('With gratitude, The Sallaamti Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💝 JazakAllah! Your donation of PKR ' . number_format($this->donation->amount) . ' has been received and is pending verification.',
            'url'     => route('donate.my'),
        ];
    }
}
