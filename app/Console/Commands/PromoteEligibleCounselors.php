<?php

namespace App\Console\Commands;

use App\Models\MatchmakerApplication;
use App\Models\User;
use App\Notifications\CounselorLevelPromoted;
use App\Services\Matchmaking\CounselorPerformanceCalculator;
use Illuminate\Console\Command;

// Never demotes — see MatchmakerApplication::PROMOTION_THRESHOLDS's own
// comment on why this is intentionally one-way. Notifies both the
// counselor (celebratory) and admins (this changes commission tier —
// RecordsCommission::tierForMatchmaker() reads 'level' directly — so it's
// never a silent change).
class PromoteEligibleCounselors extends Command
{
    protected $signature = 'matchmaker:promote-eligible-counselors';

    protected $description = 'Auto-promote certified Nikah Counselors who\'ve met the volume, quality, and tenure bar for their next level';

    public function handle(CounselorPerformanceCalculator $calculator): void
    {
        $applications = MatchmakerApplication::where('status', 'certified')
            ->whereNotNull('certified_at')
            ->get();

        $promoted = 0;

        foreach ($applications as $application) {
            $nextLevel = $application->nextLevelKey();
            if (!$nextLevel) {
                continue;
            }

            $result = $calculator->calculate($application->user_id);

            if (!$application->isEligibleForPromotion($result['score'], $result['stats'])) {
                continue;
            }

            $application->update(['level' => $nextLevel]);
            $promoted++;

            $application->user?->notify(new CounselorLevelPromoted($application->fresh(), forAdmin: false));

            User::role('admin')->each(function ($admin) use ($application) {
                try {
                    $admin->notify(new CounselorLevelPromoted($application->fresh(), forAdmin: true));
                } catch (\Throwable $e) {
                    \Log::error('CounselorLevelPromoted admin notification failed: ' . $e->getMessage());
                }
            });

            $this->info("Promoted {$application->full_name} to {$nextLevel}.");
        }

        $this->info("Checked {$applications->count()} certified counselor(s), promoted {$promoted}.");
    }
}
