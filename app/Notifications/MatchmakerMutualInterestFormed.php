<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\NikahInterest;
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
        return ['database', 'mail'];
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
