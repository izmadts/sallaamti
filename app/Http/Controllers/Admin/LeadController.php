<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HasWizardSteps;
use App\Http\Controllers\Concerns\RecordsCommission;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadShortlistItem;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahPackage;
use App\Models\NikahProfile;
use App\Models\User;
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

        $query = Lead::with(['assignedTo', 'nikahProfile.user', 'nikahPackage']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $leads = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $stats = [
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'registered' => Lead::where('status', 'registered')->count(),
            'follow_ups_due' => Lead::whereDate('next_follow_up_at', '<=', now())->whereNotIn('status', ['registered', 'not_interested', 'closed'])->count(),
        ];

        $matchmakers = User::role(['admin', 'matchmaker'])->orderBy('name')->get();

        return view('admin.leads.index', compact('leads', 'stats', 'matchmakers'));
    }

    public function create()
    {
        $this->authorize_();

        $matchmakers = User::role(['admin', 'matchmaker'])->orderBy('name')->get();

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
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = $validated['assigned_to'] ?? auth()->id();

        $lead = Lead::create($validated);

        MatchmakingTimelineEvent::log($lead, null, 'lead_received', "Lead received from {$lead->source}.");

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
        $matchmakers = User::role(['admin', 'matchmaker'])->orderBy('name')->get();
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

        return view('admin.leads.show', compact('lead', 'matchmakers', 'packages', 'searchResults'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize_();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'looking_for' => ['nullable', 'in:self,family_member'],
            'source' => ['required', 'in:facebook,instagram,whatsapp,website,phone,referral,manual,other'],
            'status' => ['required', 'in:new,contacted,interested,registered,not_interested,closed'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'nikah_package_id' => ['nullable', 'exists:nikah_packages,id'],
            'package_price' => ['nullable', 'numeric', 'min:0'],
            'package_started_at' => ['nullable', 'date'],
        ]);

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

        $statusChanged = $lead->status !== $validated['status'];
        $lead->update($validated);

        if ($packageChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'package_changed', 'Package changed to ' . ($lead->fresh()->nikahPackage?->name ?? 'None') . '.');

            if ($lead->nikah_package_id) {
                $this->recordPackageCommission($lead->fresh());
            }
        }

        if ($statusChanged) {
            MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'status_changed', 'Status changed to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
        }

        return back()->with('status', 'Lead updated.');
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
        abort_unless($item->lead_id === $lead->id, 404);

        $item->update(['sent_at' => now()]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'candidate_shared', 'Shortlisted candidate marked as shared with the lead.');

        return back()->with('status', 'Marked as shared.');
    }

    public function removeFromShortlist(Lead $lead, LeadShortlistItem $item)
    {
        $this->authorize_();
        abort_unless($item->lead_id === $lead->id, 404);

        $item->delete();

        return back()->with('status', 'Removed from shortlist.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize_();

        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }

    // Admin's own copy of Matchmaker\ClientController::regenerateProgressLink()
    // — same token, same route name-space kept separate on purpose (see this
    // controller's own docblock on staying self-contained rather than
    // depending on matchmaker-namespaced routes). Exists so admin retains
    // full oversight (raw link + phone, always visible on this page) even
    // though the assigned counselor's own view never shows either.
    public function regenerateProgressLink(Lead $lead)
    {
        $this->authorize_();
        abort_unless($lead->phone, 422, 'Add a phone number for this client first.');

        $hadTokenAlready = (bool) $lead->progress_link_token;
        $lead->update(['progress_link_token' => Str::random(24)]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'progress_link_regenerated', 'Progress page link was ' . ($hadTokenAlready ? 'regenerated by admin — the old copy no longer works' : 'generated by admin') . '.');

        return back()->with('status', 'Progress link ready.');
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

    public function rejectPackagePayment(Request $request, Lead $lead)
    {
        $this->authorize_();
        abort_unless($lead->package_payment_status === 'submitted', 422, 'No package payment awaiting review.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $lead->update([
            'package_payment_status' => 'rejected',
            'package_payment_rejection_reason' => $validated['reason'],
        ]);

        MatchmakingTimelineEvent::log($lead, $lead->nikahProfile, 'package_payment_rejected', 'Package payment rejected: ' . $validated['reason']);

        return back()->with('status', 'Package payment rejected — the client can resubmit from their progress link.');
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
