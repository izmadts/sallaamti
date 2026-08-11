<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /** Minimum time a lesson must be open before it can be marked complete. */
    private const MIN_SECONDS_BEFORE_COMPLETE = 20;

    public function show(Lesson $lesson)
    {
        $course = $lesson->course;
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $lessons = $course->lessons; // for sidebar navigation
        $isCompleted = $lesson->isCompletedBy(Auth::user());

        // Record when the learner first opened this lesson — firstOrCreate
        // only sets started_at on creation, so re-visiting doesn't reset it.
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'lesson_id' => $lesson->id],
            ['started_at' => now()]
        );
        $secondsRemaining = $isCompleted ? 0 : max(0, self::MIN_SECONDS_BEFORE_COMPLETE - now()->diffInSeconds($progress->started_at ?? now()));

        return view('lessons.show', compact('lesson', 'course', 'lessons', 'isCompleted', 'secondsRemaining'));
    }

    public function complete(Lesson $lesson)
    {
        abort_unless($lesson->course->isEnrolledBy(Auth::user()), 403);

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'lesson_id' => $lesson->id],
            ['started_at' => now()]
        );

        $secondsOpen = now()->diffInSeconds($progress->started_at ?? now());
        if ($secondsOpen < self::MIN_SECONDS_BEFORE_COMPLETE) {
            return back()->with('error', 'Please spend a bit more time reviewing the lesson before marking it complete.');
        }

        $progress->update(['completed_at' => now()]);

        return back()->with('status', 'Lesson marked complete!');
    }
}
