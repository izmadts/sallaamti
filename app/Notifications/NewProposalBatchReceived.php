<?php

namespace App\Notifications;

use App\Models\ProposalBatch;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Fired from Matchmaker\ClientController::sendBatch() — previously a
// client was never notified of a new proposal batch at all, only finding
// out via the matchmaker manually re-sharing the /p/{lead} link. Mirrors
// LeadClientMessageReceived's exact channel/pattern.
class NewProposalBatchReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProposalBatch $batch) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '💍 New match proposal',
            'body' => 'Your Nikah Counselor has sent you a new proposal to review.',
            'data' => ['type' => 'proposal_batch_received', 'lead_id' => (string) $this->batch->lead_id, 'batch_id' => (string) $this->batch->id],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Match Proposal — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line('Your Nikah Counselor has sent you a new proposal to review.')
            ->action('Open Sallaamti App', url('/'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '💍 Your Nikah Counselor sent you a new proposal.',
            'lead_id' => $this->batch->lead_id,
            'batch_id' => $this->batch->id,
        ];
    }
}
