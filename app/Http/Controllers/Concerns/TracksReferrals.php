<?php

namespace App\Http\Controllers\Concerns;

use App\Models\MatchmakerApplication;
use App\Models\MatchmakerReferral;
use App\Models\User;

// Turns a certified counselor's referral link (?ref=MM-PK-000145 on
// /register — see RegisteredUserController::create()) into a real
// attribution record. Deliberately called explicitly from each of the
// three genuine self-service registration completions, never from inside
// RegistersMinimalUsers::createMinimalUser() itself — that helper is
// shared with admin/matchmaker-driven walk-in account creation, which
// must never pick up a stale referral code sitting in staff's own
// browser session.
trait TracksReferrals
{
    private function captureReferralCode(?string $code): void
    {
        if (!$code) {
            return;
        }

        $code = strtoupper(trim($code));

        $isValidCertifiedCode = MatchmakerApplication::where('counselor_code', $code)
            ->where('status', 'certified')
            ->exists();

        if ($isValidCertifiedCode) {
            session(['referral_counselor_code' => $code]);
        }
    }

    // One-shot: session()->pull() reads and immediately forgets, so a
    // second, unrelated registration later in the same browser session
    // (e.g. a shared/public computer) never gets attributed too.
    private function attributeReferral(User $user): void
    {
        $code = session()->pull('referral_counselor_code');

        if (!$code) {
            return;
        }

        $application = MatchmakerApplication::where('counselor_code', $code)->where('status', 'certified')->first();

        if (!$application || !$application->user_id || $application->user_id === $user->id) {
            return;
        }

        MatchmakerReferral::firstOrCreate(
            ['referred_user_id' => $user->id],
            ['counselor_user_id' => $application->user_id, 'counselor_code' => $code]
        );
    }
}
