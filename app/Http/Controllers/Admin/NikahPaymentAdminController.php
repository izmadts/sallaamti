<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use Illuminate\Http\Request;

class NikahPaymentAdminController extends Controller
{

    public function index(Request $request)
    {
        $profiles = NikahProfile::with('user')
            ->where('payment_status', 'submitted')
            ->latest()
            ->paginate(10);

        return view('admin.nikah.nikah-payments', compact('profiles'));
    }  

    public function confirm(NikahProfile $profile)
    {
        $profile->update([
            'payment_status' => 'confirmed',
            'payment_confirmed_at' => now(),
        ]);

        return back()->with('status', 'Payment confirmed. Profile can now proceed to CNIC verification.');
    }

    public function reject(Request $request, NikahProfile $profile)
    {
        $request->validate(['payment_rejection_reason' => 'required|string|max:500']);

        $profile->update([
            'payment_status' => 'rejected',
            'payment_rejection_reason' => $request->payment_rejection_reason,
        ]);

        return back()->with('status', 'Payment rejected. User will need to resubmit.');
    }
}
