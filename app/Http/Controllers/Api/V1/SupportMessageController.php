<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\User;
use App\Notifications\NewAdminMessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// A member's own support conversation with the Sallaamti team — polled by
// the app rather than pushed over a websocket (no persistent process
// available on this host; see the 90-second video-length-cap decision for
// the same hosting-constraint reasoning). A few seconds' lag is an
// acceptable trade for needing zero extra infrastructure.
class SupportMessageController extends Controller
{
    private function payload(AdminMessage $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'is_mine' => $message->isFromMember(),
            'created_at' => $message->created_at->toIso8601String(),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $messages = AdminMessage::where('user_id', $user->id)->oldest()->get();

        // Mark admin's replies read now that the member has fetched them —
        // same "read on open" convention as NikahGuardianMessageController.
        AdminMessage::where('user_id', $user->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(fn (AdminMessage $m) => $this->payload($m)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $isFirstMessage = !AdminMessage::where('user_id', $user->id)->exists();

        $message = AdminMessage::create([
            'user_id' => $user->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        User::role('admin')->each(function (User $admin) use ($message, $isFirstMessage) {
            try {
                $admin->notify(new NewAdminMessageReceived($message, $isFirstMessage));
            } catch (\Throwable $e) {
                \Log::error('NewAdminMessageReceived notification failed: ' . $e->getMessage());
            }
        });

        return response()->json(['message' => $this->payload($message)], 201);
    }
}
