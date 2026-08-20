<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'category', 'track', 'level', 'min_age', 'max_age', 'thumbnail', 'is_published', 'created_by'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'min_age' => 'integer',
            'max_age' => 'integer',
        ];
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()->where('user_id', $user->id)->exists();
    }

    // Progress % for a given user
    public function progressFor(User $user): int
    {
        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) return 0;

        $completed = LessonProgress::whereIn('lesson_id', $this->lessons()->pluck('id'))
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();

        return (int) round(($completed / $totalLessons) * 100);
    }

    public function completedLessonsCountFor(User $user): int
    {
        return LessonProgress::whereIn('lesson_id', $this->lessons()->pluck('id'))
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function allLessonsCompletedAndPassedBy(User $user): bool
    {
        foreach ($this->lessons as $lesson) {
            // Lesson itself must be marked complete
            if (!$lesson->isCompletedBy($user)) {
                return false;
            }

            // If the lesson has its own quiz, it must be passed
            if ($lesson->quiz && !$lesson->quiz->isPassedBy($user)) {
                return false;
            }
        }

        return true;
    }

    public function isCertificateEligibleFor(User $user): bool
    {
        // Must have completed all lessons + lesson quizzes
        if (!$this->allLessonsCompletedAndPassedBy($user)) {
            return false;
        }

        // If course has a final quiz, must have passed it
        if ($this->quiz && !$this->quiz->isPassedBy($user)) {
            return false;
        }

        return true;
    }
}
