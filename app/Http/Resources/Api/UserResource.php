<?php

namespace App\Http\Resources\Api;

use App\Models\MatchmakerApplication;
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
            // Only meaningful for counselors — mirrors PerformanceController's
            // own tier lookup so the mobile app's top bar can show the same
            // badge (🥉/🥈/🥇/⭐) without a separate round trip at login.
            'tier' => $this->hasRole('matchmaker') ? $this->matchmakerTier() : null,
        ];
    }

    private function matchmakerTier(): string
    {
        $application = MatchmakerApplication::where('user_id', $this->id)->where('status', 'certified')->first();

        return $application?->level ?? 'nikah_counselor';
    }
}
