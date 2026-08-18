<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPostDispatch extends Model
{
    protected $fillable = [
        'community_post_id',
        'platform',
        'social_account_id',
        'status',
        'external_post_id',
        'error_message',
        'attempts',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function communityPost()
    {
        return $this->belongsTo(CommunityPost::class);
    }

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
