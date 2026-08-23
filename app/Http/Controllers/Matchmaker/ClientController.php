<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadShortlistItem;
use App\Models\MatchmakingRequirement;
use App\Models\MatchmakingTimelineEvent;
use App\Models\MatchProposal;
use App\Models\NikahProfile;
use App\Models\ProposalBatch;
use App\Models\User;
use App\Services\Matchmaking\CompatibilityScorer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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

    private function baseQuery()
    {
        $query = Lead::with(['assignedTo', 'nikahProfile.user']);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('assigned_to', auth()->id());
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->baseQuery();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        return view('matchmaker.clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('matchmaker.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = auth()->id();

        $lead = Lead::create($validated);

        MatchmakingTimelineEvent::log($lead, null, 'lead_received', "Lead received from {$lead->source}.");

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
            'timelineEvents.matchmaker',
        ]);

        $searchResults = collect();
        if (request()->filled('search_city') || request()->filled('search_gender') || request()->filled('search_sect')) {
            $query = NikahProfile::with('user')
                ->where('is_active', true)
                ->whereNull('suspended_at')
                ->where('visibility', 'public')
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
                    ->where('is_active', true)
                    ->whereNull('suspended_at')
                    ->where('visibility', 'public')
                    ->whereHas('user', fn ($q) => $q->where('gender', $targetGender))
                    ->whereNotIn('id', $lead->shortlistItems->pluck('nikah_profile_id'))
                    ->latest('created_at')
                    ->limit(200) // bound the scoring pool; not a full-catalog scan
                    ->get();

                $suggestions = app(CompatibilityScorer::class)->rank($lead->requirement, $pool)->take(10);
            }
        }

        return view('matchmaker.clients.show', compact('lead', 'searchResults', 'suggestions'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorizeClient($lead);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'status' => ['required', 'in:new,contacted,interested,registered,not_interested,closed'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $statusChanged = $lead->status !== $validated['status'];
        $lead->update($validated);

        if ($statusChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'status_changed', "Status changed to " . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
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

        $this->saveWizardStep('account', [
            'name' => $lead->name,
            'identifier' => $lead->email ?: $lead->phone,
            'gender' => $lead->gender,
        ]);

        $lead->update(['status' => 'registered']);

        MatchmakingTimelineEvent::log($lead, null, 'registration_started', 'Matchmaker started assisted registration.');

        return redirect()->route('admin.nikah.profiles.create.step', 'account')
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

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'requirements_saved', 'Matchmaking requirements ' . ($requirement->wasRecentlyCreated ? 'created' : 'updated') . '.', [
            'item_count' => count($validated['items'] ?? []),
        ]);

        return back()->with('status', 'Requirements saved.');
    }

    // --- Proposal Batches (spec doc §20-24) ---

    public function createBatch(Lead $lead)
    {
        $this->authorizeClient($lead);
        abort_unless($lead->nikah_profile_id, 422, 'This client needs a linked Nikah profile before a proposal batch can be sent to them.');

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

    // Generates a 14-day signed link per candidate (no login required to
    // respond — see Public\MatchmakingActionController) and hands the
    // matchmaker a "copy to send" URL rather than the system emailing it
    // automatically, so it works for a client who doesn't use email at
    // all — WhatsApp, SMS, or read aloud over a call, whatever actually
    // reaches them.
    public function sendBatch(Lead $lead, ProposalBatch $batch)
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id, 404);

        if ($batch->proposals()->count() === 0) {
            return back()->with('error', 'Add at least one candidate before sending.');
        }

        $now = now();
        foreach ($batch->proposals as $proposal) {
            $proposal->update(['status' => 'sent', 'sent_at' => $now, 'link_token' => $proposal->link_token ?? Str::random(40)]);
        }
        $batch->update(['status' => 'sent', 'sent_at' => $now]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'proposal_batch_sent', "Proposal Batch #{$batch->batch_number} sent ({$batch->proposals()->count()} candidates).");

        return back()->with('status', "Batch #{$batch->batch_number} marked as sent — copy the link below for each candidate and send it to {$lead->name} however reaches them best.");
    }

    // The link doesn't expire on its own — it stays valid until the client
    // actually responds, since a matchmaker relaying it by hand (WhatsApp,
    // SMS, a phone call) has no control over how quickly it reaches them.
    // The only way to kill a copy that's already out is regenerateLink()
    // below, which swaps the token so the old URL stops matching.
    public function regenerateLink(Lead $lead, ProposalBatch $batch, MatchProposal $proposal)
    {
        $this->authorizeClient($lead);
        abort_unless($batch->lead_id === $lead->id && $proposal->proposal_batch_id === $batch->id, 404);
        abort_unless($proposal->sent_at, 422, 'This candidate has not been sent a link yet.');
        abort_if($proposal->status === 'responded', 422, 'The client already responded to this proposal — nothing to regenerate.');

        $proposal->update(['link_token' => Str::random(40)]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'proposal_link_regenerated', 'A proposal link was regenerated — the old copy no longer works.');

        return back()->with('status', 'New link generated — the old one has stopped working. Copy and send the new link below.');
    }

    // Permanent (no time expiry) — see the note on regenerateLink() above
    // for how a link actually gets invalidated.
    public static function proposalLink(MatchProposal $proposal): ?string
    {
        if (!$proposal->sent_at || !$proposal->link_token) {
            return null;
        }

        return URL::signedRoute('public.matchmaking.proposal.show', ['proposal' => $proposal->id, 'token' => $proposal->link_token]);
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
        $lead->update(['progress_link_token' => Str::random(40)]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'progress_link_regenerated', 'Progress page link was ' . ($hadTokenAlready ? 'regenerated — the old copy no longer works' : 'generated') . '.');

        return back()->with('status', 'Progress link ready — copy it below and send it to ' . $lead->name . '. They\'ll need the last 7 digits of the WhatsApp number on file to view it, every time.');
    }

    public static function progressLink(Lead $lead): ?string
    {
        if (!$lead->progress_link_token) {
            return null;
        }

        return URL::signedRoute('public.matchmaking.progress.show', ['lead' => $lead->id, 'token' => $lead->progress_link_token]);
    }

    private function authorizeClient(Lead $lead): void
    {
        if (auth()->user()->hasRole('admin')) {
            return;
        }

        abort_unless($lead->assigned_to === auth()->id(), 403, 'This client is assigned to another matchmaker.');
    }
}
