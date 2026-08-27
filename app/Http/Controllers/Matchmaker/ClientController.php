<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Concerns\HasWizardSteps;
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
use App\Services\Matchmaking\CompatibilityScorer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// The matchmaker's own themed workspace — separate routes/views from
// Admin\LeadController (which stays exactly as-is for admin's global
// oversight), but built on the exact same Lead/LeadShortlistItem models,
// never a parallel data store (spec doc §52). Scoped to the current
// matchmaker's own assigned leads unless they're also a full admin.
class ClientController extends Controller
{
    use HasWizardSteps;

    protected string $wizardKey = 'admin_nikah_profile';

    // Full admins already bypass the assigned_to scoping; leads.manage grants
    // the same cross-matchmaker visibility without the rest of the admin
    // role — the "Senior Matchmaker" ability, assignable via the normal
    // admin > Users > Roles permission grid (App\Support\PermissionCatalog).
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

    public function index(Request $request)
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

        $clients = $query->with(['requirement.items', 'shortlistItems', 'proposalBatches'])
            ->orderByDesc('created_at')->paginate(15)->withQueryString();

        $mine = $this->baseQuery();
        $stats = [
            'new' => (clone $mine)->where('status', 'new')->count(),
            'contacted' => (clone $mine)->where('status', 'contacted')->count(),
            'registered' => (clone $mine)->where('status', 'registered')->count(),
            'follow_ups_due' => (clone $mine)->whereDate('next_follow_up_at', '<=', now())->whereNotIn('status', ['registered', 'not_interested', 'closed'])->count(),
        ];

        $matchmakers = $canManageTeam ? User::role(['admin', 'matchmaker'])->orderBy('name')->get() : collect();

        return view('matchmaker.clients.index', compact('clients', 'stats', 'canManageTeam', 'matchmakers'));
    }

    public function create()
    {
        $canManageTeam = $this->canManageTeam();
        $matchmakers = $canManageTeam ? User::role(['admin', 'matchmaker'])->orderBy('name')->get() : collect();

        return view('matchmaker.clients.create', compact('canManageTeam', 'matchmakers'));
    }

    public function store(Request $request)
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
        // A regular matchmaker can never assign a client to someone else,
        // even by tampering with the request — 'prohibited' above already
        // rejects the field outright for them, this is the value fallback.
        $validated['assigned_to'] = ($canManageTeam && !empty($validated['assigned_to'])) ? $validated['assigned_to'] : auth()->id();

        $lead = Lead::create($validated);

        MatchmakingTimelineEvent::log($lead, null, 'lead_received', "Lead received from {$lead->source}.");

        // Every client gets their one standing link from the moment
        // they're added — no separate "generate" step to remember. Needs a
        // phone number since that's the secret they prove ownership of on
        // every visit; if none was given yet, update() generates it the
        // moment a phone number is finally entered.
        if ($lead->phone) {
            $lead->update(['progress_link_token' => Str::random(24)]);
            MatchmakingTimelineEvent::log($lead, null, 'progress_link_generated', 'Progress page link was generated.');
        }

        return redirect()->route('matchmaker.clients.show', $lead)->with('status', 'Client added.');
    }

    public function show(Lead $lead)
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

        $searchResults = collect();
        if (request()->filled('search_city') || request()->filled('search_gender') || request()->filled('search_sect')) {
            $query = NikahProfile::with('user')
                ->matchmakerVisible()
                ->whereNotIn('id', $lead->shortlistItems->pluck('nikah_profile_id'));

            if (request()->filled('search_gender')) {
                $query->whereHas('user', fn ($q) => $q->where('gender', request('search_gender')));
            }
            if (request()->filled('search_city')) {
                $query->where('city', 'like', '%' . request('search_city') . '%');
            }
            if (request()->filled('search_sect')) {
                $query->where('sect', 'like', '%' . request('search_sect') . '%');
            }

            $searchResults = $query->orderByDesc('created_at')->limit(20)->get();
        }

        $suggestions = collect();
        if ($lead->requirement && $lead->requirement->items->isNotEmpty()) {
            $targetGender = $lead->gender === 'male' ? 'female' : ($lead->gender === 'female' ? 'male' : null);

            if ($targetGender) {
                $pool = NikahProfile::with('user')
                    ->matchmakerVisible()
                    ->whereHas('user', fn ($q) => $q->where('gender', $targetGender))
                    ->whereNotIn('id', $lead->shortlistItems->pluck('nikah_profile_id'))
                    ->latest('created_at')
                    ->limit(200) // bound the scoring pool; not a full-catalog scan
                    ->get();

                $suggestions = app(CompatibilityScorer::class)->rank($lead->requirement, $pool)->take(10);
            }
        }

        $canManageTeam = $this->canManageTeam();
        $matchmakers = $canManageTeam ? User::role(['admin', 'matchmaker'])->orderBy('name')->get() : collect();

        return view('matchmaker.clients.show', compact('lead', 'searchResults', 'suggestions', 'canManageTeam', 'matchmakers'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorizeClient($lead);
        $canManageTeam = $this->canManageTeam();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            // The matchmaker's own phone field is never pre-filled with
            // the real number (see Lead::maskedPhone()) — blank on submit
            // means "leave it as-is", not "clear it", same as changing a
            // password without re-typing the old one. Only a non-empty
            // submission actually touches the stored value (see below).
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'status' => ['required', 'in:new,contacted,interested,registered,not_interested,closed'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'assigned_to' => [$canManageTeam ? 'nullable' : 'prohibited', 'exists:users,id'],
        ]);

        if (!$canManageTeam || empty($validated['assigned_to'])) {
            unset($validated['assigned_to']);
        }

        if (!$request->filled('phone')) {
            unset($validated['phone']);
        }

        $reassigned = isset($validated['assigned_to']) && $validated['assigned_to'] != $lead->assigned_to;
        $statusChanged = $lead->status !== $validated['status'];
        $hadNoLinkYet = !$lead->progress_link_token;
        $lead->update($validated);

        if ($reassigned) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'reassigned', 'Client reassigned to ' . ($lead->assignedTo?->name ?? 'a different Nikah Counselor') . '.');
        }

        if ($statusChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'status_changed', "Status changed to " . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
        }

        // Phone just arrived for a client added without one — mint their
        // one standing link now instead of waiting for a manual "Generate
        // Link" click or the first consent request.
        if ($hadNoLinkYet && $lead->phone) {
            $lead->update(['progress_link_token' => Str::random(24)]);
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'progress_link_generated', 'Progress page link was generated.');
        }

        return back()->with('status', 'Client updated.');
    }

    public function convert(Lead $lead)
    {
        $this->authorizeClient($lead);

        if ($lead->isConverted()) {
            return back()->with('error', 'This lead has already been converted.');
        }

        session()->forget($this->wizardSessionKey());

        // Tracked so finalize() can link the finished profile straight back
        // to this Lead automatically — previously the matchmaker had to
        // remember to come back and use "Already have a profile for them?"
        // to connect the two manually.
        session(["{$this->wizardSessionKey()}.lead_id" => $lead->id]);

        $this->saveWizardStep('account', [
            'name' => $lead->name,
            'identifier' => $lead->email ?: $lead->phone,
            'gender' => $lead->gender,
        ]);

        $lead->update(['status' => 'registered']);

        MatchmakingTimelineEvent::log($lead, null, 'registration_started', 'Nikah Counselor started assisted registration.');

        return redirect()->route('matchmaker.nikah.profiles.create.step', 'account')
            ->with('status', "Continue registering {$lead->name} below — their details are pre-filled. Come back to this client afterward to link the finished profile.");
    }

    public function linkProfile(Request $request, Lead $lead)
    {
        $this->authorizeClient($lead);

        $validated = $request->validate(['nikah_profile_id' => ['required', 'exists:nikah_profiles,id']]);

        $lead->update(['nikah_profile_id' => $validated['nikah_profile_id'], 'status' => 'registered']);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'profile_linked', 'Sallaamti Nikah profile linked to this client.');

        return back()->with('status', 'Client linked to the Nikah profile.');
    }

    public function addToShortlist(Request $request, Lead $lead)
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

        return back()->with('status', 'Added to shortlist.');
    }

    public function removeFromShortlist(Lead $lead, LeadShortlistItem $item)
    {
        $this->authorizeClient($lead);
        abort_unless($item->lead_id === $lead->id, 404);

        $item->delete();

        return back()->with('status', 'Removed from shortlist.');
    }

    // --- Requirements (spec doc §10-12) ---

    public function saveRequirement(Request $request, Lead $lead)
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

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'requirements_saved', 'Nikah counseling requirements ' . ($requirement->wasRecentlyCreated ? 'created' : 'updated') . '.', [
            'item_count' => count($validated['items'] ?? []),
        ]);

        return back()->with('status', 'Requirements saved.');
    }

    // --- Consent (spec doc §14-15) ---
    //
    // A recorded moment, not a live checkbox — the matchmaker gets consent
    // verbally, over WhatsApp, or in person, and logs it here. An active
    // "matchmaking_participation" consent gates createBatch() below; the
    // other types are informational records only, not currently enforced
    // anywhere else, so recording them never blocks any existing action.

    public function recordConsent(Request $request, Lead $lead)
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

        return back()->with('status', 'Consent recorded.');
    }

    // Instead of the matchmaker just asserting consent happened, this asks
    // the client to confirm it themselves through their own progress link
    // — the real requester→recipient→confirm flow the platform was
    // missing. recordConsent() above is untouched for the genuinely
    // verbal/phone/in-person cases where that's how consent actually
    // happened.
    public function requestConsent(Request $request, Lead $lead)
    {
        $this->authorizeClient($lead);

        $validated = $request->validate([
            'consent_type' => ['required', 'in:' . implode(',', array_keys(MatchmakingConsent::TYPES))],
        ]);

        abort_unless($lead->phone, 422, 'Add a phone number for this client first — that\'s what secures the link they\'ll confirm through.');

        if (!$lead->progress_link_token) {
            $lead->update(['progress_link_token' => Str::random(24)]);
        }

        $alreadyPending = MatchmakingConsentRequest::where('lead_id', $lead->id)
            ->where('consent_type', $validated['consent_type'])
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->with('status', "Already waiting on {$lead->name} to confirm this.");
        }

        MatchmakingConsentRequest::create([
            'lead_id' => $lead->id,
            'consent_type' => $validated['consent_type'],
            'requested_by' => auth()->id(),
            'requested_at' => now(),
        ]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_requested', MatchmakingConsent::TYPES[$validated['consent_type']] . ' consent requested — waiting on the client to confirm via their secure link.');

        return back()->with('status', "Consent request sent — {$lead->name} will see it next time they open their progress link.");
    }

    public function revokeConsent(Lead $lead, MatchmakingConsent $consent)
    {
        $this->authorizeClient($lead);
        abort_unless($consent->lead_id === $lead->id, 404);
        abort_if(!$consent->isActive(), 422, 'This consent was already revoked.');

        $consent->update(['revoked_at' => now(), 'revoked_by' => auth()->id()]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'consent_revoked', MatchmakingConsent::TYPES[$consent->consent_type] . ' consent revoked.');

        return back()->with('status', 'Consent revoked.');
    }

    // --- Proposal Batches (spec doc §20-24) ---

    public function createBatch(Lead $lead)
    {
        $this->authorizeClient($lead);
        abort_unless($lead->nikah_profile_id, 422, 'This client needs a linked Nikah profile before a proposal batch can be sent to them.');
        abort_unless($lead->hasActiveConsent('matchmaking_participation'), 422, 'Record this client\'s consent to participate in Nikah counseling (see the Consent section on Overview) before sending them proposals.');

        $batchNumber = ProposalBatch::where('lead_id', $lead->id)->max('batch_number') + 1;

        $batch = ProposalBatch::create([
            'lead_id' => $lead->id,
            'nikah_profile_id' => $lead->nikah_profile_id,
            'matchmaker_id' => auth()->id(),
            'batch_number' => $batchNumber,
            'status' => 'draft',
        ]);

        return redirect()->route('matchmaker.clients.show', $lead)->with('status', "Proposal Batch #{$batchNumber} started — add candidates below.");
    }

    public function addProposal(Request $request, Lead $lead, ProposalBatch $batch)
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
            return back()->with('error', 'A proposal batch is capped at 5 curated candidates — create a new batch for more.');
        }

        MatchProposal::firstOrCreate(
            ['proposal_batch_id' => $batch->id, 'candidate_profile_id' => $validated['candidate_profile_id']],
            ['match_reasons' => $validated['match_reasons'] ?? null, 'internal_notes' => $validated['internal_notes'] ?? null]
        );

        return back()->with('status', 'Candidate added to the batch.');
    }

    public function removeProposal(Lead $lead, ProposalBatch $batch, MatchProposal $proposal)
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id && $proposal->proposal_batch_id === $batch->id, 404);
        abort_unless($batch->status === 'draft', 422);

        $proposal->delete();

        return back()->with('status', 'Candidate removed from the batch.');
    }

    // Just flips every candidate in the batch to 'sent' — no per-candidate
    // link to generate any more. The client reviews and responds to each
    // one from their own one standing progress link instead (see
    // Public\MatchmakingProgressController::respondToProposal()).
    public function sendBatch(Lead $lead, ProposalBatch $batch)
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id, 404);

        if ($batch->proposals()->count() === 0) {
            return back()->with('error', 'Add at least one candidate before sending.');
        }

        if ($lead->nikah_package_id && $lead->packageExpired()) {
            return back()->with('error', "This client's {$lead->nikahPackage->name} package expired on {$lead->package_expires_at->format('d M Y')} — renew it (in the admin lead page) before sending more proposals.");
        }

        $remaining = $lead->remainingProposalAllowance();
        if ($remaining !== null && $batch->proposals()->count() > $remaining) {
            $word = $remaining === 1 ? 'proposal' : 'proposals';
            return back()->with('error', "This client's {$lead->nikahPackage->name} package only has {$remaining} {$word} remaining, but this batch has {$batch->proposals()->count()}. Remove some candidates, or ask an admin to upgrade/renew the package.");
        }

        $now = now();
        $batch->proposals()->update(['status' => 'sent', 'sent_at' => $now]);
        $batch->update(['status' => 'sent', 'sent_at' => $now]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'proposal_batch_sent', "Proposal Batch #{$batch->batch_number} sent ({$batch->proposals()->count()} candidates).");

        return back()->with('status', "Batch #{$batch->batch_number} marked as sent — {$lead->name} will see these candidates on their own progress link.");
    }

    // STALE (2026-08-25): kept only because Api\V1\Matchmaker\ClientController
    // (paused Flutter app) still calls this — see that controller's own
    // regenerateLink() comment. The named route this builds a URL for
    // (public.matchmaking.proposal.show) no longer exists: proposal
    // responses now happen on the client's one progress link
    // (Public\MatchmakingProgressController::respondToProposal()) instead
    // of a separate per-proposal link. Calling this from anywhere reachable
    // today would throw — it isn't, since the web UI stopped calling it in
    // the same change that removed the route.
    public static function proposalLink(MatchProposal $proposal): ?string
    {
        // The route this used to build a URL for (public.matchmaking.
        // proposal.show) no longer exists — proposal responses now happen
        // on the client's one progress link instead. Returns null rather
        // than throwing a RouteNotFoundException so the paused Flutter
        // app's API layer (its only remaining caller — see Api\V1\
        // Matchmaker\ClientController's own STALE comment) degrades to
        // "no link" instead of a 500 if it's ever hit before that layer
        // gets reworked.
        return null;
    }

    // A standing link the client can revisit any time to see their own
    // status, timeline, and full proposal/response history — gated by the
    // last 7 digits of the WhatsApp number on file rather than a login,
    // re-checked on every visit (see Public\MatchmakingProgressController).
    // One button does both first-generation and regeneration: regenerating
    // kills the old copy the same way it does for a single proposal link.
    public function regenerateProgressLink(Lead $lead)
    {
        $this->authorizeClient($lead);
        abort_unless($lead->phone, 422, 'Add a phone number for this client first — it\'s what they\'ll enter to unlock the page.');

        $hadTokenAlready = (bool) $lead->progress_link_token;
        $lead->update(['progress_link_token' => Str::random(24)]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'progress_link_regenerated', 'Progress page link was ' . ($hadTokenAlready ? 'regenerated — the old copy no longer works' : 'generated') . '.');

        return back()->with('status', 'Progress link ready — use Copy Link below and paste it wherever you\'re messaging ' . $lead->name . '. They\'ll need the last 7 digits of the WhatsApp number on file to view it, every time.');
    }

    public static function progressLink(Lead $lead): ?string
    {
        if (!$lead->progress_link_token) {
            return null;
        }

        return route('public.matchmaking.progress.show', ['lead' => $lead->id, 't' => $lead->progress_link_token]);
    }

    private function authorizeClient(Lead $lead): void
    {
        if ($this->canManageTeam()) {
            return;
        }

        abort_unless($lead->assigned_to === auth()->id(), 403, 'This client is assigned to another Nikah Counselor, so it\'s hidden from your account for privacy. If this client should be yours, ask your admin to reassign it to you — or, if you need to see every counselor\'s clients, ask admin to grant your account the "leads.manage" permission.');
    }
}
