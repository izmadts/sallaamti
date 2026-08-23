<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\ModuleRedirects;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // A card click that sent a guest here (e.g. they already had an
        // account and switched to the Sign In tab) still finishes that same
        // journey — same explicit-module priority as RegisteredUserController.
        $target = ModuleRedirects::resolve($request->input('module'));

        if (Auth::user()->isDeactivated()) {
            Auth::user()->reactivate();

            return redirect()->intended($target ?? route('dashboard', absolute: false))
                ->with('status', 'Welcome back! Your account has been reactivated.');
        }

        return redirect()->intended($target ?? route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
