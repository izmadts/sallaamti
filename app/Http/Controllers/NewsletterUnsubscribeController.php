<?php

namespace App\Http\Controllers;

use App\Models\User;

class NewsletterUnsubscribeController extends Controller
{
    // Reached from a signed link in every bulk-email footer — no login
    // required, since the person clicking it may not even remember their
    // password, and forcing a login here would just mean the email keeps
    // arriving.
    public function unsubscribe(User $user)
    {
        $user->update(['newsletter_opt_out' => true]);

        return view('newsletter-unsubscribed', ['user' => $user]);
    }
}
