<?php

namespace App\Http\Controllers;

use App\Models\NikahGuardianMessage;
use App\Models\NikahInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NikahGuardianMessageController extends Controller
{
    public function show(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);

        abort_unless(
            $interest->sender_profile_id === $myProfile->id ||
                $interest->receiver_profile_id === $myProfile->id,
            403
        );

        abort_unless($interest->status === 'accepted', 403, 'Messaging is only available after mutual acceptance.');

        $messages = $interest->guardianMessages()->with('sender')->orderBy('created_at')->get();

        // Mark messages from other party as read
        $interest->guardianMessages()
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return view('nikah.messages', compact('interest', 'messages'));
    }

    public function store(Request $request, NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);

        abort_unless(
            $interest->sender_profile_id === $myProfile->id ||
                $interest->receiver_profile_id === $myProfile->id,
            403
        );

        abort_unless($interest->status === 'accepted', 403);

        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        NikahGuardianMessage::create([
            'nikah_interest_id' => $interest->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return back()->with('status', 'Message sent.');
    }
}
