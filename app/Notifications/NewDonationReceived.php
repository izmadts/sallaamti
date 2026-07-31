<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewDonationReceived extends Notification
{
    use Queueable;

    public function __construct(public Donation $donation) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Donation Submitted — PKR ' . number_format($this->donation->amount))
            ->greeting('Assalamu Alaikum!')
            ->line('A new donation has been submitted and needs verification.')
            ->line('**Donor:** ' . ($this->donation->donor_name ?? 'Anonymous'))
            ->line('**Amount:** PKR ' . number_format($this->donation->amount))
            ->line('**Method:** ' . ucfirst(str_replace('_', ' ', $this->donation->payment_method)))
            ->action('Review Donation', route('admin.donations.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💝 New donation: PKR ' . number_format($this->donation->amount) . ' from ' . ($this->donation->donor_name ?? 'Anonymous') . ' — needs verification.',
            'url'     => route('admin.donations.index'),
        ];
    }
}
