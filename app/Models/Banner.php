<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'subtitle',
        'title',
        'description',
        'button_text',
        'button_url',
        'image',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('order')->get();
    }
    
}
