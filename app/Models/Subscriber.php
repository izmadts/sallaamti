<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = [
        'email',
        'verification_token',
        'unsubscribe_token',
        'verified_at',
        'unsubscribed_at',
        'is_active'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'is_active' => 'boolean'
    ];
}
