<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\RecordsCommission;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadShortlistItem;
use App\Models\MatchmakerApplication;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahPackage;
use App\Models\NikahProfile;
use App\Models\User;
use App\Notifications\LeadsBatchAssigned;
use App\Support\MatchmakerStageAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Lean matchmaker-assist CRM — MVP slice of a much larger spec. Deliberately
// does NOT build the full leads/requirements/compatibility-scoring/
// proposal-batch/consent apparatus that was proposed; this exists to solve
// the one observed real problem (WhatsApp/Facebook leads going nowhere
// because self-registration is a barrier for them) with the smallest thing
// that could work: capture the lead, convert to a real Nikah profile via
// the walk-in wizard that already exists, and let a matchmaker track a
// shortlist + a simple package/price against it. Everything actual
// "sending" of profiles still happens over WhatsApp in reality — this just
// stops that trail from being lost when the chat scrolls away.
class LeadController extends Controller
{
    use HasWizardSteps, RecordsCommission;

    // Reuses the walk-in wizard's own session namespace (see convert()
    // below) — must match Admin\NikahProfileWizardController::$wizardKey
    // exactly, or the pre-seeded step data lands somewhere that
    // controller never looks.
    protected string $wizardKey = 'admin_nikah_profile';

    // SECURITY FIX (2026-08-24): this used to allow anyone holding
    // nikah.create-profile — a permission auto-granted to every plain
    // matchmaker (see 2026_08_20_071522_add_nikah_create_profile_
    // permission_for_matchmaker.php) — straight into this controller,
    // which unlike Matchmaker\ClientController::authorizeClient() applies
    // NO per-record scoping in index()/show()/update()/destroy(). Any
    // matchmaker could view, edit, or permanently delete any OTHER
    // matchmaker's lead by guessing the ID. A plain matchmaker already
    // has their own fully self-scoped equivalent at /matchmaker/clients
    // (Matchmaker\ClientController) — this admin surface is for real
    // cross-matchmaker oversight only, so it now requires the exact same
    // gate Matchmaker\ClientController::canManageTeam() already uses for
    // that purpose, not the much more broadly held nikah.* permissions.
    private function authorize_(): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $user->can('leads.manage'), 403);
    }

    public function index(Request $request)
    {
        $this->authorize_();

        // Matchmaking-process "journey" tags the leads table shows per row
        // (point 4 of the NC-app restructuring ask) — a quick-glance
        // pipeline readout independent of the lead's own lifecycle `status`
        // column, computed as EXISTS/COUNT subqueries rather than N+1
        // per-row queries across a paginated list.
        $query = Lead::with(['assignedTo', 'nikahProfile.user', 'nikahPackage'])
            ->withExists('requirement')
            ->withCount('shortlistItems')
            ->withExists(['consents as has_participation_consent' => fn ($q) => $q->active()->where('consent_type', 'matchmaking_participation')])
            ->withExists(['proposalBatches as has_sent_batch' => fn ($q) => $q->where('status', 'sent')])
            ->withExists(['proposalBatches as has_matched_proposal' => fn ($q) => $q->whereHas('proposals', fn ($pq) => $pq->where('response', 'interested'))]);

        // One or many statuses (the coloured chips above the table).
        $statuses = array_values(array_intersect((array) $request->input('status', []), Lead::statusKeys()));
        if ($statuses) {
            $query->whereIn('status', $statuses);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->boolean('my_leads')) {
            $query->where('assigned_to', auth()->id());
        }
        if ($request->filled('payment_status')) {
            $query->where('package_payment_status', $request->payment_status);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Follow-ups that are due (the red "Follow-ups Due" card) always come
        // first, oldest due date first, so nobody waiting on a call-back gets
        // buried under fresh leads; everything else stays newest first.
        $dueStatuses = Lead::NO_FOLLOW_UP_STATUSES;
        $today = now()->toDateString();
        if ($request->boolean('follow_ups_due')) {
            $query->whereDate('next_follow_up_at', '<=', $today)->whereNotIn('status', $dueStatuses);
        }
        $placeholders = implode(',', array_fill(0, count($dueStatuses), '?'));
        $dueSql = "next_follow_up_at IS NOT NULL AND DATE(next_follow_up_at) <= ? AND status NOT IN ({$placeholders})";
        $dueBindings = array_merge([$today], $dueStatuses);
        // Sort (owner, 12 Nov 2026): "priority" (default) = Hot leads first
        // (owner, 14 Nov 2026), then due follow-ups, then by status priority (Lead::STATUSES order), newest first
        // within a status. "newest" and "follow_up" are plain orders.
        $sort = in_array($request->input('sort'), ['priority', 'newest', 'follow_up'], true) ? $request->input('sort') : 'priority';
        if ($sort === 'newest') {
            $query->orderByDesc('created_at');
        } elseif ($sort === 'follow_up') {
            $query->orderByRaw('next_follow_up_at IS NULL')->orderBy('next_follow_up_at')->orderByDesc('created_at');
        } else {
            $query->orderByRaw("CASE WHEN status = 'hot' THEN 0 ELSE 1 END")
                ->orderByRaw("CASE WHEN {$dueSql} THEN 0 ELSE 1 END", $dueBindings)
                ->orderByRaw("CASE WHEN {$dueSql} THEN next_follow_up_at END ASC", $dueBindings)
                ->orderByRaw(Lead::statusPrioritySql())
                ->orderByDesc('created_at');
        }
        $leads = $query->paginate(20)->withQueryString();

        $stats = [
            'hot' => Lead::where('status', 'hot')->count(),
            'new' => Lead::where('status', 'new')->count(),
            'paid' => Lead::where('status', 'paid')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'registered' => Lead::where('status', 'registered')->count(),
            'follow_ups_due' => Lead::whereDate('next_follow_up_at', '<=', $today)->whereNotIn('status', $dueStatuses)->count(),
            'payment_due' => Lead::where('package_payment_status', 'submitted')->count(),
        ];
        $statusCounts = Lead::selectRaw('status, COUNT(*) as n')->groupBy('status')->pluck('n', 'status')->all();

        // Found live (16 Nov 2026) — this plain role query never included
        // matchmaker_trainee, so a Training/Assessed/Probation counselor
        // (the whole point of the trainee-access addendum) was invisible
        // here even though counselorsForAssignment()'s own richer picker
        // already accounted for them. Fixed everywhere this exact query is
        // used (index/create/show's own "Assigned To" dropdowns).
        $matchmakers = User::role(['admin', 'matchmaker', 'matchmaker_trainee'])->orderBy('name')->get();

        // City filter dropdown (SEO Agent backlog #1, 15 Nov 2026): each
        // city's count of still-open leads (New / Follow-up / Registered
        // (Account) / Pending Payment) — the ones batch-assignment actually
        // cares about moving, not the whole lead history for that city.
        $openStatuses = ['new', 'contacted', 'account_registered', 'pending_payment'];
        $cityPlaceholders = implode(',', array_fill(0, count($openStatuses), '?'));
        $cities = Lead::query()
            ->whereNotNull('city')->where('city', '!=', '')
            ->selectRaw("city, COUNT(*) as total, SUM(CASE WHEN status IN ({$cityPlaceholders}) THEN 1 ELSE 0 END) as open_count", $openStatuses)
            ->groupBy('city')
            ->orderByDesc('open_count')
            ->get();

        return view('admin.leads.index', compact('leads', 'stats', 'matchmakers', 'statusCounts', 'statuses', 'sort', 'cities'));
    }

    public function create()
    {
        $this->authorize_();

        // Found live (16 Nov 2026) — this plain role query never included
        // matchmaker_trainee, so a Training/Assessed/Probation counselor
        // (the whole point of the trainee-access addendum) was invisible
        // here even though counselorsForAssignment()'s own richer picker
        // already accounted for them. Fixed everywhere this exact query is
        // used (index/create/show's own "Assigned To" dropdowns).
        $matchmakers = User::role(['admin', 'matchmaker', 'matchmaker_trainee'])->orderBy('name')->get();

        return view('admin.leads.create', compact('matchmakers'));
    }

    public function store(Request $request)
    {
        $this->authorize_();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:80'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $validated['city'] = Lead::normalizeCity($validated['city'] ?? null);
        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = $validated['assigned_to'] ?? auth()->id();

        $lead = Lead::create($validated);

        MatchmakingTimelineEvent::log($lead, null, 'lead_received', "Lead received from {$lead->source}.");

        // Only when assigned to someone other than the admin creating it —
        // an admin assigning a fresh lead to themselves doesn't need a
        // push telling them what they just did.
        if ($lead->assigned_to != auth()->id()) {
            $this->notifyAssignment($lead);
        }

        return redirect()->route('admin.leads.show', $lead)->with('status', 'Lead added.');
    }

    public function show(Lead $lead)
    {
        $this->authorize_();

        $lead->load([
            'assignedTo', 'createdBy', 'nikahProfile.user', 'nikahPackage', 'pendingPackage',
            'shortlistItems.nikahProfile.user', 'shortlistItems.createdBy', 'timelineEvents.matchmaker',
            'consents.recordedBy', 'consents.revokedBy', 'consentRequests.requestedBy',
            'proposalBatches.proposals.candidate.user',
        ]);
        // Found live (16 Nov 2026) — this plain role query never included
        // matchmaker_trainee, so a Training/Assessed/Probation counselor
        // (the whole point of the trainee-access addendum) was invisible
        // here even though counselorsForAssignment()'s own richer picker
        // already accounted for them. Fixed everywhere this exact query is
        // used (index/create/show's own "Assigned To" dropdowns).
        $matchmakers = User::role(['admin', 'matchmaker', 'matchmaker_trainee'])->orderBy('name')->get();
        // One-time packages (e.g. "Sallaamti Verified") aren't a real
        // matchmaking service a counselor assigns — every profile already
        // pays its own verification fee automatically, which fires its own
        // 'verified_profile' commission. Assigning a one-time package here
        // too would fire a second, separate commission for the same Rs.
        // amount — so this dropdown only offers real time-boxed packages.
        $packages = NikahPackage::active()->ordered()->get()->reject->isOneTime()->values();

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

        $lockedToCreator = $this->isLockedToCreator($lead);

        return view('admin.leads.show', compact('lead', 'matchmakers', 'packages', 'searchResults', 'lockedToCreator'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize_();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:80'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'status' => ['required', \App\Models\Lead::statusRule()],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'nikah_package_id' => ['nullable', 'exists:nikah_packages,id'],
            'package_price' => ['nullable', 'numeric', 'min:0'],
            'package_started_at' => ['nullable', 'date'],
        ]);

        $validated['city'] = Lead::normalizeCity($validated['city'] ?? null);
        $packageChanged = ($validated['nikah_package_id'] ?? null) != $lead->nikah_package_id;

        // Price/dates are derived from the chosen package by default (an
        // admin can still override the price for a negotiated discount) —
        // duration_days on the package drives expiry, not a manually-typed
        // date, so it can't drift out of sync with what the package
        // actually promises.
        if (!empty($validated['nikah_package_id'])) {
            $package = NikahPackage::findOrFail($validated['nikah_package_id']);
            $validated['package_price'] = $validated['package_price'] ?? $package->price;
            $validated['package_started_at'] = $validated['package_started_at'] ?? ($packageChanged ? now()->toDateString() : $lead->package_started_at);
            $validated['package_expires_at'] = $package->duration_days
                ? Carbon::parse($validated['package_started_at'])->addDays($package->duration_days)
                : null;
        } else {
            $validated['package_price'] = null;
            $validated['package_started_at'] = null;
            $validated['package_expires_at'] = null;
        }

        // A lead a plain counselor created for themselves stays theirs —
        // admin can still reassign anything else, but not pull a
        // self-created lead away from the counselor already working it
        // (that's what was leaving counselors 403'd on their own leads).
        // Silently keeping the field locked wouldn't be visible if admin
        // did try to change it, so this overrides the value AND swaps in
        // an explicit status message instead of the normal "Lead updated."
        $lockedToCreator = $this->isLockedToCreator($lead);
        $attemptedReassign = $lockedToCreator
            && (string) ($validated['assigned_to'] ?? '') !== (string) $lead->created_by;

        if ($lockedToCreator) {
            $validated['assigned_to'] = $lead->created_by;
        }

        $statusChanged = $lead->status !== $validated['status'];
        $previousAssignedTo = $lead->assigned_to;
        $lead->update($validated);

        if ($lead->assigned_to && $lead->assigned_to != $previousAssignedTo) {
            $this->notifyAssignment($lead);
        }

        if ($packageChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'package_changed', 'Package changed to ' . ($lead->fresh()->nikahPackage?->name ?? 'None') . '.');

            if ($lead->nikah_package_id) {
                $this->recordPackageCommission($lead->fresh());
            }
        }

        if ($statusChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'status_changed', 'Status changed to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
        }

        if ($attemptedReassign) {
            return back()->with('status', 'Lead updated. Note: this lead was created by ' . ($lead->createdBy?->name ?? 'a Nikah Counselor') . ' — it stays assigned to them and can\'t be reassigned to someone else.');
        }

        return back()->with('status', 'Lead updated.');
    }

    // A plain counselor's own lead is permanently theirs — reassigning it
    // away is what left them 403'd trying to open a lead they created
    // themselves. A lead created by an admin or a leads.manage holder
    // (a "Senior Matchmaker") stays freely reassignable, since neither of
    // those is a specific counselor losing "their" client.
    private function isLockedToCreator(Lead $lead): bool
    {
        $creator = $lead->createdBy;

        return $creator !== null && !$creator->hasRole('admin') && !$creator->can('leads.manage');
    }

    // Pushes to the counselor's Nikah Counselor app the moment a lead
    // lands (or is reassigned) to them — the single most time-sensitive
    // event a counselor needs to know about, since a fresh lead cools fast.
    private function notifyAssignment(Lead $lead): void
    {
        $counselor = User::find($lead->assigned_to);
        if (!$counselor) {
            return;
        }

        try {
            $counselor->notify(new \App\Notifications\MatchmakerLeadAssigned($lead));
        } catch (\Throwable $e) {
            \Log::error('MatchmakerLeadAssigned notification failed: ' . $e->getMessage());
        }
    }

    // Hands off to the exact same walk-in wizard NikahProfileWizardController
    // (Admin) already provides for matchmaker-assisted registration —
    // pre-seeding its session-backed 'account' step with what's already
    // known about this lead so the matchmaker isn't retyping it, rather
    // than building a second registration flow.
    public function convert(Lead $lead)
    {
        $this->authorize_();

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

        MatchmakingTimelineEvent::log($lead, null, 'registration_started', 'Admin started assisted registration.');

        return redirect()->route('admin.nikah.profiles.create.step', 'account')
            ->with('status', "Continue registering {$lead->name} below — their details are pre-filled. Come back to this lead afterward to link the finished profile.");
    }

    // The walk-in wizard's finalize() step doesn't know about leads, so the
    // link-back happens here: the matchmaker picks which of their leads
    // this newly-created profile belongs to, once it's created.
    public function linkProfile(Request $request, Lead $lead)
    {
        $this->authorize_();

        $validated = $request->validate(['nikah_profile_id' => ['required', 'exists:nikah_profiles,id']]);

        $lead->update(['nikah_profile_id' => $validated['nikah_profile_id'], 'status' => 'registered']);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'profile_linked', 'Sallaamti Nikah profile linked to this lead.');

        return back()->with('status', 'Lead linked to the Nikah profile.');
    }

    public function addToShortlist(Request $request, Lead $lead)
    {
        $this->authorize_();

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

    public function markShortlistSent(Lead $lead, LeadShortlistItem $item)
    {
        $this->authorize_();
        abort_unless((int) $item->lead_id === (int) $lead->id, 404);

        $item->update(['sent_at' => now()]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'candidate_shared', 'Shortlisted candidate marked as shared with the lead.');

        return back()->with('status', 'Marked as shared.');
    }

    public function removeFromShortlist(Lead $lead, LeadShortlistItem $item)
    {
        $this->authorize_();
        abort_unless((int) $item->lead_id === (int) $lead->id, 404);

        $item->delete();

        return back()->with('status', 'Removed from shortlist.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize_();

        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }

    // Admin's own equivalent of Matchmaker\ClientController::setLoginPassword()
    // — a plain matchmaker's version lives under the matchmaker.* route
    // group (gated by the 'matchmaker' role middleware), which a full admin
    // without that role can't reach, so this exists here too rather than
    // sending admin through a route they'd get bounced from.
    public function setLoginPassword(Request $request, Lead $lead)
    {
        $this->authorize_();

        $user = $lead->nikahProfile?->user;
        abort_unless($user, 422, 'This client needs a linked Sallaamti profile before a login password can be set.');

        $validated = $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'must_change_password' => true,
            'has_real_password' => true,
        ]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'login_password_set', 'A temporary login password was set for this client by admin.');

        return back()->with('status', 'Login password set — share it with the client now.');
    }

    // A client's own package selection + payment proof
    // (Public\MatchmakingProgressController::selectPackage()) only ever
    // records a claim — this is the one moment that actually activates it,
    // mirroring update()'s own package-assignment branch above exactly
    // (same price/date derivation, same recordPackageCommission() call) so
    // a client-submitted package behaves identically to an admin-typed one
    // once confirmed.
    public function confirmPackagePayment(Lead $lead)
    {
        $this->authorize_();
        abort_unless($lead->package_payment_status === 'submitted' && $lead->pending_package_id, 422, 'No package payment awaiting review.');

        $package = NikahPackage::findOrFail($lead->pending_package_id);
        $startedAt = now()->toDateString();

        $lead->update([
            'nikah_package_id' => $package->id,
            'package_price' => $package->price,
            'package_started_at' => $startedAt,
            'package_expires_at' => $package->duration_days ? Carbon::parse($startedAt)->addDays($package->duration_days) : null,
            'pending_package_id' => null,
            'package_payment_status' => 'confirmed',
        ]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'package_changed', "Package payment confirmed — {$package->name} is now active.");
        $this->recordPackageCommission($lead->fresh());

        return back()->with('status', 'Package payment confirmed — package is now active.');
    }

    // Also doubles as "undo a mistaken Confirm" — unlike the plain-reject
    // case (submitted -> rejected, nothing else active yet), undoing a
    // confirm has to unwind everything confirmPackagePayment() set: the
    // package itself (nikah_package_id/price/dates), restoring
    // pending_package_id so the original request is still visible to
    // review properly, and voiding the commission that confirming already
    // fired.
    public function rejectPackagePayment(Request $request, Lead $lead)
    {
        $this->authorize_();
        abort_unless(in_array($lead->package_payment_status, ['submitted', 'confirmed']), 422, 'No package payment awaiting review.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $wasConfirmed = $lead->package_payment_status === 'confirmed';

        $updates = [
            'package_payment_status' => 'rejected',
            'package_payment_rejection_reason' => $validated['reason'],
        ];

        if ($wasConfirmed) {
            $updates['pending_package_id'] = $lead->nikah_package_id;
            $updates['nikah_package_id'] = null;
            $updates['package_price'] = null;
            $updates['package_started_at'] = null;
            $updates['package_expires_at'] = null;
        }

        $lead->update($updates);

        if ($wasConfirmed) {
            $commissionResult = $this->voidCommissionFor(Lead::class, $lead->id, 'package', 'Package payment confirmation reversed: ' . $validated['reason']);

            $message = match ($commissionResult) {
                'voided' => 'Package payment confirmation undone — the matchmaker\'s commission for it was voided too.',
                'already_paid' => 'Package payment confirmation undone — but a commission for it was already marked Paid. That will need to be clawed back manually.',
                default => 'Package payment confirmation undone.',
            };

            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'package_payment_rejected', 'Package payment confirmation reversed: ' . $validated['reason']);

            return back()->with('status', $message);
        }

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'package_payment_rejected', 'Package payment rejected: ' . $validated['reason']);

        return back()->with('status', 'Package payment rejected — the client can resubmit from their progress link.');
    }

    // Pipeline stages a counselor must have reached before they're trusted
    // with fresh website leads (SEO Agent backlog #1, 15 Nov 2026, owner's
    // own words: "naye Nikah Counselors ko batch-wise, shehar ke mutabiq,
    // carefully assign karein") — never Rejected/Withdrawn, and never
    // someone who hasn't even finished Training yet.
    private const ASSIGNMENT_ELIGIBLE_STAGES = ['training', 'assessed', 'probation', 'certified'];

    // Data source for the bulk-assign modal's counselor picker: every
    // counselor who has reached an eligible pipeline stage and isn't
    // deactivated, with their own city (self-reported on MatchmakerApplication::area,
    // normalized the same way lead city is) and current open-lead load —
    // same-city first, then same-province, then everyone else, ties broken
    // by whoever has the least on their plate.
    public function counselorsForAssignment(Request $request)
    {
        $this->authorize_();

        $targetCity = $request->filled('city') ? Lead::normalizeCity($request->string('city')) : null;
        $targetState = Lead::cityState($targetCity);

        $counselors = MatchmakerApplication::with('user')
            ->whereIn('status', self::ASSIGNMENT_ELIGIBLE_STAGES)
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->get()
            ->filter(fn ($application) => $application->user !== null)
            ->map(function ($application) use ($targetCity, $targetState) {
                $city = Lead::normalizeCity($application->area);
                $state = Lead::cityState($city);
                $sameCity = $targetCity && $city && mb_strtolower($city) === mb_strtolower($targetCity);
                $sameState = $targetState && $state && $state === $targetState;
                // The NC app's own API (EnsureUserIsMatchmakerApi) requires
                // the matchmaker OR matchmaker_trainee role — the latter
                // granted from Training onward since the 15 Nov 2026
                // trainee-access addendum, so a Training/Assessed/Probation
                // applicant now genuinely has app access too, just with a
                // lower lead cap (MatchmakerStageAccess) than a certified
                // counselor.
                $stage = MatchmakerStageAccess::stageFor($application->user);
                $hasAppAccess = $stage !== null;
                $openLeads = Lead::where('assigned_to', $application->user->id)->whereNotIn('status', Lead::INACTIVE_STATUSES)->count();
                $leadCap = MatchmakerStageAccess::leadCapFor($stage);
                $atCap = $leadCap !== null && $openLeads >= $leadCap;

                return [
                    'id' => $application->user->id,
                    'name' => $application->user->name,
                    'city' => $city,
                    'stage' => MatchmakerApplication::STEPS[$application->status] ?? $application->status,
                    'open_leads' => $openLeads,
                    'lead_cap' => $leadCap,
                    'at_cap' => $atCap,
                    'same_city' => (bool) $sameCity,
                    'same_province' => (bool) $sameState,
                    'has_app_access' => $hasAppAccess,
                    'ineligible_reason' => !$hasAppAccess
                        ? 'NC app access starts at Training'
                        : ($atCap ? "Lead cap reached ({$openLeads}/{$leadCap})" : null),
                ];
            })
            ->sortBy(fn ($c) => [$c['has_app_access'] && !$c['at_cap'] ? 0 : 1, $c['same_city'] ? 0 : 1, $c['same_province'] ? 0 : 1, $c['open_leads']])
            ->values();

        return response()->json(['counselors' => $counselors]);
    }

    // Moves a whole batch (checkbox-selected on the City-filtered leads
    // list, hard-capped at 50) to one counselor in a single click, writes a
    // timeline event per lead so the audit trail reads the same as any
    // other reassignment, and fires exactly ONE push — not one per lead —
    // so a counselor given 20 leads gets one notification, not 20.
    public function bulkAssign(Request $request)
    {
        $this->authorize_();

        $validated = $request->validate([
            'lead_ids' => ['required', 'array', 'min:1', 'max:50'],
            'lead_ids.*' => ['integer', 'exists:leads,id'],
            'counselor_id' => ['required', 'exists:users,id'],
            'reassign' => ['nullable', 'boolean'],
        ]);

        $counselor = User::findOrFail($validated['counselor_id']);
        $application = MatchmakerApplication::where('user_id', $counselor->id)->first();
        $stage = MatchmakerStageAccess::stageFor($counselor);
        abort_unless(
            $counselor->is_active
                && $application
                && in_array($application->status, self::ASSIGNMENT_ELIGIBLE_STAGES, true)
                // Hard gate (15 Nov 2026) — the NC app's own API requires
                // one of these two roles. matchmaker_trainee (granted from
                // Training onward, same-day addendum) now covers a
                // Training/Assessed/Probation applicant too.
                && $stage !== null,
            422,
            'This counselor is not eligible to receive leads right now — they need NC app access (starts at Training) before leads can be assigned to them.'
        );

        $leadCap = MatchmakerStageAccess::leadCapFor($stage);
        $openCount = Lead::where('assigned_to', $counselor->id)->whereNotIn('status', Lead::INACTIVE_STATUSES)->count();

        $reassign = $request->boolean('reassign');
        $query = Lead::whereIn('id', $validated['lead_ids'])->with('createdBy');
        if (!$reassign) {
            // Owner, 15 Nov 2026: most open leads sit on the admin's own
            // account (admin both created and self-assigned them, long
            // before this feature existed) — treating only assigned_to IS
            // NULL as "unassigned" meant a city batch with re-assign
            // unticked moved almost nothing, since nearly every lead
            // already had SOME value there. A lead held by an admin/staff
            // account is effectively unassigned for this purpose; only a
            // lead already with another real counselor needs the re-assign
            // tick to move.
            $query->where(function ($q) {
                $q->whereNull('assigned_to')
                    ->orWhereHas('assignedTo', fn ($aq) => $aq->role('admin'));
            });
        }

        // A lead created by a counselor for themselves stays theirs even
        // during a bulk sweep — same rule update() already enforces one
        // lead at a time (isLockedToCreator()), just applied across a batch.
        $leads = $query->get()->reject(fn ($lead) => $this->isLockedToCreator($lead));

        // Stage lead cap (15 Nov 2026 addendum) — take only as many as fit
        // under the counselor's cap (null = certified, uncapped). Doesn't
        // reject the whole request — a batch of 20 against a trainee with
        // 3 slots left just assigns 3 and says so, same spirit as
        // isLockedToCreator() silently trimming the batch above.
        if ($leadCap !== null) {
            $remainingSlots = max(0, $leadCap - $openCount);
            $leads = $leads->take($remainingSlots);
        }

        if ($leads->isEmpty()) {
            return back()->with('error', $leadCap !== null && $openCount >= $leadCap
                ? "{$counselor->name} is already at their lead cap ({$openCount}/{$leadCap})."
                : 'No eligible leads in that selection — they may already be assigned, or locked to the counselor who created them.');
        }

        $admin = auth()->user();
        foreach ($leads as $lead) {
            $lead->update(['assigned_to' => $counselor->id]);
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'status_changed', "Assigned to {$counselor->name} by {$admin->name}.");
        }

        $cityLabel = $leads->pluck('city')->filter()->unique()->implode(', ') ?: 'their region';

        try {
            $counselor->notify(new LeadsBatchAssigned($cityLabel, $leads->count()));
        } catch (\Throwable $e) {
            \Log::error('LeadsBatchAssigned notification failed: ' . $e->getMessage());
        }

        return back()->with('status', "{$leads->count()} lead(s) assigned to {$counselor->name}.");
    }

    // Admin → Leads → By Counselor: per counselor and city, how much of
    // what was handed to them has actually moved — assigned vs. contacted
    // vs. registered vs. paid, and when they last touched any of it.
    public function byCounselorReport()
    {
        $this->authorize_();

        // Same gap as the "Assigned To" dropdowns above — a trainee can
        // hold and work leads now too, so their activity belongs in this
        // report.
        $counselors = User::role(['admin', 'matchmaker', 'matchmaker_trainee'])->orderBy('name')->get();
        $areas = MatchmakerApplication::whereNotNull('user_id')->pluck('area', 'user_id');

        $rows = $counselors->map(function ($user) use ($areas) {
            $base = fn () => Lead::where('assigned_to', $user->id);

            return [
                'user' => $user,
                'city' => $areas[$user->id] ?? null,
                'assigned' => $base()->count(),
                'contacted' => $base()->where('status', 'contacted')->count(),
                'registered' => $base()->whereIn('status', ['registered', 'account_registered'])->count(),
                'paid' => $base()->whereIn('status', ['paid', 'verified'])->count(),
                'last_activity' => $base()->max('updated_at'),
            ];
        })->filter(fn ($row) => $row['assigned'] > 0)->sortByDesc('assigned')->values();

        return view('admin.leads.by-counselor', compact('rows'));
    }

    // Same permission-gated pattern as Admin\MatchmakerApplicationController::file()
    // — a client's payment screenshot is on the 'private' disk, so it needs
    // a route behind admin auth rather than a raw Storage URL.
    public function file(Lead $lead, string $type)
    {
        $this->authorize_();
        abort_unless($type === 'package_payment_screenshot', 404);

        $path = $lead->{$type};
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path);
    }
}
