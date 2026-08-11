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
}
