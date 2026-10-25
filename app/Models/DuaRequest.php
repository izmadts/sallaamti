<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DuaRequest extends Model
{
    protected $fillable = ['user_id', 'body', 'is_anonymous', 'image', 'media', 'status', 'rejection_reason'];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'media' => 'array',
    ];

    // Images only — a dua request is a short prayer text, not a video post.
    // Mirrors CommunityPost::mediaItems()/setMediaAttribute() exactly; see
    // its comments for why photo/video (here: image) stay in sync with the
    // carousel's first item.
    public function mediaItems(): array
    {
        if (!empty($this->media)) {
            return $this->media;
        }

        if ($this->image) {
            return [['type' => 'image', 'path' => $this->image]];
        }

        return [];
    }

    public function setMediaAttribute(?array $items): void
    {
        $items = $items ?? [];
        $this->attributes['media'] = $items === [] ? null : json_encode($items);
        $this->attributes['image'] = $items[0]['path'] ?? null;
    }

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

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->visible()->topLevel()->with('replies.user')->latest();
    }

    // Unfiltered by top-level/reply — used only for a count() including
    // replies (see WallController's withCount('allComments as comments_count')).
    public function allComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->visible();
    }

    public function savedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->savedPosts->contains('user_id', $user->id);
    }
}
