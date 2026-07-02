<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        'teacher' => \App\Http\Middleware\EnsureUserIsTeacher::class,
        'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        'maintenance' => \App\Http\Middleware\MaintenanceMode::class,
        ]);
    })->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\MaintenanceMode::class,
    ]);
})->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
