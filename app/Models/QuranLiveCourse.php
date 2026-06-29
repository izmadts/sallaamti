<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranLiveCourse extends Model
{
    protected $fillable = ['title', 'description', 'teacher_id', 'class_days', 'class_time', 'monthly_fee', 'is_published', 'created_by'];

    protected function casts(): array
    {
        return ['class_days' => 'array', 'is_published' => 'boolean'];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function admissions()
    {
        return $this->hasMany(QuranAdmission::class);
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
