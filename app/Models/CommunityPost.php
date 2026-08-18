<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommunityPost extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'body',
        'photo',
        'event_at',
        'tags',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'event_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeWithTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reactionTypeBy(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        return $this->reactions->firstWhere('user_id', $user->id)?->type;
    }
}
