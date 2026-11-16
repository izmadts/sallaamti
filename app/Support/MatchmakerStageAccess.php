<?php

namespace App\Support;

use App\Models\MatchmakerApplication;
use App\Models\User;

// Single source of truth for the Training/Assessed/Probation/Certified
// feature matrix and lead caps from the 15 Nov 2026 trainee-access
// addendum. Used both server-side (EnsureFeatureUnlocked, lead-cap checks)
// and to tell the Flutter app what to show (UserResource's nc_* fields),
// so the two can never drift on what a given stage unlocks.
class MatchmakerStageAccess
{
    public const LEAD_CAPS = [
        'training' => 10,
        'assessed' => 25,
        'probation' => 50,
        'certified' => null,
    ];

    private const TRAINING_FEATURES = ['add_client', 'lead_actions', 'walk_in_wizard'];
    private const ASSESSED_FEATURES = ['requirements', 'consent', 'browse'];
    private const PROBATION_FEATURES = ['shortlist', 'proposal_batch', 'packages'];
    private const CERTIFIED_FEATURES = ['referral', 'certificate'];

    public const FEATURES = [
        'training' => self::TRAINING_FEATURES,
        'assessed' => [...self::TRAINING_FEATURES, ...self::ASSESSED_FEATURES],
        'probation' => [...self::TRAINING_FEATURES, ...self::ASSESSED_FEATURES, ...self::PROBATION_FEATURES],
        'certified' => [...self::TRAINING_FEATURES, ...self::ASSESSED_FEATURES, ...self::PROBATION_FEATURES, ...self::CERTIFIED_FEATURES],
    ];

    // Dashboard/notifications/login/Interests aren't in FEATURES at all —
    // they're available at every stage including Training, same as before
    // this addendum.
    public static function stageFor(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        if ($user->hasRole('matchmaker')) {
            return 'certified';
        }

        if ($user->hasRole('matchmaker_trainee')) {
            return MatchmakerApplication::where('user_id', $user->id)
                ->whereIn('status', ['training', 'assessed', 'probation'])
                ->latest()
                ->value('status');
        }

        return null;
    }

    public static function featuresFor(?string $stage): array
    {
        return self::FEATURES[$stage] ?? [];
    }

    public static function leadCapFor(?string $stage): ?int
    {
        if ($stage === null) {
            return 0;
        }

        return array_key_exists($stage, self::LEAD_CAPS) ? self::LEAD_CAPS[$stage] : 0;
    }
}
