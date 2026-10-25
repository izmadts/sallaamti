<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\SendsNikahInterest;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MatchmakingConsent;
use App\Models\MatchmakingConsentRequest;
use App\Models\MatchmakingTimelineEvent;
use App\Models\MatchProposal;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

// The authenticated-member counterpart to Public\MatchmakingProgressController
// — lets a client who already has a real app account (see Admin\
// NikahProfileWizardController::finalize() and its mobile-API twin,
// Api\V1\Matchmaker\ClientProfileController::store(), both of which now
// hand the client a real login) see and respond to their Nikah Counselor's
// proposed matches and consent requests from inside the app itself, instead
// of only through the public /p/{lead} token link. Mirrors that controller's
// respondToConsent()/respondToProposal() logic closely (same models, same
// SendsNikahInterest bridge, same timeline-event conventions), just
// Sanctum-authenticated via LeadPolicy instead of a last-7-digits+token
// check — this is the replacement /p/{lead} is retired in favor of once
// it's live.
class NikahMatchmakingController extends Controller
{
    use SendsNikahInterest;

    private function myLead(Request $request): Lead
    {
        $profile = $request->user()->nikahProfile;
        abort_unless($profile, 409, 'Create your Nikah profile first.');

        $lead = Lead::where('nikah_profile_id', $profile->id)->first();
        abort_unless($lead, 404, 'No matchmaking activity yet.');

        // Controller has no AuthorizesRequests trait (Laravel 11+ skeleton
        // dropped it) — Gate::forUser() resolves the same auto-discovered
        // LeadPolicy and throws the same 403 authorize() would.
        Gate::forUser($request->user())->authorize('view', $lead);

        return $lead;
    }

    public function overview(Request $request): JsonResponse
    {
        $lead = $this->myLead($request);

        $lead->load([
            // Draft batches are the counselor's own in-progress work —
            // never shown to the client until actually sent.
            'proposalBatches' => fn ($q) => $q->where('status', '!=', 'draft')->latest(),
            'proposalBatches.proposals.candidate.user',
            'consentRequests' => fn ($q) => $q->where('status', 'pending')->latest('requested_at'),
            'nikahPackage',
            'pendingPackage',
        ]);

        $browseController = app(NikahBrowseController::class);

        return response()->json([
            'proposal_batches' => $lead->proposalBatches->map(fn ($batch) => [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'status' => $batch->status,
                'sent_at' => $batch->sent_at?->toIso8601String(),
                'proposals' => $batch->proposals->map(fn ($proposal) => [
                    'id' => $proposal->id,
                    'status' => $proposal->status,
                    'response' => $proposal->response,
                    'match_reasons' => $proposal->match_reasons,
                    'candidate' => $browseController->cardPayload($proposal->candidate, [], []),
                ]),
            ]),
            'consent_requests' => $lead->consentRequests->map(fn ($cr) => [
                'id' => $cr->id,
                'consent_type' => $cr->consent_type,
                'consent_label' => MatchmakingConsent::TYPES[$cr->consent_type] ?? $cr->consent_type,
                'requested_at' => $cr->requested_at?->toIso8601String(),
            ]),
            'package' => [
                'name' => $lead->nikahPackage?->name,
                'expires_at' => $lead->package_expires_at?->toIso8601String(),
                'payment_status' => $lead->package_payment_status,
                'pending_package_name' => $lead->pendingPackage?->name,
            ],
            'is_matched' => $lead->isMatched(),
        ]);
    }

    public function respondToProposal(Request $request, MatchProposal $proposal): JsonResponse
    {
        $lead = $this->myLead($request);
        abort_unless((int) $proposal->batch->lead_id === (int) $lead->id, 404);
        abort_if($proposal->status === 'responded', 422, 'This request has already been responded to.');

        $validated = $request->validate([
            'response' => ['required', 'in:interested,not_interested,maybe'],
        ]);

        $interestId = null;
        $bridgeNote = '';

        if ($validated['response'] === 'interested') {
            $result = $this->sendInterestBetweenProfiles($proposal->batch->nikahProfile, $proposal->candidate);
            $interestId = $result['interest']?->id;
            $bridgeNote = match ($result['error']) {
                'not_eligible' => ' Their interest could not yet be forwarded to the candidate — the client\'s own profile needs to be verified and payment confirmed first.',
                'blocked' => ' Their interest could not be forwarded — contact between these two profiles is blocked.',
                'previously_declined' => ' Their interest was not forwarded — the candidate previously declined an interest from this client.',
                default => ' Their interest was forwarded to the candidate.',
            };
        }

        $proposal->update([
            'status' => 'responded',
            'response' => $validated['response'],
            'responded_at' => now(),
            'nikah_interest_id' => $interestId,
        ]);

        $batch = $proposal->batch;
        if ($batch->proposals()->where('status', '!=', 'responded')->doesntExist()) {
            $batch->update(['status' => 'completed']);
        } elseif ($batch->status !== 'partially_responded') {
            $batch->update(['status' => 'partially_responded']);
        }

        $label = str_replace('_', ' ', $validated['response']);
        MatchmakingTimelineEvent::log($lead, $batch->nikahProfile, 'proposal_response', "Client responded \"{$label}\" to a candidate in Proposal Batch #{$batch->batch_number} via the Sallaamti app.{$bridgeNote}");

        if ($lead->assigned_to) {
            try {
                User::find($lead->assigned_to)?->notify(new \App\Notifications\MatchmakerProposalResponded($proposal->fresh(), $lead));
            } catch (\Throwable $e) {
                \Log::error('MatchmakerProposalResponded notification failed: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => __('db.Thank you — your response has been recorded.')]);
    }

    public function consentRequests(Request $request): JsonResponse
    {
        $lead = $this->myLead($request);

        $pending = $lead->consentRequests()->where('status', 'pending')->latest('requested_at')->get();

        return response()->json([
            'consent_requests' => $pending->map(fn ($cr) => [
                'id' => $cr->id,
                'consent_type' => $cr->consent_type,
                'consent_label' => MatchmakingConsent::TYPES[$cr->consent_type] ?? $cr->consent_type,
                'requested_at' => $cr->requested_at?->toIso8601String(),
            ]),
        ]);
    }

    public function respondToConsent(Request $request, MatchmakingConsentRequest $consentRequest): JsonResponse
    {
        $lead = $this->myLead($request);
        abort_unless((int) $consentRequest->lead_id === (int) $lead->id, 404);
        abort_if(!$consentRequest->isPending(), 422, 'This request has already been responded to.');

        $validated = $request->validate([
            'decision' => ['required', 'in:grant,decline'],
        ]);

        if ($validated['decision'] === 'grant') {
            MatchmakingConsent::create([
                'lead_id' => $lead->id,
                'consent_type' => $consentRequest->consent_type,
                'method' => 'digital_form',
                'recorded_by' => $consentRequest->requested_by,
                'granted_at' => now(),
            ]);
            $consentRequest->update(['status' => 'granted', 'responded_at' => now()]);
            $label = explode(' — ', MatchmakingConsent::TYPES[$consentRequest->consent_type])[0];
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_recorded', "{$label} consent confirmed by the client themselves via the Sallaamti app.");
            $statusMessage = __('db.Thank you — your consent has been recorded.');
        } else {
            $consentRequest->update(['status' => 'declined', 'responded_at' => now()]);
            $label = explode(' — ', MatchmakingConsent::TYPES[$consentRequest->consent_type])[0];
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_declined', "{$label} consent was declined by the client.");
            $statusMessage = __('db.Noted — your Nikah Counselor has been informed.');
        }

        if ($lead->assigned_to) {
            try {
                User::find($lead->assigned_to)?->notify(new \App\Notifications\MatchmakerConsentResponded($consentRequest, $lead));
            } catch (\Throwable $e) {
                \Log::error('MatchmakerConsentResponded notification failed: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => $statusMessage]);
    }
}
