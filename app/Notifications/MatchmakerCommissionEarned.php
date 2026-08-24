<?php

namespace App\Notifications;

use App\Models\CommissionLedgerEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchmakerCommissionEarned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CommissionLedgerEntry $entry) {}

    private function label(): string
    {
        return match (true) {
            $this->entry->rule_type === 'verified_profile' => 'a Verified Profile',
            $this->entry->rule_type === 'recognition_bonus' => 'a Sallaamti Nikah Success Award',
            $this->entry->is_renewal => 'a package renewal',
            default => 'a new package sale',
        };
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You Earned a Commission — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('You earned Rs. ' . number_format((float) $this->entry->commission_amount) . ' commission for ' . $this->label() . '.')
            ->line('It will be eligible for approval from ' . $this->entry->eligible_at->format('d M Y') . '.')
            ->action('View My Commissions', route('matchmaker.commissions.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💰 You earned Rs. ' . number_format((float) $this->entry->commission_amount) . ' commission for ' . $this->label() . '.',
            'url' => route('matchmaker.commissions.index'),
        ];
    }
}
