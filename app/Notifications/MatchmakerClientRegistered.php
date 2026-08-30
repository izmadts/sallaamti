<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Fired when a client completes stage 1 (self-registration) on their own
// progress link — the one moment in that flow that previously logged only
// to the timeline with nobody actually told. Informational only, no admin
// review gate here (unlike documents/package), so this goes to the
// assigned counselor alone, same as MatchmakerConsentResponded/
// MatchmakerProposalResponded.
class MatchmakerClientRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '📝 Client registered themselves',
            'body' => "{$this->lead->name} completed their own Nikah profile registration via their progress link.",
            'data' => ['type' => 'client_registered', 'lead_id' => (string) $this->lead->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Client Self-Registered — Sallaamti Matchmaker')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("{$this->lead->name} just completed their own Nikah profile registration via their secure progress link.")
            ->line('Their next step is uploading verification documents — nothing needed from you unless they get stuck.')
            ->action('View Client', route('matchmaker.clients.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "📝 {$this->lead->name} completed their own Nikah profile registration.",
            'lead_id' => $this->lead->id,
            'url' => route('matchmaker.clients.show', $this->lead),
        ];
    }
}
