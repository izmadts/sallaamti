<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyContent extends Model
{
    protected $fillable = ['type', 'arabic_text', 'translation', 'reference', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Deterministic pick — same content for every visitor on a given day, no
    // "shown today" tracking table needed, zero write cost per view.
    public static function forToday(): ?self
    {
        $pool = static::where('is_active', true)->orderBy('id')->get();

        if ($pool->isEmpty()) {
            return null;
        }

        return $pool[now()->dayOfYear % $pool->count()];
    }
}
