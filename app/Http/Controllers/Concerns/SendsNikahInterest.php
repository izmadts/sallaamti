<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Lead;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahBlock;
use App\Models\NikahInterest;
use App\Models\NikahProfile;
use App\Models\User;
use App\Notifications\MatchmakerMutualInterestFormed;
use App\Notifications\NikahInterestAccepted;
use App\Notifications\NikahInterestDeclined;
use App\Notifications\NikahInterestReceived;

// The platform already has a full self-service mutual-interest mechanism
// (NikahInterest: send/notify/accept/decline, with contact reveal and
// guardian messaging unlocking automatically on acceptance) — this trait is
// the single place that logic lives so both the ordinary member-to-member
// flow (NikahInterestController) and the matchmaker CRM's proposal-response
// bridge (Public\MatchmakingActionController) and assisted-response action
// (Matchmaker\NikahBrowseController) share identical guards instead of
// three copies of the same trust rules quietly drifting apart.
trait SendsNikahInterest
{
    private function sendInterestBetweenProfiles(NikahProfile $sender, NikahProfile $receiver): array
    {
        if (!$sender->canInteract()) {
            return ['interest' => null, 'error' => 'not_eligible'];
        }

        if (NikahBlock::existsBetween($sender->id, $receiver->id)) {
            return ['interest' => null, 'error' => 'blocked'];
        }

        $wasDeclined = NikahInterest::where('sender_profile_id', $sender->id)
            ->where('receiver_profile_id', $receiver->id)
            ->where('status', 'declined')
            ->exists();

        if ($wasDeclined) {
            return ['interest' => null, 'error' => 'previously_declined'];
        }

        $interest = NikahInterest::firstOrCreate([
            'sender_profile_id' => $sender->id,
            'receiver_profile_id' => $receiver->id,
        ]);

        if ($interest->wasRecentlyCreated) {
            try {
                $receiver->user->notify(new NikahInterestReceived($interest));
            } catch (\Throwable $e) {
                \Log::error('NikahInterestReceived notification failed: ' . $e->getMessage());
            }
        }

        return ['interest' => $interest, 'error' => null];
    }

    private function acceptNikahInterest(NikahInterest $interest): void
    {
        $interest->update(['status' => 'accepted', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new NikahInterestAccepted($interest));
        } catch (\Throwable $e) {
            \Log::error('NikahInterestAccepted notification failed: ' . $e->getMessage());
        }

        $this->notifyMatchmakersOfMutualInterest($interest);
    }

    private function declineNikahInterest(NikahInterest $interest): void
    {
        $interest->update(['status' => 'declined', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new NikahInterestDeclined($interest));
        } catch (\Throwable $e) {
            \Log::error('NikahInterestDeclined notification failed: ' . $e->getMessage());
        }
    }

    // Mutual interest matters to a matchmaker regardless of whether the
    // interest itself originated from a proposal they sent or the member
    // acting entirely on their own — if either side has a Lead, that
    // matchmaker should see it in their timeline and get notified.
    private function notifyMatchmakersOfMutualInterest(NikahInterest $interest): void
    {
        foreach ([$interest->sender_profile_id, $interest->receiver_profile_id] as $profileId) {
            $lead = Lead::where('nikah_profile_id', $profileId)->first();

            if (!$lead) {
                continue;
            }

            MatchmakingTimelineEvent::log($lead, NikahProfile::find($profileId), 'mutual_interest', 'Mutual interest confirmed with another candidate.');

            if ($lead->assigned_to) {
                try {
                    User::find($lead->assigned_to)?->notify(new MatchmakerMutualInterestFormed($interest, $lead));
                } catch (\Throwable $e) {
                    \Log::error('MatchmakerMutualInterestFormed notification failed: ' . $e->getMessage());
                }
            }
        }
    }
}
