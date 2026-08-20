<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Security audit finding: an OAuth login that matches an existing account
// by email links it silently — if the account owner didn't just do that
// themselves, this is the only signal they'd ever get. Sent every time,
// not just the first time, since a repeat unexpected link is just as worth
// flagging as the first.
class SocialProviderLinked extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $provider) {}

    public function build()
    {
        return $this
            ->subject('A new sign-in method was linked to your Sallaamti account')
            ->view('emails.auth.provider-linked');
    }
}
