<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\MatchmakerApplication;
use App\Models\TrainingQuizAttempt;
use App\Models\TrainingQuizQuestion;
use App\Notifications\TrainingAssessmentPassed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Trainee-access addendum (15/16 Nov 2026). Deliberately NOT behind
// EnsureFeatureUnlocked — a trainee takes this to earn Assessed, but
// anyone (already Assessed/Probation/Certified included) can re-read the
// guide and see their own past scores any time, per the owner's explicit
// ask. Guide content itself is client-side (Flutter's TrainingGuideContent,
// same convention as the existing /guide screen) — only the quiz needs a
// backend, since grading must be server-authoritative.
class TrainingQuizController extends Controller
{
    private const PASS_THRESHOLD_PERCENT = 70;

    public function questions(): JsonResponse
    {
        $questions = TrainingQuizQuestion::orderBy('sort_order')->get()->map(fn ($q) => [
            'id' => $q->id,
            'question_en' => $q->question_en,
            'question_ur' => $q->question_ur,
            'options' => collect($q->options)->map(fn ($o, $i) => ['index' => $i, 'text_en' => $o['text_en'], 'text_ur' => $o['text_ur']])->values(),
        ]);

        return response()->json(['questions' => $questions]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'integer', 'exists:training_quiz_questions,id'],
            'answers.*.selected_index' => ['required', 'integer', 'min:0'],
        ]);

        $application = MatchmakerApplication::where('user_id', auth()->id())->latest()->first();
        abort_unless($application, 422, 'No counselor application found for this account.');

        $questions = TrainingQuizQuestion::whereIn('id', collect($validated['answers'])->pluck('question_id'))->get()->keyBy('id');

        $score = 0;
        foreach ($validated['answers'] as $answer) {
            $question = $questions->get($answer['question_id']);
            if ($question && (int) $question->correct_index === (int) $answer['selected_index']) {
                $score++;
            }
        }

        $total = count($validated['answers']);
        $passed = $total > 0 && ($score / $total) * 100 >= self::PASS_THRESHOLD_PERCENT;

        $attempt = TrainingQuizAttempt::create([
            'matchmaker_application_id' => $application->id,
            'score' => $score,
            'total' => $total,
            'passed' => $passed,
            'answers' => $validated['answers'],
        ]);

        $promoted = false;
        if ($passed && $application->status === 'training') {
            $application->update(['status' => 'assessed']);
            $promoted = true;

            try {
                $application->user->notify(new TrainingAssessmentPassed($application, $score, $total));
            } catch (\Throwable $e) {
                \Log::error('TrainingAssessmentPassed notification failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'score' => $score,
            'total' => $total,
            'passed' => $passed,
            'promoted' => $promoted,
            'attempt_id' => $attempt->id,
        ]);
    }

    public function attempts(): JsonResponse
    {
        $application = MatchmakerApplication::where('user_id', auth()->id())->latest()->first();

        if (!$application) {
            return response()->json(['attempts' => []]);
        }

        $attempts = TrainingQuizAttempt::where('matchmaker_application_id', $application->id)
            ->latest('created_at')
            ->get(['id', 'score', 'total', 'passed', 'created_at']);

        return response()->json(['attempts' => $attempts]);
    }
}
