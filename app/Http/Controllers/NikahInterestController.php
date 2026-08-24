<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\SendsNikahInterest;
use App\Models\NikahInterest;
use App\Models\NikahProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class NikahInterestController extends Controller
{
    use SendsNikahInterest;

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

        $result = $this->sendInterestBetweenProfiles($myProfile, $profile);

        return match ($result['error']) {
            'not_eligible' => back()->with('error', 'Your profile must be fully verified and paid before you can send interest to others.'),
            'blocked' => abort(403),
            'previously_declined' => back()->with('status', 'This person already declined your interest — you can\'t resend it.'),
            default => back()->with('status', 'Interest sent! You will be notified if accepted.'),
        };
    }

    public function index()
    {
        $myProfile = Auth::user()->nikahProfile;

        // QA finding: this is reachable from the dashboard's generic
        // Notifications card (covers every module's unread notifications,
        // not just Nikah interests) — a member with no Nikah profile yet
        // clicking it crashed on receivedInterests() on null. Every sibling
        // method here already guards this; this one didn't.
        if (!$myProfile) {
            return redirect()->route('nikah.create')->with('status', 'Create your Nikah profile to send and receive interests.');
        }

        $received = $myProfile->receivedInterests()->with('sender.user')->latest()->get();
        $sent = $myProfile->sentInterests()->with('receiver.user')->latest()->get();

        return view('nikah.interests', compact('received', 'sent'));
    }

    public function accept(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile && $interest->receiver_profile_id === $myProfile->id, 403);

        if (!$myProfile->canInteract()) {
            return back()->with('error', 'Your profile must be fully verified and paid before you can accept interests.');
        }

        $this->acceptNikahInterest($interest);

        return back()->with('status', 'Interest accepted! Contact details are now visible to both sides.');
    }

    public function decline(NikahInterest $interest)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile && $interest->receiver_profile_id === $myProfile->id, 403);

        $this->declineNikahInterest($interest);

        return back()->with('status', 'Interest declined.');
    }
}
