<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingBooking extends Model
{
    protected $fillable = [
        'support_query_id',
        'member_id',
        'counselor_id',
        'scheduled_at',
        'duration_minutes',
        'status',
        'contact_method',
        'meeting_link',
        'notes',
        'internal_notes',
        'cancellation_reason',
        'member_rating',
        'member_feedback',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'reminded_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function supportQuery()
    {
        return $this->belongsTo(SupportQuery::class, 'support_query_id');
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'requested' => 'yellow',
            'confirmed' => 'blue',
            'completed' => 'green',
            'cancelled' => 'gray',
            'no_show' => 'red',
            default => 'gray',
        };
    }

    // "Anonymous to the counselor" — the checkbox's own wording. Admin views
    // deliberately keep showing the real name regardless; this only governs
    // counselor-facing display.
    public function isAnonymous(): bool
    {
        return (bool) $this->supportQuery?->is_anonymous;
    }

    public function isUrgent(): bool
    {
        return $this->supportQuery?->priority === 'high';
    }
}
