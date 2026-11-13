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

    // Lead lifecycle statuses (owner, 12 Nov 2026): one list for every
    // screen, API and validation rule. Order here = priority order used for
    // sorting ("who needs attention first"). `bg`/`fg` = badge colours (inline styles: the committed CSS
    // build has no new Tailwind classes).
    // `auto` = set by the system from the linked account/profile (see
    // syncAutoStatus()); staff can still pick any status by hand.
    public const STATUSES = [
        'pending_payment'    => ['label' => 'Pending Payment',    'bg' => '#ffedd5', 'fg' => '#9a3412',  'auto' => true],
        'interested'         => ['label' => 'Interested',         'bg' => '#dcfce7', 'fg' => '#166534',     'auto' => false],
        'account_registered' => ['label' => 'Account Registered', 'bg' => '#cffafe', 'fg' => '#155e75',        'auto' => true],
        'registered'         => ['label' => 'Profile Pending',    'bg' => '#f3e8ff', 'fg' => '#6b21a8',  'auto' => true],
        'new'                => ['label' => 'New',                'bg' => '#dbeafe', 'fg' => '#1e40af',        'auto' => false],
        'contacted'          => ['label' => 'Contacted',          'bg' => '#fef9c3', 'fg' => '#854d0e',  'auto' => false],
        'verified'           => ['label' => 'Verified',           'bg' => '#059669', 'fg' => '#ffffff',     'auto' => true],
        'matched'            => ['label' => 'Matched',            'bg' => '#fce7f3', 'fg' => '#9d174d',        'auto' => true],
        'not_interested'     => ['label' => 'Not Interested',     'bg' => '#fee2e2', 'fg' => '#b91c1c',           'auto' => false],
        'number_not_valid'   => ['label' => 'Number Not Valid',   'bg' => '#e7e5e4', 'fg' => '#44403c',     'auto' => false],
        'closed'             => ['label' => 'Closed',             'bg' => '#f3f4f6', 'fg' => '#4b5563',        'auto' => false],
    ];

    // No call-back needed for these, so they never show in "Follow-ups Due".
    public const NO_FOLLOW_UP_STATUSES = ['registered', 'verified', 'matched', 'not_interested', 'number_not_valid', 'closed'];

    // Dead ends: not anyone's open task.
    public const INACTIVE_STATUSES = ['not_interested', 'number_not_valid', 'closed'];

    // The system may move a lead automatically only while it is in one of
    // these; a manual decision (not interested, wrong number, closed) or a
    // match is never overwritten.
    public const AUTO_FROM_STATUSES = ['new', 'contacted', 'interested', 'account_registered', 'registered', 'pending_payment', 'verified'];

    public static function statusKeys(): array
    {
        return array_keys(self::STATUSES);
    }

    public static function statusRule(): string
    {
        return 'in:' . implode(',', self::statusKeys());
    }

    // value => label; $translate uses the db.* strings, falling back to
    // English when a translation row doesn't exist yet.
    public static function statusOptions(bool $translate = false): array
    {
        $out = [];
        foreach (self::STATUSES as $key => $s) {
            $out[$key] = $translate ? self::translateLabel($s['label']) : $s['label'];
        }

        return $out;
    }

    public static function statusLabel(?string $status, bool $translate = false): string
    {
        $label = self::STATUSES[$status]['label'] ?? ucfirst(str_replace('_', ' ', (string) $status));

        return $translate ? self::translateLabel($label) : $label;
    }

    // Inline style for a status badge/chip.
    public static function statusStyle(?string $status): string
    {
        $s = self::STATUSES[$status] ?? ['bg' => '#f3f4f6', 'fg' => '#4b5563'];

        return "background:{$s['bg']};color:{$s['fg']}";
    }

    // SQL CASE for ORDER BY: priority position of each status.
    public static function statusPrioritySql(): string
    {
        $cases = '';
        foreach (self::statusKeys() as $i => $key) {
            $cases .= " WHEN '{$key}' THEN {$i}";
        }

        return "CASE status{$cases} ELSE 99 END";
    }

    private static function translateLabel(string $label): string
    {
        $t = __('db.' . $label);

        return $t === 'db.' . $label ? $label : $t;
    }

    // Last 10 digits: 0300-1234567, +92 300 1234567 and 923001234567 all
    // become 3001234567, so a lead matches the account made with that number.
    public static function phoneKey(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return strlen($digits) >= 10 ? substr($digits, -10) : null;
    }

    // The status the linked account/profile says this lead is at, or null
    // when there is nothing to go on (no account with this phone).
    public static function statusFromProfile(?NikahProfile $profile, bool $hasAccount): ?string
    {
        if ($profile) {
            if ($profile->verification_status === 'verified') {
                return 'verified';
            }
            if (in_array($profile->payment_status, ['unpaid', 'submitted', 'rejected'], true) && (float) $profile->payment_amount > 0) {
                return 'pending_payment';
            }

            return 'registered';
        }

        return $hasAccount ? 'account_registered' : null;
    }

    // Moves the lead to $status if the system is allowed to (see
    // AUTO_FROM_STATUSES) and logs it on the lead's timeline.
    public function applyAutoStatus(?string $status): bool
    {
        if (!$status || $status === $this->status || !in_array($this->status, self::AUTO_FROM_STATUSES, true)) {
            return false;
        }
        $this->forceFill(['status' => $status])->saveQuietly();
        MatchmakingTimelineEvent::log($this, $this->nikahProfile, 'status_changed', 'Status changed automatically to ' . self::statusLabel($status) . '.');

        return true;
    }

    // Leads that belong to this profile: linked directly, or same phone as
    // the profile owner's account.
    public static function forProfile(NikahProfile $profile)
    {
        $key = self::phoneKey($profile->user?->phone);

        return self::where(function ($q) use ($profile, $key) {
            $q->where('nikah_profile_id', $profile->id);
            if ($key) {
                $q->orWhere(fn ($qq) => $qq->whereNull('nikah_profile_id')->where('phone', 'like', '%' . $key));
            }
        })->get();
    }

    public static function syncForProfile(NikahProfile $profile): void
    {
        $status = self::statusFromProfile($profile, true);
        foreach (self::forProfile($profile) as $lead) {
            $lead->applyAutoStatus($status);
        }
    }

    public static function syncForUser(User $user): void
    {
        $key = self::phoneKey($user->phone);
        if (!$key) {
            return;
        }
        $profile = $user->nikahProfile ?? null;
        $status = self::statusFromProfile($profile, true);
        foreach (self::whereNull('nikah_profile_id')->where('phone', 'like', '%' . $key)->get() as $lead) {
            $lead->applyAutoStatus($status);
        }
    }

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'date',
            'package_price' => 'decimal:2',
            'package_started_at' => 'date',
            'package_expires_at' => 'date',
            'account_setup_skipped_at' => 'datetime',
            // Without these, PDO hands back these foreign keys as PHP
            // strings, which json_encode then quotes — the NC mobile app's
            // `as int?` cast on nikah_profile_id (Api\V1\Matchmaker\
            // ClientController::listPayload()) crashed on exactly this,
            // caught live via `flutter run` on a real device. Same latent
            // bug on nikah_package_id (detailPayload() / NikahHireCounselor
            // Controller), fixed before it got the chance to do the same.
            'nikah_profile_id' => 'integer',
            'nikah_package_id' => 'integer',
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

    public function clientMessages()
    {
        return $this->hasMany(LeadClientMessage::class)->orderBy('created_at');
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
        // Per-requirement detail the mobile app shows under a "why this %?"
        // tap — without this, the percentage was a number with no way to
        // see which of the client's actual requirements it came from.
        $breakdown = [];

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

            $breakdown[] = [
                'type' => $item->requirement_type,
                'label' => MatchmakingRequirementItem::TYPES[$item->requirement_type] ?? $item->requirement_type,
                'value' => $item->requirement_value,
                'priority' => $item->priority,
                'matched' => $matched,
            ];
        }

        if ($total === 0) {
            return ['level' => null, 'percentage' => 0, 'considered' => 0, 'breakdown' => []];
        }

        $percentage = (int) round(($score / $total) * 100);

        return [
            'level' => $percentage >= 75 ? 'high' : ($percentage >= 40 ? 'medium' : 'low'),
            'percentage' => $percentage,
            'considered' => $considered,
            'breakdown' => $breakdown,
        ];
    }

    // Single source of truth for "what's this client's next step" — shows
    // the counselor/admin exactly where a client is stuck, without having
    // to reverse-engineer it from the raw timeline log. Previously also
    // rendered the now-retired public /p/{lead} progress link; kept as-is
    // for the counselor/admin dashboards, which still use it independently.
    // Priority order: registration -> documents -> package/payment ->
    // consent -> optional PIN setup -> proposals -> waiting on a sent
    // interest's response.
    public function progressQueue(): \Illuminate\Support\Collection
    {
        $queue = collect();

        if (!$this->nikahProfile) {
            $queue->push(['type' => 'registration', 'data' => null]);

            return $queue;
        }

        // CNIC photo upload is retired project-wide — the number is the
        // only thing this stage still waits on.
        if (empty($this->nikahProfile->cnic_number)) {
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
