<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\NikahInterest;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchmakerMutualInterestFormed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public NikahInterest $interest, public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💍 Mutual interest confirmed',
            'body' => "Mutual interest confirmed for your client {$this->lead->name} — contact details are already shared, follow up on next steps.",
            'data' => ['type' => 'mutual_interest', 'lead_id' => (string) $this->lead->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mutual Interest Confirmed — Sallaamti Matchmaker')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("Mutual interest has been confirmed for your client {$this->lead->name}.")
            ->line('Contact details have already been shared automatically between both sides — you may want to follow up about next steps (family involvement, etc).')
            ->action('View Client', route('matchmaker.clients.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Mutual interest confirmed for {$this->lead->name}.",
            'interest_id' => $this->interest->id,
            'lead_id' => $this->lead->id,
            'url' => route('matchmaker.clients.show', $this->lead),
        ];
    }
}
