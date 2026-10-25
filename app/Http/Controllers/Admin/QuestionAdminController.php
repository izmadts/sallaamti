<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Notifications\QuestionAnswered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionAdminController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Question::count(),
            'pending' => Question::pending()->count(),
            'answered' => Question::answered()->count(),
        ];

        $status = $request->get('status', 'pending');
        $query = Question::with(['asker', 'canonical'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $questions = $query->paginate(20)->withQueryString();

        // For the "link to existing" picker — every already-answered,
        // non-duplicate question, so admin can attach a new duplicate to it.
        $answerable = Question::publiclyVisible()->orderByDesc('answered_at')->limit(200)->get();

        return view('admin.questions.index', compact('questions', 'stats', 'status', 'answerable'));
    }

    public function answer(Request $request, Question $question)
    {
        $request->validate(['answer' => ['required', 'string', 'max:5000']]);

        $question->update([
            'answer' => $request->answer,
            'answered_by' => Auth::id(),
            'answered_at' => now(),
            'status' => 'answered',
            'canonical_question_id' => null,
        ]);

        $question->asker->notify(new QuestionAnswered($question));

        return back()->with('status', 'Question answered and is now public.');
    }

    public function linkToExisting(Request $request, Question $question)
    {
        $validated = $request->validate([
            'canonical_question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        abort_if((int) $validated['canonical_question_id'] === $question->id, 422, 'A question can\'t be linked to itself.');

        $canonical = Question::publiclyVisible()->findOrFail($validated['canonical_question_id']);

        $question->update([
            'canonical_question_id' => $canonical->id,
            'status' => 'answered',
            'answered_by' => Auth::id(),
            'answered_at' => now(),
        ]);

        $question->asker->notify(new QuestionAnswered($question));

        return back()->with('status', 'Linked to the existing answer.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return back()->with('status', 'Question deleted.');
    }
}
