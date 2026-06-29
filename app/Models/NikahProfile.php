<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NikahProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
}
