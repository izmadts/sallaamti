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
        'notes',
        'cancellation_reason',
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
}
