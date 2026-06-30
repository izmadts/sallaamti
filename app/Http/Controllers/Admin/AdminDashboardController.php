<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\NikahProfile;
use App\Models\QuranLiveCourse;
use App\Models\VolunteerApplication;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_nikah_verification' => NikahProfile::where('verification_status', 'pending')->count(),
            'pending_nikah_payments' => NikahProfile::where('payment_status', 'submitted')->count(),
            'total_courses' => Course::count(),
            'total_live_courses' => QuranLiveCourse::count(),
            'pending_volunteers' => VolunteerApplication::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
