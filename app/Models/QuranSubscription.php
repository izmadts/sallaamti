<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranSubscription extends Model
{
    protected $fillable = [
        'quran_live_course_id',
        'user_id',
        'quran_admission_id',
        'month',
        'amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_screenshot',
        'payment_rejection_reason',
        'payment_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'quran_live_course_id' => 'integer',
            'user_id' => 'integer',
            'quran_admission_id' => 'integer',
            'payment_confirmed_at' => 'datetime',
        ];
    }

    public function course()
    {
        return $this->belongsTo(QuranLiveCourse::class, 'quran_live_course_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function admission()
    {
        return $this->belongsTo(QuranAdmission::class, 'quran_admission_id');
    }
}
