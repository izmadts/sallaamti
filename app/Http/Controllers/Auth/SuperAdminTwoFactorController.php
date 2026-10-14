<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeMail;
use App\Support\DeviceTrust;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

// Second step of login for Super Admin accounts only — see
// LoginRequest::completeLogin(). A password/PIN match alone lands here
// instead of a real session; only a code emailed to the account's own
// address can finish it. Nothing here grants access on its own — without
// the 'super_admin_2fa_pending' session value (set only by a verified
// password/PIN check), there's no account to log into.
class SuperAdminTwoFactorController extends Controller
{
    public function verifyForm(): View|RedirectResponse
    {
        if (!session()->has('super_admin_2fa_pending')) {
            return redirect()->route('login');
        }

        return view('auth.super-admin-2fa');
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = session('super_admin_2fa_pending');

        if (!$pending) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'digits:6']]);

        $user = User::find($pending['user_id']);

        if (!$user || !$user->hasRole('super_admin') || !OtpCode::verify($user->email, $request->code, 'super_admin_2fa')) {
            return back()->withErrors(['code' => 'That code is invalid or has expired.']);
        }

        Auth::login($user, $pending['remember'] ?? false);
        DeviceTrust::trust($user, $request);
        $request->session()->regenerate();

        session()->forget('super_admin_2fa_pending');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function resend(): RedirectResponse
    {
        $pending = session('super_admin_2fa_pending');

        if (!$pending || !($user = User::find($pending['user_id']))) {
            return redirect()->route('login');
        }

        $otp = OtpCode::generateFor($user->email, 'super_admin_2fa', $user->id);

        Notification::route('mail', $user->email)->notify(new OtpCodeMail(
            $otp->code,
            subject: 'Your Sallaamti Super Admin Security Code',
            intro: 'Someone is signing in to your Super Admin account. If this is you, enter this code to continue:',
        ));

        return back()->with('status', 'A new code has been sent to your email.');
    }
}
