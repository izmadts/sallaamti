<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CounselingBooking;
use App\Models\CounselorAvailability;
use App\Models\QueryResponse;
use App\Models\SupportQuery;
use App\Models\User;
use App\Notifications\CounselingBookingRequested;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// The mobile-app side of CounselingBookingController (web). Same category/
// contact-method options, same CounselorAvailability::generateSlotsFor()
// slot engine, and the exact same locked-transaction conflict check on
// create — just a single-request booking submission instead of a
// session-persisted 5-step wizard, since a mobile client naturally holds
// that state client-side instead.
class CounselingController extends Controller
{
    private const CATEGORIES = ['marital', 'parenting', 'financial', 'legal', 'spiritual', 'other'];
    private const CONTACT_METHODS = ['phone', 'video', 'in_person', 'chat'];

    private function counselorPayload(User $counselor): array
    {
        return ['id' => $counselor->id, 'name' => $counselor->name, 'avatar' => $counselor->apiAvatarUrl()];
    }

    private function bookingPayload(CounselingBooking $booking): array
    {
        $query = $booking->supportQuery;

        return [
            'id' => $booking->id,
            'status' => $booking->status,
            'category' => $query?->category,
            'subject' => $query?->subject,
            'description' => $query?->description,
            'is_anonymous' => $booking->isAnonymous(),
            'is_urgent' => $booking->isUrgent(),
            'contact_method' => $booking->contact_method,
            'scheduled_at' => $booking->scheduled_at,
            'duration_minutes' => $booking->duration_minutes,
            'meeting_link' => $booking->meeting_link,
            'counselor' => $booking->counselor ? $this->counselorPayload($booking->counselor) : null,
            'notes' => $booking->notes,
            'cancellation_reason' => $booking->cancellation_reason,
            'member_rating' => $booking->member_rating,
            'member_feedback' => $booking->member_feedback,
            'created_at' => $booking->created_at,
        ];
    }

    public function meta(): JsonResponse
    {
        return response()->json([
            'categories' => self::CATEGORIES,
            'contact_methods' => self::CONTACT_METHODS,
            'counselors' => User::role('counselor')->orderBy('name')->get()->map(fn (User $c) => $this->counselorPayload($c))->values(),
        ]);
    }

    public function slots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            // Comma-separated instead of an array param - sidesteps
            // needing bracket-notation query encoding (key[]=1&key[]=2)
            // on the client just for a short list of IDs.
            'counselor_ids' => ['nullable', 'string'],
        ]);

        $counselorIds = isset($validated['counselor_ids'])
            ? array_map('intval', explode(',', $validated['counselor_ids']))
            : User::role('counselor')->pluck('id')->all();
        $date = Carbon::parse($validated['date']);

        // Once the member has narrowed down to one specific counselor, show
        // their whole day (open + already-booked) instead of only the gaps,
        // so the member can see the full schedule at a glance. That view
        // only makes sense for a single counselor - mixing several
        // counselors' booked times together would just be confusing.
        $showBooked = count($counselorIds) === 1;

        $counselorNames = User::whereIn('id', $counselorIds)->pluck('name', 'id');

        $slots = [];
        foreach ($counselorIds as $counselorId) {
            $counselorSlots = $showBooked
                ? CounselorAvailability::slotsWithStatusFor($counselorId, $date)
                : collect(CounselorAvailability::generateSlotsFor($counselorId, $date))
                    ->map(fn ($time) => ['datetime' => $time, 'booked' => false])
                    ->all();

            foreach ($counselorSlots as $slot) {
                $slots[] = [
                    'counselor_id' => $counselorId,
                    'counselor_name' => $counselorNames[$counselorId] ?? null,
                    'datetime' => $slot['datetime']->toDateTimeString(),
                    'booked' => $slot['booked'],
                ];
            }
        }

        usort($slots, fn ($a, $b) => $a['datetime'] <=> $b['datetime']);

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:' . implode(',', self::CATEGORIES)],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'is_anonymous' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
            'contact_method' => ['required', 'in:' . implode(',', self::CONTACT_METHODS)],
            'counselor_id' => ['nullable', 'integer', 'exists:users,id'],
            'scheduled_at' => ['required_without:preferred_at', 'nullable', 'date', 'after:now'],
            'preferred_at' => ['required_without:scheduled_at', 'nullable', 'date', 'after:now'],
        ]);

        $user = $request->user();
        $isPreference = empty($validated['scheduled_at']);
        $counselorId = $isPreference ? null : $validated['counselor_id'];
        $scheduledAt = Carbon::parse($isPreference ? $validated['preferred_at'] : $validated['scheduled_at']);

        // Same gap-lock re-check as the web finalize() — the client's slot
        // list came from an earlier request, and another member could book
        // the same counselor/time in between. See CounselingBookingController
        // for the full reasoning.
        $booking = DB::transaction(function () use ($validated, $user, $counselorId, $scheduledAt) {
            if ($counselorId) {
                $conflict = CounselingBooking::where('counselor_id', $counselorId)
                    ->where('scheduled_at', $scheduledAt->toDateTimeString())
                    ->whereIn('status', ['requested', 'confirmed'])
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    return null;
                }
            }

            $query = SupportQuery::create([
                'user_id' => $user->id,
                'category' => $validated['category'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'priority' => !empty($validated['is_urgent']) ? 'high' : 'medium',
            ]);

            return CounselingBooking::create([
                'support_query_id' => $query->id,
                'member_id' => $user->id,
                'counselor_id' => $counselorId,
                'scheduled_at' => $scheduledAt->toDateTimeString(),
                'contact_method' => $validated['contact_method'],
                'status' => 'requested',
            ]);
        });

        abort_if(!$booking, 409, 'That slot was just taken — please pick another.');

        try {
            $booking->counselor?->notify(new CounselingBookingRequested($booking));
        } catch (\Throwable $e) {
            \Log::error('CounselingBookingRequested notification failed: ' . $e->getMessage());
        }

        return response()->json(['booking' => $this->bookingPayload($booking)], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = CounselingBooking::where('member_id', $request->user()->id)
            ->with(['counselor', 'supportQuery'])
            ->orderByDesc('scheduled_at')
            ->get();

        return response()->json(['bookings' => $bookings->map(fn (CounselingBooking $b) => $this->bookingPayload($b))->values()]);
    }

    private function authorizeOwner(CounselingBooking $booking, Request $request): void
    {
        abort_unless((int) $booking->member_id === (int) $request->user()->id, 403);
    }

    public function show(CounselingBooking $booking, Request $request): JsonResponse
    {
        $this->authorizeOwner($booking, $request);
        $booking->load(['counselor', 'supportQuery.responses.responder']);

        $payload = $this->bookingPayload($booking);
        $payload['responses'] = ($booking->supportQuery?->responses ?? collect())
            ->where('is_internal', false)
            ->map(fn (QueryResponse $r) => [
                'id' => $r->id,
                'message' => $r->message,
                'responder' => $r->responder ? ['name' => $r->responder->name, 'avatar' => $r->responder->apiAvatarUrl()] : null,
                'created_at' => $r->created_at,
            ])->values();

        return response()->json(['booking' => $payload]);
    }

    public function cancel(CounselingBooking $booking, Request $request): JsonResponse
    {
        $this->authorizeOwner($booking, $request);
        abort_if(in_array($booking->status, ['completed', 'cancelled']), 422, 'This session can no longer be cancelled.');

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at' => now(),
        ]);

        return response()->json(['booking' => $this->bookingPayload($booking->fresh(['counselor', 'supportQuery']))]);
    }

    public function reply(CounselingBooking $booking, Request $request): JsonResponse
    {
        $this->authorizeOwner($booking, $request);
        abort_unless($booking->support_query_id, 404);

        $validated = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        $response = QueryResponse::create([
            'support_query_id' => $booking->support_query_id,
            'responder_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);
        $response->load('responder');

        return response()->json(['response' => [
            'id' => $response->id,
            'message' => $response->message,
            'responder' => ['name' => $response->responder->name, 'avatar' => $response->responder->apiAvatarUrl()],
            'created_at' => $response->created_at,
        ]], 201);
    }

    public function rate(CounselingBooking $booking, Request $request): JsonResponse
    {
        $this->authorizeOwner($booking, $request);
        abort_unless($booking->status === 'completed', 422, 'You can only rate a completed session.');

        $validated = $request->validate([
            'member_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'member_feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->update($validated);

        return response()->json(['booking' => $this->bookingPayload($booking->fresh(['counselor', 'supportQuery']))]);
    }
}
