<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QueryResponse;
use App\Models\SupportQuery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportQueryAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportQuery::with(['user', 'assignedTo'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $queries = $query->paginate(15)->withQueryString();
        $counselors = User::role('counselor')->get();

        $stats = [
            'new'         => SupportQuery::where('status', 'new')->count(),
            'assigned'    => SupportQuery::where('status', 'assigned')->count(),
            'in_progress' => SupportQuery::where('status', 'in_progress')->count(),
            'resolved'    => SupportQuery::where('status', 'resolved')->count(),
        ];

        return view('admin.support.index', compact('queries', 'counselors', 'stats'));
    }

    public function show(SupportQuery $query)
    {
        $query->load(['user', 'assignedTo', 'responses.responder']);
        $counselors = User::role('counselor')->get();
        return view('admin.support.show', compact('query', 'counselors'));
    }

    public function assign(Request $request, SupportQuery $query)
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);

        $query->update([
            'assigned_to' => $request->assigned_to,
            'status'      => 'assigned',
        ]);

        $counselor = User::find($request->assigned_to);
        $counselor->notify(new \App\Notifications\SupportQueryAssigned($query));
        $query->user->notify(new \App\Notifications\SupportQueryStatusChanged($query, 'assigned'));

        return back()->with('status', "Query assigned to {$counselor->name}.");
    }

    public function updateStatus(Request $request, SupportQuery $query)
    {
        $request->validate(['status' => 'required|in:new,assigned,in_progress,resolved,closed']);
        $query->update(['status' => $request->status]);
        $query->user->notify(new \App\Notifications\SupportQueryStatusChanged($query, $request->status));
        return back()->with('status', 'Status updated.');
    }

    public function reply(Request $request, SupportQuery $query)
    {
        $request->validate([
            'message'     => ['required', 'string', 'min:5', 'max:5000'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        QueryResponse::create([
            'support_query_id' => $query->id,
            'responder_id'     => Auth::id(),
            'message'          => $request->message,
            'is_internal'      => $request->has('is_internal'),
        ]);

        if (!$request->has('is_internal')) {
            if ($query->status === 'assigned') {
                $query->update(['status' => 'in_progress']);
            }
            $query->user->notify(new \App\Notifications\SupportQueryReplied($query));
        }

        return back()->with('status', 'Response sent.');
    }

    public function destroy(SupportQuery $query)
    {
        $query->delete();

        return redirect()->route('admin.support.index')->with('status', 'Support query deleted.');
    }
}
