<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A member's submitted question for "Ap k Sawalat" (Q&A). A duplicate of an
// already-answered question is linked via canonical_question_id rather than
// answered again — see effectiveAnswer()/effectiveAnsweredAt(), which always
// resolve through the canonical so a duplicate never shows stale/duplicate
// data of its own.
class Question extends Model
{
    protected $fillable = [
        'user_id',
        'question',
        'answer',
        'answered_by',
        'answered_at',
        'status',
        'canonical_question_id',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    public function asker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function canonical()
    {
        return $this->belongsTo(Question::class, 'canonical_question_id');
    }

    public function duplicates()
    {
        return $this->hasMany(Question::class, 'canonical_question_id');
    }

    public function isAnswered(): bool
    {
        return $this->status === 'answered';
    }

    public function effectiveAnswer(): ?string
    {
        return $this->canonical?->answer ?? $this->answer;
    }

    public function effectiveAnsweredAt(): ?\Illuminate\Support\Carbon
    {
        return $this->canonical?->answered_at ?? $this->answered_at;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    // Only a canonical (root) answered question is ever listed publicly —
    // a duplicate stays invisible as its own entry, same effect as being
    // merged into the first.
    public function scopePubliclyVisible($query)
    {
        return $query->answered()->whereNull('canonical_question_id');
    }
}
