<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\QuizGrader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Scoring, the pass threshold and the attempt rate limit all live in
// QuizGrader — shared with the app's Api\V1\LearningController so a learner
// gets marked the same way whichever surface they sit the quiz on.
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
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $quiz = $course->quiz;
        abort_unless($quiz, 404);

        QuizGrader::guardAgainstBruteForce($quiz, Auth::user());
        $attempt = QuizGrader::grade($quiz, $request->input('answers', []), Auth::user());

        return redirect()->route('quiz.show', $course)->with('status', QuizGrader::resultMessage($attempt, $quiz));
    }

    public function showLessonQuiz(Lesson $lesson)
    {
        $course = $lesson->course;
        abort_unless($course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $quiz = $lesson->quiz;
        abort_unless($quiz, 404, 'No quiz available for this lesson.');

        $quiz->load('questions');
        $bestAttempt = $quiz->bestAttemptFor(Auth::user());

        return view('quiz.show-lesson', compact('lesson', 'course', 'quiz', 'bestAttempt'));
    }

    public function submitLessonQuiz(Request $request, Lesson $lesson)
    {
        abort_unless($lesson->course->isEnrolledBy(Auth::user()), 403, 'Enroll in this course first.');

        $quiz = $lesson->quiz;
        abort_unless($quiz, 404);

        QuizGrader::guardAgainstBruteForce($quiz, Auth::user());
        $attempt = QuizGrader::grade($quiz, $request->input('answers', []), Auth::user());

        // Passing the lesson's own quiz proves the lesson was worked through,
        // so it counts as completed without a second explicit tap.
        if ($attempt->passed) {
            LessonProgress::updateOrCreate(
                ['user_id' => Auth::id(), 'lesson_id' => $lesson->id],
                ['completed_at' => now()]
            );
        }

        return redirect()->route('lesson.quiz.show', $lesson)->with('status', QuizGrader::resultMessage($attempt, $quiz));
    }
}
