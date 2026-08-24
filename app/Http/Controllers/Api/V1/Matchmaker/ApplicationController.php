<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use Illuminate\Http\JsonResponse;

// Read-only certification status. No write endpoints here — the
// application itself is submitted via a public web form pre-account
// (NikahCounselorApplicationController), and the Agreement/NDA is accepted
// via a separate signed public link pre-dates-having-login-credentials
// (Public\MatchmakerAgreementController) — both genuinely out of scope for
// an authenticated app screen.
class ApplicationController extends Controller
{
    public function show(): JsonResponse
    {
        $application = MatchmakerApplication::where('user_id', auth()->id())->latest()->first();

        if (!$application) {
            return response()->json(['has_application' => false]);
        }

        $stepKeys = array_keys(MatchmakerApplication::STEPS);

        return response()->json([
            'has_application' => true,
            'status' => $application->status,
            'status_label' => MatchmakerApplication::STEPS[$application->status] ?? ucfirst($application->status),
            'is_terminal' => in_array($application->status, MatchmakerApplication::TERMINAL_EXIT_STATUSES, true),
            'step_index' => array_search($application->status, $stepKeys, true),
            'steps' => MatchmakerApplication::STEPS,
            'level' => $application->level,
            'level_label' => MatchmakerApplication::LEVELS[$application->level] ?? $application->level,
            'counselor_code' => $application->counselor_code,
            'certified_at' => $application->certified_at?->toIso8601String(),
            'agreement_accepted_at' => $application->agreement_accepted_at?->toIso8601String(),
            'nda_accepted_at' => $application->nda_accepted_at?->toIso8601String(),
            'has_accepted_agreement_and_nda' => $application->hasAcceptedAgreementAndNda(),
        ]);
    }
}
