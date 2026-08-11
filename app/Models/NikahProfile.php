<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NikahProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'public_token',
        'age',
        'height',
        'marital_status',
        'open_to_polygamy',
        'sect',
        'caste',
        'prayer_frequency',
        'hijab_or_beard',
        'smokes',
        'diet',
        'education',
        'profession',
        'city',
        'country',
        'ethnicity',
        'language',
        'family_type',
        'guardian_name',
        'guardian_contact',
        'guardian_relation',
        'about',
        'expectations',
        'photo',
        'allow_photo_sharing',
        'cnic_number',
        'cnic_front_image',
        'cnic_back_image',
        'verification_status',
        'is_demo',
        'rejection_reason',
        'visibility',
        'is_active',
        'suspended_at',
        'suspension_reason',
        'guardian_verified_at',
        'last_active_at',
        'payment_status',
        'payment_amount',
        'payment_method',
        'payment_reference',
        'payment_screenshot',
        'payment_rejection_reason',
        'payment_confirmed_at',
        'pref_min_age',
        'pref_max_age',
        'pref_city',
        'pref_sect',
        'pref_education',
        'pref_marital_status',
    ];

    protected function casts(): array
    {
        return [
            'open_to_polygamy' => 'boolean',
            'allow_photo_sharing' => 'boolean',
            'is_active' => 'boolean',
            'is_demo' => 'boolean',
            'suspended_at' => 'datetime',
            'guardian_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'payment_confirmed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sentInterests()
    {
        return $this->hasMany(NikahInterest::class, 'sender_profile_id');
    }

    public function receivedInterests()
    {
        return $this->hasMany(NikahInterest::class, 'receiver_profile_id');
    }

    // Helper: is this profile fully verified and visible in search?
    public function isSearchable(): bool
    {
        return $this->verification_status === 'verified'
            && $this->is_active
            && !$this->isSuspended()
            && $this->visibility === 'public';
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function isPaymentConfirmed(): bool
    {
        return $this->payment_status === 'confirmed';
    }

    public function isGuardianVerified(): bool
    {
        return $this->guardian_verified_at !== null;
    }

    // Trust badge stack — three independently-earned signals, shown instead of
    // one binary "Verified" so a seeker can see exactly what's been checked.
    public function trustBadges(): array
    {
        return [
            'payment' => $this->isPaymentConfirmed(),
            'cnic' => $this->verification_status === 'verified',
            'guardian' => $this->isGuardianVerified(),
        ];
    }

    public function matchPercentageWith(NikahProfile $viewer): int
    {
        return $this->matchBreakdownWith($viewer)['percentage'];
    }

    // Same scoring as matchPercentageWith(), but returns the per-criterion
    // breakdown so the UI can explain "why 82%?" instead of a bare number.
    public function matchBreakdownWith(NikahProfile $viewer): array
    {
        $criteria = [];

        // Age match (viewer's preference vs this profile's age)
        if ($viewer->pref_min_age && $viewer->pref_max_age) {
            $criteria[] = [
                'label' => 'Age (' . $viewer->pref_min_age . '-' . $viewer->pref_max_age . ')',
                'weight' => 20,
                'matched' => $this->age >= $viewer->pref_min_age && $this->age <= $viewer->pref_max_age,
            ];
        }

        // City match
        if ($viewer->pref_city) {
            $criteria[] = [
                'label' => 'City (' . $viewer->pref_city . ')',
                'weight' => 15,
                'matched' => strtolower($this->city) === strtolower($viewer->pref_city),
            ];
        }

        // Sect match
        if ($viewer->pref_sect) {
            $criteria[] = [
                'label' => 'Sect (' . $viewer->pref_sect . ')',
                'weight' => 15,
                'matched' => strtolower($this->sect ?? '') === strtolower($viewer->pref_sect),
            ];
        }

        // Education match
        if ($viewer->pref_education) {
            $criteria[] = [
                'label' => 'Education (' . $viewer->pref_education . ')',
                'weight' => 10,
                'matched' => str_contains(strtolower($this->education ?? ''), strtolower($viewer->pref_education)),
            ];
        }

        // Marital status match
        if ($viewer->pref_marital_status) {
            $criteria[] = [
                'label' => 'Marital status',
                'weight' => 15,
                'matched' => $this->marital_status === $viewer->pref_marital_status,
            ];
        }

        // Deen/lifestyle — assortative: people usually seek a similar practice
        // level, so these compare the viewer's own values against the
        // candidate's rather than needing a separate set of preference fields.
        if ($viewer->prayer_frequency) {
            $criteria[] = [
                'label' => 'Prayer regularity',
                'weight' => 15,
                'matched' => $this->prayer_frequency === $viewer->prayer_frequency,
            ];
        }

        if ($viewer->hijab_or_beard) {
            $criteria[] = [
                'label' => 'Hijab/Beard',
                'weight' => 10,
                'matched' => $this->hijab_or_beard === $viewer->hijab_or_beard,
            ];
        }

        if ($viewer->smokes) {
            $criteria[] = [
                'label' => 'Smoking',
                'weight' => 5,
                'matched' => $this->smokes === $viewer->smokes,
            ];
        }

        if ($viewer->diet) {
            $criteria[] = [
                'label' => 'Diet',
                'weight' => 5,
                'matched' => $this->diet === $viewer->diet,
            ];
        }

        if ($viewer->ethnicity) {
            $criteria[] = [
                'label' => 'Ethnicity',
                'weight' => 5,
                'matched' => strtolower($this->ethnicity ?? '') === strtolower($viewer->ethnicity),
            ];
        }

        $total = array_sum(array_column($criteria, 'weight'));
        $score = array_sum(array_map(fn($c) => $c['matched'] ? $c['weight'] : 0, $criteria));

        return [
            'percentage' => $total === 0 ? 0 : (int) round(($score / $total) * 100),
            'criteria' => $criteria,
        ];
    }
    public function savedProfiles()
    {
        return $this->hasMany(NikahSavedProfile::class, 'nikah_profile_id');
    }

    public function savedByProfiles()
    {
        return $this->hasMany(NikahSavedProfile::class, 'saved_profile_id');
    }

    public function blockedProfiles()
    {
        return $this->hasMany(NikahBlock::class, 'blocker_profile_id');
    }
    public function photos()
    {
        return $this->hasMany(\App\Models\NikahPhoto::class)->orderBy('order');
    }

    public function primaryPhoto(): ?\App\Models\NikahPhoto
    {
        return $this->photos()->where('is_primary', true)->first()
            ?? $this->photos()->first();
    }

    public function moderationNotes()
    {
        return $this->hasMany(NikahModerationNote::class)->latest();
    }

    // Percentage of the optional-but-valuable fields a seeker has filled in —
    // shown as a nudge on their profile page, not used for matching or search.
    public function completenessPercentage(): int
    {
        $fields = [
            $this->height,
            $this->sect,
            $this->caste,
            $this->education,
            $this->profession,
            $this->family_type,
            $this->guardian_relation,
            $this->about,
            $this->expectations,
            $this->pref_min_age && $this->pref_max_age,
        ];

        $filled = count(array_filter($fields, fn($v) => !empty($v)));
        $total = count($fields) + 1; // +1 for the photo check below

        if ($this->photos()->exists()) {
            $filled++;
        }

        return (int) round(($filled / $total) * 100);
    }
}
