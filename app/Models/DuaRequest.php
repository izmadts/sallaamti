<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuaRequest extends Model
{
    protected $fillable = ['user_id', 'body', 'is_anonymous', 'status', 'rejection_reason'];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(DuaReaction::class);
    }

    public function reactedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->reactions->contains('user_id', $user->id);
    }
}
