<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterSubscribed;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = Subscriber::firstOrCreate(
            ['email' => $request->email],
            [
                'verification_token' => Str::random(64),
                'is_active'          => true,
            ]
        );

        if ($subscriber->wasRecentlyCreated) {

            try {
                Mail::to($subscriber->email)->send(new NewsletterSubscribed($subscriber));
            } catch (\Throwable $e) {
                \Log::error('Newsletter subscription email failed: ' . $e->getMessage());
            }

            return back()->with('success', 'Please check your email to verify your subscription.');
        }

        return back()->with('info', 'This email is already subscribed.');
    }

    public function verify(string $token)
    {
        $subscriber = Subscriber::where('verification_token', $token)->firstOrFail();

        $subscriber->update([
            'verified_at' => now(),
            'verification_token' => null,
        ]);

        return redirect('/')
            ->with('success', 'Your newsletter subscription has been verified.');
    }
}
