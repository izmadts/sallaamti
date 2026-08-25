<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use App\Services\Matchmaking\CounselorPerformanceCalculator;
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
    public function index(CounselorPerformanceCalculator $calculator)
    {
        $matchmakerId = Auth::id();

        $result = $calculator->calculate($matchmakerId);
        $stats = $result['stats'];
        $score = $result['score'];
        $commissionEarned = $result['commission_earned'];

        $application = MatchmakerApplication::where('user_id', $matchmakerId)->where('status', 'certified')->first();

        // How close to the next level, so a counselor can see exactly what
        // moves the needle instead of levels feeling arbitrary — see
        // MatchmakerApplication::nextLevelProgress().
        $levelProgress = $application ? $application->nextLevelProgress($score, $stats) : null;

        return view('matchmaker.performance.index', compact('stats', 'score', 'commissionEarned', 'application', 'levelProgress'));
    }
}
