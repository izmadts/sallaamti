<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'published_at',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Admin/manager-authored posts go straight to the public wall — everyone
    // else's needs a review pass first (the whole point of the approval
    // gate is to keep an open submission form from becoming a spam vector).
    public static function isAutoPublishedFor(User $user): bool
    {
        return $user->can('posts.manage') || $user->hasRole('admin');
    }
}
