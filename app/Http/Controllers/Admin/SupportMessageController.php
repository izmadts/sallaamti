<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\User;
use App\Notifications\AdminMessageReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// A team inbox, not a per-admin one — any admin can see and reply to any
// member's conversation, same convention as the Lead Client chat.
class SupportMessageController extends Controller
{
    public function index(Request $request)
    {
        // One row per member who's ever messaged in, with their latest
        // message and an unread count — a conversation list, not a flat
        // message list. "Unread" here means a member's message the admin
        // team hasn't read yet — sender_id = user_id identifies those
        // (vs. an admin's own reply, which is written already-read).
        $threads = User::query()
            ->whereHas('adminMessages')
            ->with(['adminMessages' => fn ($q) => $q->latest()->limit(1)])
            ->withCount(['adminMessages as unread_count' => function ($q) {
                $q->where('is_read', false)
                    ->whereColumn('sender_id', 'admin_messages.user_id');
            }])
            ->get()
            ->sortByDesc(fn (User $u) => $u->adminMessages->first()?->created_at)
            ->values();

        return view('admin.support-messages.index', compact('threads'));
    }

    public function show(User $user)
    {
        $messages = AdminMessage::where('user_id', $user->id)->oldest()->with('sender')->get();

        AdminMessage::where('user_id', $user->id)
            ->where('sender_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.support-messages.show', compact('user', 'messages'));
    }

    public function reply(Request $request, User $user)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = AdminMessage::create([
            'user_id' => $user->id,
            'sender_id' => Auth::id(),
            'body' => $validated['body'],
            'is_read' => true,
        ]);

        try {
            $user->notify(new AdminMessageReplied($message));
        } catch (\Throwable $e) {
            \Log::error('AdminMessageReplied notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Reply sent.');
    }
}
