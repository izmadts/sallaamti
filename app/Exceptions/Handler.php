<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Sentry
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                app('sentry')->captureException($e);
            }
        });

        // Throttle errors → friendly message
        $this->renderable(function (
            \Illuminate\Http\Exceptions\ThrottleRequestsException $e,
            $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many attempts. Please wait a minute.'
                ], 429);
            }
            return back()->withInput()
                ->with('error', 'Too many attempts. Please wait a minute before trying again.');
        });
    }
}
