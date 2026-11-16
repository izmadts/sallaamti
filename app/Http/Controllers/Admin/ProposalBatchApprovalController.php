<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProposalBatch;
use App\Notifications\ProposalBatchApprovalRejected;
use App\Services\Matchmaking\ProposalBatchDispatcher;
use Illuminate\Http\Request;

// Trainee-access addendum (15/16 Nov 2026) — a Probation-stage counselor's
// "Send Batch" lands here instead of going straight to the client; see
// ClientController::sendBatch(). Approve sends it through exactly the way a
// certified counselor's batch would; reject sends it back with a note.
class ProposalBatchApprovalController extends Controller
{
    public function index()
    {
        $batches = ProposalBatch::with('lead', 'matchmaker', 'proposals.candidate.user')
            ->where('status', 'pending_approval')
            ->latest()
            ->paginate(20);

        return view('admin.proposal-batches.pending', compact('batches'));
    }

    public function approve(ProposalBatch $batch)
    {
        abort_unless($batch->status === 'pending_approval', 422, 'This batch is no longer waiting for approval.');

        app(ProposalBatchDispatcher::class)->dispatch($batch);

        return back()->with('status', "Proposal Batch #{$batch->batch_number} approved and sent to the client.");
    }

    public function reject(Request $request, ProposalBatch $batch)
    {
        abort_unless($batch->status === 'pending_approval', 422, 'This batch is no longer waiting for approval.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $batch->update(['status' => 'ready']);

        \App\Models\MatchmakingTimelineEvent::log($batch->lead, $batch->nikahProfile, 'proposal_batch_approval_rejected', "Proposal Batch #{$batch->batch_number} sent back by " . auth()->user()->name . ": {$validated['reason']}");

        if ($batch->matchmaker) {
            try {
                $batch->matchmaker->notify(new ProposalBatchApprovalRejected($batch, $validated['reason']));
            } catch (\Throwable $e) {
                \Log::error('ProposalBatchApprovalRejected notification failed: ' . $e->getMessage());
            }
        }

        return back()->with('status', "Proposal Batch #{$batch->batch_number} sent back to the counselor.");
    }
}
