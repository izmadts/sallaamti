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
        'preferred_days',
        'preferred_time',
        'teacher_preference',
        'comments',
        'declaration_accepted',
        'selected_level',
        'previous_level',
        'class_type',
        'timezone',
        'status',
        'admin_notes',
        'assigned_group_id',
    ];

    public function assignedGroup()
    {
        return $this->belongsTo(QuranClassGroup::class, 'assigned_group_id');
    }
    public function groupStudent()
    {
        return $this->hasOne(QuranGroupStudent::class);
    }

    protected function casts(): array
    {
        return [
            'quran_live_course_id' => 'integer',
            'user_id' => 'integer',
            'assigned_group_id' => 'integer',
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
    public function messages()
    {
        return $this->hasMany(QuranMessage::class)->oldest();
    }
}
