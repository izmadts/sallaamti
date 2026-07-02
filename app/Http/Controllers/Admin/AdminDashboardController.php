<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\NikahProfile;
use App\Models\QuranAdmission;
use App\Models\QuranLiveCourse;
use App\Models\QuranSubscription;
use App\Models\Subscriber;
use App\Models\Donation;
use App\Models\User;
use App\Models\VolunteerApplication;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            // Users
            'total_users' => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),

            // Nikah
            'pending_nikah_payments' => NikahProfile::where('payment_status', 'submitted')->count(),
            'pending_nikah_verification' => NikahProfile::where('payment_status', 'confirmed')
                ->where('verification_status', 'pending')->count(),
            'total_nikah_profiles' => NikahProfile::count(),
            'verified_nikah_profiles' => NikahProfile::where('verification_status', 'verified')->count(),

            // Quran Courses
            'total_courses' => Course::count(),
            'published_courses' => Course::where('is_published', true)->count(),
            'total_enrollments' => Enrollment::count(),
            'total_certificates' => Certificate::count(),

            // Live Quran Courses
            'total_live_courses' => QuranLiveCourse::count(),
            'pending_live_subscriptions' => QuranSubscription::where('payment_status', 'submitted')->count(),
            'pending_admissions' => QuranAdmission::count(),

            // Volunteers
            'pending_volunteers' => VolunteerApplication::where('status', 'pending')->count(),
            'total_volunteers' => VolunteerApplication::where('status', 'approved')->count(),
            
            // Donations
            'total_donations' => Donation::count(),
            // Subscribers
            'total_subscribers' => Subscriber::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
