<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    // One connected account per platform (facebook, instagram, twitter,
    // youtube, tiktok) that CommunityPosts can be auto-published to.
    public const PLATFORMS = ['facebook', 'instagram', 'twitter', 'youtube', 'tiktok'];

    protected $fillable = [
        'platform',
        'display_name',
        'external_account_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'extra',
        'connected_by',
        'status',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            // Live API credentials — the only encrypted-at-rest fields in
            // this app, unlike the plaintext OAuth client secrets Setting
            // stores (those are app-level, these are live account tokens).
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'extra' => 'array',
        ];
    }

    public function connectedBy()
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public function dispatches()
    {
        return $this->hasMany(SocialPostDispatch::class);
    }

    public static function active(string $platform): ?self
    {
        return static::where('platform', $platform)->where('status', 'connected')->first();
    }
}
