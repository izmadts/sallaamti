<?php

namespace App\Support;

use App\Models\Lead;
use Illuminate\Support\Collection;

// Surfaces the same 7-step workflow already documented in
// guide/partials/matchmaker.blade.php (Step 1 - Step 7) and already
// enforced by Matchmaker\ClientController's gates (no batch without a
// linked profile + active consent, etc.) as a computed, always-current
// checklist — nothing here is a new business rule, just a visible
// reflection of rules that already exist. No new columns/tables: every
// "done" check reads relations/fields the app already has.
class LeadJourney
{
    public const STAGES = [
        'contacted' => ['label' => 'Contact Made', 'icon' => '📞'],
        'requirements' => ['label' => 'Requirements Captured', 'icon' => '📋'],
        'shortlist' => ['label' => 'Shortlist Built', 'icon' => '⭐'],
        'registered' => ['label' => 'Profile Registered', 'icon' => '📝'],
        'consent' => ['label' => 'Consent Confirmed', 'icon' => '✅'],
        'package' => ['label' => 'Package Active', 'icon' => '💳'],
        'proposals' => ['label' => 'Proposal Sent', 'icon' => '💌'],
    ];

    private const TAB_FOR_STAGE = [
        'contacted' => 'overview',
        'requirements' => 'requirements',
        'shortlist' => 'shortlist',
        'registered' => 'overview',
        'consent' => 'consent',
        'package' => 'batches',
        'proposals' => 'batches',
    ];

    private const NEXT_ACTION_TEXT = [
        'contacted' => 'Update their status once you\'ve made contact.',
        'requirements' => 'Capture what they\'re looking for — guides candidate search.',
        'shortlist' => 'Build a shortlist of candidates before creating a proposal batch.',
        'registered' => 'Link or register their Nikah profile.',
        'consent' => 'Record their Nikah Counseling Participation consent before sending proposals.',
        'package' => 'They need an active package before proposals count against an allowance.',
        'proposals' => 'Send a proposal batch from the shortlist.',
    ];

    // Ordered checklist for one lead: ['key','label','icon','done','tab'].
    public static function forLead(Lead $lead): array
    {
        return collect(self::STAGES)
            ->map(fn ($meta, $key) => [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'done' => self::isDone($lead, $key),
                'tab' => self::TAB_FOR_STAGE[$key],
            ])
            ->values()
            ->all();
    }

    public static function completedCount(Lead $lead): int
    {
        return collect(self::forLead($lead))->where('done', true)->count();
    }

    // First not-done stage key, or null once everything is done.
    public static function currentStage(Lead $lead): ?string
    {
        foreach (self::forLead($lead) as $stage) {
            if (!$stage['done']) {
                return $stage['key'];
            }
        }

        return null;
    }

    public static function nextActionText(Lead $lead): ?string
    {
        $stage = self::currentStage($lead);

        return $stage ? self::NEXT_ACTION_TEXT[$stage] : null;
    }

    private static function isDone(Lead $lead, string $key): bool
    {
        return match ($key) {
            'contacted' => $lead->status !== 'new',
            'requirements' => $lead->requirement !== null && $lead->requirement->items->isNotEmpty(),
            'shortlist' => $lead->shortlistItems->isNotEmpty(),
            'registered' => $lead->isConverted(),
            'consent' => $lead->hasActiveConsent('matchmaking_participation'),
            'package' => $lead->nikah_package_id !== null && !$lead->packageExpired(),
            'proposals' => $lead->proposalBatches->whereIn('status', ['sent', 'partially_responded', 'completed'])->isNotEmpty(),
        };
    }

    // Groups a counselor's active leads by their current blocking stage —
    // the dashboard's "Needs Your Attention" panel. Leads already fully
    // done (currentStage() === null), or closed/not-interested, aren't
    // anyone's open task and are left out.
    public static function needsAttention(Collection $leads): Collection
    {
        return $leads
            ->reject(fn (Lead $lead) => in_array($lead->status, ['not_interested', 'closed'], true))
            ->map(fn (Lead $lead) => ['lead' => $lead, 'stage' => self::currentStage($lead)])
            ->filter(fn ($row) => $row['stage'] !== null)
            ->groupBy('stage')
            ->map(fn ($rows) => $rows->pluck('lead'))
            ->sortBy(fn ($rows, $stage) => array_search($stage, array_keys(self::STAGES)));
    }
}
