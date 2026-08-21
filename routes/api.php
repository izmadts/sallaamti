<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\NikahBrowseController;
use App\Http\Controllers\Api\V1\NikahFileController;
use App\Http\Controllers\Api\V1\NikahInterestController;
use App\Http\Controllers\Api\V1\NikahPaymentController;
use App\Http\Controllers\Api\V1\NikahProfileController;
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

        // Nikah — Phase 1. See NikahProfileController's class docblock for
        // why store() is a single upsert endpoint rather than named steps.
        Route::prefix('nikah')->name('nikah.')->group(function () {
            Route::get('profile', [NikahProfileController::class, 'show'])->name('profile.show');
            Route::post('profile', [NikahProfileController::class, 'store'])->name('profile.store');
            Route::post('profile/submit', [NikahProfileController::class, 'submit'])->name('profile.submit');
            Route::post('payment', [NikahPaymentController::class, 'store'])->name('payment.store');

            Route::get('browse', [NikahBrowseController::class, 'index'])->name('browse.index');
            Route::get('profile/{profile}', [NikahBrowseController::class, 'show'])->name('browse.show');
            Route::post('profile/{profile}/save', [NikahBrowseController::class, 'toggleSave'])->name('browse.save');
            Route::get('file/{profile}/{type}', [NikahFileController::class, 'show'])->name('file');

            Route::post('interests/{profile}', [NikahInterestController::class, 'send'])->name('interests.send');
            Route::get('interests', [NikahInterestController::class, 'index'])->name('interests.index');
            Route::post('interests/{interest}/accept', [NikahInterestController::class, 'accept'])->name('interests.accept');
            Route::post('interests/{interest}/decline', [NikahInterestController::class, 'decline'])->name('interests.decline');
        });
    });
});