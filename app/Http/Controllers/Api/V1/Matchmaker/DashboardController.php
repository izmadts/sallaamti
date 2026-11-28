<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController as WebDashboardController;
use App\Models\Lead;
use App\Support\NcClientState;
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
            'tasks' => $this->todaysTasks($request->user()->id),
            // The counselor's verification representative is customer
            // service — the same admin-configured WhatsApp number the member
            // app's own support button uses (Api\V1\DashboardController).
            'support' => [
                'whatsapp_number' => $this->supportWhatsapp(),
            ],
        ]);
    }

    // "Aaj ka kaam" — every open client of this counselor that has a next
    // step, in the order the simplified app walks them through (call first,
    // then register, get verified, requirements, package, shortlist, send).
    private function todaysTasks(int $matchmakerId): array
    {
        $leads = Lead::where('assigned_to', $matchmakerId)
            ->whereNotIn('status', Lead::INACTIVE_STATUSES)
            ->with(['nikahProfile', 'requirement.items', 'shortlistItems', 'proposalBatches'])
            ->orderByRaw('next_follow_up_at IS NULL, next_follow_up_at')
            ->latest('id')
            ->get();

        return $leads
            ->map(fn (Lead $lead) => ['lead' => $lead, 'task' => NcClientState::nextTask($lead)])
            ->filter(fn ($row) => $row['task'] !== null)
            ->sortBy(fn ($row) => array_search($row['task'], NcClientState::TASKS, true))
            ->take(30)
            ->map(fn ($row) => [
                'id' => $row['lead']->id,
                'name' => $row['lead']->name,
                'gender' => $row['lead']->gender,
                'phone' => $row['lead']->phone,
                'nikah_profile_id' => $row['lead']->nikah_profile_id,
                'verification_state' => NcClientState::verificationState($row['lead']),
                'task' => $row['task'],
                'next_follow_up_at' => $row['lead']->next_follow_up_at?->toDateString(),
            ])
            ->values()
            ->all();
    }

    private function supportWhatsapp(): ?string
    {
        $number = normalize_whatsapp_number(setting('social_whatsapp') ?: setting('site_phone'));

        return $number !== '' ? $number : null;
    }
}
