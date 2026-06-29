<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserAvatarController extends Controller
{
    public function show(User $user): StreamedResponse
    {
        abort_unless($user->avatar && Storage::disk('private')->exists($user->avatar), 404);

        return Storage::disk('private')->response($user->avatar);
    }
}
