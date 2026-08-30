<?php

namespace App\Http\Controllers;

use App\Models\QuranSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuranSubscriptionFileController extends Controller
{
    public function show(QuranSubscription $subscription)
    {
        $path = $subscription->payment_screenshot;
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        $user = Auth::user();
        abort_unless((int) $subscription->user_id === (int) $user->id || $user->hasRole('admin'), 403);

        return Storage::disk('private')->response($path);
    }
}
