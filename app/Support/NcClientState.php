<?php

namespace App\Support;

use App\Models\Lead;

// The Nikah Counselor app's simplified view of one client (owner decision,
// 28 Nov 2026): counselors no longer handle verification at all — customer
// service (the verification representative, working from the admin panel)
// calls the client, verifies them and takes the verification payment. Until
// that happens the counselor must not work the client, so the app shows a
// strong "pending" marker and locks Shortlist/Proposals.
//
// verificationState():
//   not_registered — no linked Nikah profile yet (counselor should register)
//   pending        — registered, waiting on customer service
//   rejected       — customer service rejected the verification or payment
//   verified       — verified AND verification payment confirmed, i.e. the
//                    same bar NikahProfile::canInteract() uses everywhere else
//
// nextTask() is the one thing the counselor should do next for this client,
// in the order the simplified app walks them through (Today's Tasks list).
class NcClientState
{
    public const TASKS = ['call', 'register', 'verify', 'requirements', 'package', 'shortlist', 'send'];

    public static function verificationState(Lead $lead): string
    {
        $profile = $lead->nikahProfile;

        if (!$lead->nikah_profile_id || !$profile) {
            return 'not_registered';
        }

        if ($profile->canInteract()) {
            return 'verified';
        }

        if ($profile->verification_status === 'rejected' || $profile->payment_status === 'rejected') {
            return 'rejected';
        }

        return 'pending';
    }

    // Expects requirement.items, shortlistItems and proposalBatches loaded
    // (the caller eager-loads them for the whole list at once).
    public static function nextTask(Lead $lead): ?string
    {
        if (in_array($lead->status, Lead::INACTIVE_STATUSES, true)) {
            return null;
        }

        $followUpDue = $lead->next_follow_up_at !== null
            && $lead->next_follow_up_at->lte(now()->endOfDay())
            && !in_array($lead->status, Lead::NO_FOLLOW_UP_STATUSES, true);

        if ($lead->status === 'new' || $followUpDue) {
            return 'call';
        }

        return match (self::verificationState($lead)) {
            'not_registered' => 'register',
            'pending', 'rejected' => 'verify',
            default => self::workTask($lead),
        };
    }

    private static function workTask(Lead $lead): ?string
    {
        if ($lead->requirement === null || $lead->requirement->items->isEmpty()) {
            return 'requirements';
        }

        if ($lead->nikah_package_id === null || $lead->packageExpired()) {
            return 'package';
        }

        if ($lead->shortlistItems->isEmpty()) {
            return 'shortlist';
        }

        $sent = $lead->proposalBatches->whereIn('status', ['sent', 'partially_responded', 'completed'])->isNotEmpty();

        return $sent ? null : 'send';
    }
}
