<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

// One place that knows how a quiz is scored and what counts as a pass.
// Four callers grade the same way — the web's course quiz and lesson quiz
// (QuizController) and the app's two equivalents (Api\V1\LearningController) —
// so the marking rule lives here rather than being copy-pasted into each.
// Any change to scoring (partial credit, negative marking, attempt limits)
// then lands in web and mobile together instead of drifting apart.
class QuizGrader
{
    /** Max submissions per quiz per user per hour — blocks brute-forcing answers. */
    private const MAX_ATTEMPTS_PER_HOUR = 10;

    /**
     * Marks $answers (keyed by question id, valued by chosen option index)
     * against $quiz and records the attempt.
     */
    public static function grade(Quiz $quiz, array $answers, User $user): QuizAttempt
    {
        $questions = $quiz->questions;

        $correctCount = 0;
        foreach ($questions as $question) {
            $submitted = $answers[$question->id] ?? null;
            if ($submitted !== null && (int) $submitted === $question->correct_option) {
                $correctCount++;
            }
        }

        $scorePercentage = $questions->count() > 0 ? (int) round(($correctCount / $questions->count()) * 100) : 0;

        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'answers' => $answers,
            'score_percentage' => $scorePercentage,
            'passed' => $scorePercentage >= $quiz->passing_percentage,
        ]);
    }

    /**
     * Aborts with 429 once this user has burned through their hourly
     * submissions for this quiz. Call before grade().
     */
    public static function guardAgainstBruteForce(Quiz $quiz, User $user): void
    {
        $key = "quiz-submit:{$quiz->id}:{$user->id}";

        abort_if(
            RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS_PER_HOUR),
            429,
            'Too many attempts. Please wait before trying this quiz again.'
        );

        RateLimiter::hit($key, 3600);
    }

    /** The message shown after an attempt — identical wording on web and mobile. */
    public static function resultMessage(QuizAttempt $attempt, Quiz $quiz): string
    {
        return $attempt->passed
            ? "Congratulations! You scored {$attempt->score_percentage}% and passed."
            : "You scored {$attempt->score_percentage}%. You need {$quiz->passing_percentage}% to pass — try again.";
    }
}
