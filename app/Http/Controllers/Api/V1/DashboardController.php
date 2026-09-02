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
    private const TILES = ['nikah', 'quran', 'quran_live', 'skills', 'counseling', 'donation', 'volunteer', 'wall', 'community'];

    // Which of the 4 togglable preferences (see the users table's
    // *_module_enabled columns, same ones the web nav's module dropdown
    // reads/writes) gates each tile — a tile with no entry here is always
    // shown, matching web's nav (Donation/Volunteer/Wall have no toggle).
    private const GATED_BY = [
        'nikah' => 'nikah_module_enabled',
        'quran' => 'quran_module_enabled',
        'quran_live' => 'quran_module_enabled',
        'skills' => 'skills_module_enabled',
        'counseling' => 'counseling_module_enabled',
    ];

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $modules = array_values(array_filter(
            self::TILES,
            fn (string $tile) => !isset(self::GATED_BY[$tile]) || $user->{self::GATED_BY[$tile]}
        ));

        $whatsapp = normalize_whatsapp_number(setting('social_whatsapp') ?: setting('site_phone'));

        return response()->json([
            'user' => new UserResource($user),
            'modules' => $modules,
            'support' => [
                'whatsapp_number' => $whatsapp !== '' ? $whatsapp : null,
            ],
        ]);
    }
}
