<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Phase 0 scope only: enough for the app's dashboard shell to show the
// signed-in member and which module tiles to display. Real per-module stats
// (enrollments, matches, bookings…) arrive alongside each module's own
// phase, once that module has an API surface worth summarizing.
class DashboardController extends Controller
{
    // The app's home-screen tiles — deliberately its own list rather than
    // reusing Faq::MODULES (which also includes non-tile entries like
    // "general"), so growing one doesn't silently reshape the other.
    private const TILES = ['nikah', 'quran', 'quran_live', 'skills', 'counseling', 'donation', 'volunteer', 'wall'];

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => new UserResource($user),
            'modules' => self::TILES,
        ]);
    }
}
