<?php

namespace App\Http\Controllers\Concerns;

use App\Models\CommissionLedgerEntry;
use App\Models\CommissionRule;
use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\MatchmakerReferral;
use App\Models\NikahProfile;
use App\Models\User;
use App\Notifications\MatchmakerCommissionEarned;

// Where a matchmaking commission actually gets recorded — deliberately
// the only place either trigger point (payment confirmation, package
// assignment) touches CommissionLedgerEntry, so the two never drift on
// tier lookup, rule matching, or the 7-day hold. No CommissionRule for a
// given (type, tier) combination just means no entry gets created —
// admin hasn't configured a rate yet, never a hard failure.
trait RecordsCommission
{
    private function tierForMatchmaker(User $matchmaker): string
    {
        return MatchmakerApplication::where('user_id', $matchmaker->id)
            ->where('status', 'certified')
            ->value('level') ?? 'nikah_counselor';
    }

    // Fired once, right after NikahProfileWizardController/NikahPaymentAdminController
    // flips payment_status to 'confirmed' — never before, since an
    // unconfirmed payment isn't real revenue yet. A walk-in profile's
    // created_by takes priority; a self-service profile falls back to
    // whichever counselor's referral link the member originally
    // registered through (Concerns\TracksReferrals) — a counselor's
    // marketing bringing in someone who registers AND verifies themselves
    // is the same kind of real acquisition work a walk-in represents.
    private function recordVerifiedProfileCommission(NikahProfile $profile): void
    {
        $matchmaker = $profile->createdBy;

        if (!$matchmaker) {
            $referral = MatchmakerReferral::where('referred_user_id', $profile->user_id)->first();
            $matchmaker = $referral?->counselor;
        }

        if (!$matchmaker || !$matchmaker->hasRole('matchmaker')) {
            return;
        }

        // A profile can cycle payment_status through submitted -> rejected
        // -> submitted -> confirmed (reject() explicitly invites the member
        // to resubmit) without ever losing the ledger entry from its first
        // confirmation, since ledger entries have no "reversed" status.
        // Without this guard, that cycle pays the matchmaker twice for the
        // same profile.
        $alreadyRecorded = CommissionLedgerEntry::where('source_type', NikahProfile::class)
            ->where('source_id', $profile->id)
            ->where('rule_type', 'verified_profile')
            ->exists();

        if ($alreadyRecorded) {
            return;
        }

        $tier = $this->tierForMatchmaker($matchmaker);
        $rule = CommissionRule::findFor('verified_profile', null, $tier);

        if (!$rule) {
            return;
        }

        $baseAmount = (float) $profile->payment_amount;

        $entry = CommissionLedgerEntry::create([
            'matchmaker_id' => $matchmaker->id,
            'source_type' => NikahProfile::class,
            'source_id' => $profile->id,
            'rule_type' => 'verified_profile',
            'nikah_package_id' => null,
            'is_renewal' => false,
            'tier_at_time' => $tier,
            'rate_type' => $rule->rate_type,
            'rate_value' => $rule->rate_value,
            'base_amount' => $baseAmount,
            'commission_amount' => $rule->applyTo($baseAmount),
            'status' => 'pending',
            'eligible_at' => now()->addDays(7),
        ]);

        $this->notifyCommissionEarned($matchmaker, $entry);
    }

    // Fired when admin assigns/changes a Lead's nikah_package_id — the
    // only real "package purchased" signal that exists today (see
    // project_matchmaker_hiring_status). Renewal is auto-detected from
    // whether this Lead already has a prior package-type ledger entry.
    private function recordPackageCommission(Lead $lead): void
    {
        $matchmaker = $lead->assignedTo;

        if (!$matchmaker || !$matchmaker->hasRole('matchmaker') || !$lead->nikah_package_id) {
            return;
        }

        $isRenewal = CommissionLedgerEntry::where('source_type', Lead::class)
            ->where('source_id', $lead->id)
            ->where('rule_type', 'package')
            ->exists();

        $tier = $this->tierForMatchmaker($matchmaker);
        $rule = CommissionRule::findFor('package', $lead->nikah_package_id, $tier, $isRenewal);

        if (!$rule) {
            return;
        }

        $baseAmount = (float) $lead->package_price;

        $entry = CommissionLedgerEntry::create([
            'matchmaker_id' => $matchmaker->id,
            'source_type' => Lead::class,
            'source_id' => $lead->id,
            'rule_type' => 'package',
            'nikah_package_id' => $lead->nikah_package_id,
            'is_renewal' => $isRenewal,
            'tier_at_time' => $tier,
            'rate_type' => $rule->rate_type,
            'rate_value' => $rule->rate_value,
            'base_amount' => $baseAmount,
            'commission_amount' => $rule->applyTo($baseAmount),
            'status' => 'pending',
            'eligible_at' => now()->addDays(7),
        ]);

        $this->notifyCommissionEarned($matchmaker, $entry);
    }

    private function notifyCommissionEarned(User $matchmaker, CommissionLedgerEntry $entry): void
    {
        try {
            $matchmaker->notify(new MatchmakerCommissionEarned($entry));
        } catch (\Throwable $e) {
            \Log::error('MatchmakerCommissionEarned notification failed: ' . $e->getMessage());
        }
    }
}
