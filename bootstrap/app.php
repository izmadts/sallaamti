<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        'admin.only' => \App\Http\Middleware\EnsureUserIsFullAdmin::class,
        'teacher' => \App\Http\Middleware\EnsureUserIsTeacher::class,
        'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        'maintenance' => \App\Http\Middleware\MaintenanceMode::class,
        'blog.manage' => \App\Http\Middleware\EnsureCanManageBlog::class,
        'nikah.activity' => \App\Http\Middleware\TrackNikahActivity::class,
        'counselor' => \App\Http\Middleware\EnsureUserIsCounselor::class,
        'matchmaker' => \App\Http\Middleware\EnsureUserIsMatchmaker::class,
        ]);
    })->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
        \App\Http\Middleware\MaintenanceMode::class,
        \App\Http\Middleware\EnsureUserIsActive::class,
        \App\Http\Middleware\TrackIntendedUrlForGuests::class,
    ]);
    $middleware->api(append: [
        \App\Http\Middleware\SetApiLocale::class,
    ]);
})->withExceptions(function (Exceptions $exceptions): void {
    // A logout click on a page left open past the session lifetime is a
    // stale CSRF token, not a real security event — the safe thing to do
    // is just complete the logout instead of showing the "Session
    // Expired" page for what the member experiences as a broken Logout
    // button. Every other form still gets the normal 419 page.
    $exceptions->render(function (TokenMismatchException $e, $request) {
        if ($request->routeIs('logout')) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    });
})->create();
