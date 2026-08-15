<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class OtpController extends Controller
{
    use RegistersMinimalUsers;

    public function requestForm(): View
    {
        return view('auth.otp-request');
    }

    public function request(Request $request): RedirectResponse
    {
        $user = User::where('phone', $request->input('phone'))->first();
        $purpose = $user ? 'login' : 'registration';

        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
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
            $user = $this->createMinimalUser($pending['name'], $pending['email'], $pending['phone'], provider: 'whatsapp');
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

        session()->forget('otp_pending');
        session()->flash('conversion_event', $pending['purpose'] === 'registration' ? 'user_registered' : 'user_login');

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
