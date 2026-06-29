<?php

namespace App\Http\Controllers;

use App\Models\NikahInterest;
use App\Models\NikahProfile;
use Illuminate\Support\Facades\Auth;

class NikahInterestController extends Controller
{
    public function send(NikahProfile $profile)
    {
        $myProfile = Auth::user()->nikahProfile;

        if (!$myProfile) {
            return redirect()->route('nikah.create');
        }

        $interest = NikahInterest::firstOrCreate([
            'sender_profile_id' => $myProfile->id,
            'receiver_profile_id' => $profile->id,
        ]);

        $profile->user->notify(new \App\Notifications\NikahInterestReceived($interest));

        return back()->with('status', 'Interest sent! You will be notified if accepted.');
    }

    public function index()
    {
        $myProfile = Auth::user()->nikahProfile;

        $received = $myProfile->receivedInterests()->with('sender.user')->latest()->get();
        $sent = $myProfile->sentInterests()->with('receiver.user')->latest()->get();

        return view('nikah.interests', compact('received', 'sent'));
    }

    public function accept(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($interest->receiver_profile_id === $myProfile->id, 403);

        $interest->update(['status' => 'accepted', 'responded_at' => now()]);

        $interest->sender->user->notify(new \App\Notifications\NikahInterestAccepted($interest));

        return back()->with('status', 'Interest accepted! Contact details are now visible to both sides.');
    }

    public function decline(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($interest->receiver_profile_id === $myProfile->id, 403);

        $interest->update(['status' => 'declined', 'responded_at' => now()]);

        return back()->with('status', 'Interest declined.');
    }
    
}
