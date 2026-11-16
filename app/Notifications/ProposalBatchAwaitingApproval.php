<?php

namespace App\Notifications;

use App\Models\ProposalBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Trainee-access addendum (15/16 Nov 2026) — a Probation-stage counselor's
// "Send Batch" doesn't reach the client directly; it lands here first. Same
// admin-broadcast shape as NewPackagePaymentSubmitted.
class ProposalBatchAwaitingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProposalBatch $batch) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proposal Batch Awaiting Your Approval — Sallaamti')
            ->greeting('Assalamu Alaikum!')
            ->line('A Probation-stage Nikah Counselor built a proposal batch and needs it approved before it reaches the client.')
            ->line('**Counselor:** ' . $this->batch->matchmaker?->name)
            ->line('**Client:** ' . $this->batch->lead?->name)
            ->line('**Candidates:** ' . $this->batch->proposals()->count())
            ->action('Review Batch', route('admin.proposal-batches.index'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '📋 Proposal Batch #' . $this->batch->batch_number . ' from ' . ($this->batch->matchmaker?->name ?? 'a counselor') . ' is waiting for approval.',
            'url' => route('admin.proposal-batches.index'),
        ];
    }
}
