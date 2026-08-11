<?php

namespace App\Http\Controllers;

use App\Models\NikahBlock;
use App\Models\NikahProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NikahFileController extends Controller
{
    public function show(NikahProfile $profile, string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['photo', 'cnic_front_image', 'cnic_back_image', 'payment_screenshot']), 404);

        $path = $profile->{$type};
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        $user = Auth::user();
        $myProfile = $user->nikahProfile;

        // Rule 1: Admins can see everything (needed for CNIC verification)
        $isAdmin = method_exists($user, 'hasRole') ? $user->hasRole('admin') : (($user->role ?? null) === 'admin');

        if ($isAdmin) {
            return Storage::disk('private')->response($path);
        }

        // Rule 2: CNIC & Payment images are NEVER visible to other users, only the owner + admin
        if (in_array($type, ['cnic_front_image', 'cnic_back_image', 'payment_screenshot'])) {
            abort_unless($profile->user_id === $user->id, 403);
            return Storage::disk('private')->response($path);
        }

        // Rule 3: Photo — owner can always see their own
        if ($profile->user_id === $user->id) {
            return Storage::disk('private')->response($path);
        }

        // Rule 4: Photo — other users only see it if allowed AND mutually accepted
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
