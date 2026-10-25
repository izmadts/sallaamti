<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\PaginatesApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\User;
use App\Notifications\NewQuestionSubmitted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// "Ap k Sawalat" — members ask, admin answers. A question is only public
// once answered, and only as its canonical (root) copy — a duplicate that
// admin links to an existing answer never appears as a second entry.
class QuestionController extends Controller
{
    use PaginatesApiResponses;

    private function payload(Question $question, ?int $viewerId = null): array
    {
        $isMine = $viewerId !== null && (int) $question->user_id === $viewerId;

        return [
            'id' => $question->id,
            'question' => $question->question,
            'answer' => $question->effectiveAnswer(),
            'status' => $question->status,
            'answered_at' => $question->effectiveAnsweredAt()?->toIso8601String(),
            'created_at' => $question->created_at?->toIso8601String(),
            ...$isMine ? ['is_mine' => true] : [],
        ];
    }

    /** Public browse — answered, canonical questions only. */
    public function index(Request $request): JsonResponse
    {
        $questions = Question::publiclyVisible()->latest('answered_at')->paginate(15);

        return response()->json($this->paginated($questions, 'questions', fn (Question $q) => $this->payload($q)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        $question = Question::create([
            'user_id' => $request->user()->id,
            'question' => $validated['question'],
        ]);

        User::role('admin')->each(function ($admin) use ($question) {
            try {
                $admin->notify(new NewQuestionSubmitted($question));
            } catch (\Throwable $e) {
                \Log::error('NewQuestionSubmitted notification failed: ' . $e->getMessage());
            }
        });

        return response()->json([
            'question' => $this->payload($question, $request->user()->id),
            'message' => __('db.Thanks — we\'ll notify you once it\'s answered.'),
        ], 201);
    }

    /** The member's own questions across every status. */
    public function mine(Request $request): JsonResponse
    {
        $questions = Question::where('user_id', $request->user()->id)->latest()->paginate(15);

        return response()->json($this->paginated($questions, 'questions', fn (Question $q) => $this->payload($q, $request->user()->id)));
    }
}
