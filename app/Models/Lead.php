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
        'pending_package_id',
        'package_payment_method',
        'package_payment_reference',
        'package_payment_screenshot',
        'package_payment_status',
        'package_payment_rejection_reason',
        'account_setup_skipped_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'date',
            'package_price' => 'decimal:2',
            'package_started_at' => 'date',
            'package_expires_at' => 'date',
            'account_setup_skipped_at' => 'datetime',
        ];
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // A matchmaker's own screens show this instead of the real number —
    // admin-side views keep showing the raw phone unmasked. Keeps the
    // first 2 and last 2 characters (enough to recognize "yes, that's the
    // client I have on file") and masks everything in between, regardless
    // of format (+92, 03xx, spaces, dashes — no assumptions about shape).
    public function maskedPhone(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $length = mb_strlen($this->phone);
        if ($length <= 4) {
            return str_repeat('•', $length);
        }

        return mb_substr($this->phone, 0, 2) . str_repeat('•', $length - 4) . mb_substr($this->phone, -2);
    }

    // The one exception to "a matchmaker never sees the real phone": the
    // counselor who personally typed this number in when creating the lead
    // already knows it — masking it back at them just forces re-entry from
    // memory/notes. Anyone else (reassigned, or another counselor entirely)
    // still only ever gets maskedPhone().
    public function visiblePhoneFor(User $user): ?string
    {
        if ($this->phone && $this->created_by === $user->id) {
            return $this->phone;
        }

        return $this->maskedPhone();
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

    // The package a client selected on their own progress link and
    // submitted payment proof for — not yet active. Admin confirming it
    // (Admin\LeadController::confirmPackagePayment()) is what actually
    // copies this into nikah_package_id and fires commission; nothing
    // about a client's own claim activates the package on its own.
    public function pendingPackage()
    {
        return $this->belongsTo(NikahPackage::class, 'pending_package_id');
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
