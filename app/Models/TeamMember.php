<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name', 'role', 'bio', 'photo', 'order', 'is_active', 'is_founder',
        'facebook_url', 'instagram_url', 'tiktok_url', 'whatsapp_number',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_founder' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
