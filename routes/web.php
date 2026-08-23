<?php

use App\Http\Controllers\NewsletterUnsubscribeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NikahProfileController;
use App\Http\Controllers\NikahProfileWizardController;
use App\Http\Controllers\Admin\NikahProfileWizardController as AdminNikahProfileWizardController;
use App\Http\Controllers\Admin\NikahVerificationController;
use App\Http\Controllers\NikahInterestController;
use App\Http\Controllers\NikahFileController;
use App\Http\Controllers\NikahPhotoController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\Admin\DuaWallAdminController;
use App\Http\Controllers\Admin\CommunityPostController;
use App\Http\Controllers\Admin\BulkMessageController;
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

    return view('index', compact('banners'));
})->name('index');

Route::get('/newsletter/unsubscribe/{user}', [NewsletterUnsubscribeController::class, 'unsubscribe'])
    ->middleware('signed')
    ->name('newsletter.unsubscribe');

// Static pages
Route::get('/about', function () {
    $teamMembers = \App\Models\TeamMember::active()->orderBy('order')->get();
    $testimonials = \App\Models\Testimonial::published()->orderBy('order')->get();
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
Route::get('/skills', [\App\Http\Controllers\SkillsController::class, 'index'])->name('skills.index');
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

// Public Posts — any logged-in member/staff can submit one (admin approval
// required unless the author already holds posts.manage), and every
// published post gets its own shareable public URL. Also doubles as a
// lightweight public "author page" per user (name/avatar/short bio) via
// /posts/author/{username} — both are real indexable URLs, growing the
// site's public URL count for SEO/social sharing. Static segments
// (create/mine) must be registered before the {post:slug} wildcard below,
// or "create"/"mine" would be swallowed as a slug lookup and 404.
Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
Route::get('/posts/author/{user:username}', [\App\Http\Controllers\PostController::class, 'byAuthor'])->name('posts.by-author');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/posts/create', [\App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/mine', [\App\Http\Controllers\PostController::class, 'mine'])->name('posts.mine');
    Route::get('/posts/{post:slug}/edit', [\App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post:slug}', [\App\Http\Controllers\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post:slug}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');
});
Route::get('/posts/{post:slug}', [\App\Http\Controllers\PostController::class, 'show'])->name('posts.show');

// Member Testimonials — any logged-in member can submit one; it appears on
// the public /testimonial page once an admin approves it (see
// Admin\TestimonialController::approve/reject). Static segments (create/
// mine) must come before the {testimonial} wildcard, same reasoning as
// the Posts block above.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/testimonials/create', [\App\Http\Controllers\TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/testimonials', [\App\Http\Controllers\TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('/testimonials/mine', [\App\Http\Controllers\TestimonialController::class, 'mine'])->name('testimonials.mine');
    Route::get('/testimonials/{testimonial}/edit', [\App\Http\Controllers\TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('/testimonials/{testimonial}', [\App\Http\Controllers\TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [\App\Http\Controllers\TestimonialController::class, 'destroy'])->name('testimonials.destroy');
});

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

    // A user can hold more than one staff role at once (e.g. counselor +
    // matchmaker). The generic /dashboard above always resolves to a single
    // role's panel by priority order, so it can't be the target of a
    // role-specific "My Dashboard" nav link — this gives the Matchmaking
    // dropdown a link that always lands on the matchmaker panel regardless
    // of what other roles the account has.
    Route::get('/dashboard/matchmaker', [DashboardController::class, 'matchmaker'])->name('dashboard.matchmaker');

    // User Guide — tabbed documentation, one tab per role the visitor
    // actually holds (see UserGuideController).
    Route::get('/guide', [\App\Http\Controllers\UserGuideController::class, 'index'])->name('guide.index');

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
    // Admission is a wizard (HasWizardSteps, same session-per-step pattern
    // as the Nikah profile wizard) instead of one long single-page form.
    Route::get('/quran-live/{course}/admission', [QuranLiveCourseController::class, 'admissionStart'])->name('quran-live.admission');
    Route::get('/quran-live/{course}/admission/step/{step}', [QuranLiveCourseController::class, 'admissionShowStep'])->name('quran-live.admission.step');
    Route::post('/quran-live/{course}/admission/step/{step}', [QuranLiveCourseController::class, 'admissionSaveStep'])->name('quran-live.admission.step.save');
    Route::get('/quran-live/{course}/admission/review', [QuranLiveCourseController::class, 'admissionReview'])->name('quran-live.admission.review');
    Route::post('/quran-live/{course}/admission/finalize', [QuranLiveCourseController::class, 'admissionFinalize'])->name('quran-live.admission.finalize');
    Route::get('/quran-live/{course}/subscribe/{admission}', [QuranLiveCourseController::class, 'subscribe'])->name('quran-live.subscribe');
    Route::post('/quran-live/{course}/subscribe/{admission}', [QuranLiveCourseController::class, 'storeSubscription'])->name('quran-live.subscribe.store');
    Route::get('/quran-subscriptions/{subscription}/screenshot', [QuranSubscriptionFileController::class, 'show'])->name('quran-subscription.screenshot');
    Route::post('/quran-admissions/{admission}/reply', [QuranLiveCourseController::class, 'reply'])->name('quran-live.admission.reply');
    
    // --- DONATIONS (auth extras) ---
    Route::get('/my-donations', [DonationController::class, 'myDonations'])->name('donate.my');
    Route::get('/donation-screenshot/{donation}', [DonationController::class, 'screenshot'])->name('donation.screenshot');
    Route::get('/thank-you', fn() => view('thank-you'))->name('thank-you');

    // --- FAMILY COUNSELING BOOKING ---
    Route::get('/support/book', [CounselingBookingController::class, 'start'])->name('counseling.book.start');
    Route::get('/support/book/step/{step}', [CounselingBookingController::class, 'showStep'])->name('counseling.book.step');
    Route::post('/support/book/step/{step}', [CounselingBookingController::class, 'saveStep'])->name('counseling.book.step.save');
    Route::get('/support/book/review', [CounselingBookingController::class, 'review'])->name('counseling.book.review');
    Route::post('/support/book/finalize', [CounselingBookingController::class, 'finalize'])->name('counseling.book.finalize');
    Route::get('/support/bookings', [CounselingBookingController::class, 'index'])->name('counseling.bookings.index');
    Route::get('/support/bookings/{booking}', [CounselingBookingController::class, 'show'])->name('counseling.bookings.show');
    Route::post('/support/bookings/{booking}/cancel', [CounselingBookingController::class, 'cancel'])->name('counseling.bookings.cancel');
    Route::post('/support/bookings/{booking}/reply', [CounselingBookingController::class, 'reply'])->name('counseling.bookings.reply');
    Route::post('/support/bookings/{booking}/rate', [CounselingBookingController::class, 'rate'])->name('counseling.bookings.rate');
});

// ============================================================
// ADMIN ROUTES
// ============================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // User Management, Settings, and Maintenance — always admin-role-only
    // (see PermissionCatalog's docblock). The general 'admin' middleware
    // above lets in any staff role holding even one narrow permission (e.g.
    // wall.view), so these need the stricter 'admin.only' on top of it —
    // role assignment, live app config, and destructive maintenance actions
    // are not "can this staff member moderate content" style decisions.
    Route::middleware('admin.only')->group(function () {
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        // Must be registered before users/{user} below — otherwise Laravel's
        // implicit route-model binding swallows this as {user}="broadcast"
        // (confirmed via router match test) and 404s.
        Route::get('users/broadcast', [BulkMessageController::class, 'create'])->name('users.broadcast');
        Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::put('users/{user}/details', [UserManagementController::class, 'updateDetails'])->name('users.update-details');
        Route::post('users/{user}/send-password-reset', [UserManagementController::class, 'sendPasswordReset'])->name('users.send-password-reset');
        Route::put('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
        Route::put('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::get('users/roles/manage', [UserManagementController::class, 'roles'])->name('users.roles');
        Route::post('users/roles', [UserManagementController::class, 'storeRole'])->name('users.roles.store');
        Route::post('users/roles/{role}/permissions', [UserManagementController::class, 'updatePermissions'])->name('users.roles.permissions');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/demo-nikah-profiles/generate', [SettingsController::class, 'generateDemoNikahProfiles'])->name('settings.demo-nikah.generate');
        Route::post('settings/demo-nikah-profiles/remove', [SettingsController::class, 'removeDemoNikahProfiles'])->name('settings.demo-nikah.remove');
        Route::get('maintenance', [SystemMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance/optimize-database', [SystemMaintenanceController::class, 'optimizeDatabase'])->name('maintenance.optimize-database');
        Route::post('maintenance/optimize-images', [SystemMaintenanceController::class, 'optimizeImages'])->name('maintenance.optimize-images');

        // Bulk messaging (Email / WhatsApp) — uses the same Gmail/WhatsApp
        // credentials as Integrations, and touches every member's contact
        // info at once, so it stays admin-role-only rather than being added
        // to PermissionCatalog.
        Route::post('users/broadcast', [BulkMessageController::class, 'store'])->name('bulk-messages.store');
        Route::get('subscribers/broadcast', [BulkMessageController::class, 'createForSubscribers'])->name('subscribers.broadcast');
        Route::post('subscribers/broadcast', [BulkMessageController::class, 'storeForSubscribers'])->name('bulk-messages.subscribers.store');
        Route::get('bulk-messages', [BulkMessageController::class, 'index'])->name('bulk-messages.index');
        Route::get('bulk-messages/{bulkMessage}', [BulkMessageController::class, 'show'])->name('bulk-messages.show');
    });

    Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
    Route::post('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');
    Route::resource('banners', BannerController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');
    Route::post('testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::post('testimonials/{testimonial}/reject', [TestimonialController::class, 'reject'])->name('testimonials.reject');
    Route::resource('team-members', TeamMemberController::class)->except(['show']);
    Route::post('team-members/{team_member}/toggle', [TeamMemberController::class, 'toggle'])->name('team-members.toggle');

    Route::resource('faqs', \App\Http\Controllers\Admin\FaqAdminController::class)->except(['show']);

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
    // Live API credentials, so this whole block is admin-role-only (see the
    // Users/Settings/Maintenance group above for why).
    Route::middleware('admin.only')->group(function () {
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
    });

    // Certificates (admin-issued) — 'admin.only', not in PermissionCatalog;
    // issuing an arbitrary certificate isn't a "delegate to one narrow
    // staff role" style action (security audit finding, see the Courses
    // block above for the same reasoning).
    Route::middleware('admin.only')->group(function () {
        Route::get('certificates', [CertificateAdminController::class, 'index'])->name('certificates.index');
        Route::get('certificates/create', [CertificateAdminController::class, 'create'])->name('certificates.create');
        Route::post('certificates', [CertificateAdminController::class, 'store'])->name('certificates.store');
        Route::delete('certificates/{certificate}', [CertificateAdminController::class, 'destroy'])->name('certificates.destroy');
    });

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
    // Narrower than nikah.manage below — gated inside the controller via
    // nikah.manage OR nikah.create-profile, so matchmaker can help a walk-in
    // create an account without also unlocking approve/reject/payments/etc.
    // A step wizard (HasWizardSteps, same session-per-step pattern as the
    // member-facing Nikah wizard) rather than one long single-page form.
    Route::get('/nikah-profiles/create', [AdminNikahProfileWizardController::class, 'start'])->name('nikah.profiles.create');
    Route::get('/nikah-profiles/create/step/{step}', [AdminNikahProfileWizardController::class, 'showStep'])->name('nikah.profiles.create.step');
    Route::post('/nikah-profiles/create/step/{step}', [AdminNikahProfileWizardController::class, 'saveStep'])->name('nikah.profiles.create.step.save');
    Route::get('/nikah-profiles/create/review', [AdminNikahProfileWizardController::class, 'review'])->name('nikah.profiles.create.review');
    Route::post('/nikah-profiles/create/finalize', [AdminNikahProfileWizardController::class, 'finalize'])->name('nikah.profiles.create.finalize');

    // Lean matchmaker-assist CRM — see LeadController's class docblock for
    // scope. Gated inside the controller the same way as the walk-in
    // wizard above (nikah.manage OR nikah.create-profile), not admin.only,
    // so a matchmaker can run their own leads without the full admin role.
    Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class)->except(['edit']);
    Route::post('/leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/link-profile', [\App\Http\Controllers\Admin\LeadController::class, 'linkProfile'])->name('leads.link-profile');
    Route::post('/leads/{lead}/shortlist', [\App\Http\Controllers\Admin\LeadController::class, 'addToShortlist'])->name('leads.shortlist.add');
    Route::post('/leads/{lead}/shortlist/{item}/sent', [\App\Http\Controllers\Admin\LeadController::class, 'markShortlistSent'])->name('leads.shortlist.sent');
    Route::delete('/leads/{lead}/shortlist/{item}', [\App\Http\Controllers\Admin\LeadController::class, 'removeFromShortlist'])->name('leads.shortlist.remove');
    Route::middleware('admin.only')->group(function () {
        Route::get('/nikah-contact-requests', [NikahVerificationController::class, 'contactRequests'])->name('nikah.contact-requests');
        Route::post('/nikah-contact-requests/{contactRequest}/approve', [NikahVerificationController::class, 'approveContactRequest'])->name('nikah.contact-requests.approve');
        Route::post('/nikah-contact-requests/{contactRequest}/deny', [NikahVerificationController::class, 'denyContactRequest'])->name('nikah.contact-requests.deny');
    });
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

    // Quran Courses Management + Quran Live Classes Management — not in
    // PermissionCatalog (nothing here is a "delegate to one narrow staff
    // role" style resource), so — like Users/Settings/Maintenance and
    // Integrations above — this needs 'admin.only' on top of the general
    // 'admin' gate. Security audit finding: the general 'admin' middleware
    // admits anyone holding even one unrelated narrow permission (e.g.
    // volunteers.view), which without this wrapper could reach course
    // content management and Quran Live payment confirmation.
    Route::middleware('admin.only')->group(function () {
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
    });
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

    // Public Posts moderation (permission-gated, see App\Support\PermissionCatalog)
    Route::get('posts', [\App\Http\Controllers\Admin\PostAdminController::class, 'index'])->name('posts.index')->middleware('can:posts.view');
    Route::post('posts/{post}/approve', [\App\Http\Controllers\Admin\PostAdminController::class, 'approve'])->name('posts.approve')->middleware('can:posts.manage');
    Route::post('posts/{post}/reject', [\App\Http\Controllers\Admin\PostAdminController::class, 'reject'])->name('posts.reject')->middleware('can:posts.manage');
    Route::delete('posts/{post}', [\App\Http\Controllers\Admin\PostAdminController::class, 'destroy'])->name('posts.destroy')->middleware('can:posts.delete');

    // Donation Management (permission-gated)
    Route::get('donations', [DonationAdminController::class, 'index'])->name('donations.index')->middleware('can:donations.view');
    Route::post('donations/{donation}/confirm', [DonationAdminController::class, 'confirm'])->name('donations.confirm')->middleware('can:donations.manage');
    Route::post('donations/{donation}/reject', [DonationAdminController::class, 'reject'])->name('donations.reject')->middleware('can:donations.manage');
    Route::get('donation-screenshot/{donation}', [DonationAdminController::class, 'screenshot'])->name('admin.donation.screenshot')->middleware('can:donations.view');
    Route::delete('donations/{donation}', [DonationAdminController::class, 'destroy'])->name('donations.destroy')->middleware('can:donations.delete');

    // Subscribers — 'admin.only'; this is a raw contact-info export/delete
    // surface (email/phone PII), not a delegatable moderation resource
    // (security audit finding).
    Route::middleware('admin.only')->group(function () {
        Route::get('/subscribers', [SubscriberAdminController::class, 'index'])->name('subscribers.index');
        Route::delete('/subscribers/{subscriber}', [SubscriberAdminController::class, 'destroy'])->name('subscribers.destroy');
    });

    // API Console — 'admin.only'; a test call impersonates any chosen
    // member via a real access token, so this is the same privilege
    // category as Subscribers/Localization above.
    Route::middleware('admin.only')->group(function () {
        Route::get('api-console', [\App\Http\Controllers\Admin\ApiConsoleController::class, 'index'])->name('api-console.index');
        Route::post('api-console/test', [\App\Http\Controllers\Admin\ApiConsoleController::class, 'test'])->name('api-console.test');
    });

    // Localization — 'admin.only'; site-wide config, same category as
    // Settings above, not a delegatable resource (security audit finding).
    Route::middleware('admin.only')->group(function () {
        Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
        Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
        Route::put('languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
        Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');
        Route::post('languages/{language}/set-default', [LanguageController::class, 'setDefault'])->name('languages.set-default');

        Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
        Route::post('translations/rescan', [TranslationController::class, 'rescan'])->name('translations.rescan');
        Route::get('translations/fetch/{locale}', [TranslationController::class, 'fetchByLocale'])->name('translations.fetch');
        Route::post('translations', [TranslationController::class, 'store'])->name('translations.store');
        Route::put('translations/{translation}', [TranslationController::class, 'update'])->name('translations.update');
        Route::delete('translations/{translation}', [TranslationController::class, 'destroy'])->name('translations.destroy');
    });

    // Counseling Bookings
    // Counseling Bookings (permission-gated, see App\Support\PermissionCatalog)
    Route::get('counseling-bookings', [CounselingBookingAdminController::class, 'index'])->name('counseling-bookings.index')->middleware('can:counseling.view');
    Route::get('counseling-bookings/{booking}', [CounselingBookingAdminController::class, 'show'])->name('counseling-bookings.show')->middleware('can:counseling.view');
    Route::post('counseling-bookings/{booking}/reassign', [CounselingBookingAdminController::class, 'reassign'])->name('counseling-bookings.reassign')->middleware('can:counseling.manage');
    Route::post('counseling-bookings/{booking}/cancel', [CounselingBookingAdminController::class, 'cancel'])->name('counseling-bookings.cancel')->middleware('can:counseling.delete');
    Route::post('counseling-bookings/{booking}/reply', [CounselingBookingAdminController::class, 'reply'])->name('counseling-bookings.reply')->middleware('can:counseling.manage');
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
    Route::post('/students/{student}/message', [QuranTeacherController::class, 'replyToStudent'])->name('students.message');
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
    Route::get('/bookings/{booking}', [CounselorBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [CounselorBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/complete', [CounselorBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [CounselorBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/no-show', [CounselorBookingController::class, 'markNoShow'])->name('bookings.no-show');
    Route::post('/bookings/{booking}/reply', [CounselorBookingController::class, 'reply'])->name('bookings.reply');
});

// ============================================================
// MATCHMAKER ROUTES — browse-only, redacted (no CNIC/photo/contact); a
// contact detail is only visible once admin approves a specific request.
// ============================================================

Route::middleware(['auth', 'matchmaker'])->prefix('matchmaker')->name('matchmaker.')->group(function () {
    Route::get('/nikah-profiles', [\App\Http\Controllers\Matchmaker\NikahBrowseController::class, 'index'])->name('nikah.index');
    Route::get('/nikah-requests', [\App\Http\Controllers\Matchmaker\NikahBrowseController::class, 'myRequests'])->name('nikah.requests');
    Route::get('/nikah-profiles/{profile}', [\App\Http\Controllers\Matchmaker\NikahBrowseController::class, 'show'])->name('nikah.show');
    Route::post('/nikah-profiles/{profile}/request-contact', [\App\Http\Controllers\Matchmaker\NikahBrowseController::class, 'requestContact'])->name('nikah.request-contact');

    // The matchmaker's own themed workspace — separate from /admin/leads
    // (which stays as admin's global oversight view), built on the exact
    // same Lead/LeadShortlistItem models. See Matchmaker\ClientController.
    Route::resource('clients', \App\Http\Controllers\Matchmaker\ClientController::class)->except(['edit', 'destroy'])->parameters(['clients' => 'lead']);
    Route::post('/clients/{lead}/convert', [\App\Http\Controllers\Matchmaker\ClientController::class, 'convert'])->name('clients.convert');
    Route::post('/clients/{lead}/link-profile', [\App\Http\Controllers\Matchmaker\ClientController::class, 'linkProfile'])->name('clients.link-profile');
    Route::post('/clients/{lead}/shortlist', [\App\Http\Controllers\Matchmaker\ClientController::class, 'addToShortlist'])->name('clients.shortlist.add');
    Route::delete('/clients/{lead}/shortlist/{item}', [\App\Http\Controllers\Matchmaker\ClientController::class, 'removeFromShortlist'])->name('clients.shortlist.remove');
    Route::post('/clients/{lead}/requirements', [\App\Http\Controllers\Matchmaker\ClientController::class, 'saveRequirement'])->name('clients.requirements.save');
    Route::post('/clients/{lead}/proposal-batches', [\App\Http\Controllers\Matchmaker\ClientController::class, 'createBatch'])->name('clients.batches.create');
    Route::post('/clients/{lead}/proposal-batches/{batch}/proposals', [\App\Http\Controllers\Matchmaker\ClientController::class, 'addProposal'])->name('clients.batches.proposals.add');
    Route::delete('/clients/{lead}/proposal-batches/{batch}/proposals/{proposal}', [\App\Http\Controllers\Matchmaker\ClientController::class, 'removeProposal'])->name('clients.batches.proposals.remove');
    Route::post('/clients/{lead}/proposal-batches/{batch}/send', [\App\Http\Controllers\Matchmaker\ClientController::class, 'sendBatch'])->name('clients.batches.send');
    Route::post('/clients/{lead}/proposal-batches/{batch}/proposals/{proposal}/regenerate-link', [\App\Http\Controllers\Matchmaker\ClientController::class, 'regenerateLink'])->name('clients.batches.proposals.regenerate-link');
    Route::post('/clients/{lead}/progress-link', [\App\Http\Controllers\Matchmaker\ClientController::class, 'regenerateProgressLink'])->name('clients.progress-link.regenerate');
});

// Public, no-login signed-link actions — reachable only via a link a
// matchmaker sends the client directly (WhatsApp/SMS/read aloud), not
// through the website's normal navigation. See
// Public\MatchmakingActionController's class docblock.
Route::middleware('signed')->prefix('m')->name('public.matchmaking.')->group(function () {
    Route::get('/proposal/{proposal}', [\App\Http\Controllers\Public\MatchmakingActionController::class, 'showProposal'])->name('proposal.show');
    Route::post('/proposal/{proposal}/respond', [\App\Http\Controllers\Public\MatchmakingActionController::class, 'respondProposal'])->name('proposal.respond');
    Route::get('/progress/{lead}', [\App\Http\Controllers\Public\MatchmakingProgressController::class, 'show'])->name('progress.show');
    Route::post('/progress/{lead}/verify', [\App\Http\Controllers\Public\MatchmakingProgressController::class, 'verify'])->name('progress.verify');
});

require __DIR__ . '/auth.php';
