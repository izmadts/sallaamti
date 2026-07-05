<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranLiveCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'level_number',
        'duration',
        'topics',
        'outcome',
        'category',
        'gender_preference',
        'max_students_per_group',
        'teacher_id',
        'class_days',
        'class_time',
        'monthly_fee',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'class_days' => 'array',
            'topics' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function admissions()
    {
        return $this->hasMany(QuranAdmission::class);
    }
    public function classGroups()
    {
        return $this->hasMany(QuranClassGroup::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(QuranSubscription::class);
    }
    public function dailyLinks()
    {
        return $this->hasMany(QuranDailyLink::class);
    }

    public function admissionFor(User $user): ?QuranAdmission
    {
        return $this->admissions()->where('user_id', $user->id)->first();
    }

    public function subscriptionFor(User $user, ?string $month = null): ?QuranSubscription
    {
        $month = $month ?? now()->format('Y-m');
        return $this->subscriptions()->where('user_id', $user->id)->where('month', $month)->first();
    }

    public function hasActiveSubscriptionFor(User $user): bool
    {
        $sub = $this->subscriptionFor($user);
        return $sub && $sub->payment_status === 'confirmed';
    }

    public function todaysLink(): ?QuranDailyLink
    {
        return $this->dailyLinks()->where('class_date', now()->toDateString())->first();
    }
}
