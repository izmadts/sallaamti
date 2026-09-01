<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;

// The minimum-dwell-time rule lives on the Lesson model (see
// Lesson::MIN_SECONDS_BEFORE_COMPLETE and secondsBeforeCompleteFor), shared
// with the app's Api\V1\LearningController so both surfaces gate completion
// identically.
class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $course = $lesson->course;
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $lessons = $course->lessons; // for sidebar navigation
        $isCompleted = $lesson->isCompletedBy(Auth::user());
        $secondsRemaining = $lesson->secondsBeforeCompleteFor(Auth::user());

        return view('lessons.show', compact('lesson', 'course', 'lessons', 'isCompleted', 'secondsRemaining'));
    }

    public function complete(Lesson $lesson)
    {
        abort_unless($lesson->course->isEnrolledBy(Auth::user()), 403);

        $progress = $lesson->startProgressFor(Auth::user());

        if ($lesson->secondsBeforeCompleteFor(Auth::user()) > 0) {
            return back()->with('error', 'Please spend a bit more time reviewing the lesson before marking it complete.');
        }

        $progress->update(['completed_at' => now()]);

        return back()->with('status', 'Lesson marked complete!');
    }
}
