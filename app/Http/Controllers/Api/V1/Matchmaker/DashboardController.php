<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController as WebDashboardController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Thin JSON wrapper over WebDashboardController::matchmakerDashboardData() —
// same stats/queries as the web matchmaker dashboard, never a second copy.
// Composition, not inheritance: extending WebDashboardController broke
// PHP's method-signature compatibility check on index().
class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = app(WebDashboardController::class)->matchmakerDashboardData($request->user());

        return response()->json([
            'stats' => $data['stats'],
            'follow_ups' => $data['followUps']->map(fn ($lead) => [
                'id' => $lead->id,
                'name' => $lead->name,
                'status' => $lead->status,
                'next_follow_up_at' => $lead->next_follow_up_at?->toDateString(),
            ]),
            'recent_leads' => $data['recentLeads']->map(fn ($lead) => [
                'id' => $lead->id,
                'name' => $lead->name,
                'status' => $lead->status,
                'source' => $lead->source,
                'created_at' => $lead->created_at->toIso8601String(),
            ]),
            'recent_activity' => $data['recentActivity']->map(fn ($event) => [
                'id' => $event->id,
                'event_type' => $event->event_type,
                'description' => $event->description,
                'created_at' => $event->created_at->toIso8601String(),
            ]),
        ]);
    }
}
