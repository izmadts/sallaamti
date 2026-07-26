<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NikahProfileController;
use App\Http\Controllers\Admin\NikahVerificationController;
use App\Http\Controllers\NikahInterestController;
use App\Http\Controllers\NikahFileController;
use App\Http\Controllers\NikahPhotoController;
use App\Http\Controllers\UserAvatarController;
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
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SubscriberAdminController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\EditorImageController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\ActivityPostController;
use App\Http\Controllers\Admin\CertificateAdminController;
use App\Http\Controllers\SupportQueryController;
use App\Http\Controllers\Admin\SupportQueryAdminController;

// ============================================================
// PUBLIC ROUTES (no auth required)
// ============================================================

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();
    $testimonials = \App\Models\Testimonial::where('is_active', true)->orderBy('order')->get();
    return view('index', compact('banners', 'testimonials'));
})->name('index');
// Route::get('/', function () {
//     if (auth()->check()) {
//         return redirect()->route('dashboard');
//     }
//     $banners = \App\Models\Banner::active()->get();
//     $testimonials = \App\Models\Testimonial::where('is_active', true)->orderBy('order')->get();
//     return view('index', compact('banners', 'testimonials'));
// })->name('home');
// Static pages
Route::get('/about', function () {
    $teamMembers = \App\Models\TeamMember::active()->orderBy('order')->get();
    return view('about', compact('teamMembers'));
});
Route::get('/activities', function () {
    $activityPosts = \App\Models\ActivityPost::active()->orderBy('activity_date', 'desc')->orderBy('order')->get();
    return view('activities', compact('activityPosts'));
});
Route::get('/events', fn() => view('events'));
Route::get('/sermons', fn() => view('sermons'));
Route::get('/team', function () {
    $teamMembers = \App\Models\TeamMember::active()->orderBy('order')->get();
    return view('team', compact('teamMembers'));
});
Route::get('/testimonial', fn() => view('testimonial'));
Route::get('/contact', fn() => view('contact'));

// Public module pages (browsable without login)
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/quran-live', [QuranLiveCourseController::class, 'index'])->name('quran-live.index');
Route::get('/quran-live/{course}', [QuranLiveCourseController::class, 'show'])->name('quran-live.show');

// Blog (public reading)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog_post:slug}', [BlogController::class, 'show'])->name('blog.show');

// Volunteer (guests can apply)
Route::get('/volunteer', [VolunteerController::class, 'create'])->name('volunteer.create');
Route::post('/volunteer', [VolunteerController::class, 'store'])->name('volunteer.store');

// Donation (guests can donate)
Route::get('/donate', [DonationController::class, 'create'])->name('donate.create');
Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');
Route::get('/donate/{donation}/thank-you', [DonationController::class, 'thankYou'])->name('donate.thank-you');

// Contact
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Newsletter
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/subscriber/verify/{token}', [SubscriberController::class, 'verify'])->name('subscriber.verify');
Route::get('/subscriber/unsubscribe/{id}', [SubscriberController::class, 'unsubscribe'])->name('subscriber.unsubscribe');

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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Avatar
    Route::get('/user/avatar/{user}', [UserAvatarController::class, 'show'])->name('user.avatar');

    // Notifications
    Route::post('/notifications/mark-all-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');

    // --- NIKAH MODULE ---
    Route::get('/nikah/create', [NikahProfileController::class, 'create'])->name('nikah.create');
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
    Route::post('/nikah/report/{profile}', [NikahSafetyController::class, 'report'])->name('nikah.report');
    Route::post('/nikah/toggle-active', [NikahSafetyController::class, 'toggleActive'])->name('nikah.toggle-active');

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

    // Settings & Frontend
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
    Route::post('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');
    Route::resource('banners', BannerController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');
    Route::resource('team-members', TeamMemberController::class)->except(['show']);
    Route::post('team-members/{team_member}/toggle', [TeamMemberController::class, 'toggle'])->name('team-members.toggle');
    Route::resource('activity-posts', ActivityPostController::class)->except(['show']);
    Route::post('activity-posts/{activity_post}/toggle', [ActivityPostController::class, 'toggle'])->name('activity-posts.toggle');

    // Certificates (admin-issued)
    Route::get('certificates', [CertificateAdminController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [CertificateAdminController::class, 'create'])->name('certificates.create');
    Route::post('certificates', [CertificateAdminController::class, 'store'])->name('certificates.store');

    // Nikah Management
    Route::get('/nikah-verifications', [NikahVerificationController::class, 'index'])->name('nikah.verifications');
    Route::post('/nikah-verifications/{profile}/approve', [NikahVerificationController::class, 'approve'])->name('nikah.approve');
    Route::post('/nikah-verifications/{profile}/reject', [NikahVerificationController::class, 'reject'])->name('nikah.reject');
    Route::get('/nikah-payments', [NikahPaymentAdminController::class, 'index'])->name('nikah.payments');
    Route::post('/nikah-payments/{profile}/confirm', [NikahPaymentAdminController::class, 'confirm'])->name('nikah.payments.confirm');
    Route::post('/nikah-payments/{profile}/reject', [NikahPaymentAdminController::class, 'reject'])->name('nikah.payments.reject');
    Route::get('nikah-reports', [NikahSafetyController::class, 'adminReports'])->name('nikah.reports');
    Route::post('nikah-reports/{report}/dismiss', [NikahSafetyController::class, 'dismissReport'])->name('nikah.reports.dismiss');

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
    Route::get('quran-admissions', [QuranClassGroupAdminController::class, 'admissions'])->name('quran-admissions.index');
    Route::post('quran-admissions/{admission}/assign', [QuranClassGroupAdminController::class, 'assignToGroup'])->name('quran-admissions.assign');
    Route::post('quran-admissions/{admission}/reject', [QuranClassGroupAdminController::class, 'rejectAdmission'])->name('quran-admissions.reject');
    Route::post('quran-group-students/{student}/status', [QuranClassGroupAdminController::class, 'updateStudentStatus'])->name('quran-group-students.status');
    // Volunteer Management
    Route::get('volunteers', [VolunteerAdminController::class, 'index'])->name('volunteers.index');
    Route::post('volunteers/{volunteer}/approve', [VolunteerAdminController::class, 'approve'])->name('volunteers.approve');
    Route::post('volunteers/{volunteer}/reject', [VolunteerAdminController::class, 'reject'])->name('volunteers.reject');

    // Donation Management
    Route::get('donations', [DonationAdminController::class, 'index'])->name('donations.index');
    Route::post('donations/{donation}/confirm', [DonationAdminController::class, 'confirm'])->name('donations.confirm');
    Route::post('donations/{donation}/reject', [DonationAdminController::class, 'reject'])->name('donations.reject');
    Route::get('donation-screenshot/{donation}', [DonationAdminController::class, 'screenshot'])->name('admin.donation.screenshot');

    // Subscribers
    Route::get('/subscribers', [SubscriberAdminController::class, 'index'])->name('subscribers.index');
    Route::delete('/subscribers/{subscriber}', [SubscriberAdminController::class, 'destroy'])->name('subscribers.destroy');

    Route::get('support', [SupportQueryAdminController::class, 'index'])->name('support.index');
    Route::get('support/{query}', [SupportQueryAdminController::class, 'show'])->name('support.show');
    Route::post('support/{query}/assign', [SupportQueryAdminController::class, 'assign'])->name('support.assign');
    Route::post('support/{query}/status', [SupportQueryAdminController::class, 'updateStatus'])->name('support.status');
    Route::post('support/{query}/reply', [SupportQueryAdminController::class, 'reply'])->name('support.reply');
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
    Route::post('/courses/{course}/daily-link', [QuranTeacherController::class, 'postDailyLink'])->name('courses.daily-link.store');
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

require __DIR__ . '/auth.php';
