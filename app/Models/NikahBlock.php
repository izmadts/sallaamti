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
}
