<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('teacher')) {
            return $this->teacherDashboard($user);
        }

        if ($user->hasRole('counselor')) {
            return $this->counselorDashboard($user);
        }

        return $this->memberDashboard($user);
    }

    private function teacherDashboard($user)
    {
        $courses = \App\Models\QuranLiveCourse::where('teacher_id', $user->id)->get();

        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => \App\Models\QuranSubscription::whereIn('quran_live_course_id', $courses->pluck('id'))
                ->where('month', now()->format('Y-m'))
                ->where('payment_status', 'confirmed')
                ->count(),
            'todays_links_posted' => \App\Models\QuranDailyLink::whereIn('quran_live_course_id', $courses->pluck('id'))
                ->where('class_date', now()->toDateString())
                ->count(),
        ];

        return view('dashboard.teacher', compact('user', 'courses', 'stats'));
    }

    private function counselorDashboard($user)
    {
        // Placeholder until Family Counseling module is built
        return view('dashboard.counselor', compact('user'));
    }

    private function memberDashboard($user)
    {
        $enrollments = $user->enrollments()->with('course')->latest()->get();
        $nikahProfile = $user->nikahProfile;
        $certificates = $user->certificates()->with('course')->latest()->get();

        $liveSubscriptions = \App\Models\QuranSubscription::where('user_id', $user->id)
            ->where('month', now()->format('Y-m'))
            ->with('course')
            ->get();

        $unreadNotifications = $user->unreadNotifications->take(5);

        $stats = [
            'courses_enrolled' => $enrollments->count(),
            'certificates_earned' => $certificates->count(),
            'lessons_completed' => \App\Models\LessonProgress::where('user_id', $user->id)
                ->whereNotNull('completed_at')->count(),
        ];

        return view('dashboard.member', compact(
            'user',
            'enrollments',
            'nikahProfile',
            'certificates',
            'liveSubscriptions',
            'unreadNotifications',
            'stats'
        ));
    }
}
