<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::query();

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $donations = $query->latest()->paginate(15)->withQueryString();
        $totalConfirmed = Donation::where('payment_status', 'confirmed')->sum('amount');

        return view('admin.donations.index', compact('donations', 'totalConfirmed'));
    }

    public function confirm(Donation $donation)
    {
        $donation->update(['payment_status' => 'confirmed', 'payment_confirmed_at' => now()]);
        return back()->with('status', 'Donation confirmed. May Allah reward the donor.');
    }

    public function reject(Request $request, Donation $donation)
    {
        $request->validate(['payment_rejection_reason' => 'required|string|max:500']);
        $donation->update(['payment_status' => 'rejected', 'payment_rejection_reason' => $request->payment_rejection_reason]);
        return back()->with('status', 'Donation rejected.');
    }
    public function screenshot(Donation $donation)
    {
        $path = $donation->payment_screenshot;
        abort_unless($path && \Storage::disk('private')->exists($path), 404);
        return \Storage::disk('private')->response($path);
    }
}
