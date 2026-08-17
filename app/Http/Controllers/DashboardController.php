<?php

namespace App\Http\Controllers;

use App\Models\QuranClassGroup;
use App\Models\QuranGroupStudent;
use App\Models\QuranLiveCourse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        // Teacher and counselor accounts get their own purpose-built panels
        // instead of the member dashboard (Nikah/course-enrollment cards
        // that don't apply to their role).
        if ($authUser->hasRole('teacher')) {
            return $this->teacherDashboard($authUser);
        }

        if ($authUser->hasRole('counselor')) {
            return $this->counselorDashboard($authUser);
        }

        return $this->memberDashboard($authUser);
    }

    private function teacherDashboard($authUser)
    {
        $courses = QuranLiveCourse::where('teacher_id', $authUser->id)->get();
        $groupIds = QuranClassGroup::where('teacher_id', $authUser->id)->pluck('id');

        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => QuranGroupStudent::whereIn('quran_class_group_id', $groupIds)
                ->where('status', 'active')
                ->count(),
            'todays_links_posted' => $courses->filter(fn ($course) => $course->todaysLink())->count(),
        ];

        return view('dashboard.teacher', [
            'user' => $authUser,
            'courses' => $courses,
            'stats' => $stats,
        ]);
    }

    private function counselorDashboard($authUser)
    {
        $stats = [
            'pending_requests' => \App\Models\CounselingBooking::where('counselor_id', $authUser->id)->where('status', 'requested')->count(),
            'upcoming' => \App\Models\CounselingBooking::where('counselor_id', $authUser->id)->whereIn('status', ['requested', 'confirmed'])->count(),
            'completed_total' => \App\Models\CounselingBooking::where('counselor_id', $authUser->id)->where('status', 'completed')->count(),
        ];

        $nextBookings = \App\Models\CounselingBooking::where('counselor_id', $authUser->id)
            ->whereIn('status', ['requested', 'confirmed'])
            ->with('member')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        return view('dashboard.counselor', ['user' => $authUser, 'stats' => $stats, 'nextBookings' => $nextBookings]);
    }

    private function memberDashboard($authUser)
    {
        $user = $authUser->load([
            'nikahProfile',
            'enrollments.course',
            'certificates.course',
        ]);

        $this->updateVisitStreak($user);

        // Quran courses progress
        $enrollments = $user->enrollments->map(function ($enrollment) use ($user) {
            return [
                'course' => $enrollment->course,
                'progress' => $enrollment->course->progressFor($user),
            ];
        });

        // Nikah module status
        $nikahProfile = $user->nikahProfile;

        // Quran Live subscriptions
        $liveSubscriptions = \App\Models\QuranSubscription::where('user_id', $user->id)
            ->where('month', now()->format('Y-m'))
            ->where('payment_status', 'confirmed')
            ->with('course')
            ->get();

        // Recent notifications
        $notifications = $user->notifications()->latest()->take(5)->get();
        $unreadCount = $user->unreadNotifications()->count();

        // Certificates earned
        $certificates = $user->certificates->take(3);

        return view('dashboard', compact(
            'user',
            'enrollments',
            'nikahProfile',
            'liveSubscriptions',
            'notifications',
            'unreadCount',
            'certificates'
        ));
    }

    // Called once per dashboard visit (not on every request) — a simple
    // "did you open the dashboard today" streak, not tied to any specific
    // module. $user is the same instance Auth::user() returns for this
    // request, so updating it here is enough for the Blade view's
    // Auth::user()->current_streak to reflect the new value immediately.
    private function updateVisitStreak($user): void
    {
        $today = now()->toDateString();

        if ($user->last_active_date?->toDateString() === $today) {
            return;
        }

        $streak = $user->last_active_date?->toDateString() === now()->subDay()->toDateString()
            ? $user->current_streak + 1
            : 1;

        $user->update([
            'current_streak' => $streak,
            'longest_streak' => max($streak, $user->longest_streak),
            'last_active_date' => $today,
        ]);
    }
}
