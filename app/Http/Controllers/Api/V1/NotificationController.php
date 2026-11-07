<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// A member's own in-app notification inbox, mirroring
// Api\V1\Matchmaker\NotificationController's pattern — same underlying
// `notifications` table every Nikah notification already writes to via
// the 'database' channel, just scoped to whichever member is asking.
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return response()->json([
            'notifications' => $notifications->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->data['type'] ?? null,
                'message' => $n->data['message'] ?? null,
                'url' => $n->data['url'] ?? null,
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at->toIso8601String(),
                // The full raw row — a live push always carries its complete
                // data payload (post_id/comment_id/interest_id/lead_id/etc.),
                // but tapping the SAME notification later from this list only
                // ever had type/url to go on. Several of the app's resolver
                // branches need one of these extra ids to build the exact
                // deep link, so this is what lets a list-tap resolve
                // identically to a push-tap instead of falling back to a
                // generic screen.
                'data' => $n->data,
            ]),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json(['unread_count' => $request->user()->unreadNotifications()->count()]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }
}
