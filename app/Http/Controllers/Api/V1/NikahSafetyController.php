<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NikahBlock;
use App\Models\NikahInterest;
use App\Models\NikahProfile;
use App\Models\NikahReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// JSON equivalent of NikahSafetyController (web) — same rules, just JSON
// responses instead of redirect-back-with-flash.
class NikahSafetyController extends Controller
{
    public function block(NikahProfile $profile): JsonResponse
    {
        $myProfile = auth()->user()->nikahProfile;
        abort_unless($myProfile, 403);

        NikahBlock::firstOrCreate([
            'blocker_profile_id' => $myProfile->id,
            'blocked_profile_id' => $profile->id,
        ]);

        return response()->json(['message' => __('db.Profile blocked. You will no longer see this profile.')]);
    }

    public function unblock(NikahBlock $block): JsonResponse
    {
        $myProfile = auth()->user()->nikahProfile;
        abort_unless($myProfile && (int) $block->blocker_profile_id === (int) $myProfile->id, 403);

        $block->delete();

        return response()->json(['message' => __('db.Profile unblocked.')]);
    }

    public function blockedList(): JsonResponse
    {
        $myProfile = auth()->user()->nikahProfile;
        abort_unless($myProfile, 404);

        $blocks = $myProfile->blockedProfiles()->with('blocked.user')->latest()->get();

        return response()->json([
            'blocked' => $blocks->map(fn (NikahBlock $block) => [
                'block_id' => $block->id,
                'profile_id' => $block->blocked?->id,
                'name' => $block->blocked?->user?->name,
                'city' => $block->blocked?->city,
                'age' => $block->blocked?->age,
            ]),
        ]);
    }

    public function report(Request $request, NikahProfile $profile): JsonResponse
    {
        $myProfile = auth()->user()->nikahProfile;
        abort_unless($myProfile, 403);

        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:1000'],
            'nikah_interest_id' => ['nullable', 'integer', 'exists:nikah_interests,id'],
        ]);

        $interestId = null;
        if ($request->filled('nikah_interest_id')) {
            $interest = NikahInterest::find($request->nikah_interest_id);
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

        User::role('admin')->each(function ($admin) use ($report) {
            try {
                $admin->notify(new \App\Notifications\NewNikahReportFiled($report));
            } catch (\Throwable $e) {
                \Log::error('NewNikahReportFiled notification failed: ' . $e->getMessage());
            }
        });

        return response()->json(['message' => __('db.Report submitted. Our team will review it shortly.')], 201);
    }

    public function toggleActive(): JsonResponse
    {
        $profile = auth()->user()->nikahProfile;
        abort_unless($profile, 404);

        abort_if($profile->isSuspended(), 403, 'Your profile has been suspended by our moderation team and cannot be reactivated here. Please contact support.');

        $profile->update(['is_active' => !$profile->is_active]);

        return response()->json([
            'is_active' => $profile->is_active,
            'message' => $profile->is_active
                ? __('db.Your profile is now active and visible in search.')
                : __('db.Your profile is now hidden from search.'),
        ]);
    }
}
