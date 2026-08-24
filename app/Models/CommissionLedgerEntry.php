<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionLedgerEntry extends Model
{
    protected $fillable = [
        'matchmaker_id', 'source_type', 'source_id',
        'rule_type', 'nikah_package_id', 'is_renewal', 'tier_at_time',
        'rate_type', 'rate_value', 'base_amount', 'commission_amount',
        'status', 'eligible_at', 'approved_at', 'approved_by',
        'paid_at', 'paid_by', 'flagged_at', 'flag_reason', 'flagged_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_renewal' => 'boolean',
            'rate_value' => 'decimal:2',
            'base_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'eligible_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
            'flagged_at' => 'datetime',
        ];
    }

    public function matchmaker()
    {
        return $this->belongsTo(User::class, 'matchmaker_id');
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function nikahPackage()
    {
        return $this->belongsTo(NikahPackage::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function flaggedBy()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function isFlagged(): bool
    {
        return $this->flagged_at !== null;
    }

    public function isEligibleForApproval(): bool
    {
        return $this->status === 'pending'
            && !$this->isFlagged()
            && $this->eligible_at !== null
            && now()->gte($this->eligible_at);
    }
}
