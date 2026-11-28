<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\CommissionRule;
use App\Models\MatchmakerApplication;
use App\Services\Matchmaking\CounselorPerformanceCalculator;
use Illuminate\Http\JsonResponse;

// JSON mirror of Matchmaker\PerformanceController (web). Since 28 Nov 2026
// the Quality Score is package conversion only (owner decision — see
// CounselorPerformanceCalculator). Uses that same calculator so this, the web
// page and the daily auto-promotion check can never disagree (this endpoint
// used to carry its own copy of the formula).
class PerformanceController extends Controller
{
    public function index(): JsonResponse
    {
        $matchmakerId = auth()->id();

        $performance = app(CounselorPerformanceCalculator::class)->calculate($matchmakerId);
        $stats = $performance['stats'];
        $score = $performance['score'];

        $application = MatchmakerApplication::where('user_id', $matchmakerId)->where('status', 'certified')->first();
        $tier = $application?->level ?? 'nikah_counselor';

        return response()->json([
            'stats' => $stats,
            'score' => $score,
            'commission_earned' => $performance['commission_earned'],
            'tier' => $tier,
            'counselor_code' => $application?->counselor_code,
            // Distinguishes "no certified application on file" from "at the
            // top level already" — both leave level_progress null below, and
            // the app needs to tell those two apart (very different UI).
            'is_certified' => $application !== null,
            // "X to go" toward the next level — same shape as the web
            // panel's progress bar (MatchmakerApplication::nextLevelProgress()).
            'level_progress' => $application?->nextLevelProgress($score, $stats),
            'commission_rates' => $this->commissionRatesByLevel(),
        ]);
    }

    // The first-purchase package commission rate for every level, so the app
    // can show a counselor concretely what leveling up is worth. Packages are
    // now the counselor's whole job (verification moved to customer service),
    // so this reads the 'package' rule instead of 'verified_profile'. Reads
    // whatever admin has configured in CommissionRule rather than hardcoding.
    private function commissionRatesByLevel(): array
    {
        $rates = [];

        foreach (MatchmakerApplication::LEVELS as $level => $label) {
            $rule = CommissionRule::findFor('package', null, $level);
            $rates[$level] = [
                'label' => $label,
                'rate' => $rule ? $this->formatRate($rule) : null,
            ];
        }

        return $rates;
    }

    private function formatRate(CommissionRule $rule): string
    {
        return $rule->rate_type === 'percentage'
            ? rtrim(rtrim((string) $rule->rate_value, '0'), '.') . '%'
            : 'Rs. ' . number_format((float) $rule->rate_value);
    }
}
