<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['course_id', 'lesson_id', 'title', 'passing_percentage'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function bestAttemptFor(User $user): ?QuizAttempt
    {
        return $this->attempts()->where('user_id', $user->id)->orderByDesc('score_percentage')->first();
    }

    public function isPassedBy(User $user): bool
    {
        return $this->attempts()->where('user_id', $user->id)->where('passed', true)->exists();
    }
}
