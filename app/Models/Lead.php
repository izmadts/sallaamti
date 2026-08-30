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

    // How well a candidate fits THIS client's stated Requirements — must_have
    // items count 3x, preferred 2x, flexible 1x toward the percentage.
    // 'level' is null (not 'low') when there's nothing to judge by — no
    // Requirement saved yet, or every item is a type nothing can be
    // compared against (see MatchmakingRequirementItem::matchesCandidate) —
    // so the UI can tell "no data" apart from "genuinely a weak match".
    public function matchScoreWith(NikahProfile $candidate): array
    {
        $weights = ['must_have' => 3, 'preferred' => 2, 'flexible' => 1];
        $total = 0;
        $score = 0;
        $considered = 0;

        foreach ($this->requirement?->items ?? [] as $item) {
            $matched = $item->matchesCandidate($candidate);
            if ($matched === null) {
                continue;
            }

            $weight = $weights[$item->priority] ?? 1;
            $total += $weight;
            $considered++;
            if ($matched) {
                $score += $weight;
            }
        }

        if ($total === 0) {
            return ['level' => null, 'percentage' => 0, 'considered' => 0];
        }

        $percentage = (int) round(($score / $total) * 100);

        return [
            'level' => $percentage >= 75 ? 'high' : ($percentage >= 40 ? 'medium' : 'low'),
            'percentage' => $percentage,
            'considered' => $considered,
        ];
    }

    // Single source of truth for "what does this client see next on their
    // progress link" — used both to render that link
    // (Public\MatchmakingProgressController) and to show the counselor/
    // admin exactly where a client is stuck, without either side having to
    // reverse-engineer it from the raw timeline log. Same priority order
    // requested for the client link itself: registration -> documents ->
    // package/payment -> consent -> optional PIN setup -> proposals ->
    // waiting on a sent interest's response.
    public function progressQueue(): \Illuminate\Support\Collection
    {
        $queue = collect();

        if (!$this->nikahProfile) {
            $queue->push(['type' => 'registration', 'data' => null]);

            return $queue;
        }

        if (empty($this->nikahProfile->cnic_front_image) || empty($this->nikahProfile->cnic_back_image) || empty($this->nikahProfile->cnic_number)) {
            $queue->push(['type' => 'documents', 'data' => null]);
        }

        if (!$this->nikah_package_id) {
            if (in_array($this->package_payment_status, [null, 'rejected'], true)) {
                $queue->push(['type' => 'package', 'data' => null]);
            } elseif ($this->package_payment_status === 'submitted') {
                $queue->push(['type' => 'package_pending', 'data' => null]);
            }
        }

        foreach ($this->consentRequests->where('status', 'pending') as $consentRequest) {
            $queue->push(['type' => 'consent', 'data' => $consentRequest]);
        }

        $linkedAccount = $this->nikahProfile?->user ?: User::where('phone', $this->phone)->first();
        if (!$this->account_setup_skipped_at && !$linkedAccount?->pin) {
            $queue->push(['type' => 'account_setup', 'data' => null]);
        }

        foreach ($this->proposalBatches->where('status', '!=', 'draft') as $batch) {
            $pending = $batch->proposals->whereNull('response');
            if ($pending->isNotEmpty()) {
                $queue->push(['type' => 'proposal_batch', 'data' => $batch, 'pending' => $pending]);
            }
        }

        if ($queue->isEmpty()) {
            $pendingSentInterest = NikahInterest::where('sender_profile_id', $this->nikahProfile->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingSentInterest) {
                $queue->push(['type' => 'interest_pending', 'data' => $pendingSentInterest]);
            }
        }

        return $queue;
    }

    public function isMatched(): bool
    {
        if (!$this->nikahProfile) {
            return false;
        }

        return NikahInterest::where('status', 'accepted')
            ->where(function ($q) {
                $q->where('sender_profile_id', $this->nikahProfile->id)
                    ->orWhere('receiver_profile_id', $this->nikahProfile->id);
            })->exists();
    }

    // Short, human label for whatever progressQueue() says is current —
    // this is what a counselor/admin actually reads at a glance.
    public function progressStageLabel(): string
    {
        if ($this->isMatched()) {
            return '🎉 Matched';
        }

        $current = $this->progressQueue()->first();

        if (!$current) {
            return 'All caught up';
        }

        return match ($current['type']) {
            'registration' => 'Awaiting client registration',
            'documents' => 'Awaiting document upload',
            'package' => 'Awaiting package selection & payment',
            'package_pending' => 'Payment submitted — awaiting admin review',
            'consent' => 'Awaiting consent response',
            'account_setup' => 'Awaiting optional PIN setup',
            'proposal_batch' => 'Awaiting response to a proposal',
            'interest_pending' => 'Waiting on the other side to respond',
            default => 'In progress',
        };
    }
}
