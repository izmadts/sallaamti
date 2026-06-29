<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizAdminController extends Controller
{
    public function edit(Course $course)
    {
        $quiz = $course->quiz ?? null;
        return view('admin.quiz.edit', compact('course', 'quiz'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'passing_percentage' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $validated['course_id'] = $course->id;

        Quiz::updateOrCreate(['course_id' => $course->id], $validated);

        return redirect()->route('admin.courses.quiz.edit', $course)->with('status', 'Quiz settings saved.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question' => ['required', 'string'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string'],
            'correct_option' => ['required', 'integer'],
        ]);

        $validated['quiz_id'] = $quiz->id;
        $validated['order'] = $quiz->questions()->max('order') + 1;

        QuizQuestion::create($validated);

        return back()->with('status', 'Question added.');
    }

    public function destroyQuestion(QuizQuestion $question)
    {
        $quiz = $question->quiz;
        $question->delete();
        return redirect()->route('admin.courses.quiz.edit', $quiz->course)->with('status', 'Question removed.');
    }

    public function editLessonQuiz(\App\Models\Lesson $lesson)
    {
        $quiz = $lesson->quiz;
        return view('admin.quiz.edit-lesson', compact('lesson', 'quiz'));
    }

    public function storeLessonQuiz(Request $request, \App\Models\Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'passing_percentage' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $validated['lesson_id'] = $lesson->id;

        Quiz::updateOrCreate(['lesson_id' => $lesson->id], $validated);

        return redirect()->route('admin.lessons.quiz.edit', $lesson)->with('status', 'Lesson quiz settings saved.');
    }
}
