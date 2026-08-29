<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\MatchProposal;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Fired from Public\MatchmakingProgressController::respondToProposal() —
// previously the only proposal-batch event with no notification at all,
// unlike every other client-facing action on their secure link (consent,
// mutual interest). A time-sensitive one: an "interested" response is
// exactly the moment a counselor should follow up.
class MatchmakerProposalResponded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MatchProposal $proposal, public Lead $lead) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    private function label(): string
    {
        return str_replace('_', ' ', $this->proposal->response);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Client Responded to a Proposal — Sallaamti Matchmaker')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("{$this->lead->name} responded \"{$this->label()}\" to a candidate in Proposal Batch #{$this->proposal->batch->batch_number}.")
            ->action('View Client', route('matchmaker.clients.show', $this->lead));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "💌 {$this->lead->name} responded \"{$this->label()}\" to a proposal.",
            'lead_id' => $this->lead->id,
            'url' => route('matchmaker.clients.show', $this->lead),
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💌 Client responded to a proposal',
            'body' => "{$this->lead->name} responded \"{$this->label()}\" to a candidate.",
            'data' => ['type' => 'proposal_responded', 'lead_id' => (string) $this->lead->id],
        ];
    }
}
