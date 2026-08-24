<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'phone',
        'email',
        'looking_for',
        'source',
        'status',
        'assigned_to',
        'notes',
        'next_follow_up_at',
        'nikah_profile_id',
        'progress_link_token',
        'nikah_package_id',
        'package',
        'package_price',
        'package_started_at',
        'package_expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'date',
            'package_price' => 'decimal:2',
            'package_started_at' => 'date',
            'package_expires_at' => 'date',
        ];
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nikahProfile()
    {
        return $this->belongsTo(NikahProfile::class);
    }

    public function nikahPackage()
    {
        return $this->belongsTo(NikahPackage::class);
    }

    public function shortlistItems()
    {
        return $this->hasMany(LeadShortlistItem::class);
    }

    public function timelineEvents()
    {
        return $this->hasMany(MatchmakingTimelineEvent::class)->latest('created_at');
    }

    public function requirement()
    {
        return $this->hasOne(MatchmakingRequirement::class);
    }

    public function proposalBatches()
    {
        return $this->hasMany(ProposalBatch::class)->latest('id');
    }

    public function consents()
    {
        return $this->hasMany(MatchmakingConsent::class)->latest('granted_at');
    }

    public function consentRequests()
    {
        return $this->hasMany(MatchmakingConsentRequest::class)->latest('requested_at');
    }

    public function hasActiveConsent(string $type): bool
    {
        return $this->consents()->active()->where('consent_type', $type)->exists();
    }

    // Only proposals that actually went out count against the package's
    // cap — a matchmaker can freely draft and swap candidates before
    // sending without burning allowance.
    public function proposalsSentCount(): int
    {
        return MatchProposal::whereHas('batch', fn ($q) => $q->where('lead_id', $this->id))
            ->whereNotNull('sent_at')
            ->count();
    }

    // Null means "no cap" — either no package assigned, or the package
    // itself has no proposal_limit (e.g. Verified, which isn't a
    // matchmaking package at all).
    public function remainingProposalAllowance(): ?int
    {
        if (!$this->nikah_package_id || !$this->nikahPackage || $this->nikahPackage->proposal_limit === null) {
            return null;
        }

        return max(0, $this->nikahPackage->proposal_limit - $this->proposalsSentCount());
    }

    public function packageExpired(): bool
    {
        return $this->package_expires_at !== null && $this->package_expires_at->isPast();
    }

    public function isConverted(): bool
    {
        return $this->nikah_profile_id !== null;
    }
}
