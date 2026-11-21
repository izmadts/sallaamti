<?php

namespace App\Support;

use App\Models\Lead;
use App\Models\MatchmakingTimelineEvent;
use App\Models\User;

// Owner (21 Nov 2026): "NC ki profiles per unki complete related status aur
// details show hona chahiye — kitni leads hain, kitni lead ko touch keya
// hy, kitni leads k status change keye" — the Nikah Counselor Applications
// list and each counselor's own profile page both needed a quick-glance (and
// a fuller) read of what a counselor has actually been doing, not just
// their pipeline stage. "Touched" and "status changes" come from
// MatchmakingTimelineEvent.matchmaker_id — whoever was logged in when the
// event happened — not from the lead's current assignee, so this reflects
// real activity even after a lead has since been reassigned elsewhere.
class CounselorActivity
{
    // Batched across every counselor on the current page (index()) — two
    // grouped queries total, not one pair per row.
    public static function bulkFor(array $userIds): array
    {
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (empty($userIds)) {
            return [];
        }

        $leadCounts = Lead::whereIn('assigned_to', $userIds)
            ->selectRaw('assigned_to, COUNT(*) as n')
            ->groupBy('assigned_to')
            ->pluck('n', 'assigned_to');

        $touchedCounts = MatchmakingTimelineEvent::whereIn('matchmaker_id', $userIds)
            ->whereNotNull('lead_id')
            ->selectRaw('matchmaker_id, COUNT(DISTINCT lead_id) as n')
            ->groupBy('matchmaker_id')
            ->pluck('n', 'matchmaker_id');

        $statusChangeCounts = MatchmakingTimelineEvent::whereIn('matchmaker_id', $userIds)
            ->where('event_type', 'status_changed')
            ->selectRaw('matchmaker_id, COUNT(*) as n')
            ->groupBy('matchmaker_id')
            ->pluck('n', 'matchmaker_id');

        $out = [];
        foreach ($userIds as $id) {
            $out[$id] = [
                'leads' => (int) ($leadCounts[$id] ?? 0),
                'touched' => (int) ($touchedCounts[$id] ?? 0),
                'status_changes' => (int) ($statusChangeCounts[$id] ?? 0),
            ];
        }

        return $out;
    }

    // Fuller breakdown for a single counselor's own profile page — same
    // per-status breakdown Admin\LeadController::byCounselorReport() already
    // shows on the cross-counselor report, plus the touched/status-change
    // figures above.
    public static function detailFor(User $user): array
    {
        $base = fn () => Lead::where('assigned_to', $user->id);

        return self::bulkFor([$user->id])[$user->id] + [
            'contacted' => $base()->where('status', 'contacted')->count(),
            'registered' => $base()->whereIn('status', ['registered', 'account_registered'])->count(),
            'paid' => $base()->whereIn('status', ['paid', 'verified'])->count(),
            'last_activity' => $base()->max('updated_at'),
        ];
    }
}
