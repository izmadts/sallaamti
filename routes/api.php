<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\Matchmaker as MatchmakerApi;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\NikahBrowseController;
use App\Http\Controllers\Api\V1\NikahCounselorApplicationController;
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
    Route::get('meta/country-states', [MetaController::class, 'countryStates'])->name('meta.country-states');
    Route::get('meta/nikah-counselor-application', [MetaController::class, 'nikahCounselorApplicationEnums'])->name('meta.nikah-counselor-application');

    // Guest "Apply to become a Nikah Counselor" — no account required, see
    // NikahCounselorApplicationController's class docblock. Throttled like
    // the auth endpoints above since it's an unauthenticated write.
    Route::post('nikah-counselor-application', [NikahCounselorApplicationController::class, 'store'])->middleware('throttle:5,1')->name('nikah-counselor-application.store');

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

        // Nikah Counselor (matchmaker) app — see EnsureUserIsMatchmakerApi's
        // class docblock for why this uses its own 'api.matchmaker' gate
        // instead of the web-only 'matchmaker' alias.
        Route::prefix('matchmaker')->name('matchmaker.')->middleware('api.matchmaker')->group(function () {
            Route::get('dashboard', [MatchmakerApi\DashboardController::class, 'index'])->name('dashboard');

            Route::get('clients', [MatchmakerApi\ClientController::class, 'index'])->name('clients.index');
            Route::post('clients', [MatchmakerApi\ClientController::class, 'store'])->name('clients.store');
            Route::get('clients/{lead}', [MatchmakerApi\ClientController::class, 'show'])->name('clients.show');
            Route::patch('clients/{lead}', [MatchmakerApi\ClientController::class, 'update'])->name('clients.update');
            Route::post('clients/{lead}/link-profile', [MatchmakerApi\ClientController::class, 'linkProfile'])->name('clients.link-profile');
            Route::post('clients/{lead}/shortlist', [MatchmakerApi\ClientController::class, 'addToShortlist'])->name('clients.shortlist.add');
            Route::delete('clients/{lead}/shortlist/{item}', [MatchmakerApi\ClientController::class, 'removeFromShortlist'])->name('clients.shortlist.remove');
            Route::post('clients/{lead}/requirements', [MatchmakerApi\ClientController::class, 'saveRequirement'])->name('clients.requirements.save');

            Route::post('clients/{lead}/consents', [MatchmakerApi\ClientController::class, 'recordConsent'])->name('clients.consents.record');
            Route::post('clients/{lead}/consents/request', [MatchmakerApi\ClientController::class, 'requestConsent'])->name('clients.consents.request');
            Route::post('clients/{lead}/consents/{consent}/revoke', [MatchmakerApi\ClientController::class, 'revokeConsent'])->name('clients.consents.revoke');
            Route::post('clients/{lead}/progress-link', [MatchmakerApi\ClientController::class, 'regenerateProgressLink'])->name('clients.progress-link.regenerate');

            Route::post('clients/{lead}/proposal-batches', [MatchmakerApi\ClientController::class, 'createBatch'])->name('clients.batches.create');
            Route::post('clients/{lead}/proposal-batches/{batch}/proposals', [MatchmakerApi\ClientController::class, 'addProposal'])->name('clients.batches.proposals.add');
            Route::delete('clients/{lead}/proposal-batches/{batch}/proposals/{proposal}', [MatchmakerApi\ClientController::class, 'removeProposal'])->name('clients.batches.proposals.remove');
            Route::post('clients/{lead}/proposal-batches/{batch}/send', [MatchmakerApi\ClientController::class, 'sendBatch'])->name('clients.batches.send');
            Route::post('clients/{lead}/proposal-batches/{batch}/proposals/{proposal}/regenerate-link', [MatchmakerApi\ClientController::class, 'regenerateLink'])->name('clients.batches.proposals.regenerate-link');

            // Stateless walk-in registration — see ClientProfileController's class docblock
            Route::get('clients/{lead}/profile', [MatchmakerApi\ClientProfileController::class, 'show'])->name('clients.profile.show');
            Route::post('clients/{lead}/profile', [MatchmakerApi\ClientProfileController::class, 'store'])->name('clients.profile.store');
            Route::post('clients/{lead}/profile/payment', [MatchmakerApi\ClientProfileController::class, 'payment'])->name('clients.profile.payment');
            Route::get('clients/{lead}/profile/file/{type}', [MatchmakerApi\ClientFileController::class, 'show'])->name('clients.profile.file');

            Route::get('browse', [MatchmakerApi\NikahBrowseController::class, 'index'])->name('browse.index');
            Route::get('browse/{profile}', [MatchmakerApi\NikahBrowseController::class, 'show'])->name('browse.show');
            Route::post('browse/{profile}/request-contact', [MatchmakerApi\NikahBrowseController::class, 'requestContact'])->name('browse.request-contact');

            Route::get('interests', [MatchmakerApi\InterestController::class, 'index'])->name('interests.index');
            Route::post('interests/{interest}/accept', [MatchmakerApi\InterestController::class, 'accept'])->name('interests.accept');
            Route::post('interests/{interest}/decline', [MatchmakerApi\InterestController::class, 'decline'])->name('interests.decline');

            Route::get('commissions', [MatchmakerApi\CommissionController::class, 'index'])->name('commissions.index');
            Route::get('performance', [MatchmakerApi\PerformanceController::class, 'index'])->name('performance.index');
            Route::get('referral', [MatchmakerApi\ReferralController::class, 'show'])->name('referral.show');
            Route::get('application', [MatchmakerApi\ApplicationController::class, 'show'])->name('application.show');
            Route::get('meta/enums', [MatchmakerApi\MetaController::class, 'enums'])->name('meta.enums');

            Route::post('device-token', [MatchmakerApi\DeviceTokenController::class, 'store'])->name('device-token.store');
            Route::delete('device-token', [MatchmakerApi\DeviceTokenController::class, 'destroy'])->name('device-token.destroy');

            Route::get('notifications', [MatchmakerApi\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('notifications/unread-count', [MatchmakerApi\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
            Route::post('notifications/{id}/read', [MatchmakerApi\NotificationController::class, 'markRead'])->name('notifications.read');
            Route::post('notifications/read-all', [MatchmakerApi\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        });
    });
});