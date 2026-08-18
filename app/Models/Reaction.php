<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    // One "Ameen"-style set of reaction types, shared by every reactable
    // model — [emoji, label].
    public const TYPES = [
        'ameen' => ['🤲', 'Ameen'],
        'jazakallah' => ['🌟', 'JazakAllah'],
        'mashaallah' => ['✨', 'MashaAllah'],
    ];

    protected $fillable = ['reactable_type', 'reactable_id', 'user_id', 'type'];

    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
