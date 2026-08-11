<?php

namespace App\Http\Controllers;

use App\Models\NikahBlock;
use App\Models\NikahInterest;
use App\Models\NikahProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class NikahInterestController extends Controller
{
    /** Blocks mass-sending interest requests (spam/harassment) across the browse grid. */
    private function guardAgainstBruteForce(string $key): void
    {
        abort_if(
            RateLimiter::tooManyAttempts($key, 20),
            429,
            'Too many interest requests sent. Please wait before sending more.'
        );
        RateLimiter::hit($key, 3600);
    }

    public function send(NikahProfile $profile)
    {
        $myProfile = Auth::user()->nikahProfile;

        if (!$myProfile) {
            return redirect()->route('nikah.create');
        }

        $this->guardAgainstBruteForce('nikah-interest-send:' . Auth::id());

        abort_if(NikahBlock::existsBetween($myProfile->id, $profile->id), 403);

        $wasDeclined = NikahInterest::where('sender_profile_id', $myProfile->id)
            ->where('receiver_profile_id', $profile->id)
            ->where('status', 'declined')
            ->exists();

        if ($wasDeclined) {
            return back()->with('status', 'This person already declined your interest — you can\'t resend it.');
        }

        $interest = NikahInterest::firstOrCreate([
            'sender_profile_id' => $myProfile->id,
            'receiver_profile_id' => $profile->id,
        ]);

        try {
            $profile->user->notify(new \App\Notifications\NikahInterestReceived($interest));
        } catch (\Throwable $e) {
            \Log::error('NikahInterestReceived notification failed: ' . $e->getMessage());
        }

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
        abort_unless($myProfile && $interest->receiver_profile_id === $myProfile->id, 403);

        $interest->update(['status' => 'accepted', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new \App\Notifications\NikahInterestAccepted($interest));
        } catch (\Throwable $e) {
            \Log::error('NikahInterestAccepted notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Interest accepted! Contact details are now visible to both sides.');
    }

    public function decline(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile && $interest->receiver_profile_id === $myProfile->id, 403);

        $interest->update(['status' => 'declined', 'responded_at' => now()]);

        try {
            $interest->sender->user->notify(new \App\Notifications\NikahInterestDeclined($interest));
        } catch (\Throwable $e) {
            \Log::error('NikahInterestDeclined notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Interest declined.');
    }
    
}
