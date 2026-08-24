<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakerApplication extends Model
{
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

    public const TERMINAL_EXIT_STATUSES = ['rejected', 'withdrawn'];

    public const LEVELS = [
        'nikah_counselor' => 'Nikah Counselor',
        'certified_nikah_counselor' => 'Certified Nikah Counselor',
        'senior_nikah_counselor' => 'Senior Nikah Counselor',
        'regional_nikah_coordinator' => 'Regional Nikah Coordinator',
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
        'area', 'address',
        'payout_method', 'payout_account_title', 'payout_account_number', 'payout_bank_name',
        'consent_accepted', 'terms_accepted',
        'ip_address', 'user_agent', 'device_city',
        'status', 'level', 'notes', 'reviewed_by',
        'counselor_code', 'certified_at', 'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'consent_accepted' => 'boolean',
            'terms_accepted' => 'boolean',
            'certified_at' => 'datetime',
            'rejected_at' => 'datetime',
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
}
