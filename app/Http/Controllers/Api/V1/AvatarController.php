<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Deliberately more permissive than the web UserAvatarController (which
// only lets you view your own avatar, or an admin view anyone's) — a
// profile picture isn't sensitive the way a Nikah matrimonial photo is,
// and every mobile screen showing another member's name (Wall post
// authors, comment authors, counselor picker, etc.) needs to be able to
// show their avatar too. Session-auth-only on web meant no Sanctum
// request could authenticate here at all regardless of whose avatar it
// was, which is the more pressing bug this fixes.
class AvatarController extends Controller
{
    public function show(User $user): StreamedResponse
    {
        abort_unless($user->avatar && Storage::disk('private')->exists($user->avatar), 404);

        return Storage::disk('private')->response($user->avatar);
    }
}
