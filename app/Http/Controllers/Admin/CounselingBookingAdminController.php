<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselingBooking;
use App\Models\User;
use Illuminate\Http\Request;

class CounselingBookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = CounselingBooking::with(['member', 'counselor'])->orderByDesc('scheduled_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('counselor_id')) {
            $query->where('counselor_id', $request->counselor_id);
        }

        $bookings = $query->paginate(20)->withQueryString();
        $counselors = User::role('counselor')->orderBy('name')->get();

        return view('admin.counseling-bookings.index', compact('bookings', 'counselors'));
    }

    public function show(CounselingBooking $booking)
    {
        $booking->load(['member', 'counselor', 'supportQuery.responses.responder']);
        $counselors = User::role('counselor')->orderBy('name')->get();

        return view('admin.counseling-bookings.show', compact('booking', 'counselors'));
    }

    public function reassign(Request $request, CounselingBooking $booking)
    {
        $request->validate(['counselor_id' => ['required', 'exists:users,id']]);

        $booking->update(['counselor_id' => $request->counselor_id, 'status' => 'requested', 'confirmed_at' => null]);

        return back()->with('status', 'Booking reassigned.');
    }

    public function cancel(Request $request, CounselingBooking $booking)
    {
        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason', 'Cancelled by admin.'),
            'cancelled_at' => now(),
        ]);

        return back()->with('status', 'Booking cancelled.');
    }
}
