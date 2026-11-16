<?php

namespace App\Notifications;

use App\Models\ProposalBatch;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Sent to the Probation-stage counselor when admin sends their
// pending_approval batch back (to 'ready') with a note instead of approving
// it — see Admin\ProposalBatchApprovalController::reject().
class ProposalBatchApprovalRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProposalBatch $batch, public string $reason) {}

    public function via($notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '📋 Proposal Batch needs changes',
            'body' => "Batch #{$this->batch->batch_number} wasn't approved yet: {$this->reason}",
            'data' => ['type' => 'proposal_batch_approval_rejected'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Proposal Batch Needs Changes — Sallaamti')
            ->greeting('Assalamu Alaikum ' . $notifiable->name . '!')
            ->line("Proposal Batch #{$this->batch->batch_number} wasn't approved to send yet.")
            ->line('Note from admin: ' . $this->reason)
            ->line('It\'s back in your drafts — make the change and send it again.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'proposal_batch_approval_rejected',
            'message' => "📋 Batch #{$this->batch->batch_number} wasn't approved: {$this->reason}",
        ];
    }
}
