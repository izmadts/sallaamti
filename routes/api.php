<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FaqController;
use Illuminate\Support\Facades\Route;

// GitHub Auto Deploy Webhook
// This route must be OUTSIDE auth middleware so GitHub can access it without login
Route::post('/deploy', [App\Http\Controllers\DeployController::class, 'handle']);

// Sallaamti mobile app — Phase 0 (auth foundation). Every route here is
// stateless bearer-token auth (Sanctum), not session — see
// AuthController's class docblock for why. Rate-limited the same as the
// web login/register/OTP routes to prevent credential-stuffing/OTP abuse.
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');
        Route::post('otp/request', [AuthController::class, 'otpRequest'])->middleware('throttle:5,1')->name('otp.request');
        Route::post('otp/verify', [AuthController::class, 'otpVerify'])->middleware('throttle:10,1')->name('otp.verify');
        Route::post('social/google', [AuthController::class, 'socialGoogle'])->middleware('throttle:10,1')->name('social.google');
        Route::post('social/facebook', [AuthController::class, 'socialFacebook'])->middleware('throttle:10,1')->name('social.facebook');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');
        });
    });

    Route::get('faqs', [FaqController::class, 'index'])->name('faqs.index');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    });
});