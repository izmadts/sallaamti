<?php

namespace App\Http\Controllers\Concerns;

use App\Models\NikahBlock;
use App\Models\NikahProfile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Shared by the web NikahFileController (session auth) and the mobile
// Api\V1\NikahFileController (Sanctum auth) so the access rules for these
// private-disk images — CNIC and payment screenshots never leave
// owner+admin, profile photo needs a mutual accepted interest — live in
// exactly one place.
trait AuthorizesNikahFileAccess
{
    protected function resolveNikahFile(NikahProfile $profile, string $type, User $user): StreamedResponse
    {
        abort_unless(in_array($type, ['photo', 'cnic_front_image', 'cnic_back_image', 'payment_screenshot']), 404);

        $path = $profile->{$type};
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        $myProfile = $user->nikahProfile;

        $isAdmin = method_exists($user, 'hasRole') ? $user->hasRole('admin') : (($user->role ?? null) === 'admin');

        if ($isAdmin) {
            return Storage::disk('private')->response($path);
        }

        if (in_array($type, ['cnic_front_image', 'cnic_back_image', 'payment_screenshot'])) {
            abort_unless($profile->user_id === $user->id, 403);
            return Storage::disk('private')->response($path);
        }

        if ($profile->user_id === $user->id) {
            return Storage::disk('private')->response($path);
        }

        abort_unless($profile->allow_photo_sharing, 403);
        abort_unless($myProfile, 403);
        abort_if(NikahBlock::existsBetween($myProfile->id, $profile->id), 403);

        $hasAcceptedInterest = $profile->receivedInterests()
            ->where('sender_profile_id', $myProfile->id)
            ->where('status', 'accepted')
            ->exists()
            ||
            $profile->sentInterests()
            ->where('receiver_profile_id', $myProfile->id)
            ->where('status', 'accepted')
            ->exists();

        abort_unless($hasAcceptedInterest, 403);

        return Storage::disk('private')->response($path);
    }
}
