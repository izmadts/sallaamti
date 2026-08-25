<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewPackagePaymentSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Package Payment Proof Submitted — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A client submitted payment proof for a matchmaking package and needs confirmation.')
            ->line('**Client:** ' . $this->lead->name)
            ->line('**Package:** ' . ($this->lead->pendingPackage?->name ?? 'Unknown'))
            ->line('**Method:** ' . ucfirst(str_replace('_', ' ', $this->lead->package_payment_method)))
            ->action('Review Payment', route('admin.leads.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💳 New package payment proof from ' . $this->lead->name . ' — needs confirmation.',
            'url' => route('admin.leads.show', $this->lead),
        ];
    }
}
