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
        'video',
        'media',
        'event_at',
        'tags',
        'poll_options',
        'is_announcement',
        'hashtags',
        'social_targets',
        'status',
        'published_at',
        'is_pinned',
        'pinned_at',
        'queue_position',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'poll_options' => 'array',
            'social_targets' => 'array',
            'media' => 'array',
            'is_announcement' => 'boolean',
            'event_at' => 'datetime',
            'published_at' => 'datetime',
            'is_pinned' => 'boolean',
            'pinned_at' => 'datetime',
        ];
    }

    // The full ordered carousel — each entry a stored path, same shape the
    // 'media' column holds: ['type' => 'image'|'video', 'path' => '...'].
    // Falls back to the single legacy photo/video for a post saved before
    // this column existed, so every reader can use mediaItems() uniformly
    // regardless of when the post was created.
    public function mediaItems(): array
    {
        if (!empty($this->media)) {
            return $this->media;
        }

        if ($this->photo) {
            return [['type' => 'image', 'path' => $this->photo]];
        }

        if ($this->video) {
            return [['type' => 'video', 'path' => $this->video]];
        }

        return [];
    }

    // Keeps ->photo/->video mirroring the carousel's first item, so every
    // pre-existing reader of those two columns (social publishers, admin
    // list/edit views, the public web pages) keeps showing the cover item
    // without needing to know about the carousel at all.
    public function setMediaAttribute(?array $items): void
    {
        $items = $items ?? [];
        $this->attributes['media'] = $items === [] ? null : json_encode($items);

        $first = $items[0] ?? null;
        $this->attributes['photo'] = $first && $first['type'] === 'image' ? $first['path'] : null;
        $this->attributes['video'] = $first && $first['type'] === 'video' ? $first['path'] : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')->orderBy('queue_position');
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

    public function socialDispatches()
    {
        return $this->hasMany(SocialPostDispatch::class);
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

    public function pollVotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommunityPostPollVote::class);
    }

    public function isPoll(): bool
    {
        return !empty($this->poll_options);
    }

    public function myPollVote(?User $user): ?int
    {
        if (!$user || !$this->isPoll()) {
            return null;
        }

        return $this->pollVotes->firstWhere('user_id', $user->id)?->option_index;
    }

    // Each option's label plus its live vote count/percentage — computed
    // from pollVotes() rather than a separate counters column, since a
    // member can change their vote (unique per post+user) and a denormalized
    // count would need the same upkeep anyway.
    public function pollResults(): array
    {
        if (!$this->isPoll()) {
            return [];
        }

        $counts = $this->pollVotes->countBy('option_index');
        $total = $this->pollVotes->count();

        return collect($this->poll_options)->values()->map(function (string $label, int $index) use ($counts, $total) {
            $votes = $counts->get($index, 0);
            return [
                'index' => $index,
                'label' => $label,
                'votes' => $votes,
                'percentage' => $total > 0 ? (int) round($votes / $total * 100) : 0,
            ];
        })->all();
    }
}
