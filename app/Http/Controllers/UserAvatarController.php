<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserAvatarController extends Controller
{
    public function show(User $user): StreamedResponse
    {
        $viewer = Auth::user();
        abort_unless($viewer->id === $user->id || $viewer->hasRole('admin'), 403);
        abort_unless($user->avatar && Storage::disk('private')->exists($user->avatar), 404);

        return Storage::disk('private')->response($user->avatar);
    }
}
