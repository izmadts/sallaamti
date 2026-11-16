<?php

namespace App\Http\Resources\Api;

use App\Models\MatchmakerApplication;
use App\Support\MatchmakerStageAccess;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'city' => $this->city,
            'avatar_url' => $this->apiAvatarUrl(),
            'roles' => $this->getRoleNames(),
            'modules' => [
                'nikah' => (bool) $this->nikah_module_enabled,
                'quran' => (bool) $this->quran_module_enabled,
                'counseling' => (bool) $this->counseling_module_enabled,
                'skills' => (bool) $this->skills_module_enabled,
            ],
            'has_nikah_profile' => $this->nikahProfile()->exists(),
            // "Silent" mode — see the notification_sound_enabled migration's
            // own comment. Read on app startup to pick which Android
            // notification channel (sound vs. silent) a locally-shown
            // foreground push uses.
            'notification_sound_enabled' => (bool) $this->notification_sound_enabled,
            'must_change_password' => (bool) $this->must_change_password,
            // When false, "Change Password" shouldn't ask for a current one
            // — there's no real password yet to check it against (e.g. a
            // phone+OTP self-signup) — see updatePassword()'s own comment.
            'has_real_password' => (bool) $this->has_real_password,
            // Same test as Post::isAutoPublishedFor() — the app uses this to
            // decide whether to show the Community Posts feature at all
            // (common members are pointed at Wall for their own posting
            // instead); the web itself stays open to any member, unchanged.
            'can_write_posts' => $this->can('posts.manage') || $this->hasRole('admin'),
            // Only meaningful for counselors — mirrors PerformanceController's
            // own tier lookup so the mobile app's top bar can show the same
            // badge (🥉/🥈/🥇/⭐) without a separate round trip at login.
            'tier' => $this->hasRole('matchmaker') ? $this->matchmakerTier() : null,
            // 15 Nov 2026 trainee-access addendum — null/[]/0 for anyone
            // without NC-app access at all; 'certified' with every feature
            // unlocked for a full 'matchmaker'; the real
            // training/assessed/probation stage (and its narrower feature
            // set + lead cap) for a 'matchmaker_trainee'. The NC app reads
            // these instead of hard-coding the feature matrix itself.
            'nc_stage' => MatchmakerStageAccess::stageFor($this->resource),
            'nc_features' => MatchmakerStageAccess::featuresFor(MatchmakerStageAccess::stageFor($this->resource)),
            'nc_lead_cap' => MatchmakerStageAccess::leadCapFor(MatchmakerStageAccess::stageFor($this->resource)),
        ];
    }

    private function matchmakerTier(): string
    {
        $application = MatchmakerApplication::where('user_id', $this->id)->where('status', 'certified')->first();

        return $application?->level ?? 'nikah_counselor';
    }
}
