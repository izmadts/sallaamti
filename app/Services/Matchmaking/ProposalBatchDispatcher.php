<?php

namespace App\Services\Matchmaking;

use App\Models\MatchmakingTimelineEvent;
use App\Models\ProposalBatch;
use App\Notifications\NewProposalBatchReceived;

// The one place a batch actually reaches the client — flips it (and every
// proposal in it) to 'sent' and fires the push. Shared by ClientController::
// sendBatch() (a certified counselor sends straight through) and
// Admin\ProposalBatchApprovalController::approve() (a Probation trainee's
// batch waited at 'pending_approval' for exactly this to be called on their
// behalf), so neither path can drift on what "sent" actually means.
class ProposalBatchDispatcher
{
    public function dispatch(ProposalBatch $batch): void
    {
        $lead = $batch->lead;
        $now = now();

        $batch->proposals()->update(['status' => 'sent', 'sent_at' => $now]);
        $batch->update(['status' => 'sent', 'sent_at' => $now]);

        MatchmakingTimelineEvent::log($lead, $batch->nikahProfile, 'proposal_batch_sent', "Proposal Batch #{$batch->batch_number} sent ({$batch->proposals()->count()} candidates).");

        if ($batch->nikahProfile?->user) {
            try {
                $batch->nikahProfile->user->notify(new NewProposalBatchReceived($batch));
            } catch (\Throwable $e) {
                \Log::error('NewProposalBatchReceived notification failed: ' . $e->getMessage());
            }
        }
    }
}
