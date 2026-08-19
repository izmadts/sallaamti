<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranLiveCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'level_number',
        'min_age',
        'max_age',
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
            'min_age' => 'integer',
            'max_age' => 'integer',
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

    // All of this account's admissions (children) for this course — a
    // family with two kids in the same course has two rows here.
    public function admissionsFor(User $user)
    {
        return $this->admissions()->where('user_id', $user->id)->oldest()->get();
    }

    // Kept for the few call sites that only care "has this account applied
    // at all" (e.g. redirecting a guest); prefer admissionsFor() wherever a
    // family might have more than one child.
    public function admissionFor(User $user): ?QuranAdmission
    {
        return $this->admissions()->where('user_id', $user->id)->first();
    }

    // Subscriptions are per child (per admission), not per account — two
    // siblings in the same course each owe their own monthly fee.
    public function subscriptionFor(QuranAdmission $admission, ?string $month = null): ?QuranSubscription
    {
        $month = $month ?? now()->format('Y-m');
        return $this->subscriptions()->where('quran_admission_id', $admission->id)->where('month', $month)->first();
    }

    public function hasActiveSubscriptionFor(QuranAdmission $admission): bool
    {
        $sub = $this->subscriptionFor($admission);
        return $sub && $sub->payment_status === 'confirmed';
    }

    public function todaysLink(): ?QuranDailyLink
    {
        return $this->dailyLinks()->where('class_date', now()->toDateString())->first();
    }
}
