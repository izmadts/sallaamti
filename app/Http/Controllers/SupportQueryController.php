<?php

namespace App\Http\Controllers;

use App\Models\QueryResponse;
use App\Models\SupportQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportQueryController extends Controller
{
    public function index()
    {
        $queries = SupportQuery::where('user_id', Auth::id())
            ->latest()->paginate(10);
        return view('support.index', compact('queries'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'     => ['required', 'in:marital,parenting,financial,legal,spiritual,other'],
            'subject'      => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string', 'min:20', 'max:5000'],
            'is_anonymous' => ['nullable', 'boolean'],
            'priority'     => ['nullable', 'in:low,medium,high'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_anonymous'] = $request->has('is_anonymous');

        $query = SupportQuery::create($validated);

        // Notify admins
        \App\Models\User::role('admin')->each(function ($admin) use ($query) {
            $admin->notify(new \App\Notifications\NewSupportQuery($query));
        });

        return redirect()->route('support.show', $query)
            ->with('status', 'Your query has been submitted. Our team will respond soon, in sha Allah.');
    }

    public function show(SupportQuery $query)
    {
        abort_unless($query->user_id === Auth::id(), 403);
        $query->load(['responses.responder', 'assignedTo']);
        return view('support.show', compact('query'));
    }

    public function reply(Request $request, SupportQuery $query)
    {
        abort_unless($query->user_id === Auth::id(), 403);
        abort_unless(in_array($query->status, ['assigned', 'in_progress']), 403, 'Query is not open for replies.');

        $request->validate(['message' => ['required', 'string', 'min:5', 'max:2000']]);

        QueryResponse::create([
            'support_query_id' => $query->id,
            'responder_id'     => Auth::id(),
            'message'          => $request->message,
            'is_internal'      => false,
        ]);

        if ($query->assignedTo) {
            $query->assignedTo->notify(new \App\Notifications\SupportQueryReplied($query));
        }

        return back()->with('status', 'Reply sent.');
    }
}
