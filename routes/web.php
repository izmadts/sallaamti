<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NikahProfileController;
use App\Http\Controllers\Admin\NikahVerificationController;
use App\Http\Controllers\NikahInterestController;
use App\Http\Controllers\NikahFileController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\NikahPaymentController;
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


//Public Sallaamti Front Website Routes
Route::get('/welcome', function () {return view('welcome');});
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    $banners = \App\Models\Banner::active();
    $testimonials = \App\Models\Testimonial::where('is_active', true)->orderBy('order')->get();
    return view('index', compact('banners', 'testimonials'));
})->name('home');
Route::get('/about', function () {return view('about');});
Route::get('/activities', function () {return view('activities');});
Route::get('/events', function () {return view('events');});
Route::get('/sermons', function () {return view('sermons');});
Route::get('/blog', function () {return view('blog');});
Route::get('/team', function () {return view('team');});
Route::get('/testimonial', function () {return view('testimonial');});
Route::get('/contact', function () {return view('contact');});
//Volunteer Routes
Route::get('/volunteer', [VolunteerController::class, 'create'])->name('volunteer.create');
Route::post('/volunteer', [VolunteerController::class, 'store'])->name('volunteer.store');
//Donation Routes
Route::get('/donate', [DonationController::class, 'create'])->name('donate.create');
Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');
Route::get('/donate/{donation}/thank-you', [DonationController::class, 'thankYou'])->name('donate.thank-you');


// Remove the old closure-based dashboard route entirely, replace with:
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'active'])
    ->name('dashboard');

Route::middleware(['auth', 'active'])->group(
    function () {
        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/nikah/create', [NikahProfileController::class, 'create'])->name('nikah.create');
            Route::post('/nikah', [NikahProfileController::class, 'store'])->name('nikah.store');
            Route::get('/nikah/my-profile', [NikahProfileController::class, 'show'])->name('nikah.show');
            Route::get('/nikah/my-profile/edit', [NikahProfileController::class, 'edit'])->name('nikah.edit');
            Route::put('/nikah/my-profile', [NikahProfileController::class, 'update'])->name('nikah.update');
            Route::get('/nikah/browse', [NikahProfileController::class, 'browse'])->name('nikah.browse');
            Route::get('/nikah/file/{profile}/{type}', [NikahFileController::class, 'show'])->name('nikah.file');
            Route::get('/user/avatar/{user}', [UserAvatarController::class, 'show'])->name('user.avatar');
            Route::post('/nikah/interest/{profile}', [NikahInterestController::class, 'send'])->name('nikah.interest.send');
            Route::get('/nikah/interests', [NikahInterestController::class, 'index'])->name('nikah.interests');
            Route::post('/nikah/interests/{interest}/accept', [NikahInterestController::class, 'accept'])->name('nikah.interest.accept');
            Route::get('/nikah/file/{profile}/{type}', [NikahFileController::class, 'show'])->name('nikah.file');
            Route::post('/nikah/interests/{interest}/decline', [NikahInterestController::class, 'decline'])->name('nikah.interest.decline');
            Route::get('/nikah/payment', [NikahPaymentController::class, 'show'])->name('nikah.payment');
            Route::post('/nikah/payment', [NikahPaymentController::class, 'store'])->name('nikah.payment.store');
            // Quran Online Student-facing
            Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
            Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
            Route::post('/courses/{course:slug}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
            Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
            Route::post('/lessons/{lesson}/complete', [LessonController::class, 'complete'])->name('lessons.complete');
            Route::get('/my-learning', [CourseController::class, 'myLearning'])->name('courses.my-learning');
            Route::get('/courses/{course:slug}/quiz', [QuizController::class, 'show'])->name('quiz.show');
            Route::post('/courses/{course:slug}/quiz', [QuizController::class, 'submit'])->name('quiz.submit');
            Route::get('/lessons/{lesson}/quiz', [QuizController::class, 'showLessonQuiz'])->name('lesson.quiz.show');
            Route::post('/lessons/{lesson}/quiz', [QuizController::class, 'submitLessonQuiz'])->name('lesson.quiz.submit');
            Route::post('/courses/{course:slug}/certificate', [CertificateController::class, 'generate'])->name('certificate.generate');
            Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificate.download');
            Route::get('/my-certificates', [CertificateController::class, 'index'])->name('certificate.index');
            //Quran Live Classes
            Route::get('/quran-live', [QuranLiveCourseController::class, 'index'])->name('quran-live.index');
            Route::get('/quran-live/{course}', [QuranLiveCourseController::class, 'show'])->name('quran-live.show');
            Route::get('/quran-live/{course}/admission', [QuranLiveCourseController::class, 'admissionForm'])->name('quran-live.admission');
            Route::post('/quran-live/{course}/admission', [QuranLiveCourseController::class, 'storeAdmission'])->name('quran-live.admission.store');
            Route::get('/quran-live/{course}/subscribe', [QuranLiveCourseController::class, 'subscribe'])->name('quran-live.subscribe');
            Route::post('/quran-live/{course}/subscribe', [QuranLiveCourseController::class, 'storeSubscription'])->name('quran-live.subscribe.store');
            Route::get('/quran-subscriptions/{subscription}/screenshot', [QuranSubscriptionFileController::class, 'show'])->name('quran-subscription.screenshot');
            //donation Auth Route
            Route::get('/my-donations', [DonationController::class, 'myDonations'])->name('donate.my');
            Route::get('/donation-screenshot/{donation}', [DonationController::class, 'screenshot'])->name('donation.screenshot');
            // Route::get('/thank-you', function () {return view('thank-you');})->name('thank-you');


        }); // End of auth middleware group

        Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

            //User Management Routes
            Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
            Route::put('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
            Route::put('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
            Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
            Route::get('users/roles/manage', [UserManagementController::class, 'roles'])->name('users.roles');
            //Site Settings, FrontEnd and Banners 
            Route::get('settings', fn() => view('admin.settings.index'))->name('settings.index');
            
            Route::get('banners', fn() => view('admin.banners.index'))->name('banners.index');
            Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
            Route::post('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');
            Route::resource('banners', BannerController::class);

            //Testimonials Management
            Route::resource('testimonials', TestimonialController::class);
            Route::post('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');

            //nikah verification management
            Route::get('/nikah-verifications', [NikahVerificationController::class, 'index'])->name('nikah.verifications');
            Route::post('/nikah-verifications/{profile}/approve', [NikahVerificationController::class, 'approve'])->name('nikah.approve');
            Route::post('/nikah-verifications/{profile}/reject', [NikahVerificationController::class, 'reject'])->name('nikah.reject');
            Route::get('/nikah-payments', [NikahPaymentAdminController::class, 'index'])->name('nikah.payments');
            Route::post('/nikah-payments/{profile}/confirm', [NikahPaymentAdminController::class, 'confirm'])->name('nikah.payments.confirm');
            Route::post('/nikah-payments/{profile}/reject', [NikahPaymentAdminController::class, 'reject'])->name('nikah.payments.reject');
            //Quran course management
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
            //Quran Live Classes management
            Route::resource('quran-live-courses', QuranLiveCourseAdminController::class);
            //Route::get('quran-live-courses/{course}/subscriptions', [QuranLiveCourseAdminController::class, 'subscriptions'])->name('quran-live-courses.subscriptions');
            Route::get('quran-live-courses/{quranLiveCourse}/subscriptions', [QuranLiveCourseAdminController::class, 'subscriptions'])->name('quran-live-courses.subscriptions');
            Route::post('quran-subscriptions/{subscription}/confirm', [QuranLiveCourseAdminController::class, 'confirmPayment'])->name('quran-subscriptions.confirm');
            Route::post('quran-subscriptions/{subscription}/reject', [QuranLiveCourseAdminController::class, 'rejectPayment'])->name('quran-subscriptions.reject');
            //Volunteer management
            Route::get('volunteers', [VolunteerAdminController::class, 'index'])->name('volunteers.index');
            Route::post('volunteers/{volunteer}/approve', [VolunteerAdminController::class, 'approve'])->name('volunteers.approve');
            Route::post('volunteers/{volunteer}/reject', [VolunteerAdminController::class, 'reject'])->name('volunteers.reject');
            //donation management
            Route::get('donations', [DonationAdminController::class, 'index'])->name('donations.index');
            Route::post('donations/{donation}/confirm', [DonationAdminController::class, 'confirm'])->name('donations.confirm');
            Route::post('donations/{donation}/reject', [DonationAdminController::class, 'reject'])->name('donations.reject');
            Route::get('donation-screenshot/{donation}', [DonationAdminController::class, 'screenshot'])->name('admin.donation.screenshot');
            // Additional admin OWN DASHBOARD routes
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            //Subscription management
            Route::get('/subscribers', [SubscriberAdminController::class, 'index'])->name('subscribers.index');
            Route::delete('/subscribers/{subscriber}', [SubscriberAdminController::class, 'destroy'])->name('subscribers.destroy');
            Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        }); // End of admin middleware group

        Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
            Route::get('/courses', [QuranTeacherController::class, 'index'])->name('courses.index');
            Route::get('/courses/{course}', [QuranTeacherController::class, 'show'])->name('courses.show');
            Route::post('/courses/{course}/daily-link', [QuranTeacherController::class, 'postDailyLink'])->name('courses.daily-link.store');
        }); // End of teacher middleware group
    });
// Public verification route — no auth needed, anyone can verify a certificate is real
Route::get('/verify-certificate/{certificateNumber}', [CertificateController::class, 'verify'])->name('certificate.verify');
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::get('/subscriber/verify/{token}', [SubscriberController::class, 'verify'])->name('subscriber.verify');
Route::get('/subscriber/unsubscribe/{id}', [SubscriberController::class, 'unsubscribe'])->name('subscriber.unsubscribe');






require __DIR__.'/auth.php';
