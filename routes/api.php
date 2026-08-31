<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AvatarController;
use App\Http\Controllers\Api\V1\CounselingController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\DonationController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Matchmaker as MatchmakerApi;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\NikahBrowseController;
use App\Http\Controllers\Api\V1\NikahCounselorApplicationController;
use App\Http\Controllers\Api\V1\NikahFileController;
use App\Http\Controllers\Api\V1\NikahGuardianMessageController;
use App\Http\Controllers\Api\V1\NikahHireCounselorController;
use App\Http\Controllers\Api\V1\NikahInterestController;
use App\Http\Controllers\Api\V1\NikahPaymentController;
use App\Http\Controllers\Api\V1\NikahProfileController;
use App\Http\Controllers\Api\V1\NikahSafetyController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\VolunteerController;
use App\Http\Controllers\Api\V1\WallController;
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
        Route::get('avatar/{user}', [AvatarController::class, 'show'])->name('avatar');

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::post('/', [ProfileController::class, 'update'])->name('update');
            Route::post('modules', [ProfileController::class, 'updateModules'])->name('modules.update');
            Route::post('password', [ProfileController::class, 'updatePassword'])->name('password.update');
        });

        Route::post('device-token', [DeviceTokenController::class, 'store'])->name('device-token.store');
        Route::delete('device-token', [DeviceTokenController::class, 'destroy'])->name('device-token.destroy');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

        // Nikah — Phase 1. See NikahProfileController's class docblock for
        // why store() is a single upsert endpoint rather than named steps.
        Route::prefix('nikah')->name('nikah.')->group(function () {
            Route::get('profile', [NikahProfileController::class, 'show'])->name('profile.show');
            Route::post('profile', [NikahProfileController::class, 'store'])->name('profile.store');
            Route::post('profile/submit', [NikahProfileController::class, 'submit'])->name('profile.submit');
            Route::post('payment', [NikahPaymentController::class, 'store'])->name('payment.store');

            Route::get('browse', [NikahBrowseController::class, 'index'])->name('browse.index');
            Route::get('saved', [NikahBrowseController::class, 'savedProfiles'])->name('saved.index');
            Route::get('profile/{profile}', [NikahBrowseController::class, 'show'])->name('browse.show');
            Route::post('profile/{profile}/save', [NikahBrowseController::class, 'toggleSave'])->name('browse.save');
            Route::get('file/{profile}/{type}', [NikahFileController::class, 'show'])->name('file');

            Route::post('interests/{profile}', [NikahInterestController::class, 'send'])->name('interests.send');
            Route::get('interests', [NikahInterestController::class, 'index'])->name('interests.index');
            Route::post('interests/{interest}/accept', [NikahInterestController::class, 'accept'])->name('interests.accept');
            Route::post('interests/{interest}/decline', [NikahInterestController::class, 'decline'])->name('interests.decline');

            Route::get('interests/{interest}/messages', [NikahGuardianMessageController::class, 'index'])->name('messages.index');
            Route::post('interests/{interest}/messages', [NikahGuardianMessageController::class, 'store'])->name('messages.store');

            Route::post('block/{profile}', [NikahSafetyController::class, 'block'])->name('block');
            Route::delete('block/{block}', [NikahSafetyController::class, 'unblock'])->name('unblock');
            Route::get('blocked', [NikahSafetyController::class, 'blockedList'])->name('blocked');
            Route::post('report/{profile}', [NikahSafetyController::class, 'report'])->name('report');
            Route::post('toggle-active', [NikahSafetyController::class, 'toggleActive'])->name('toggle-active');

            // Optional bridge into the counselor-assisted flow — a
            // self-service member choosing to bring in a Nikah Counselor.
            // See NikahHireCounselorController's class docblock.
            Route::get('counselors', [NikahHireCounselorController::class, 'counselors'])->name('counselors.index');
            Route::post('hire-counselor', [NikahHireCounselorController::class, 'hire'])->name('hire-counselor');
            Route::get('my-lead', [NikahHireCounselorController::class, 'myLead'])->name('my-lead');
            Route::get('lead-packages', [NikahHireCounselorController::class, 'packages'])->name('lead-packages');
            Route::post('lead-package', [NikahHireCounselorController::class, 'submitPackage'])->name('lead-package');
        });

        // Counselor<->client messaging for a hired Lead — reachable by
        // either party (see NikahHireCounselorController::authorizeLeadParty()),
        // so this deliberately sits outside both the nikah.* and
        // matchmaker.* prefix groups rather than duplicated under each.
        Route::get('leads/{lead}/messages', [NikahHireCounselorController::class, 'messages'])->name('leads.messages.index');
        Route::post('leads/{lead}/messages', [NikahHireCounselorController::class, 'sendMessage'])->name('leads.messages.store');

        Route::prefix('volunteer')->name('volunteer.')->group(function () {
            Route::get('status', [VolunteerController::class, 'status'])->name('status');
            Route::post('apply', [VolunteerController::class, 'apply'])->name('apply');
            Route::get('certificate', [VolunteerController::class, 'certificate'])->name('certificate');
        });

        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get('meta', [DonationController::class, 'meta'])->name('meta');
            Route::get('/', [DonationController::class, 'index'])->name('index');
            Route::post('/', [DonationController::class, 'store'])->name('store');
        });

        Route::prefix('counseling')->name('counseling.')->group(function () {
            Route::get('meta', [CounselingController::class, 'meta'])->name('meta');
            Route::get('slots', [CounselingController::class, 'slots'])->name('slots');
            Route::get('bookings', [CounselingController::class, 'index'])->name('bookings.index');
            Route::post('bookings', [CounselingController::class, 'store'])->name('bookings.store');
            Route::get('bookings/{booking}', [CounselingController::class, 'show'])->name('bookings.show');
            Route::post('bookings/{booking}/cancel', [CounselingController::class, 'cancel'])->name('bookings.cancel');
            Route::post('bookings/{booking}/reply', [CounselingController::class, 'reply'])->name('bookings.reply');
            Route::post('bookings/{booking}/rate', [CounselingController::class, 'rate'])->name('bookings.rate');
        });

        Route::prefix('wall')->name('wall.')->group(function () {
            Route::get('/', [WallController::class, 'index'])->name('index');
            Route::get('saved', [WallController::class, 'saved'])->name('saved');
            Route::post('dua', [WallController::class, 'storeDua'])->name('dua.store');
            Route::post('{type}/{id}/react', [WallController::class, 'react'])->whereIn('type', ['dua', 'post'])->whereNumber('id')->name('react');
            Route::post('{type}/{id}/save', [WallController::class, 'save'])->whereIn('type', ['dua', 'post'])->whereNumber('id')->name('save');
            Route::get('{type}/{id}/comments', [WallController::class, 'comments'])->whereIn('type', ['dua', 'post'])->whereNumber('id')->name('comments.index');
            Route::post('{type}/{id}/comments', [WallController::class, 'storeComment'])->whereIn('type', ['dua', 'post'])->whereNumber('id')->name('comments.store');
            Route::delete('comments/{comment}', [WallController::class, 'destroyComment'])->name('comments.destroy');
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
            Route::post('clients/{lead}/set-password', [MatchmakerApi\ClientController::class, 'setLoginPassword'])->name('clients.set-password');

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
            Route::get('meta/payment-accounts', [MatchmakerApi\MetaController::class, 'paymentAccounts'])->name('meta.payment-accounts');
            Route::get('meta/packages', [MatchmakerApi\MetaController::class, 'packages'])->name('meta.packages');

            Route::post('device-token', [MatchmakerApi\DeviceTokenController::class, 'store'])->name('device-token.store');
            Route::delete('device-token', [MatchmakerApi\DeviceTokenController::class, 'destroy'])->name('device-token.destroy');

            Route::get('notifications', [MatchmakerApi\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('notifications/unread-count', [MatchmakerApi\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
            Route::post('notifications/{id}/read', [MatchmakerApi\NotificationController::class, 'markRead'])->name('notifications.read');
            Route::post('notifications/read-all', [MatchmakerApi\NotificationController::class, 'markAllRead'])->name('notifications.read-all');

            Route::get('certificate', [MatchmakerApi\CertificateController::class, 'show'])->name('certificate.show');
            Route::get('certificate/download', [MatchmakerApi\CertificateController::class, 'download'])->name('certificate.download');
            Route::post('certificate/request-dispatch', [MatchmakerApi\CertificateController::class, 'requestDispatch'])->name('certificate.request-dispatch');
        });
    });
});