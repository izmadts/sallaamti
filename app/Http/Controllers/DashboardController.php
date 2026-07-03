<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'nikahProfile',
            'enrollments.course',
            'certificates.course',
        ]);

        // Profile completion percentage
        $profileCompletion = $this->calculateProfileCompletion($user);

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
            'profileCompletion',
            'enrollments',
            'nikahProfile',
            'liveSubscriptions',
            'notifications',
            'unreadCount',
            'certificates'
        ));
    }

    private function calculateProfileCompletion($user): int
    {
        $fields = [
            'name' => filled($user->name),
            'email' => filled($user->email),
            'phone' => filled($user->phone),
            'gender' => filled($user->gender),
            'city' => filled($user->city),
            'avatar' => filled($user->avatar),
        ];

        $completed = collect($fields)->filter()->count();
        return (int) round(($completed / count($fields)) * 100);
    }
}
