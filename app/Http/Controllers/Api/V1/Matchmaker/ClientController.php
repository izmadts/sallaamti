<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadShortlistItem;
use App\Models\MatchmakingConsent;
use App\Models\MatchmakingConsentRequest;
use App\Models\MatchmakingRequirement;
use App\Models\MatchmakingTimelineEvent;
use App\Models\MatchProposal;
use App\Models\NikahProfile;
use App\Models\ProposalBatch;
use App\Models\User;
use App\Http\Controllers\Matchmaker\ClientController as WebClientController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// JSON mirror of Matchmaker\ClientController (web) — same Lead/model calls,
// same authorizeClient()/canManageTeam() rules, copied rather than shared
// via a trait since they're a handful of small private methods and the two
// controllers otherwise return completely different response shapes
// (redirect+flash vs JSON).
class ClientController extends Controller
{
    private function canManageTeam(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->can('leads.manage');
    }

    private function baseQuery()
    {
        $query = Lead::with(['assignedTo', 'nikahProfile.user']);

        if (!$this->canManageTeam()) {
            $query->where('assigned_to', auth()->id());
        }

        return $query;
    }

    private function authorizeClient(Lead $lead): void
    {
        if ($this->canManageTeam()) {
            return;
        }

        abort_unless($lead->assigned_to === auth()->id(), 403, 'This client is assigned to another matchmaker.');
    }

    public function index(Request $request): JsonResponse
    {
        $canManageTeam = $this->canManageTeam();

        $query = $this->baseQuery();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($canManageTeam && $request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $clients = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $mine = $this->baseQuery();
        $stats = [
            'new' => (clone $mine)->where('status', 'new')->count(),
            'contacted' => (clone $mine)->where('status', 'contacted')->count(),
            'registered' => (clone $mine)->where('status', 'registered')->count(),
            'follow_ups_due' => (clone $mine)->whereDate('next_follow_up_at', '<=', now())->whereNotIn('status', ['registered', 'not_interested', 'closed'])->count(),
        ];

        return response()->json([
            'clients' => collect($clients->items())->map(fn ($lead) => $this->listPayload($lead)),
            'current_page' => $clients->currentPage(),
            'last_page' => $clients->lastPage(),
            'total' => $clients->total(),
            'stats' => $stats,
            'can_manage_team' => $canManageTeam,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $canManageTeam = $this->canManageTeam();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'assigned_to' => [$canManageTeam ? 'nullable' : 'prohibited', 'exists:users,id'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = ($canManageTeam && !empty($validated['assigned_to'])) ? $validated['assigned_to'] : auth()->id();

        $lead = Lead::create($validated);

        MatchmakingTimelineEvent::log($lead, null, 'lead_received', "Lead received from {$lead->source}.");

        return response()->json(['client' => $this->detailPayload($lead->fresh(['assignedTo', 'nikahProfile.user']))], 201);
    }

    public function show(Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);

        $lead->load([
            'assignedTo', 'nikahProfile.user',
            'shortlistItems.nikahProfile.user', 'shortlistItems.createdBy',
            'requirement.items',
            'proposalBatches.proposals.candidate.user',
            'proposalBatches.proposals.nikahInterest',
            'timelineEvents.matchmaker',
            'consents.recordedBy', 'consents.revokedBy',
            'consentRequests',
        ]);

        return response()->json(['client' => $this->detailPayload($lead)]);
    }

    public function update(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);
        $canManageTeam = $this->canManageTeam();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            // See Matchmaker\ClientController::update() — a live progress_link_token
            // means the client unlocks their progress page with the last 7
            // digits of this exact phone; clearing it would silently and
            // permanently lock them out.
            'phone' => [$lead->progress_link_token ? 'required' : 'nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['sometimes', 'required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'status' => ['sometimes', 'required', 'in:new,contacted,interested,registered,not_interested,closed'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'assigned_to' => [$canManageTeam ? 'nullable' : 'prohibited', 'exists:users,id'],
        ]);

        if (!$canManageTeam || empty($validated['assigned_to'])) {
            unset($validated['assigned_to']);
        }

        $reassigned = isset($validated['assigned_to']) && $validated['assigned_to'] != $lead->assigned_to;
        $statusChanged = isset($validated['status']) && $lead->status !== $validated['status'];
        $lead->update($validated);

        if ($reassigned) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'reassigned', 'Client reassigned to ' . ($lead->assignedTo?->name ?? 'a different matchmaker') . '.');
        }

        if ($statusChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'status_changed', "Status changed to " . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
        }

        return response()->json(['client' => $this->detailPayload($lead->fresh(['assignedTo', 'nikahProfile.user']))]);
    }

    public function linkProfile(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);

        $validated = $request->validate(['nikah_profile_id' => ['required', 'exists:nikah_profiles,id']]);

        $lead->update(['nikah_profile_id' => $validated['nikah_profile_id'], 'status' => 'registered']);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'profile_linked', 'Sallaamti Nikah profile linked to this client.');

        return response()->json(['client' => $this->detailPayload($lead->fresh(['assignedTo', 'nikahProfile.user']))]);
    }

    public function addToShortlist(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);

        $validated = $request->validate([
            'nikah_profile_id' => ['required', 'exists:nikah_profiles,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $item = LeadShortlistItem::firstOrCreate(
            ['lead_id' => $lead->id, 'nikah_profile_id' => $validated['nikah_profile_id']],
            ['note' => $validated['note'] ?? null, 'created_by' => auth()->id()]
        );

        if ($item->wasRecentlyCreated) {
            $candidate = NikahProfile::find($validated['nikah_profile_id']);
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'candidate_shortlisted', "Candidate {$candidate?->public_token} shortlisted.");
        }

        return response()->json(['message' => __('db.Added to shortlist.')], $item->wasRecentlyCreated ? 201 : 200);
    }

    public function removeFromShortlist(Lead $lead, LeadShortlistItem $item): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($item->lead_id === $lead->id, 404);

        $item->delete();

        return response()->json(['message' => __('db.Removed from shortlist.')]);
    }

    public function saveRequirement(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['array'],
            'items.*.requirement_type' => ['required_with:items', 'string', 'max:100'],
            'items.*.requirement_value' => ['required_with:items', 'string', 'max:255'],
            'items.*.priority' => ['required_with:items', 'in:must_have,preferred,flexible'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $requirement = MatchmakingRequirement::firstOrCreate(
            ['lead_id' => $lead->id],
            ['created_by' => auth()->id()]
        );
        $requirement->update(['notes' => $validated['notes'] ?? null]);

        $requirement->items()->delete();
        foreach ($validated['items'] ?? [] as $item) {
            $requirement->items()->create($item);
        }

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'requirements_saved', 'Matchmaking requirements ' . ($requirement->wasRecentlyCreated ? 'created' : 'updated') . '.', [
            'item_count' => count($validated['items'] ?? []),
        ]);

        return response()->json(['message' => __('db.Requirements saved.')]);
    }

    public function recordConsent(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);

        $validated = $request->validate([
            'consent_type' => ['required', 'in:' . implode(',', array_keys(MatchmakingConsent::TYPES))],
            'method' => ['required', 'in:' . implode(',', array_keys(MatchmakingConsent::METHODS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['lead_id'] = $lead->id;
        $validated['recorded_by'] = auth()->id();
        $validated['granted_at'] = now();

        MatchmakingConsent::create($validated);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_recorded', MatchmakingConsent::TYPES[$validated['consent_type']] . ' consent recorded (' . MatchmakingConsent::METHODS[$validated['method']] . ').');

        return response()->json(['message' => __('db.Consent recorded.')], 201);
    }

    public function requestConsent(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);

        $validated = $request->validate([
            'consent_type' => ['required', 'in:' . implode(',', array_keys(MatchmakingConsent::TYPES))],
        ]);

        abort_unless($lead->phone, 422, 'Add a phone number for this client first — that\'s what secures the link they\'ll confirm through.');

        if (!$lead->progress_link_token) {
            $lead->update(['progress_link_token' => Str::random(40)]);
        }

        $alreadyPending = MatchmakingConsentRequest::where('lead_id', $lead->id)
            ->where('consent_type', $validated['consent_type'])
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return response()->json(['message' => "Already waiting on {$lead->name} to confirm this."]);
        }

        MatchmakingConsentRequest::create([
            'lead_id' => $lead->id,
            'consent_type' => $validated['consent_type'],
            'requested_by' => auth()->id(),
            'requested_at' => now(),
        ]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_requested', MatchmakingConsent::TYPES[$validated['consent_type']] . ' consent requested — waiting on the client to confirm via their secure link.');

        return response()->json(['message' => "Consent request sent — {$lead->name} will see it next time they open their progress link."], 201);
    }

    public function revokeConsent(Lead $lead, MatchmakingConsent $consent): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($consent->lead_id === $lead->id, 404);
        abort_if(!$consent->isActive(), 422, 'This consent was already revoked.');

        $consent->update(['revoked_at' => now(), 'revoked_by' => auth()->id()]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_revoked', MatchmakingConsent::TYPES[$consent->consent_type] . ' consent revoked.');

        return response()->json(['message' => __('db.Consent revoked.')]);
    }

    public function regenerateProgressLink(Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($lead->phone, 422, 'Add a phone number for this client first — it\'s what they\'ll enter to unlock the page.');

        $hadTokenAlready = (bool) $lead->progress_link_token;
        $lead->update(['progress_link_token' => Str::random(40)]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'progress_link_regenerated', 'Progress page link was ' . ($hadTokenAlready ? 'regenerated — the old copy no longer works' : 'generated') . '.');

        return response()->json(['message' => __('db.Progress link ready.'), 'url' => WebClientController::progressLink($lead->fresh())]);
    }

    public function createBatch(Lead $lead): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($lead->nikah_profile_id, 422, 'This client needs a linked Nikah profile before a proposal batch can be sent to them.');
        abort_unless($lead->hasActiveConsent('matchmaking_participation'), 422, 'Record this client\'s consent to participate in matchmaking before sending them proposals.');

        $batchNumber = ProposalBatch::where('lead_id', $lead->id)->max('batch_number') + 1;

        $batch = ProposalBatch::create([
            'lead_id' => $lead->id,
            'nikah_profile_id' => $lead->nikah_profile_id,
            'matchmaker_id' => auth()->id(),
            'batch_number' => $batchNumber,
            'status' => 'draft',
        ]);

        return response()->json(['batch' => $this->batchPayload($batch)], 201);
    }

    public function addProposal(Request $request, Lead $lead, ProposalBatch $batch): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id, 404);
        abort_unless($batch->status === 'draft', 422, 'Only a draft batch can have candidates added.');

        $validated = $request->validate([
            'candidate_profile_id' => ['required', 'exists:nikah_profiles,id'],
            'match_reasons' => ['nullable', 'array'],
            'match_reasons.*' => ['string', 'max:255'],
            'internal_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($batch->proposals()->count() >= 5) {
            return response()->json(['message' => 'A proposal batch is capped at 5 curated candidates — create a new batch for more.'], 422);
        }

        MatchProposal::firstOrCreate(
            ['proposal_batch_id' => $batch->id, 'candidate_profile_id' => $validated['candidate_profile_id']],
            ['match_reasons' => $validated['match_reasons'] ?? null, 'internal_notes' => $validated['internal_notes'] ?? null]
        );

        return response()->json(['batch' => $this->batchPayload($batch->fresh('proposals.candidate.user'))], 201);
    }

    public function removeProposal(Lead $lead, ProposalBatch $batch, MatchProposal $proposal): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id && $proposal->proposal_batch_id === $batch->id, 404);
        abort_unless($batch->status === 'draft', 422);

        $proposal->delete();

        return response()->json(['message' => __('db.Candidate removed from the batch.')]);
    }

    public function sendBatch(Lead $lead, ProposalBatch $batch): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id, 404);

        if ($batch->proposals()->count() === 0) {
            return response()->json(['message' => 'Add at least one candidate before sending.'], 422);
        }

        if ($lead->nikah_package_id && $lead->packageExpired()) {
            return response()->json(['message' => "This client's {$lead->nikahPackage->name} package expired on {$lead->package_expires_at->format('d M Y')} — renew it before sending more proposals."], 422);
        }

        $remaining = $lead->remainingProposalAllowance();
        if ($remaining !== null && $batch->proposals()->count() > $remaining) {
            $word = $remaining === 1 ? 'proposal' : 'proposals';
            return response()->json(['message' => "This client's {$lead->nikahPackage->name} package only has {$remaining} {$word} remaining, but this batch has {$batch->proposals()->count()}."], 422);
        }

        $now = now();
        foreach ($batch->proposals as $proposal) {
            $proposal->update(['status' => 'sent', 'sent_at' => $now, 'link_token' => $proposal->link_token ?? Str::random(40)]);
        }
        $batch->update(['status' => 'sent', 'sent_at' => $now]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'proposal_batch_sent', "Proposal Batch #{$batch->batch_number} sent ({$batch->proposals()->count()} candidates).");

        return response()->json(['batch' => $this->batchPayload($batch->fresh('proposals.candidate.user'))]);
    }

    public function regenerateLink(Lead $lead, ProposalBatch $batch, MatchProposal $proposal): JsonResponse
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id && $proposal->proposal_batch_id === $batch->id, 404);
        abort_unless($proposal->sent_at, 422, 'This candidate has not been sent a link yet.');
        abort_if($proposal->status === 'responded', 422, 'The client already responded to this proposal — nothing to regenerate.');

        $proposal->update(['link_token' => Str::random(40)]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'proposal_link_regenerated', 'A proposal link was regenerated — the old copy no longer works.');

        return response()->json(['url' => WebClientController::proposalLink($proposal->fresh())]);
    }

    private function listPayload(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'status' => $lead->status,
            'source' => $lead->source,
            'assigned_to' => $lead->assignedTo ? ['id' => $lead->assignedTo->id, 'name' => $lead->assignedTo->name] : null,
            'is_converted' => $lead->isConverted(),
            'next_follow_up_at' => $lead->next_follow_up_at?->toDateString(),
            'created_at' => $lead->created_at->toIso8601String(),
        ];
    }

    private function detailPayload(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'gender' => $lead->gender,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'looking_for' => $lead->looking_for,
            'status' => $lead->status,
            'source' => $lead->source,
            'notes' => $lead->notes,
            'next_follow_up_at' => $lead->next_follow_up_at?->toDateString(),
            'assigned_to' => $lead->assignedTo ? ['id' => $lead->assignedTo->id, 'name' => $lead->assignedTo->name] : null,
            'is_converted' => $lead->isConverted(),
            'nikah_profile_id' => $lead->nikah_profile_id,
            'has_progress_link' => (bool) $lead->progress_link_token,
            'progress_url' => WebClientController::progressLink($lead),
            'nikah_package_id' => $lead->nikah_package_id,
            'package_expires_at' => $lead->package_expires_at?->toDateString(),
            'remaining_proposal_allowance' => $lead->remainingProposalAllowance(),
            'created_at' => $lead->created_at->toIso8601String(),
            'shortlist' => $lead->relationLoaded('shortlistItems') ? $lead->shortlistItems->map(fn ($item) => [
                'id' => $item->id,
                'note' => $item->note,
                'created_by' => $item->createdBy?->name,
                'candidate' => $item->nikahProfile ? [
                    'id' => $item->nikahProfile->id,
                    'age' => $item->nikahProfile->age,
                    'city' => $item->nikahProfile->city,
                    'sect' => $item->nikahProfile->sect,
                ] : null,
            ]) : null,
            'requirement' => $lead->relationLoaded('requirement') && $lead->requirement ? [
                'notes' => $lead->requirement->notes,
                'items' => $lead->requirement->items->map(fn ($item) => [
                    'requirement_type' => $item->requirement_type,
                    'requirement_value' => $item->requirement_value,
                    'priority' => $item->priority,
                    'notes' => $item->notes,
                ]),
            ] : null,
            'consents' => $lead->relationLoaded('consents') ? $lead->consents->map(fn ($c) => [
                'id' => $c->id,
                'consent_type' => $c->consent_type,
                'label' => MatchmakingConsent::TYPES[$c->consent_type] ?? $c->consent_type,
                'method' => $c->method,
                'is_active' => $c->isActive(),
                'granted_at' => $c->granted_at?->toIso8601String(),
                'revoked_at' => $c->revoked_at?->toIso8601String(),
                'recorded_by' => $c->recordedBy?->name,
            ]) : null,
            'consent_requests' => $lead->relationLoaded('consentRequests') ? $lead->consentRequests->map(fn ($r) => [
                'id' => $r->id,
                'consent_type' => $r->consent_type,
                'label' => MatchmakingConsent::TYPES[$r->consent_type] ?? $r->consent_type,
                'status' => $r->status,
                'requested_at' => $r->requested_at?->toIso8601String(),
            ]) : null,
            'proposal_batches' => $lead->relationLoaded('proposalBatches') ? $lead->proposalBatches->map(fn ($b) => $this->batchPayload($b)) : null,
            'timeline' => $lead->relationLoaded('timelineEvents') ? $lead->timelineEvents->map(fn ($e) => [
                'id' => $e->id,
                'event_type' => $e->event_type,
                'description' => $e->description,
                'matchmaker' => $e->matchmaker?->name,
                'created_at' => $e->created_at->toIso8601String(),
            ]) : null,
        ];
    }

    private function batchPayload(ProposalBatch $batch): array
    {
        return [
            'id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'status' => $batch->status,
            'sent_at' => $batch->sent_at?->toIso8601String(),
            'proposals' => $batch->relationLoaded('proposals') ? $batch->proposals->map(fn ($p) => [
                'id' => $p->id,
                'status' => $p->status,
                'response' => $p->response,
                'candidate' => $p->candidate ? [
                    'id' => $p->candidate->id,
                    'age' => $p->candidate->age,
                    'city' => $p->candidate->city,
                    'sect' => $p->candidate->sect,
                ] : null,
                'share_url' => WebClientController::proposalLink($p),
            ]) : null,
        ];
    }
}
