<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show(Course $course)
    {
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $quiz = $course->quiz;
        abort_unless($quiz, 404, 'No quiz available for this course.');

        abort_unless($course->allLessonsCompletedAndPassedBy(Auth::user()), 403, 'Complete all lessons and pass their quizzes first.');

        $quiz->load('questions');
        $bestAttempt = $quiz->bestAttemptFor(Auth::user());

        return view('quiz.show', compact('course', 'quiz', 'bestAttempt'));
    }

    public function submit(Request $request, Course $course)
    {
        $quiz = $course->quiz;
        abort_unless($quiz, 404);

        $questions = $quiz->questions;
        $answers = $request->input('answers', []);

        $correctCount = 0;
        foreach ($questions as $question) {
            $submitted = $answers[$question->id] ?? null;
            if ($submitted !== null && (int) $submitted === $question->correct_option) {
                $correctCount++;
            }
        }

        $scorePercentage = $questions->count() > 0 ? (int) round(($correctCount / $questions->count()) * 100) : 0;
        $passed = $scorePercentage >= $quiz->passing_percentage;

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'answers' => $answers,
            'score_percentage' => $scorePercentage,
            'passed' => $passed,
        ]);

        return redirect()->route('quiz.show', $course)->with('status', $passed
            ? "Congratulations! You scored {$scorePercentage}% and passed."
            : "You scored {$scorePercentage}%. You need {$quiz->passing_percentage}% to pass — try again.");
    }
    public function showLessonQuiz(\App\Models\Lesson $lesson)
    {
        $course = $lesson->course;
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $quiz = $lesson->quiz;
        abort_unless($quiz, 404, 'No quiz available for this lesson.');

        $quiz->load('questions');
        $bestAttempt = $quiz->bestAttemptFor(Auth::user());

        return view('quiz.show-lesson', compact('lesson', 'course', 'quiz', 'bestAttempt'));
    }

    public function submitLessonQuiz(Request $request, \App\Models\Lesson $lesson)
    {
        $quiz = $lesson->quiz;
        abort_unless($quiz, 404);

        $questions = $quiz->questions;
        $answers = $request->input('answers', []);

        $correctCount = 0;
        foreach ($questions as $question) {
            $submitted = $answers[$question->id] ?? null;
            if ($submitted !== null && (int) $submitted === $question->correct_option) {
                $correctCount++;
            }
        }

        $scorePercentage = $questions->count() > 0 ? (int) round(($correctCount / $questions->count()) * 100) : 0;
        $passed = $scorePercentage >= $quiz->passing_percentage;

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'answers' => $answers,
            'score_percentage' => $scorePercentage,
            'passed' => $passed,
        ]);

        // Optional: auto-mark lesson complete if quiz passed
        if ($passed) {
            \App\Models\LessonProgress::updateOrCreate(
                ['user_id' => Auth::id(), 'lesson_id' => $lesson->id],
                ['completed_at' => now()]
            );
        }

        return redirect()->route('lesson.quiz.show', $lesson)->with('status', $passed
            ? "Congratulations! You scored {$scorePercentage}% and passed."
            : "You scored {$scorePercentage}%. You need {$quiz->passing_percentage}% to pass — try again.");
    }
}
