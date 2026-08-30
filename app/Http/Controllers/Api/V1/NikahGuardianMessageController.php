<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NikahBlock;
use App\Models\NikahGuardianMessage;
use App\Models\NikahInterest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// JSON equivalent of NikahGuardianMessageController (web) — same
// authorization/gating (must be one of the two parties, interest already
// mutually accepted, neither side has blocked the other), just returning
// the thread as JSON instead of a Blade view.
class NikahGuardianMessageController extends Controller
{
    private function authorize(NikahInterest $interest): void
    {
        $myProfile = auth()->user()->nikahProfile;
        abort_unless($myProfile, 403);

        abort_unless(
            (int) $interest->sender_profile_id === (int) $myProfile->id ||
                (int) $interest->receiver_profile_id === (int) $myProfile->id,
            403
        );

        abort_unless($interest->status === 'accepted', 403, 'Messaging is only available after mutual acceptance.');

        abort_if(
            NikahBlock::existsBetween($interest->sender_profile_id, $interest->receiver_profile_id),
            403,
            'This conversation is no longer available.'
        );
    }

    public function index(NikahInterest $interest): JsonResponse
    {
        $this->authorize($interest);

        $messages = $interest->guardianMessages()->with('sender')->orderBy('created_at')->get();

        $interest->guardianMessages()
            ->where('sender_id', '!=', auth()->id())
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(fn (NikahGuardianMessage $m) => [
                'id' => $m->id,
                'message' => $m->message,
                'is_mine' => (int) $m->sender_id === (int) auth()->id(),
                'sender_name' => $m->sender?->name,
                'created_at' => $m->created_at->toIso8601String(),
            ]),
        ]);
    }

    public function store(Request $request, NikahInterest $interest): JsonResponse
    {
        $this->authorize($interest);

        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $myProfile = auth()->user()->nikahProfile;

        $message = NikahGuardianMessage::create([
            'nikah_interest_id' => $interest->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        $recipientProfileId = (int) $interest->sender_profile_id === (int) $myProfile->id
            ? $interest->receiver_profile_id
            : $interest->sender_profile_id;
        $recipient = \App\Models\NikahProfile::find($recipientProfileId)?->user;

        if ($recipient) {
            try {
                $recipient->notify(new \App\Notifications\NikahGuardianMessageReceived($message));
            } catch (\Throwable $e) {
                \Log::error('NikahGuardianMessageReceived notification failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'is_mine' => true,
                'sender_name' => auth()->user()->name,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
