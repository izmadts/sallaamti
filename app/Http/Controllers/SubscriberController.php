<?php
namespace App\Http\Controllers;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: real visitors never see or fill this field, bots that
        // auto-fill every input do. Pretend success so the bot doesn't
        // learn to skip the field next time.
        if ($request->filled('website')) {
            return response()->json([
                'success' => true,
                'message' => 'Please check your email to verify subscription.'
            ]);
        }

        $request->validate([
            'email' => 'required|email|max:255'
        ]);
        $subscriber = Subscriber::where('email', $request->email)->first();
        if ($subscriber) {
            if ($subscriber->verified_at) {
                return response()->json(['success' => false,'message' => 'This email is already subscribed.']);
            }
            return response()->json(['success' => false,'message' => 'Please verify your email first.']);
        }
        $token = Str::random(64);
        $subscriber = Subscriber::create([
            'email' => $request->email,
            'verification_token' => $token,
            'unsubscribe_token' => Str::random(64),
        ]);
        try {
            Mail::send('emails.subscriber-verification', [
                'subscriber' => $subscriber
            ], function ($mail) use ($subscriber) {
                $mail->to($subscriber->email)
                    ->subject('Verify your Subscription');
            });
        } catch (\Throwable $e) {
            \Log::error('Subscriber verification email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Please check your email to verify subscription.'
        ]);
    }
    public function verify($token)
    {
        $subscriber = Subscriber::where('verification_token', $token)->firstOrFail();
        $subscriber->update([
            'verification_token' => null,
            'verified_at' => now()
        ]);
        return redirect('/')
            ->with(
                'success',
                'Your email has been verified successfully. Thank you for subscribing.'
            );
    }
    public function unsubscribe($token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now()
        ]);
        return redirect('/')->with('success', 'You have been unsubscribed.');
    }
}
