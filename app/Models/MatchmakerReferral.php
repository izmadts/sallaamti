<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakerReferral extends Model
{
    protected $fillable = ['counselor_user_id', 'counselor_code', 'referred_user_id'];

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_user_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
