<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = ['commentable_type', 'commentable_id', 'user_id', 'parent_id', 'body', 'status'];

    protected $casts = [
        'user_id' => 'integer',
        'parent_id' => 'integer',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // One level deep — a reply's own replies() would always be empty since
    // storeComment() refuses to nest under a comment that already has a parent.
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'visible')->oldest();
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'visible');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
