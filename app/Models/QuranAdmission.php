<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranAdmission extends Model
{
    protected $fillable = [
        'quran_live_course_id',
        'user_id',
        'guardian_name',
        'whatsapp_number',
        'country',
        'city_state',
        'student_name',
        'student_gender',
        'student_age',
        'education_grade',
        'learned_quran_before',
        'learning_level',
        'preferred_days',
        'preferred_time',
        'teacher_preference',
        'comments',
        'declaration_accepted',
    ];

    protected function casts(): array
    {
        return [
            'preferred_days' => 'array',
            'learned_quran_before' => 'boolean',
            'declaration_accepted' => 'boolean',
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
}
