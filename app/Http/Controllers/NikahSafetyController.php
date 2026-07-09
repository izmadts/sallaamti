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
        ]);

        NikahReport::create([
            'reporter_profile_id' => $myProfile->id,
            'reported_profile_id' => $profile->id,
            'reason' => $request->reason,
            'details' => $request->details,
        ]);

        return back()->with('status', 'Report submitted. Our team will review it shortly.');
    }

    public function toggleActive()
    {
        $profile = Auth::user()->nikahProfile;
        abort_unless($profile, 404);

        $profile->update(['is_active' => !$profile->is_active]);

        $message = $profile->is_active
            ? 'Your profile is now active and visible in search.'
            : 'Your profile is now hidden from search.';

        return back()->with('status', $message);
    }

    public function adminReports()
    {
        $reports = NikahReport::with(['reporter.user', 'reported.user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.nikah.nikah-reports', compact('reports'));
    }

    public function dismissReport(NikahReport $report)
    {
        $report->update(['status' => 'dismissed']);
        return back()->with('status', 'Report dismissed.');
    }
}
