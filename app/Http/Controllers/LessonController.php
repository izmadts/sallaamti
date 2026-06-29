<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $course = $lesson->course;
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $lessons = $course->lessons; // for sidebar navigation
        $isCompleted = $lesson->isCompletedBy(Auth::user());

        return view('lessons.show', compact('lesson', 'course', 'lessons', 'isCompleted'));
    }

    public function complete(Lesson $lesson)
    {
        abort_unless($lesson->course->isEnrolledBy(Auth::user()), 403);

        LessonProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        return back()->with('status', 'Lesson marked complete!');
    }
}
