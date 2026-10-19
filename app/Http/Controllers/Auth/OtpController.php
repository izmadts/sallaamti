<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Concerns\TracksReferrals;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeMail;
use App\Rules\ValidPhoneNumber;
use App\Support\DeviceTrust;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class OtpController extends Controller
{
    use RegistersMinimalUsers, TracksReferrals;

    public function requestForm(): View
    {
        return view('auth.otp-request');
    }

    public function request(Request $request): RedirectResponse
    {
        $user = User::where('phone', $request->input('phone'))->first();
        $purpose = $user ? 'login' : 'registration';

        $request->validate([
            'phone' => ['required', 'string', 'max:20', new ValidPhoneNumber()],
            'email' => ['required', 'email', 'max:255'],
            'name' => [$purpose === 'registration' ? 'required' : 'nullable', 'string', 'max:255'],
        ]);

        $otp = OtpCode::generateFor($request->phone, $purpose, $user?->id);

        Notification::route('mail', $request->email)->notify(new OtpCodeMail($otp->code));

        session([
            'otp_pending' => [
                'phone' => $request->phone,
                'email' => $request->email,
                'name' => $request->name,
                'purpose' => $purpose,
                'user_id' => $user?->id,
            ],
        ]);

        return redirect()->route('otp.verify');
    }

    public function verifyForm(): View|RedirectResponse
    {
        if (!session()->has('otp_pending')) {
            return redirect()->route('otp.request');
        }

        return view('auth.otp-verify', ['pending' => session('otp_pending')]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = session('otp_pending');

        if (!$pending) {
            return redirect()->route('otp.request');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (!OtpCode::verify($pending['phone'], $request->code, $pending['purpose'])) {
            return back()->withErrors(['code' => 'That code is invalid or has expired.']);
        }

        if ($pending['purpose'] === 'registration') {
            // This phone number is new, but the email typed alongside it
            // might not be — e.g. they already have an email+password
            // account and are now trying phone+OTP, not realizing it
            // creates a second, disconnected one. createMinimalUser() would
            // otherwise either crash on email's unique constraint or (with
            // a different email) silently succeed and create that very
            // duplicate — catch it here with a clear message instead.
            $existingByEmail = $pending['email'] ? User::where('email', $pending['email'])->first() : null;

            if ($existingByEmail) {
                session()->forget('otp_pending');
                return redirect()->route('login')->withErrors([
                    'email' => __('db.An account already exists with that email. Please log in instead — you can then add this phone number to it from your profile.'),
                ]);
            }

            $user = $this->createMinimalUser($pending['name'], $pending['email'], $pending['phone'], provider: 'whatsapp', mustChangePassword: true);
            $this->attributeReferral($user);
        } else {
            $user = User::findOrFail($pending['user_id']);
        }

        if (!$user->email) {
            $user->email = $pending['email'];
        }

        if ($user->email === $pending['email'] && !$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        $user->save();

        if ($user->isDeactivated()) {
            $user->reactivate();
            session()->flash('status', 'Welcome back! Your account has been reactivated.');
        }

        Auth::login($user);
        // A verified OTP proves phone ownership at least as strongly as a
        // password — this device can use the PIN shortcut afterward too
        // (see App\Support\DeviceTrust). This is also the only real path
        // a client who set up a PIN via their progress link has to get
        // into their account from a brand-new device later, since that
        // account's actual password is never shown to them anywhere.
        DeviceTrust::trust($user, $request);

        session()->forget('otp_pending');
        session()->flash('conversion_event', $pending['purpose'] === 'registration' ? 'user_registered' : 'user_login');

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
