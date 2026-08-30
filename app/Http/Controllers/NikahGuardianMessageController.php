<?php

namespace App\Http\Controllers;

use App\Models\NikahBlock;
use App\Models\NikahGuardianMessage;
use App\Models\NikahInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NikahGuardianMessageController extends Controller
{
    private function assertNotBlocked(NikahInterest $interest): void
    {
        abort_if(
            NikahBlock::existsBetween($interest->sender_profile_id, $interest->receiver_profile_id),
            403,
            'This conversation is no longer available.'
        );
    }

    public function show(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);

        abort_unless(
            (int) $interest->sender_profile_id === (int) $myProfile->id ||
                (int) $interest->receiver_profile_id === (int) $myProfile->id,
            403
        );

        abort_unless($interest->status === 'accepted', 403, 'Messaging is only available after mutual acceptance.');
        $this->assertNotBlocked($interest);

        $messages = $interest->guardianMessages()->with('sender')->orderBy('created_at')->get();

        // Mark messages from other party as read
        $interest->guardianMessages()
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        $otherProfileId = (int) $interest->sender_profile_id === (int) $myProfile->id
            ? $interest->receiver_profile_id
            : $interest->sender_profile_id;

        return view('nikah.messages', compact('interest', 'messages', 'otherProfileId'));
    }

    public function store(Request $request, NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);

        abort_unless(
            (int) $interest->sender_profile_id === (int) $myProfile->id ||
                (int) $interest->receiver_profile_id === (int) $myProfile->id,
            403
        );

        abort_unless($interest->status === 'accepted', 403);
        $this->assertNotBlocked($interest);

        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $message = NikahGuardianMessage::create([
            'nikah_interest_id' => $interest->id,
            'sender_id' => Auth::id(),
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

        return back()->with('status', 'Message sent.');
    }
}
