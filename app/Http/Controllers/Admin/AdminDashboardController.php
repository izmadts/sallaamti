<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\NikahProfile;
use App\Models\NikahReport;
use App\Models\QuranAdmission;
use App\Models\QuranClassGroup;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use App\Models\QuranSubscription;
use App\Models\Subscriber;
use App\Models\Donation;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\SupportQuery;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            // Users
            'total_users'               => User::count(),
            'new_users_this_month'      => User::whereMonth('created_at', now()->month)->count(),

            // Nikah
            'pending_nikah_payments'    => NikahProfile::where('payment_status', 'submitted')->count(),
            'pending_nikah_verification' => NikahProfile::where('payment_status', 'confirmed')
                ->where('verification_status', 'pending')->count(),
            'total_nikah_profiles'      => NikahProfile::count(),
            'verified_nikah_profiles'   => NikahProfile::where('verification_status', 'verified')->count(),
            'pending_nikah_reports'     => NikahReport::where('status', 'pending')->count(),

            // Quran Self-Paced Courses
            'total_courses'             => Course::count(),
            'published_courses'         => Course::where('is_published', true)->count(),
            'total_enrollments'         => Enrollment::count(),
            'total_certificates'        => Certificate::count(),

            // Quran Live Classes
            'total_live_courses'        => QuranLiveCourse::count(),
            'published_live_courses'    => QuranLiveCourse::where('is_published', true)->count(),
            'total_class_groups'        => QuranClassGroup::count(),
            'active_class_groups'       => QuranClassGroup::where('is_active', true)->count(),
            'total_live_students'       => QuranGroupStudent::where('status', 'active')->count(),
            'pending_admissions'        => QuranAdmission::where('status', 'pending')->count(),
            'total_admissions'          => QuranAdmission::count(),
            'pending_live_subscriptions' => QuranSubscription::where('payment_status', 'submitted')->count(),

            // Volunteers
            'pending_volunteers'        => VolunteerApplication::where('status', 'pending')->count(),
            'total_volunteers'          => VolunteerApplication::where('status', 'approved')->count(),

            // Donations
            'total_donations'           => Donation::count(),
            'pending_donations'         => Donation::where('payment_status', 'submitted')->count(),

            // Subscribers
            'total_subscribers'         => Subscriber::count(),

            // Support Queries
            'support_new'         => SupportQuery::where('status', 'new')->count(),
            'support_in_progress' => SupportQuery::where('status', 'in_progress')->count(),
            'support_resolved'    => SupportQuery::where('status', 'resolved')->count(),
            'support_closed'      => SupportQuery::where('status', 'closed')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
