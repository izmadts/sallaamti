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
        'sect',
        'caste',
        'education',
        'profession',
        'city',
        'country',
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
        'rejection_reason',
        'visibility',
        'is_active',
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
            && $this->visibility === 'public';
    }
    
    public function isPaymentConfirmed(): bool
    {
        return $this->payment_status === 'confirmed';
    }

    public function matchPercentageWith(NikahProfile $viewer): int
    {
        $score = 0;
        $total = 0;

        // Age match (viewer's preference vs this profile's age)
        if ($viewer->pref_min_age && $viewer->pref_max_age) {
            $total += 25;
            if ($this->age >= $viewer->pref_min_age && $this->age <= $viewer->pref_max_age) {
                $score += 25;
            }
        }

        // City match
        if ($viewer->pref_city) {
            $total += 20;
            if (strtolower($this->city) === strtolower($viewer->pref_city)) {
                $score += 20;
            }
        }

        // Sect match
        if ($viewer->pref_sect) {
            $total += 20;
            if (strtolower($this->sect ?? '') === strtolower($viewer->pref_sect)) {
                $score += 20;
            }
        }

        // Education match
        if ($viewer->pref_education) {
            $total += 15;
            if (str_contains(strtolower($this->education ?? ''), strtolower($viewer->pref_education))) {
                $score += 15;
            }
        }

        // Marital status match
        if ($viewer->pref_marital_status) {
            $total += 20;
            if ($this->marital_status === $viewer->pref_marital_status) {
                $score += 20;
            }
        }

        if ($total === 0) return 0;

        return (int) round(($score / $total) * 100);
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
}
