<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reactedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->reactions->contains('user_id', $user->id);
    }

    public function reactionTypeBy(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        return $this->reactions->firstWhere('user_id', $user->id)?->type;
    }

    public function savedPosts(): MorphMany
    {
        return $this->morphMany(SavedPost::class, 'saveable');
    }

    public function savedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->savedPosts->contains('user_id', $user->id);
    }
}
