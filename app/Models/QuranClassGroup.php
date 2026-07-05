<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranClassGroup extends Model
{
    protected $fillable = [
        'quran_live_course_id',
        'teacher_id',
        'group_name',
        'class_days',
        'class_time',
        'timezone',
        'gender',
        'max_students',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['class_days' => 'array', 'is_active' => 'boolean'];
    }

    public function course()
    {
        return $this->belongsTo(QuranLiveCourse::class, 'quran_live_course_id');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function students()
    {
        return $this->hasMany(QuranGroupStudent::class);
    }
    public function activeStudents()
    {
        return $this->students()->where('status', 'active')->with('user');
    }
    public function dailyLinks()
    {
        return $this->hasMany(QuranDailyLink::class);
    }
    public function assessments()
    {
        return $this->hasMany(QuranAssessment::class);
    }
    public function progressReports()
    {
        return $this->hasMany(QuranProgressReport::class);
    }

    public function todaysLink(): ?QuranDailyLink
    {
        return $this->dailyLinks()->where('class_date', now()->toDateString())->first();
    }

    public function isFull(): bool
    {
        return $this->students()->where('status', 'active')->count() >= $this->max_students;
    }
}
