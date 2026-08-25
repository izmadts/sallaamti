<?php

namespace App\Models;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Model;

class MatchmakerApplication extends Model
{
    // Separate from Certificate::qrCodeBase64() (which points at the
    // certificate verification URL, a trust check) — this one encodes
    // the counselor's own referral link, the marketing/conversion tool
    // from the hiring document's section 14 ("referral URL... or QR
    // code"). Scanning this takes someone straight to /register?ref=...,
    // not to a verification page.
    public function referralQrCodeBase64(): ?string
    {
        if (!$this->counselor_code) {
            return null;
        }

        $qrCode = new QrCode(url('/register?ref=' . $this->counselor_code));
        $writer = new PngWriter();

        return $writer->write($qrCode)->getDataUri();
    }

    // Order matters for the admin pipeline UI (progress bar / next-step
    // logic) — index position doubles as pipeline order. rejected/withdrawn
    // are terminal side-exits, not steps in the main sequence.
    public const STEPS = [
        'applied' => 'Application Received',
        'identity_verified' => 'Identity Verified',
        'references_checked' => 'References Checked',
        'interviewed' => 'Interviewed',
        'agreement_signed' => 'Matchmaker Agreement Signed',
        'nda_signed' => 'Privacy / NDA Signed',
        'training' => 'Training',
        'assessed' => 'Assessment Passed',
        'probation' => 'Probation',
        'certified' => 'Certified Nikah Counselor',
    ];

    // Urdu labels for the same STEPS keys — kept as its own const (not a
    // second language file) so the two always stay in sync: adding/
    // renaming/reordering a stage in STEPS is an obvious, un-missable
    // diff right next to this array, never a silently-stale translation.
    public const STEPS_UR = [
        'applied' => 'درخواست موصول ہوئی',
        'identity_verified' => 'شناخت کی تصدیق',
        'references_checked' => 'حوالہ جات کی جانچ',
        'interviewed' => 'انٹرویو',
        'agreement_signed' => 'معاہدے پر دستخط',
        'nda_signed' => 'رازداری کے معاہدے پر دستخط',
        'training' => 'تربیت',
        'assessed' => 'تشخیصی امتحان پاس',
        'probation' => 'آزمائشی مدت',
        'certified' => 'سرٹیفائیڈ نکاح کاؤنسلر',
    ];

    public const TERMINAL_EXIT_STATUSES = ['rejected', 'withdrawn'];

    public const LEVELS = [
        'nikah_counselor' => 'Nikah Counselor',
        'certified_nikah_counselor' => 'Certified Nikah Counselor',
        'senior_nikah_counselor' => 'Senior Nikah Counselor',
        'regional_nikah_coordinator' => 'Regional Nikah Coordinator',
    ];

    // What it takes to auto-promote into each level — checked by
    // Console\Commands\PromoteEligibleCounselors, run daily. Deliberately
    // requires all three together (real volume + real quality + real time
    // in the role), not any single number alone — a single well-verified
    // walk-in in week one shouldn't jump someone straight to Senior.
    // 'level' directly drives commission tier (RecordsCommission::
    // tierForMatchmaker()), so promotion is intentionally one-way here —
    // this never auto-demotes; that stays a deliberate admin action.
    public const PROMOTION_THRESHOLDS = [
        'certified_nikah_counselor' => ['verified' => 5, 'quality_score' => 60, 'tenure_days' => 30],
        'senior_nikah_counselor' => ['verified' => 20, 'quality_score' => 75, 'tenure_days' => 90],
        'regional_nikah_coordinator' => ['verified' => 50, 'quality_score' => 85, 'tenure_days' => 180],
    ];

    public const QUALIFICATIONS = [
        'under_matric' => 'Under Matric',
        'matric' => 'Matric',
        'intermediate' => 'Intermediate',
        'bachelors' => "Bachelor's",
        'masters' => "Master's",
        'mphil' => 'MPhil',
        'phd' => 'PhD',
        'other' => 'Other',
    ];

    protected $fillable = [
        'user_id', 'full_name', 'guardian_name', 'mobile_number', 'whatsapp_number',
        'gender', 'age', 'marital_status', 'qualification', 'qualification_other',
        'selfie_photo', 'cnic_number', 'cnic_front_image', 'cnic_back_image',
        'area', 'address', 'country',
        'payout_method', 'payout_account_title', 'payout_account_number', 'payout_bank_name',
        'consent_accepted', 'terms_accepted',
        'ip_address', 'user_agent', 'device_city',
        'status', 'level', 'notes', 'reviewed_by',
        'counselor_code', 'certified_at', 'rejected_at', 'withdrawn_at',
        'agreement_link_token', 'agreement_accepted_at', 'agreement_ip',
        'nda_accepted_at', 'nda_ip',
    ];

    public function hasAcceptedAgreementAndNda(): bool
    {
        return $this->agreement_accepted_at !== null && $this->nda_accepted_at !== null;
    }

    protected function casts(): array
    {
        return [
            'consent_accepted' => 'boolean',
            'terms_accepted' => 'boolean',
            'agreement_accepted_at' => 'datetime',
            'nda_accepted_at' => 'datetime',
            'certified_at' => 'datetime',
            'rejected_at' => 'datetime',
            'withdrawn_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isCertified(): bool
    {
        return $this->status === 'certified';
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_EXIT_STATUSES, true);
    }

    public function nextStep(): ?string
    {
        $keys = array_keys(self::STEPS);
        $currentIndex = array_search($this->status, $keys, true);

        if ($currentIndex === false || $currentIndex === count($keys) - 1) {
            return null;
        }

        return $keys[$currentIndex + 1];
    }

    public function nextLevelKey(): ?string
    {
        $keys = array_keys(self::LEVELS);
        $currentIndex = array_search($this->level, $keys, true);

        if ($currentIndex === false || $currentIndex === count($keys) - 1) {
            return null;
        }

        return $keys[$currentIndex + 1];
    }

    public function tenureDays(): ?int
    {
        return $this->certified_at?->diffInDays(now());
    }

    // Every threshold for the next level must actually be met — see
    // PROMOTION_THRESHOLDS' own comment on why all three together.
    public function isEligibleForPromotion(array $score, array $stats): bool
    {
        $nextLevel = $this->nextLevelKey();
        if (!$nextLevel) {
            return false;
        }

        $thresholds = self::PROMOTION_THRESHOLDS[$nextLevel] ?? null;
        $tenureDays = $this->tenureDays();

        if (!$thresholds || $tenureDays === null) {
            return false;
        }

        return $tenureDays >= $thresholds['tenure_days']
            && $stats['verified'] >= $thresholds['verified']
            && ($score['overall'] ?? 0) >= $thresholds['quality_score'];
    }

    // Powers the "X to go" progress bar on the Performance page — shows
    // exactly which of the three requirements (if any) are still short,
    // rather than a single opaque percentage.
    public function nextLevelProgress(array $score, array $stats): ?array
    {
        $nextLevel = $this->nextLevelKey();
        if (!$nextLevel) {
            return null;
        }

        $thresholds = self::PROMOTION_THRESHOLDS[$nextLevel] ?? null;
        if (!$thresholds) {
            return null;
        }

        $tenureDays = $this->tenureDays() ?? 0;
        $qualityScore = $score['overall'] ?? 0;

        return [
            'next_level' => $nextLevel,
            'next_level_label' => self::LEVELS[$nextLevel],
            'verified' => ['current' => $stats['verified'], 'needed' => $thresholds['verified'], 'met' => $stats['verified'] >= $thresholds['verified']],
            'quality_score' => ['current' => $qualityScore, 'needed' => $thresholds['quality_score'], 'met' => $qualityScore >= $thresholds['quality_score']],
            'tenure_days' => ['current' => $tenureDays, 'needed' => $thresholds['tenure_days'], 'met' => $tenureDays >= $thresholds['tenure_days']],
        ];
    }
}
