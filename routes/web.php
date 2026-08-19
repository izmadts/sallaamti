<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NikahProfileController;
use App\Http\Controllers\NikahProfileWizardController;
use App\Http\Controllers\Admin\NikahVerificationController;
use App\Http\Controllers\NikahInterestController;
use App\Http\Controllers\NikahFileController;
use App\Http\Controllers\NikahPhotoController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\Admin\DuaWallAdminController;
use App\Http\Controllers\Admin\CommunityPostController;
use App\Http\Controllers\Admin\SocialIntegrationController;
use App\Http\Controllers\NikahPaymentController;
use App\Http\Controllers\NikahSafetyController;
use App\Http\Controllers\NikahGuardianMessageController;
use App\Http\Controllers\Admin\NikahPaymentAdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Admin\QuizAdminController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\QuranLiveCourseController;
use App\Http\Controllers\QuranSubscriptionFileController;
use App\Http\Controllers\Admin\QuranLiveCourseAdminController;
use App\Http\Controllers\Admin\QuranClassGroupAdminController;
use App\Http\Controllers\Teacher\QuranTeacherController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\Admin\VolunteerAdminController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Admin\DonationAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SystemMaintenanceController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SubscriberAdminController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\EditorImageController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\DailyContentController;
use App\Http\Controllers\Admin\CertificateAdminController;
use App\Http\Controllers\SupportQueryController;
use App\Http\Controllers\Admin\SupportQueryAdminController;
use App\Http\Controllers\LanguageSwitchController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\CounselingBookingController;
use App\Http\Controllers\Counselor\AvailabilityController as CounselorAvailabilityController;
use App\Http\Controllers\Counselor\BookingController as CounselorBookingController;
use App\Http\Controllers\Admin\CounselingBookingAdminController;

// ============================================================
// PUBLIC ROUTES (no auth required)
// ============================================================

Route::get('/language/switch/{code}', [LanguageSwitchController::class, 'switch'])->name('language.switch');

Route::get('/', function () {
    // Facebook-style: a logged-in visitor never sees the marketing/login
    // page at all, they land on their dashboard immediately.
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('dashboard');
    }

    $banners = \App\Models\Banner::active()->get();
    $latestDuas = \App\Models\DuaRequest::where('status', 'approved')->latest()->take(3)->get();

    return view('index', compact('banners', 'latestDuas'));
})->name('index');
// Static pages
Route::get('/about', function () {
    $teamMembers = \App\Models\TeamMember::active()->orderBy('order')->get();
    $testimonials = \App\Models\Testimonial::where('is_active', true)->orderBy('order')->get();
    $previewCourses = \App\Models\Course::where('is_published', true)->take(4)->get();
    return view('about', compact('teamMembers', 'testimonials', 'previewCourses'));
});
// Retired — content now lives on the unified Wall, filtered by tag. Kept as
// redirects (not a 404) since these URLs may be bookmarked/indexed.
Route::get('/activities', fn() => redirect('/wall?tag=Activity', 301));
Route::get('/events', fn() => redirect('/wall?tag=Event', 301));
Route::get('/sermons', fn() => redirect('/wall?tag=Sermon', 301));
Route::get('/team', function () {
    $teamMembers = \App\Models\TeamMember::active()->orderBy('order')->get();
    return view('team', compact('teamMembers'));
});
Route::get('/testimonial', fn() => view('testimonial'));
Route::get('/contact', fn() => view('contact'));
Route::get('/privacy-policy', fn() => view('privacy-policy'))->name('privacy-policy');
Route::get('/terms-of-service', fn() => view('terms-of-service'))->name('terms-of-service');

// Public module pages (browsable without login)
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/quran-live', [QuranLiveCourseController::class, 'index'])->name('quran-live.index');
Route::get('/quran-live/{course}', [QuranLiveCourseController::class, 'show'])->name('quran-live.show');

// Blog (public reading)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog_post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');
    Route::post('/volunteer', [VolunteerController::class, 'store'])->name('volunteer.store');
});
// Volunteer (guests can apply)
Route::get('/volunteer', [VolunteerController::class, 'create'])->name('volunteer.create');

// Donation (guests can donate)
Route::get('/donate', [DonationController::class, 'create'])->name('donate.create');
Route::get('/donate/{donation}/thank-you', [DonationController::class, 'thankYou'])->name('donate.thank-you');

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');
});

// Newsletter — subscribe is above under throttle:10,1 (the /subscribe route);
// verify/unsubscribe both use the subscriber's own token, never a raw ID.
Route::get('/subscriber/verify/{token}', [SubscriberController::class, 'verify'])->name('subscriber.verify');
Route::get('/subscriber/unsubscribe/{token}', [SubscriberController::class, 'unsubscribe'])->name('subscriber.unsubscribe');

// Sallaamti Wall — guest-readable (good top-of-funnel content), posting/
// reacting requires login (see the auth-gated routes further down). One
// unified feed for duas + admin-authored posts (activities/events/sermons),
// filterable by tag via ?tag=.
Route::get('/wall', [WallController::class, 'index'])->name('wall.index');
Route::get('/wall/{duaRequest}/comments', [WallController::class, 'comments'])->name('wall.comments');
Route::get('/wall/post/{communityPost}/comments', [WallController::class, 'postComments'])->name('wall.post.comments');

// Certificate verification (public — no login needed)
Route::get('/verify-certificate/{certificateNumber?}', [CertificateController::class, 'verify'])->name('certificate.verify');

// Nikah profile sharing (public teaser — no login needed)
Route::get('/nikah/p/{token}', [NikahProfileController::class, 'publicView'])->name('nikah.public-view');

// XML sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// ============================================================
// AUTHENTICATED USER ROUTES
// ============================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/modules', [ProfileController::class, 'updateModules'])->name('profile.modules.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Avatar
    Route::get('/user/avatar/{user}', [UserAvatarController::class, 'show'])->name('user.avatar');

    // Notifications
    Route::post('/notifications/mark-all-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');

    // Web Push subscriptions
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    // Sallaamti Wall — posting and reacting (viewing the wall itself is public, see above)
    Route::post('/wall', [WallController::class, 'store'])->name('wall.store');
    Route::post('/wall/{duaRequest}/react', [WallController::class, 'react'])->name('wall.react');
    Route::post('/wall/post/{communityPost}/react', [WallController::class, 'postReact'])->name('wall.post.react');
    Route::get('/wall/saved', [WallController::class, 'saved'])->name('wall.saved');
    Route::post('/wall/{duaRequest}/save', [WallController::class, 'save'])->name('wall.save');
    Route::post('/wall/post/{communityPost}/save', [WallController::class, 'postSave'])->name('wall.post.save');
    Route::post('/wall/{duaRequest}/comments', [WallController::class, 'storeComment'])->name('wall.comments.store');
    Route::post('/wall/post/{communityPost}/comments', [WallController::class, 'storePostComment'])->name('wall.post.comments.store');
    Route::delete('/comments/{comment}', [WallController::class, 'destroyComment'])->name('comments.destroy');

    // --- NIKAH MODULE ---
    Route::middleware('nikah.activity')->group(function () {
    Route::get('/nikah/create', [NikahProfileWizardController::class, 'start'])->name('nikah.create');
    Route::get('/nikah/create/step/{step}', [NikahProfileWizardController::class, 'showStep'])->name('nikah.create.step');
    Route::post('/nikah/create/step/{step}', [NikahProfileWizardController::class, 'saveStep'])->name('nikah.create.step.save');
    Route::get('/nikah/create/review', [NikahProfileWizardController::class, 'review'])->name('nikah.create.review');
    Route::post('/nikah/create/finalize', [NikahProfileWizardController::class, 'finalize'])->name('nikah.create.finalize');
    Route::post('/nikah', [NikahProfileController::class, 'store'])->name('nikah.store');
    Route::get('/nikah/my-profile', [NikahProfileController::class, 'show'])->name('nikah.show');
    Route::get('/nikah/my-profile/edit', [NikahProfileController::class, 'edit'])->name('nikah.edit');
    Route::put('/nikah/my-profile', [NikahProfileController::class, 'update'])->name('nikah.update');
    Route::get('/nikah/browse', [NikahProfileController::class, 'browse'])->name('nikah.browse');
    Route::get('/nikah/saved', [NikahProfileController::class, 'saved'])->name('nikah.saved');
    Route::post('/nikah/save/{profile}', [NikahProfileController::class, 'toggleSave'])->name('nikah.save');
    Route::get('/nikah/profile/{profile}', [NikahProfileController::class, 'view'])->name('nikah.profile.view');
    Route::get('/nikah/file/{profile}/{type}', [NikahFileController::class, 'show'])->name('nikah.file');
    Route::post('/nikah/interest/{profile}', [NikahInterestController::class, 'send'])->name('nikah.interest.send');
    Route::get('/nikah/interests', [NikahInterestController::class, 'index'])->name('nikah.interests');
    Route::post('/nikah/interests/{interest}/accept', [NikahInterestController::class, 'accept'])->name('nikah.interest.accept');
    Route::post('/nikah/interests/{interest}/decline', [NikahInterestController::class, 'decline'])->name('nikah.interest.decline');
    Route::get('/nikah/interests/{interest}/messages', [NikahGuardianMessageController::class, 'show'])->name('nikah.messages.show');
    Route::post('/nikah/interests/{interest}/messages', [NikahGuardianMessageController::class, 'store'])->name('nikah.messages.store');
    Route::get('/nikah/payment', [NikahPaymentController::class, 'show'])->name('nikah.payment');
    Route::post('/nikah/payment', [NikahPaymentController::class, 'store'])->name('nikah.payment.store');
    Route::post('/nikah/photos', [NikahPhotoController::class, 'store'])->name('nikah.photos.store');
    Route::delete('/nikah/photos/{photo}', [NikahPhotoController::class, 'destroy'])->name('nikah.photos.destroy');
    Route::post('/nikah/photos/{photo}/primary', [NikahPhotoController::class, 'setPrimary'])->name('nikah.photos.primary');
    Route::get('/nikah/photos/{photo}', [NikahPhotoController::class, 'show'])->name('nikah.photos.show');
    Route::post('/nikah/block/{profile}', [NikahSafetyController::class, 'block'])->name('nikah.block');
    Route::get('/nikah/blocked', [NikahSafetyController::class, 'blockedList'])->name('nikah.blocked');
    Route::delete('/nikah/block/{block}', [NikahSafetyController::class, 'unblock'])->name('nikah.unblock');
    Route::post('/nikah/report/{profile}', [NikahSafetyController::class, 'report'])->name('nikah.report');
    Route::post('/nikah/toggle-active', [NikahSafetyController::class, 'toggleActive'])->name('nikah.toggle-active');
    });

    // --- QURAN COURSES ---
    Route::post('/courses/{course:slug}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::get('/my-learning', [CourseController::class, 'myLearning'])->name('courses.my-learning');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'complete'])->name('lessons.complete');
    Route::get('/courses/{course:slug}/quiz', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/courses/{course:slug}/quiz', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/lessons/{lesson}/quiz', [QuizController::class, 'showLessonQuiz'])->name('lesson.quiz.show');
    Route::post('/lessons/{lesson}/quiz', [QuizController::class, 'submitLessonQuiz'])->name('lesson.quiz.submit');
    Route::post('/courses/{course:slug}/certificate', [CertificateController::class, 'generate'])->name('certificate.generate');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/my-certificates', [CertificateController::class, 'index'])->name('certificate.index');
    Route::get('/my-quran-progress', [QuranLiveCourseController::class, 'myProgress'])->name('quran-live.my-progress');
    Route::get('/my-quran-class', [QuranLiveCourseController::class, 'myClass'])->name('quran-live.my-class');


    // --- QURAN LIVE CLASSES ---
    Route::get('/quran-live/{course}/admission', [QuranLiveCourseController::class, 'admissionForm'])->name('quran-live.admission');
    Route::post('/quran-live/{course}/admission', [QuranLiveCourseController::class, 'storeAdmission'])->name('quran-live.admission.store');
    Route::get('/quran-live/{course}/subscribe', [QuranLiveCourseController::class, 'subscribe'])->name('quran-live.subscribe');
    Route::post('/quran-live/{course}/subscribe', [QuranLiveCourseController::class, 'storeSubscription'])->name('quran-live.subscribe.store');
    Route::get('/quran-subscriptions/{subscription}/screenshot', [QuranSubscriptionFileController::class, 'show'])->name('quran-subscription.screenshot');
    // Add to auth user group:
    Route::get('/my-quran-class', [QuranLiveCourseController::class, 'myClass'])->name('quran-live.my-class');
    Route::get('/my-quran-progress', [QuranLiveCourseController::class, 'myProgress'])->name('quran-live.my-progress');
    
    // --- DONATIONS (auth extras) ---
    Route::get('/my-donations', [DonationController::class, 'myDonations'])->name('donate.my');
    Route::get('/donation-screenshot/{donation}', [DonationController::class, 'screenshot'])->name('donation.screenshot');
    Route::get('/thank-you', fn() => view('thank-you'))->name('thank-you');

    Route::get('/support', [SupportQueryController::class, 'index'])->name('support.index');
    Route::get('/support/create', [SupportQueryController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportQueryController::class, 'store'])->name('support.store');

    // --- FAMILY COUNSELING BOOKING ---
    // Must be registered before the /support/{query} wildcard below, or that
    // route's implicit model binding would swallow these literal paths first.
    Route::get('/support/book', [CounselingBookingController::class, 'start'])->name('counseling.book.start');
    Route::get('/support/book/step/{step}', [CounselingBookingController::class, 'showStep'])->name('counseling.book.step');
    Route::post('/support/book/step/{step}', [CounselingBookingController::class, 'saveStep'])->name('counseling.book.step.save');
    Route::get('/support/book/review', [CounselingBookingController::class, 'review'])->name('counseling.book.review');
    Route::post('/support/book/finalize', [CounselingBookingController::class, 'finalize'])->name('counseling.book.finalize');
    Route::get('/support/bookings', [CounselingBookingController::class, 'index'])->name('counseling.bookings.index');
    Route::get('/support/bookings/{booking}', [CounselingBookingController::class, 'show'])->name('counseling.bookings.show');
    Route::post('/support/bookings/{booking}/cancel', [CounselingBookingController::class, 'cancel'])->name('counseling.bookings.cancel');

    Route::get('/support/{query}', [SupportQueryController::class, 'show'])->name('support.show');
    Route::post('/support/{query}/reply', [SupportQueryController::class, 'reply'])->name('support.reply');
});

// ============================================================
// ADMIN ROUTES
// ============================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // User Management
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::put('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
    Route::put('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('users/roles/manage', [UserManagementController::class, 'roles'])->name('users.roles');
    Route::post('users/roles', [UserManagementController::class, 'storeRole'])->name('users.roles.store');
    Route::post('users/roles/{role}/permissions', [UserManagementController::class, 'updatePermissions'])->name('users.roles.permissions');

    // Settings & Frontend
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/demo-nikah-profiles/generate', [SettingsController::class, 'generateDemoNikahProfiles'])->name('settings.demo-nikah.generate');
    Route::post('settings/demo-nikah-profiles/remove', [SettingsController::class, 'removeDemoNikahProfiles'])->name('settings.demo-nikah.remove');
    Route::get('maintenance', [SystemMaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('maintenance/optimize-database', [SystemMaintenanceController::class, 'optimizeDatabase'])->name('maintenance.optimize-database');
    Route::post('maintenance/optimize-images', [SystemMaintenanceController::class, 'optimizeImages'])->name('maintenance.optimize-images');
    Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
    Route::post('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');
    Route::resource('banners', BannerController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');
    Route::resource('team-members', TeamMemberController::class)->except(['show']);
    Route::post('team-members/{team_member}/toggle', [TeamMemberController::class, 'toggle'])->name('team-members.toggle');

    Route::resource('daily-content', DailyContentController::class)->except(['show']);
    Route::post('daily-content/{daily_content}/toggle', [DailyContentController::class, 'toggle'])->name('daily-content.toggle');
    // First-wave granular-permission resource (see App\Support\PermissionCatalog)
    // — view/manage/delete split per method; the 'admin' role bypasses all
    // of this via Gate::before regardless.
    Route::resource('community-posts', CommunityPostController::class)->except(['show'])
        ->middlewareFor(['index'], 'can:community-posts.view')
        ->middlewareFor(['create', 'store', 'edit', 'update'], 'can:community-posts.manage')
        ->middlewareFor(['destroy'], 'can:community-posts.delete');
    Route::post('community-posts/{community_post}/toggle', [CommunityPostController::class, 'toggle'])->name('community-posts.toggle')->middleware('can:community-posts.manage');
    Route::post('community-posts/{community_post}/pin', [CommunityPostController::class, 'togglePin'])->name('community-posts.pin')->middleware('can:community-posts.manage');

    // Bulk import old photos/videos, then a daily scheduled batch drains
    // the queue automatically (see routes/console.php and
    // PublishScheduledCommunityPosts).
    Route::get('community-posts-bulk-upload', [CommunityPostController::class, 'bulkUpload'])->name('community-posts.bulk-upload')->middleware('can:community-posts.manage');
    Route::post('community-posts-bulk-upload', [CommunityPostController::class, 'bulkStore'])->name('community-posts.bulk-store')->middleware('can:community-posts.manage');
    Route::get('community-posts-queue', [CommunityPostController::class, 'queue'])->name('community-posts.queue')->middleware('can:community-posts.view');
    Route::post('community-posts-queue/reorder', [CommunityPostController::class, 'queueReorder'])->name('community-posts.queue.reorder')->middleware('can:community-posts.manage');
    Route::post('community-posts/{community_post}/publish-now', [CommunityPostController::class, 'queuePublishNow'])->name('community-posts.publish-now')->middleware('can:community-posts.manage');

    // Social media auto-posting — connect accounts + review/retry deliveries.
    Route::get('integrations', [SocialIntegrationController::class, 'index'])->name('integrations.index');
    Route::post('integrations/settings', [SocialIntegrationController::class, 'updateSettings'])->name('integrations.settings.update');
    // Throttled: these hit external OAuth/API endpoints on every call, and an
    // automated loop against them risks the app's API keys getting rate
    // limited or banned by the platform, not just wasted local work.
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('integrations/{platform}/connect', [SocialIntegrationController::class, 'connect'])->name('integrations.connect');
        Route::get('integrations/{platform}/callback', [SocialIntegrationController::class, 'callback'])->name('integrations.callback');
        Route::post('integrations/{account}/disconnect', [SocialIntegrationController::class, 'disconnect'])->name('integrations.disconnect');
        Route::post('social-dispatches/{dispatch}/retry', [SocialIntegrationController::class, 'retryDispatch'])->name('social-dispatches.retry');
        // WhatsApp Business — manual credential form, not an OAuth redirect
        // (see SocialIntegrationController::connectWhatsapp()), so it needs
        // its own POST route rather than the generic {platform}/connect GET.
        Route::post('integrations/whatsapp/connect', [SocialIntegrationController::class, 'connectWhatsapp'])->name('integrations.whatsapp.connect');
    });

    // Certificates (admin-issued)
    Route::get('certificates', [CertificateAdminController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [CertificateAdminController::class, 'create'])->name('certificates.create');
    Route::post('certificates', [CertificateAdminController::class, 'store'])->name('certificates.store');
    Route::delete('certificates/{certificate}', [CertificateAdminController::class, 'destroy'])->name('certificates.destroy');

    // Nikah Management — GET routes need nikah.view, mutating POST routes
    // need nikah.manage, the one DELETE needs nikah.delete (see
    // App\Support\PermissionCatalog; 'admin' role bypasses all of this).
    Route::middleware('can:nikah.view')->group(function () {
        Route::get('/nikah-profiles', [NikahVerificationController::class, 'directory'])->name('nikah.profiles');
        Route::get('/nikah-verifications', [NikahVerificationController::class, 'index'])->name('nikah.verifications');
        Route::get('/nikah-verifications/{profile}', [NikahVerificationController::class, 'show'])->name('nikah.show');
        Route::get('/nikah-payments', [NikahPaymentAdminController::class, 'index'])->name('nikah.payments');
        Route::get('nikah-reports', [NikahSafetyController::class, 'adminReports'])->name('nikah.reports');
        Route::get('nikah-reports/{report}/conversation', [NikahSafetyController::class, 'reportConversation'])->name('nikah.reports.conversation');
    });
    Route::delete('/nikah-verifications/{profile}', [NikahVerificationController::class, 'destroy'])->name('nikah.destroy')->middleware('can:nikah.delete');
    Route::middleware('can:nikah.manage')->group(function () {
        Route::post('/nikah-verifications/{profile}/contact', [NikahVerificationController::class, 'contact'])->name('nikah.contact');
        Route::post('/nikah-verifications/bulk-approve', [NikahVerificationController::class, 'bulkApprove'])->name('nikah.verifications.bulk-approve');
        Route::post('/nikah-verifications/{profile}/approve', [NikahVerificationController::class, 'approve'])->name('nikah.approve');
        Route::post('/nikah-verifications/{profile}/reject', [NikahVerificationController::class, 'reject'])->name('nikah.reject');
        Route::post('/nikah-verifications/{profile}/verify-guardian', [NikahVerificationController::class, 'verifyGuardian'])->name('nikah.verify-guardian');
        Route::post('/nikah-verifications/{profile}/remind', [NikahVerificationController::class, 'sendReminder'])->name('nikah.remind');
        Route::post('/nikah-verifications/bulk-remind', [NikahVerificationController::class, 'bulkRemind'])->name('nikah.verifications.bulk-remind');
        Route::post('/nikah-verifications/{profile}/notes', [NikahVerificationController::class, 'addNote'])->name('nikah.notes.store');
        Route::post('/nikah-verifications/{profile}/unsuspend', [NikahSafetyController::class, 'unsuspendProfile'])->name('nikah.unsuspend');
        Route::post('/nikah-payments/bulk-confirm', [NikahPaymentAdminController::class, 'bulkConfirm'])->name('nikah.payments.bulk-confirm');
        Route::post('/nikah-payments/{profile}/confirm', [NikahPaymentAdminController::class, 'confirm'])->name('nikah.payments.confirm');
        Route::post('/nikah-payments/{profile}/record-offline', [NikahPaymentAdminController::class, 'recordOffline'])->name('nikah.payments.record-offline');
        Route::post('/nikah-payments/{profile}/reject', [NikahPaymentAdminController::class, 'reject'])->name('nikah.payments.reject');
        Route::post('nikah-reports/{report}/dismiss', [NikahSafetyController::class, 'dismissReport'])->name('nikah.reports.dismiss');
        Route::post('nikah-reports/{report}/suspend', [NikahSafetyController::class, 'suspendReportedProfile'])->name('nikah.reports.suspend');
    });

    // Quran Courses Management
    Route::resource('courses', CourseAdminController::class);
    Route::get('courses/{course}/lessons/create', [CourseAdminController::class, 'createLesson'])->name('courses.lessons.create');
    Route::post('courses/{course}/lessons', [CourseAdminController::class, 'storeLesson'])->name('courses.lessons.store');
    Route::get('lessons/{lesson}/edit', [CourseAdminController::class, 'editLesson'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [CourseAdminController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [CourseAdminController::class, 'destroyLesson'])->name('lessons.destroy');
    Route::get('courses/{course}/quiz', [QuizAdminController::class, 'edit'])->name('courses.quiz.edit');
    Route::post('courses/{course}/quiz', [QuizAdminController::class, 'store'])->name('courses.quiz.store');
    Route::post('quizzes/{quiz}/questions', [QuizAdminController::class, 'storeQuestion'])->name('quiz.questions.store');
    Route::delete('quiz-questions/{question}', [QuizAdminController::class, 'destroyQuestion'])->name('quiz.questions.destroy');
    Route::get('lessons/{lesson}/quiz', [QuizAdminController::class, 'editLessonQuiz'])->name('lessons.quiz.edit');
    Route::post('lessons/{lesson}/quiz', [QuizAdminController::class, 'storeLessonQuiz'])->name('lessons.quiz.store');

    // Quran Live Classes Management
    Route::resource('quran-live-courses', QuranLiveCourseAdminController::class);
    Route::get('quran-live-courses/{quranLiveCourse}/subscriptions', [QuranLiveCourseAdminController::class, 'subscriptions'])->name('quran-live-courses.subscriptions');
    Route::post('quran-subscriptions/{subscription}/confirm', [QuranLiveCourseAdminController::class, 'confirmPayment'])->name('quran-subscriptions.confirm');
    Route::post('quran-subscriptions/{subscription}/reject', [QuranLiveCourseAdminController::class, 'rejectPayment'])->name('quran-subscriptions.reject');
    Route::get('quran-live-courses/{course}/groups', [QuranClassGroupAdminController::class, 'index'])->name('quran-live-courses.groups.index');
    Route::get('quran-live-courses/{course}/groups/create', [QuranClassGroupAdminController::class, 'create'])->name('quran-live-courses.groups.create');
    Route::post('quran-live-courses/{course}/groups', [QuranClassGroupAdminController::class, 'store'])->name('quran-live-courses.groups.store');
    Route::get('quran-class-groups/{group}/edit', [QuranClassGroupAdminController::class, 'edit'])->name('quran-class-groups.edit');
    Route::put('quran-class-groups/{group}', [QuranClassGroupAdminController::class, 'update'])->name('quran-class-groups.update');
    Route::delete('quran-class-groups/{group}', [QuranClassGroupAdminController::class, 'destroyGroup'])->name('quran-class-groups.destroy');
    Route::get('quran-admissions', [QuranClassGroupAdminController::class, 'admissions'])->name('quran-admissions.index');
    Route::post('quran-admissions/{admission}/assign', [QuranClassGroupAdminController::class, 'assignToGroup'])->name('quran-admissions.assign');
    Route::post('quran-admissions/{admission}/reject', [QuranClassGroupAdminController::class, 'rejectAdmission'])->name('quran-admissions.reject');
    Route::post('quran-group-students/{student}/status', [QuranClassGroupAdminController::class, 'updateStudentStatus'])->name('quran-group-students.status');
    // Volunteer Management (permission-gated, see App\Support\PermissionCatalog)
    Route::get('volunteers', [VolunteerAdminController::class, 'index'])->name('volunteers.index')->middleware('can:volunteers.view');
    Route::post('volunteers/{volunteer}/approve', [VolunteerAdminController::class, 'approve'])->name('volunteers.approve')->middleware('can:volunteers.manage');
    Route::post('volunteers/{volunteer}/reject', [VolunteerAdminController::class, 'reject'])->name('volunteers.reject')->middleware('can:volunteers.manage');
    Route::delete('volunteers/{volunteer}', [VolunteerAdminController::class, 'destroy'])->name('volunteers.destroy')->middleware('can:volunteers.delete');

    // Sallaamti Wall moderation (permission-gated)
    Route::get('wall', [DuaWallAdminController::class, 'index'])->name('wall.index')->middleware('can:wall.view');
    Route::post('wall/{duaRequest}/approve', [DuaWallAdminController::class, 'approve'])->name('wall.approve')->middleware('can:wall.manage');
    Route::post('wall/{duaRequest}/reject', [DuaWallAdminController::class, 'reject'])->name('wall.reject')->middleware('can:wall.manage');
    Route::delete('wall/{duaRequest}', [DuaWallAdminController::class, 'destroy'])->name('wall.destroy')->middleware('can:wall.delete');

    // Donation Management (permission-gated)
    Route::get('donations', [DonationAdminController::class, 'index'])->name('donations.index')->middleware('can:donations.view');
    Route::post('donations/{donation}/confirm', [DonationAdminController::class, 'confirm'])->name('donations.confirm')->middleware('can:donations.manage');
    Route::post('donations/{donation}/reject', [DonationAdminController::class, 'reject'])->name('donations.reject')->middleware('can:donations.manage');
    Route::get('donation-screenshot/{donation}', [DonationAdminController::class, 'screenshot'])->name('admin.donation.screenshot')->middleware('can:donations.view');
    Route::delete('donations/{donation}', [DonationAdminController::class, 'destroy'])->name('donations.destroy')->middleware('can:donations.delete');

    // Subscribers
    Route::get('/subscribers', [SubscriberAdminController::class, 'index'])->name('subscribers.index');
    Route::delete('/subscribers/{subscriber}', [SubscriberAdminController::class, 'destroy'])->name('subscribers.destroy');

    // Family Support queries (permission-gated)
    Route::get('support', [SupportQueryAdminController::class, 'index'])->name('support.index')->middleware('can:support.view');
    Route::get('support/{query}', [SupportQueryAdminController::class, 'show'])->name('support.show')->middleware('can:support.view');
    Route::post('support/{query}/assign', [SupportQueryAdminController::class, 'assign'])->name('support.assign')->middleware('can:support.manage');
    Route::post('support/{query}/status', [SupportQueryAdminController::class, 'updateStatus'])->name('support.status')->middleware('can:support.manage');
    Route::post('support/{query}/reply', [SupportQueryAdminController::class, 'reply'])->name('support.reply')->middleware('can:support.manage');
    Route::delete('support/{query}', [SupportQueryAdminController::class, 'destroy'])->name('support.destroy')->middleware('can:support.delete');

    // Localization
    Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::put('languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
    Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');
    Route::post('languages/{language}/set-default', [LanguageController::class, 'setDefault'])->name('languages.set-default');

    Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::get('translations/fetch/{locale}', [TranslationController::class, 'fetchByLocale'])->name('translations.fetch');
    Route::post('translations', [TranslationController::class, 'store'])->name('translations.store');
    Route::put('translations/{translation}', [TranslationController::class, 'update'])->name('translations.update');
    Route::delete('translations/{translation}', [TranslationController::class, 'destroy'])->name('translations.destroy');

    // Counseling Bookings
    Route::get('counseling-bookings', [CounselingBookingAdminController::class, 'index'])->name('counseling-bookings.index');
    Route::get('counseling-bookings/{booking}', [CounselingBookingAdminController::class, 'show'])->name('counseling-bookings.show');
    Route::post('counseling-bookings/{booking}/reassign', [CounselingBookingAdminController::class, 'reassign'])->name('counseling-bookings.reassign');
    Route::post('counseling-bookings/{booking}/cancel', [CounselingBookingAdminController::class, 'cancel'])->name('counseling-bookings.cancel');
});

// ============================================================
// BLOG MANAGEMENT (admin + manager + blogger)
// ============================================================

Route::middleware(['auth', 'blog.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('blog-posts', BlogPostController::class)->except(['show']);
    Route::patch('blog-posts/{blog_post}/publish', [BlogPostController::class, 'publish'])->name('blog-posts.publish');
    Route::patch('blog-posts/{blog_post}/unpublish', [BlogPostController::class, 'unpublish'])->name('blog-posts.unpublish');
    Route::post('editor-images', [EditorImageController::class, 'store'])->name('editor-images.store');
});

// ============================================================
// TEACHER ROUTES
// ============================================================

Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/courses', [QuranTeacherController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [QuranTeacherController::class, 'show'])->name('courses.show');
    // The daily-link form on teacher/courses/show.blade.php that posted here
    // is unreachable — show() above always redirects to groups.index before
    // that view can render — and the route itself never worked anyway
    // (postDailyLink() type-hints $group, so a {course} route parameter
    // never bound to it and the ownership check always 403'd). Removed
    // rather than fixed: the working, actually-used path is the groups
    // route directly below.
    // Add to teacher group:
    Route::get('/groups', [QuranTeacherController::class, 'groups'])->name('groups.index');
    Route::get('/groups/{group}', [QuranTeacherController::class, 'showGroup'])->name('groups.show');
    Route::post('/groups/{group}/daily-link', [QuranTeacherController::class, 'postDailyLink'])->name('groups.daily-link.store');
    Route::post('/groups/{group}/students/{student}/assessment', [QuranTeacherController::class, 'storeAssessment'])->name('groups.assessment.store');
    Route::post('/groups/{group}/students/{student}/progress-report', [QuranTeacherController::class, 'storeProgressReport'])->name('groups.progress-report.store');
    Route::get('/students', [QuranTeacherController::class, 'students'])->name('students.index');
    Route::get('/students/{student}', [QuranTeacherController::class, 'studentDetail'])->name('students.show');
    Route::get('/schedule', [QuranTeacherController::class, 'schedule'])->name('schedule');
});

// ============================================================
// COUNSELOR ROUTES
// ============================================================

Route::middleware(['auth', 'counselor'])->prefix('counselor')->name('counselor.')->group(function () {
    Route::get('/availability', [CounselorAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [CounselorAvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{availability}', [CounselorAvailabilityController::class, 'destroy'])->name('availability.destroy');
    Route::get('/bookings', [CounselorBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/confirm', [CounselorBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/complete', [CounselorBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [CounselorBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/no-show', [CounselorBookingController::class, 'markNoShow'])->name('bookings.no-show');
});

require __DIR__ . '/auth.php';
