<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionLedgerEntry;
use App\Models\CommissionRule;
use App\Models\MatchmakerApplication;
use App\Models\NikahPackage;
use App\Models\User;
use App\Notifications\MatchmakerCommissionEarned;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function rules()
    {
        CommissionRule::ensureSeeded();

        // One universal slab per tier for ALL matchmaking packages (not
        // one rate table per package) — confirmed explicitly by the user,
        // since every package had ended up with identical rates anyway.
        // 'package'-type rules are package-agnostic (nikah_package_id is
        // always null, see CommissionRule::ensureSeeded()), so grouping
        // is just these two buckets, not one per package.
        $rules = CommissionRule::orderBy('rule_type')
            ->orderByRaw("FIELD(tier, 'nikah_counselor', 'certified_nikah_counselor', 'senior_nikah_counselor', 'regional_nikah_coordinator')")
            ->orderBy('is_renewal')
            ->get()
            ->groupBy(fn ($rule) => $rule->rule_type === 'verified_profile' ? 'Verified Profile' : 'Matchmaking Packages');

        // Shown in the "Matchmaking Packages" group's description so admin
        // can see exactly which real packages this one slab actually
        // applies to.
        $matchmakingPackageNames = NikahPackage::ordered()->get()->reject->isOneTime()->pluck('name');

        return view('admin.commissions.rules', compact('rules', 'matchmakingPackageNames'));
    }

    public function updateRule(Request $request, CommissionRule $commissionRule)
    {
        $validated = $request->validate([
            'rate_type' => ['required', 'in:percentage,fixed'],
            'rate_value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $commissionRule->update($validated);

        return back()->with('status', 'Commission rule updated.');
    }

    public function ledger(Request $request)
    {
        $query = CommissionLedgerEntry::with('matchmaker', 'nikahPackage', 'source')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('matchmaker_id')) {
            $query->where('matchmaker_id', $request->matchmaker_id);
        }

        $entries = $query->paginate(25)->withQueryString();
        $matchmakers = User::role('matchmaker')->orderBy('name')->get();
        $totals = [
            'pending' => CommissionLedgerEntry::where('status', 'pending')->sum('commission_amount'),
            'approved' => CommissionLedgerEntry::where('status', 'approved')->sum('commission_amount'),
        ];

        return view('admin.commissions.ledger', compact('entries', 'matchmakers', 'totals'));
    }

    // Defense-in-depth: today the 'commissions.manage' permission is
    // admin-role-only, so this can't fire yet — but PermissionCatalog
    // allows delegating it to a matchmaker (e.g. a future "Senior
    // Matchmaker" role), and CommissionLedgerEntry rows are always
    // matchmaker-owned, so approve/pay must never let someone action
    // their own earnings even if that delegation happens later.
    private function abortIfSelfDealing(CommissionLedgerEntry $entry): void
    {
        abort_if((int) $entry->matchmaker_id === (int) auth()->id(), 403, 'You cannot action a commission entry that belongs to you.');
    }

    public function approve(CommissionLedgerEntry $entry)
    {
        $this->abortIfSelfDealing($entry);

        abort_unless($entry->isEligibleForApproval(), 422, $entry->isFlagged()
            ? 'This entry is flagged — clear the flag before approving.'
            : 'Not eligible yet — the 7-day hold hasn\'t passed, or it\'s already been actioned.');

        $entry->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('status', 'Commission approved.');
    }

    public function pay(CommissionLedgerEntry $entry)
    {
        $this->abortIfSelfDealing($entry);

        abort_unless($entry->status === 'approved', 422, 'Only approved commissions can be marked paid.');

        $entry->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => auth()->id(),
        ]);

        return back()->with('status', 'Marked as paid — make sure the actual transfer has already been sent.');
    }

    public function flag(Request $request, CommissionLedgerEntry $entry)
    {
        $this->abortIfSelfDealing($entry);

        abort_if($entry->status !== 'pending', 422, 'Only pending entries can be flagged.');

        $validated = $request->validate(['flag_reason' => ['required', 'string', 'max:255']]);

        $entry->update([
            'flagged_at' => now(),
            'flag_reason' => $validated['flag_reason'],
            'flagged_by' => auth()->id(),
        ]);

        return back()->with('status', 'Entry flagged for review.');
    }

    public function unflag(CommissionLedgerEntry $entry)
    {
        $this->abortIfSelfDealing($entry);

        $entry->update(['flagged_at' => null, 'flag_reason' => null, 'flagged_by' => null]);

        return back()->with('status', 'Flag cleared.');
    }

    // Lets admin correct the auto-detected first-purchase/renewal
    // classification on a still-pending entry and recompute against the
    // matching rule — per the user's explicit "admin can set or increase
    // as per term" requirement.
    public function reclassify(Request $request, CommissionLedgerEntry $entry)
    {
        $this->abortIfSelfDealing($entry);

        abort_if($entry->status !== 'pending', 422, 'Only pending entries can be reclassified.');
        abort_if($entry->rule_type !== 'package', 422, 'Only package-based entries can be reclassified.');

        $validated = $request->validate(['is_renewal' => ['required', 'boolean']]);

        $rule = CommissionRule::findFor('package', null, $entry->tier_at_time, $validated['is_renewal']);
        abort_unless($rule, 422, 'No commission rule configured for that combination yet.');

        $entry->update([
            'is_renewal' => $validated['is_renewal'],
            'rate_type' => $rule->rate_type,
            'rate_value' => $rule->rate_value,
            'commission_amount' => $rule->applyTo((float) $entry->base_amount),
        ]);

        return back()->with('status', 'Entry reclassified and recalculated.');
    }

    // The discretionary "Sallaamti Nikah Success Award" — never
    // automatic, always a deliberate admin decision with a note (hiring
    // document rule 51).
    public function grantBonus(Request $request)
    {
        $validated = $request->validate([
            'matchmaker_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $matchmaker = User::findOrFail($validated['matchmaker_id']);
        $tier = MatchmakerApplication::where('user_id', $matchmaker->id)->where('status', 'certified')->value('level') ?? 'nikah_counselor';

        $entry = CommissionLedgerEntry::create([
            'matchmaker_id' => $matchmaker->id,
            'source_type' => User::class,
            'source_id' => $matchmaker->id,
            'rule_type' => 'recognition_bonus',
            'nikah_package_id' => null,
            'is_renewal' => false,
            'tier_at_time' => $tier,
            'rate_type' => 'fixed',
            'rate_value' => $validated['amount'],
            'base_amount' => 0,
            'commission_amount' => $validated['amount'],
            'status' => 'pending',
            'eligible_at' => now(),
            'notes' => $validated['notes'],
        ]);

        try {
            $matchmaker->notify(new MatchmakerCommissionEarned($entry));
        } catch (\Throwable $e) {
            \Log::error('MatchmakerCommissionEarned (bonus) notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Recognition bonus granted.');
    }
}
