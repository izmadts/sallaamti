<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Console\Command;

// One-off (and safe to re-run) status refresh for existing leads (owner,
// 12 Nov 2026): verified profile -> Verified, profile made but fee unpaid ->
// Pending Payment, profile under review -> Profile Pending, account but no
// profile -> Account Registered. (Matched is staff-only since 13 Nov 2026: an
// accepted interest only shares contact details, it is not a rishta.) A lead is
// matched to its account by the linked profile, else by phone (last 10
// digits, only when exactly one account has that number). Leads marked Not
// Interested / Number Not Valid / Closed are never touched.
class SyncLeadStatuses extends Command
{
    protected $signature = 'leads:sync-statuses {--dry-run : Only show what would change} {--fix-matched : Undo the automatic Matched status set from accepted interests (13 Nov 2026)}';

    protected $description = 'Set lead statuses from the linked account/Nikah profile (Verified, Pending Payment, Profile Pending, Account Registered). Matched is set by staff only.';

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

        // --fix-matched: leads the first refresh moved to Matched automatically
        // (and nobody changed by hand since) go back into the normal refresh.
        $statuses = Lead::AUTO_FROM_STATUSES;
        $autoMatchedIds = [];
        if ($this->option('fix-matched')) {
            foreach (Lead::where('status', 'matched')->get(['id']) as $l) {
                $last = \App\Models\MatchmakingTimelineEvent::where('lead_id', $l->id)->where('event_type', 'status_changed')->latest('id')->first();
                if ($last && str_starts_with($last->description, 'Status changed automatically to Matched')) {
                    $autoMatchedIds[] = $l->id;
                }
            }
            $statuses[] = 'matched';
        }

        $changes = [];
        Lead::with('nikahProfile')->whereIn('status', $statuses)->orderBy('id')->chunk(500, function ($leads) use ($byKey, $autoMatchedIds, $dry, &$changes) {
            foreach ($leads as $lead) {
                if ($lead->status === 'matched' && !in_array($lead->id, $autoMatchedIds, true)) {
                    continue; // set by staff: keep
                }
                $profile = $lead->nikahProfile;
                $hasAccount = (bool) $profile;
                if (!$profile) {
                    $uid = $byKey[Lead::phoneKey($lead->phone)] ?? null;
                    if ($uid) {
                        $hasAccount = true;
                        $profile = User::find($uid)?->nikahProfile;
                    }
                }
                $status = Lead::statusFromProfile($profile, $hasAccount) ?? ($lead->status === 'matched' ? 'new' : null);
                if ($lead->status === 'matched' ? (!$status || $status === $lead->status) : !Lead::canAutoMove($lead->status, $status)) {
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
