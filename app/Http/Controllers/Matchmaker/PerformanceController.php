<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\CommissionLedgerEntry;
use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\MatchmakerReferral;
use App\Models\MatchmakingTimelineEvent;
use App\Models\MatchProposal;
use App\Models\NikahProfile;
use Illuminate\Support\Facades\Auth;

// The hiring document's motivational "My Performance" page (its own
// section 13/14) — deliberately built from ONLY what this platform can
// honestly measure today. The document's full Quality Score formula has
// 7 weighted factors; only 3 of them (Verification Rate, Paid Conversion,
// Compliance) have real data behind them right now — Client Satisfaction,
// Proposal Quality, and Follow-up Response have no tracking system yet.
// Rather than fake the other 4, this shows a clearly-labeled 3-factor
// score and says so, expanding honestly as those systems get built.
class PerformanceController extends Controller
{
    public function index()
    {
        $matchmakerId = Auth::id();

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

        $application = MatchmakerApplication::where('user_id', $matchmakerId)->where('status', 'certified')->first();

        $stats = [
            'introduced' => $introducedCount,
            'verified' => $verifiedCount,
            'paid' => $paidCount,
            'matchmaking_clients' => $clientsWithPackage,
            'proposals' => $proposalsSent,
            'mutual_interests' => $mutualInterests,
            'recognition_bonuses' => $recognitionBonuses,
            'referrals' => $referralCount,
        ];

        $score = [
            'verification_rate' => $verificationRate,
            'paid_conversion_rate' => $paidConversionRate,
            'compliance_rate' => $complianceRate,
            'overall' => $qualityScore,
        ];

        return view('matchmaker.performance.index', compact('stats', 'score', 'commissionEarned', 'application'));
    }
}
