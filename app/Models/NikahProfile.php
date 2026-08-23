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
        'date_of_birth',
        'height',
        'marital_status',
        'has_children',
        'children_count',
        'living_situation',
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
        'state',
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
        'fee_waived',
        'fee_waived_by',
        'fee_waived_reason',
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
            'user_id' => 'integer',
            'date_of_birth' => 'date',
            'has_children' => 'boolean',
            'open_to_polygamy' => 'boolean',
            'allow_photo_sharing' => 'boolean',
            'is_active' => 'boolean',
            'is_demo' => 'boolean',
            'suspended_at' => 'datetime',
            'guardian_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'payment_confirmed_at' => 'datetime',
            'fee_waived' => 'boolean',
        ];
    }

    public function feeWaivedBy()
    {
        return $this->belongsTo(User::class, 'fee_waived_by');
    }

    // Admin-configurable Nikah verification pricing (Settings > Payment) —
    // a site-wide on/off switch, plus marital-status-targeted discounts
    // (e.g. widows/divorcees at 100% off / fully free, or any partial
    // percentage) on top of it. Static so it can be called before a
    // profile row exists yet (wizard finalize/store, where only the
    // validated marital_status string is on hand). This is the single
    // source of truth for the fee — every place that used to read
    // setting('nikah_verification_fee') directly goes through here instead,
    // so the site-wide switch and the discount rule both apply everywhere
    // consistently (creation-time amount, live display, and the payment
    // page) rather than needing separate checks scattered per call site.
    public static function feeForMaritalStatus(?string $maritalStatus): float
    {
        if (!setting('nikah_payment_required', true)) {
            return 0.0;
        }

        $baseFee = (float) setting('nikah_verification_fee', config('services.nikah.verification_fee'));
        $percent = (float) setting('nikah_discount_percent', 0);

        if (!$maritalStatus || $percent <= 0) {
            return $baseFee;
        }

        $discountStatuses = array_filter(explode(',', (string) setting('nikah_discount_marital_statuses', '')));

        if (!in_array($maritalStatus, $discountStatuses, true)) {
            return $baseFee;
        }

        return round($baseFee * (1 - min($percent, 100) / 100), 2);
    }

    public function applicableVerificationFee(): float
    {
        return static::feeForMaritalStatus($this->marital_status);
    }

    // `age` (an integer column) stays the source browse/search filtering
    // queries directly against — recomputing it here whenever
    // date_of_birth changes means every existing age-based query keeps
    // working untouched while the actual input becomes a real, verifiable
    // birth date instead of a self-reported number.
    protected static function booted(): void
    {
        static::saving(function (NikahProfile $profile) {
            if ($profile->isDirty('date_of_birth') && $profile->date_of_birth) {
                $profile->age = $profile->date_of_birth->age;
            }
        });
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

    // Helper: is this profile visible in search/browse? Verification status
    // is deliberately not part of this — Browse shows pending profiles too
    // (see NikahProfileController::browse()), so a profile that appears in
    // the list must also be reachable when clicked, or it's a 404 trap.
    public function isSearchable(): bool
    {
        return $this->is_active
            && !$this->isSuspended()
            && $this->visibility === 'public';
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    // Same gate Browse already enforces before letting a member view other
    // profiles at all — sending/accepting/declining an interest is a
    // further step than viewing, so it needs at least the same bar. Without
    // this, an unverified/unpaid profile could act on interests via a
    // direct API call even though the browse/view screens block them from
    // ever reaching that profile through the normal UI flow.
    public function canInteract(): bool
    {
        return $this->payment_status === 'confirmed' && $this->verification_status === 'verified';
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

    // A ready-to-paste, plain-text profile card for admins to share in
    // WhatsApp groups / Messenger / elsewhere while matchmaking. Deliberately
    // excludes CNIC, guardian contact, and payment info — those stay
    // internal. Missing fields render as ***** rather than being dropped, so
    // the card keeps its shape wherever it's pasted.
    public function shareSummary(): string
    {
        $field = fn ($value) => filled($value) ? $value : '*****';

        $lines = [
            '💍 *Sallaamti Nikah Profile* 💍',
            '',
            '👤 Age: ' . $field($this->age),
            '📏 Height: ' . $field($this->height),
            '💑 Marital Status: ' . $field($this->marital_status ? ucfirst(str_replace('_', ' ', $this->marital_status)) : null),
            '🕌 Sect: ' . $field($this->sect),
            '🎓 Education: ' . $field($this->education),
            '💼 Profession: ' . $field($this->profession),
            '🏙️ City: ' . $field($this->city) . ($this->state ? ', ' . $this->state : '') . ($this->country ? ', ' . $this->country : ''),
            '👪 Family Type: ' . $field($this->family_type),
            '🙏 Prayer: ' . $field($this->prayer_frequency ? ucfirst($this->prayer_frequency) : null),
            '🧕 Hijab/Beard: ' . $field($this->hijab_or_beard ? ucfirst($this->hijab_or_beard) : null),
            '🚭 Smoking: ' . $field($this->smokes ? ucfirst($this->smokes) : null),
            '🍽️ Diet: ' . $field($this->diet ? ucfirst(str_replace('_', ' ', $this->diet)) : null),
            '',
            '📝 *About:*',
            $field($this->about),
            '',
            '🔎 *Looking For:*',
            $field($this->expectations),
            '',
            '👀 View full profile & show interest:',
            route('nikah.public-view', $this->public_token),
            '',
            '✨ Register or log in free on Sallaamti.com to connect directly.',
        ];

        return implode("\n", $lines);
    }

    // Same tracked fields as completenessPercentage() below, paired with a
    // human label and the matching input `id` on nikah/edit.blade.php so the
    // "Complete Your Profile" banner can link straight to what's missing
    // instead of dropping the member on the top of a long form.
    protected function trackedCompletenessFields(): array
    {
        return [
            // `photo` is the single required profile photo captured during
            // signup — deliberately not `photos()->exists()`, which is the
            // *separate*, optional gallery relationship. Checking the
            // gallery here meant a member who fully completed the wizard
            // (photo included) but never added extra gallery photos would
            // be told their profile photo was missing when it wasn't.
            'Profile Photo' => ['value' => $this->photo, 'anchor' => 'photo'],
            'Height' => ['value' => $this->height, 'anchor' => 'height'],
            'Sect' => ['value' => $this->sect, 'anchor' => 'sect'],
            'Caste' => ['value' => $this->caste, 'anchor' => 'caste'],
            'Education' => ['value' => $this->education, 'anchor' => 'education'],
            'Profession' => ['value' => $this->profession, 'anchor' => 'profession'],
            'Family Type' => ['value' => $this->family_type, 'anchor' => 'family_type'],
            'Guardian Relation' => ['value' => $this->guardian_relation, 'anchor' => 'guardian_relation'],
            'About Yourself' => ['value' => $this->about, 'anchor' => 'about'],
            'Looking For' => ['value' => $this->expectations, 'anchor' => 'expectations'],
            'Partner Age Preference' => ['value' => $this->pref_min_age && $this->pref_max_age, 'anchor' => 'pref_min_age'],
        ];
    }

    // Percentage of the optional-but-valuable fields a seeker has filled in —
    // shown as a nudge on their profile page, not used for matching or search.
    public function completenessPercentage(): int
    {
        $fields = array_column($this->trackedCompletenessFields(), 'value');

        $filled = count(array_filter($fields, fn($v) => !empty($v)));
        $total = count($fields);

        return (int) round(($filled / $total) * 100);
    }

    // Which of the tracked fields are still empty, in the order they appear
    // on the edit form — used to name what's missing and to jump the member
    // straight to the first one instead of a generic "go complete it" link.
    public function missingFields(): array
    {
        return collect($this->trackedCompletenessFields())
            ->filter(fn($field) => empty($field['value']))
            ->map(fn($field, $label) => ['label' => $label, 'anchor' => $field['anchor']])
            ->values()
            ->all();
    }
}
