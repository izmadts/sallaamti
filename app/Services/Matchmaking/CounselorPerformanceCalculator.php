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
// drift and eventually disagree with each other. See PerformanceController's
// own docblock for why this is deliberately a 3-factor score, not the
// hiring document's full 7-factor one — only 3 have real data behind them.
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

        $qualityScore = null;
        if ($verificationRate !== null && $paidConversionRate !== null) {
            $qualityScore = round(($verificationRate * 0.45) + ($paidConversionRate * 0.45) + ($complianceRate * 0.10));
        }

        $commissionEarned = CommissionLedgerEntry::where('matchmaker_id', $matchmakerId)->where('status', 'paid')->sum('commission_amount');
        $referralCount = MatchmakerReferral::where('counselor_user_id', $matchmakerId)->count();

        return [
            'stats' => [
                'introduced' => $introducedCount,
                'verified' => $verifiedCount,
                'paid' => $paidCount,
                'matchmaking_clients' => $clientsWithPackage,
                'proposals' => $proposalsSent,
                'mutual_interests' => $mutualInterests,
                'recognition_bonuses' => $recognitionBonuses,
                'referrals' => $referralCount,
            ],
            'score' => [
                'verification_rate' => $verificationRate,
                'paid_conversion_rate' => $paidConversionRate,
                'compliance_rate' => $complianceRate,
                'overall' => $qualityScore,
            ],
            'commission_earned' => $commissionEarned,
        ];
    }
}
