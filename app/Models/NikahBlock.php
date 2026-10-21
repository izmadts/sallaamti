<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NikahBlock extends Model
{
    protected $fillable = ['blocker_profile_id', 'blocked_profile_id'];

    public function blocker()
    {
        return $this->belongsTo(NikahProfile::class, 'blocker_profile_id');
    }

    public function blocked()
    {
        return $this->belongsTo(NikahProfile::class, 'blocked_profile_id');
    }

    /**
     * Whether a block exists between two profiles, in either direction.
     */
    public static function existsBetween(int $profileIdA, int $profileIdB): bool
    {
        return static::where(function ($q) use ($profileIdA, $profileIdB) {
            $q->where('blocker_profile_id', $profileIdA)
                ->where('blocked_profile_id', $profileIdB);
        })->orWhere(function ($q) use ($profileIdA, $profileIdB) {
            $q->where('blocker_profile_id', $profileIdB)
                ->where('blocked_profile_id', $profileIdA);
        })->exists();
    }

    // Every profile ID to hide from this one's Browse grid — both sides of
    // a block. A browse list filtered only on `blockedProfiles()` (profiles
    // THIS profile blocked) still shows the card of someone who blocked
    // THIS profile: the viewer could tap it and hit show()'s
    // existsBetween() 404, a dead-end that looks like a broken/blank
    // screen rather than the card simply not being there.
    public static function excludedProfileIds(int $profileId): array
    {
        return static::where('blocker_profile_id', $profileId)
            ->orWhere('blocked_profile_id', $profileId)
            ->get(['blocker_profile_id', 'blocked_profile_id'])
            ->flatMap(fn ($block) => [$block->blocker_profile_id, $block->blocked_profile_id])
            ->unique()
            ->reject(fn ($id) => $id === $profileId)
            ->values()
            ->all();
    }
}
