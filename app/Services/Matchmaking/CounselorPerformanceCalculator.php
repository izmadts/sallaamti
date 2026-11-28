<?php

namespace App\Services\Matchmaking;

use App\Models\CommissionLedgerEntry;
use App\Models\Lead;
use App\Models\MatchmakerReferral;
use App\Models\MatchmakingTimelineEvent;
use App\Models\MatchProposal;
use App\Models\NikahProfile;

// Extracted from Matchmaker\PerformanceController so the exact same
// numbers back both that page's display and the level auto-promotion
// check (Console\Commands\PromoteEligibleCounselors) — two places computing
// "how well is this counselor doing" from slightly different logic would
// drift and eventually disagree with each other. Since 28 Nov 2026 the score
// is package conversion only (owner decision) — see calculate().
class CounselorPerformanceCalculator
{
    public function calculate(int $matchmakerId): array
    {
        $walkInProfileIds = NikahProfile::where('created_by', $matchmakerId)->pluck('id');
        $referredUserIds = MatchmakerReferral::where('counselor_user_id', $matchmakerId)->pluck('referred_user_id');
        $referredProfileIds = NikahProfile::whereIn('user_id', $referredUserIds)->pluck('id');
        $allProfileIds = $walkInProfileIds->merge($referredProfileIds)->unique();

        $introducedCount = $allProfileIds->count();
        $verifiedCount = NikahProfile::whereIn('id', $allProfileIds)->where('verification_status', 'verified')->count();
        $paidCount = NikahProfile::whereIn('id', $allProfileIds)->where('payment_status', 'confirmed')->count();

        $clientsWithPackage = Lead::where('assigned_to', $matchmakerId)->whereNotNull('nikah_package_id')->count();

        // Owner (28 Nov 2026): a counselor's score and level run on packages
        // only — verification moved to customer service, so it's no longer
        // the counselor's own result. Eligible = assigned clients that are
        // verified and paid (the only ones a package can be sold to).
        $eligibleClients = Lead::where('assigned_to', $matchmakerId)
            ->whereHas('nikahProfile', fn ($q) => $q->where('verification_status', 'verified')->where('payment_status', 'confirmed'))
            ->count();

        $proposalsSent = MatchProposal::whereHas('batch', fn ($q) => $q->where('matchmaker_id', $matchmakerId))->count();

        $mutualInterests = MatchmakingTimelineEvent::where('event_type', 'mutual_interest')
            ->whereHas('lead', fn ($q) => $q->where('assigned_to', $matchmakerId))
            ->count();

        $recognitionBonuses = CommissionLedgerEntry::where('matchmaker_id', $matchmakerId)->where('rule_type', 'recognition_bonus')->count();

        $flaggedCount = CommissionLedgerEntry::where('matchmaker_id', $matchmakerId)->whereNotNull('flagged_at')->count();
        $totalEntries = CommissionLedgerEntry::where('matchmaker_id', $matchmakerId)->count();

        $verificationRate = $introducedCount > 0 ? round(($verifiedCount / $introducedCount) * 100) : null;
        $paidConversionRate = $introducedCount > 0 ? round(($paidCount / $introducedCount) * 100) : null;
        $complianceRate = $totalEntries > 0 ? max(0, round(100 - ($flaggedCount / $totalEntries) * 100)) : 100;

        $packageConversionRate = $eligibleClients > 0 ? min(100, round(($clientsWithPackage / $eligibleClients) * 100)) : null;

        // Packages only. Verification/paid rates and compliance are still
        // reported (and shown) but no longer weigh into the score.
        $qualityScore = $packageConversionRate;

        $commissionEarned = CommissionLedgerEntry::where('matchmaker_id', $matchmakerId)->where('status', 'paid')->sum('commission_amount');
        $referralCount = MatchmakerReferral::where('counselor_user_id', $matchmakerId)->count();

        return [
            'stats' => [
                'introduced' => $introducedCount,
                'verified' => $verifiedCount,
                'paid' => $paidCount,
                'matchmaking_clients' => $clientsWithPackage,
                'packages_sold' => $clientsWithPackage,
                'eligible_clients' => $eligibleClients,
                'proposals' => $proposalsSent,
                'mutual_interests' => $mutualInterests,
                'recognition_bonuses' => $recognitionBonuses,
                'referrals' => $referralCount,
            ],
            'score' => [
                'verification_rate' => $verificationRate,
                'paid_conversion_rate' => $paidConversionRate,
                'compliance_rate' => $complianceRate,
                'package_conversion_rate' => $packageConversionRate,
                'overall' => $qualityScore,
            ],
            'commission_earned' => $commissionEarned,
        ];
    }
}
