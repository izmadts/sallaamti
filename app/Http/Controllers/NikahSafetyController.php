<?php

namespace App\Http\Controllers;

use App\Models\NikahBlock;
use App\Models\NikahProfile;
use App\Models\NikahReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NikahSafetyController extends Controller
{
    public function block(NikahProfile $profile)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);

        NikahBlock::firstOrCreate([
            'blocker_profile_id' => $myProfile->id,
            'blocked_profile_id' => $profile->id,
        ]);

        return back()->with('status', 'Profile blocked. You will no longer see this profile.');
    }

    public function report(Request $request, NikahProfile $profile)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 403);

        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:1000'],
            'nikah_interest_id' => ['nullable', 'integer', 'exists:nikah_interests,id'],
        ]);

        // If a conversation is being reported, make sure it actually belongs to the reporter.
        $interestId = null;
        if ($request->filled('nikah_interest_id')) {
            $interest = \App\Models\NikahInterest::find($request->nikah_interest_id);
            if ($interest && ((int) $interest->sender_profile_id === (int) $myProfile->id || (int) $interest->receiver_profile_id === (int) $myProfile->id)) {
                $interestId = $interest->id;
            }
        }

        $report = NikahReport::create([
            'reporter_profile_id' => $myProfile->id,
            'reported_profile_id' => $profile->id,
            'nikah_interest_id' => $interestId,
            'reason' => $request->reason,
            'details' => $request->details,
        ]);

        \App\Models\User::role('admin')->each(function ($admin) use ($report) {
            try {
                $admin->notify(new \App\Notifications\NewNikahReportFiled($report));
            } catch (\Throwable $e) {
                \Log::error('NewNikahReportFiled notification failed: ' . $e->getMessage());
            }
        });

        return back()->with('status', 'Report submitted. Our team will review it shortly.');
    }

    public function blockedList()
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile, 404);

        $blocks = $myProfile->blockedProfiles()->with('blocked.user')->latest()->get();

        return view('nikah.blocked', compact('blocks'));
    }

    public function unblock(NikahBlock $block)
    {
        $myProfile = Auth::user()->nikahProfile;
        abort_unless($myProfile && (int) $block->blocker_profile_id === (int) $myProfile->id, 403);

        $block->delete();

        return back()->with('status', 'Profile unblocked.');
    }

    public function toggleActive()
    {
        $profile = Auth::user()->nikahProfile;
        abort_unless($profile, 404);

        // An admin suspension is a separate gate from this toggle — otherwise
        // a suspended member could just click "Activate" to undo moderation.
        abort_if($profile->isSuspended(), 403, 'Your profile has been suspended by our moderation team and cannot be reactivated here. Please contact support.');

        $profile->update(['is_active' => !$profile->is_active]);

        $message = $profile->is_active
            ? 'Your profile is now active and visible in search.'
            : 'Your profile is now hidden from search.';

        return back()->with('status', $message);
    }

    // Self-service, no admin approval — same spirit as toggleActive() above.
    // Reachable even though the profile is currently is_active=false (that's
    // exactly the state this exists to fix), unlike toggleActive() which a
    // paused-for-non-payment member wouldn't see a meaningful option in
    // anyway (see NikahProfile::isPausedForNonPayment()).
    public function extendPaymentDeadline()
    {
        $profile = Auth::user()->nikahProfile;
        abort_unless($profile, 404);
        abort_if($profile->isSuspended(), 403, 'Your profile has been suspended by our moderation team. Please contact support.');
        abort_unless($profile->payment_deadline_at, 422, 'This profile was never on a payment deadline.');
        abort_unless($profile->canExtendPaymentDeadline(), 422, 'You\'ve already used all ' . NikahProfile::MAX_PAYMENT_DEADLINE_EXTENSIONS . ' self-service extensions. Please contact us on WhatsApp to reactivate your profile: ' . whatsapp_link('Hi, my Nikah profile was paused and I\'ve used all my self-service extensions — can you help reactivate it?'));

        $profile->extendPaymentDeadline();

        $remaining = NikahProfile::MAX_PAYMENT_DEADLINE_EXTENSIONS - $profile->payment_deadline_extensions_used;
        return back()->with('status', "Your profile is active again — you now have 7 more days to complete the verification fee payment. ({$remaining} self-service extension(s) left after this.)");
    }

    public function adminReports()
    {
        $reports = NikahReport::with(['reporter.user', 'reported.user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        // Repeat-offender signal: how many total reports (any status) exist
        // against each reported profile on this page, so admins can spot a
        // pattern instead of reviewing each report in isolation.
        $reportCounts = NikahReport::whereIn('reported_profile_id', $reports->pluck('reported_profile_id'))
            ->selectRaw('reported_profile_id, count(*) as total')
            ->groupBy('reported_profile_id')
            ->pluck('total', 'reported_profile_id');

        return view('admin.nikah.nikah-reports', compact('reports', 'reportCounts'));
    }

    public function dismissReport(NikahReport $report)
    {
        $report->update(['status' => 'dismissed']);
        return back()->with('status', 'Report dismissed.');
    }

    public function suspendReportedProfile(Request $request, NikahReport $report)
    {
        $request->validate(['suspension_reason' => ['nullable', 'string', 'max:500']]);

        $reason = $request->suspension_reason ?: ('Reported for: ' . $report->reason);

        $reportedProfile = $report->reported;
        $reportedProfile->update([
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
        $report->update(['status' => 'reviewed']);

        try {
            $reportedProfile->user->notify(new \App\Notifications\NikahProfileSuspended($reason));
        } catch (\Throwable $e) {
            \Log::error('NikahProfileSuspended notification failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Profile suspended and hidden from search.');
    }

    public function unsuspendProfile(NikahProfile $profile)
    {
        $profile->update(['suspended_at' => null, 'suspension_reason' => null]);

        return back()->with('status', 'Profile unsuspended.');
    }

    public function reportConversation(NikahReport $report)
    {
        abort_unless($report->nikah_interest_id, 404, 'No conversation attached to this report.');

        $interest = $report->interest;
        $messages = $interest->guardianMessages()->with('sender')->orderBy('created_at')->get();

        return view('admin.nikah.nikah-report-conversation', compact('report', 'interest', 'messages'));
    }
}
