<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MatchmakingLinkAccess;
use Illuminate\Http\Request;

// A standing page a client can revisit any time to see their own status,
// timeline, and full proposal/response history — no login, but re-verified
// on every single visit against the last 7 digits of the WhatsApp number on
// file, rather than a one-time check that then persists in a session. That's
// deliberate: the link itself doesn't expire and may sit in a chat thread
// indefinitely, so nothing about "already unlocked" should survive between
// visits. See Matchmaker\ClientController::regenerateProgressLink().
class MatchmakingProgressController extends Controller
{
    public function show(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        MatchmakingLinkAccess::record($lead, 'progress_view', $request);

        $verifyUrl = \Illuminate\Support\Facades\URL::signedRoute('public.matchmaking.progress.verify', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false]);
    }

    public function verify(Request $request, Lead $lead)
    {
        $this->assertValidToken($request, $lead);

        $validated = $request->validate([
            'last7' => ['required', 'digits:7'],
        ]);

        $expected = substr(preg_replace('/\D/', '', $lead->phone ?? ''), -7);
        $verifyUrl = \Illuminate\Support\Facades\URL::signedRoute('public.matchmaking.progress.verify', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);

        if (!$expected || $validated['last7'] !== $expected) {
            MatchmakingLinkAccess::record($lead, 'progress_verify', $request, 'failed');

            return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => false])
                ->with('error', 'That doesn\'t match — check the last 7 digits of the WhatsApp number your matchmaker has on file and try again.');
        }

        MatchmakingLinkAccess::record($lead, 'progress_verify', $request, 'success');

        $lead->load(['timelineEvents.matchmaker', 'proposalBatches.proposals.candidate.user']);

        return view('public.matchmaking.progress', ['lead' => $lead, 'verifyUrl' => $verifyUrl, 'unlocked' => true]);
    }

    private function assertValidToken(Request $request, Lead $lead): void
    {
        abort_unless($lead->progress_link_token && $request->query('token') === $lead->progress_link_token, 403, 'This link is no longer valid — ask your matchmaker for a fresh one.');
    }
}
