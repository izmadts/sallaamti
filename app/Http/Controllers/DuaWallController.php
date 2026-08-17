<?php

namespace App\Http\Controllers;

use App\Models\DuaReaction;
use App\Models\DuaRequest;
use App\Notifications\DuaReceivedAmeen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DuaWallController extends Controller
{
    // Guest-readable (a good top-of-funnel page — "see what our community is
    // praying for"), but posting/reacting requires login, same as every
    // other social action on the site.
    public function index(Request $request)
    {
        $query = DuaRequest::where('status', 'approved')
            ->orderByDesc('created_at')
            ->with(['user', 'reactions']);

        $page = $request->get('page', 1);
        $perPage = 10;
        $duas = $query->get();

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $duas->forPage($page, $perPage),
            $duas->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dua-wall.partials.dua-cards', ['paginated' => $paginated])->render(),
                'has_more' => $paginated->hasMorePages(),
            ]);
        }

        return view('dua-wall.index', ['paginated' => $paginated]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        DuaRequest::create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending',
        ]);

        return back()->with('status', "Your dua has been submitted and will appear once our team reviews it. JazakAllah Khair for sharing.");
    }

    public function react(DuaRequest $duaRequest)
    {
        abort_unless($duaRequest->status === 'approved', 404);

        $existing = DuaReaction::where('dua_request_id', $duaRequest->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            DuaReaction::create([
                'dua_request_id' => $duaRequest->id,
                'user_id' => Auth::id(),
            ]);

            if ($duaRequest->user_id !== Auth::id()) {
                try {
                    $duaRequest->user->notify(new DuaReceivedAmeen($duaRequest, Auth::user()));
                } catch (\Throwable $e) {
                    \Log::error('DuaReceivedAmeen notification failed: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'reacted' => !$existing,
            'count' => $duaRequest->reactions()->count(),
        ]);
    }
}
