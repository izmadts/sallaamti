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

        if ($authUser->hasRole('matchmaker')) {
            return $this->matchmakerDashboard($authUser);
        }

        return $this->memberDashboard($authUser);
    }

    // Direct link target for the Matchmaking nav dropdown's "Matchmaker
    // Dashboard" link, so an account that holds another role too (e.g.
    // counselor) doesn't get bounced to that other role's panel — unlike
    // index() above, this never falls through to a different dashboard.
    public function matchmaker()
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasRole('matchmaker'), 403);

        return $this->matchmakerDashboard($authUser);
    }

    protected function matchmakerDashboard($authUser)
    {
        return view('dashboard.matchmaker', array_merge(['user' => $authUser], $this->matchmakerDashboardData($authUser)));
    }

    // Split out from matchmakerDashboard() so Api\V1\Matchmaker\DashboardController
    // can reuse the exact same stats/queries (via composition, not
    // inheritance — extending this class broke PHP's method-signature
    // compatibility check on index()) and JSON-encode them instead of
    // re-deriving a second copy that could drift from the web version.
    public function matchmakerDashboardData($authUser): array
    {
        $requests = \App\Models\NikahContactRequest::where('requested_by', $authUser->id)->get();

        $myLeads = \App\Models\Lead::where('assigned_to', $authUser->id);

        $myBatches = \App\Models\ProposalBatch::where('matchmaker_id', $authUser->id);

        $stats = [
            'total_profiles' => \App\Models\NikahProfile::where('is_active', true)->whereNull('suspended_at')->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
            'approved_requests' => $requests->where('status', 'approved')->count(),
            'new_leads' => (clone $myLeads)->where('status', 'new')->count(),
            'follow_ups_due' => (clone $myLeads)->whereDate('next_follow_up_at', '<=', now())->whereNotIn('status', ['registered', 'not_interested', 'closed'])->count(),
            'registered_leads' => (clone $myLeads)->where('status', 'registered')->count(),
            'active_batches' => (clone $myBatches)->whereIn('status', ['sent', 'partially_responded'])->count(),
            'awaiting_response' => \App\Models\MatchProposal::whereHas('batch', fn($q) => $q->where('matchmaker_id', $authUser->id))->where('status', 'sent')->count(),
            'interested_this_week' => \App\Models\MatchProposal::whereHas('batch', fn($q) => $q->where('matchmaker_id', $authUser->id))->where('response', 'interested')->where('responded_at', '>=', now()->subDays(7))->count(),
        ];

        $followUps = (clone $myLeads)->whereDate('next_follow_up_at', '<=', now())
            ->whereNotIn('status', ['registered', 'not_interested', 'closed'])
            ->orderBy('next_follow_up_at')
            ->take(5)
            ->get();

        $recentLeads = (clone $myLeads)->latest()->take(5)->get();

        $recentActivity = \App\Models\MatchmakingTimelineEvent::whereHas('lead', fn($q) => $q->where('assigned_to', $authUser->id))
            ->latest('created_at')
            ->take(6)
            ->get();

        return [
            'stats' => $stats,
            'followUps' => $followUps,
            'recentLeads' => $recentLeads,
            'recentActivity' => $recentActivity,
        ];
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
            'avg_rating' => round((float) \App\Models\CounselingBooking::where('counselor_id', $authUser->id)->whereNotNull('member_rating')->avg('member_rating'), 1),
        ];

        $nextBookings = \App\Models\CounselingBooking::where('counselor_id', $authUser->id)
            ->whereIn('status', ['requested', 'confirmed'])
            ->with(['member', 'supportQuery'])
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

        // Course progress, split by track — the dashboard shows separate
        // Quran and Skills cards, each with its own enrollment count, so a
        // Skills enrollment must never inflate the Quran card's badge (and
        // vice versa).
        $allEnrollments = $user->enrollments->map(function ($enrollment) use ($user) {
            return [
                'course' => $enrollment->course,
                'progress' => $enrollment->course->progressFor($user),
            ];
        });
        $enrollments = $allEnrollments->filter(fn ($e) => $e['course']->track === 'quran')->values();
        $skillsEnrollments = $allEnrollments->filter(fn ($e) => $e['course']->track === 'skills')->values();

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
            'skillsEnrollments',
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
