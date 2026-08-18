<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselingBooking;
use App\Notifications\CounselingBookingCancelled;
use App\Notifications\CounselingBookingConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $upcoming = CounselingBooking::where('counselor_id', Auth::id())
            ->whereIn('status', ['requested', 'confirmed'])
            ->with('member')
            ->orderBy('scheduled_at')
            ->get();

        $past = CounselingBooking::where('counselor_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->with('member')
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('counselor.bookings.index', compact('upcoming', 'past'));
    }

    public function confirm(CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if ($booking->status !== 'requested') {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — nothing to confirm.');
        }

        $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        $booking->member->notify(new CounselingBookingConfirmed($booking));

        return back()->with('status', 'Session confirmed.');
    }

    public function complete(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — it can\'t be marked complete.');
        }

        $request->validate(['notes' => ['nullable', 'string', 'max:2000']]);

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('status', 'Session marked as completed.');
    }

    public function cancel(Request $request, CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — it can\'t be cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at' => now(),
        ]);

        $booking->member->notify(new CounselingBookingCancelled($booking));

        return back()->with('status', 'Session cancelled.');
    }

    public function markNoShow(CounselingBooking $booking)
    {
        abort_unless($booking->counselor_id === Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
            return back()->with('error', 'This session is already ' . str_replace('_', ' ', $booking->status) . ' — it can\'t be marked no-show.');
        }

        $booking->update(['status' => 'no_show']);

        return back()->with('status', 'Marked as no-show.');
    }
}
