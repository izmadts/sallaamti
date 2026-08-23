<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MatchmakingLinkAccess;
use App\Models\MatchmakingTimelineEvent;
use App\Models\MatchProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

// No auth middleware at all — reachable only via a signed link (verified
// by the 'signed' route middleware itself) sent to the client by their
// matchmaker over whatever channel actually reaches them, per the "client
// may not use email or read a normal dashboard" design decision. Every
// hit is passively logged (IP/city/device) via MatchmakingLinkAccess,
// never blocking the actual response.
class MatchmakingActionController extends Controller
{
    public function showProposal(Request $request, MatchProposal $proposal)
    {
        $this->assertValidToken($request, $proposal);

        $proposal->load(['candidate.user', 'batch.nikahProfile.user']);

        if (!$proposal->viewed_at) {
            $proposal->update(['status' => 'viewed', 'viewed_at' => now()]);
        }

        MatchmakingLinkAccess::record($proposal, 'proposal_view', $request);

        // A separate signed URL for the respond POST — the signature on
        // *this* GET request is only valid for this route's own URI, so the
        // form posting to a different path needs its own signature, not a
        // forwarded one. Permanent (no expiry) like the show link — see
        // Matchmaker\ClientController::proposalLink()'s docblock.
        $respondUrl = URL::signedRoute('public.matchmaking.proposal.respond', ['proposal' => $proposal->id, 'token' => $proposal->link_token]);

        return view('public.matchmaking.proposal', compact('proposal', 'respondUrl'));
    }

    public function respondProposal(Request $request, MatchProposal $proposal)
    {
        $this->assertValidToken($request, $proposal);

        $validated = $request->validate([
            'response' => ['required', 'in:interested,not_interested,maybe'],
        ]);

        if ($proposal->status === 'responded') {
            return back()->with('status', 'A response was already recorded for this candidate.');
        }

        $proposal->update([
            'status' => 'responded',
            'response' => $validated['response'],
            'responded_at' => now(),
        ]);

        $batch = $proposal->batch;
        if ($batch->proposals()->where('status', '!=', 'responded')->doesntExist()) {
            $batch->update(['status' => 'completed']);
        } elseif ($batch->status !== 'partially_responded') {
            $batch->update(['status' => 'partially_responded']);
        }

        $label = str_replace('_', ' ', $validated['response']);
        MatchmakingTimelineEvent::log($batch->lead, $batch->nikahProfile, 'proposal_response', "Client responded \"{$label}\" to a candidate in Proposal Batch #{$batch->batch_number}.");

        MatchmakingLinkAccess::record($proposal, 'proposal_response', $request, $validated['response']);

        // Generate a fresh signature for the 'show' route rather than
        // forwarding the query params from this request — those were only
        // ever valid for the 'respond' URI they arrived on.
        $showUrl = URL::signedRoute('public.matchmaking.proposal.show', ['proposal' => $proposal->id, 'token' => $proposal->link_token]);

        return redirect($showUrl)->with('status', 'Thank you — your response has been recorded.');
    }

    // Laravel's own signature only proves the URL wasn't tampered with — it
    // says nothing about whether a *particular copy* of the link is still
    // the current one. The token is what regenerateLink() actually swaps to
    // kill an old copy, so both checks are needed together.
    private function assertValidToken(Request $request, MatchProposal $proposal): void
    {
        abort_unless($proposal->link_token && $request->query('token') === $proposal->link_token, 403, 'This link is no longer valid — ask your matchmaker for a fresh one.');
    }
}
