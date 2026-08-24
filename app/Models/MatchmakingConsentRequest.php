<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakingConsentRequest extends Model
{
    protected $fillable = ['lead_id', 'consent_type', 'requested_by', 'requested_at', 'status', 'responded_at'];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
