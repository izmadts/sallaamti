<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Surfaces the counselor's own Laravel notifications (already written to
// the `notifications` table by the `database` channel every counselor-
// relevant Notification class already uses — see FcmChannel's sibling
// channel entries) as a real in-app inbox, so the bell icon's badge means
// "you have unread notifications," not some unrelated dashboard stat.
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
