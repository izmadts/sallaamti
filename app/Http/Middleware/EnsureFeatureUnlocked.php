<?php

namespace App\Http\Middleware;

use App\Support\MatchmakerStageAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Runs after EnsureUserIsMatchmakerApi, which only checks "does this account
// have NC-app access at all." This checks the finer-grained question: does
// this specific stage unlock this specific feature (see
// MatchmakerStageAccess::FEATURES)? A full 'matchmaker' (certified, or any
// admin/staff account with the role) is never gated here — only
// 'matchmaker_trainee' has a stage-limited feature set.
class EnsureFeatureUnlocked
{
    // Found live (16 Nov 2026): Laravel always passes $next as the second
    // argument to a parameterized middleware's handle() — any ->middleware
    // ('feature:x')-bound parameters come AFTER it, not before. Had this
    // backwards, which 500'd every single feature-gated route (Browse,
    // Shortlist, Requirements, Consent, Proposal Batch, Packages, Referral,
    // Certificate) for every account, trainee and certified alike, since
    // the TypeError happens before the role check ever runs.
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user?->hasRole('matchmaker_trainee') || $user->hasRole('matchmaker')) {
            return $next($request);
        }

        $stage = MatchmakerStageAccess::stageFor($user);

        abort_unless(
            in_array($feature, MatchmakerStageAccess::featuresFor($stage), true),
            403,
            __('db.This feature unlocks at your next stage.')
        );

        return $next($request);
    }
}
