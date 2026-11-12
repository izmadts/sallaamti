<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\NikahInterest;
use App\Models\User;
use Illuminate\Console\Command;

// One-off (and safe to re-run) status refresh for existing leads (owner,
// 12 Nov 2026): verified profile -> Verified, profile made but fee unpaid ->
// Pending Payment, profile under review -> Profile Pending, account but no
// profile -> Account Registered, accepted interest -> Matched. A lead is
// matched to its account by the linked profile, else by phone (last 10
// digits, only when exactly one account has that number). Leads marked Not
// Interested / Number Not Valid / Closed are never touched.
class SyncLeadStatuses extends Command
{
    protected $signature = 'leads:sync-statuses {--dry-run : Only show what would change}';

    protected $description = 'Set lead statuses from the linked account/Nikah profile (Verified, Pending Payment, Profile Pending, Account Registered, Matched).';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        // phone key -> user id, only for numbers that belong to one account.
        $byKey = [];
        User::whereNotNull('phone')->select('id', 'phone')->orderBy('id')->chunk(1000, function ($users) use (&$byKey) {
            foreach ($users as $u) {
                $k = Lead::phoneKey($u->phone);
                if ($k) {
                    $byKey[$k] = array_key_exists($k, $byKey) ? false : $u->id;
                }
            }
        });

        $matchedProfileIds = NikahInterest::where('status', 'accepted')->get(['sender_profile_id', 'receiver_profile_id'])
            ->flatMap(fn ($i) => [$i->sender_profile_id, $i->receiver_profile_id])->unique()->flip();

        $changes = [];
        Lead::with('nikahProfile')->whereIn('status', Lead::AUTO_FROM_STATUSES)->orderBy('id')->chunk(500, function ($leads) use ($byKey, $matchedProfileIds, $dry, &$changes) {
            foreach ($leads as $lead) {
                $profile = $lead->nikahProfile;
                $hasAccount = (bool) $profile;
                if (!$profile) {
                    $uid = $byKey[Lead::phoneKey($lead->phone)] ?? null;
                    if ($uid) {
                        $hasAccount = true;
                        $profile = User::find($uid)?->nikahProfile;
                    }
                }
                $status = $profile && isset($matchedProfileIds[$profile->id])
                    ? 'matched'
                    : Lead::statusFromProfile($profile, $hasAccount);
                if (!$status || $status === $lead->status) {
                    continue;
                }
                $changes[$lead->status . ' -> ' . $status] = ($changes[$lead->status . ' -> ' . $status] ?? 0) + 1;
                if (!$dry) {
                    $lead->forceFill(['status' => $status])->saveQuietly();
                    \App\Models\MatchmakingTimelineEvent::log($lead, $profile, 'status_changed', 'Status changed automatically to ' . Lead::statusLabel($status) . ' (status refresh).');
                }
            }
        });

        ksort($changes);
        foreach ($changes as $k => $n) {
            $this->line(str_pad($k, 40) . $n);
        }
        $this->info(($dry ? 'Would change ' : 'Changed ') . array_sum($changes) . ' lead(s).');

        return self::SUCCESS;
    }
}
