<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use Illuminate\Http\Request;

class NikahVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = NikahProfile::with('user');

        // Filter by verification status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('verification_status', $request->status);
        }

        // Filter by gender (via related user)
        if ($request->filled('gender')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        // Search by name, email, city, or CNIC number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('cnic_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $profiles = $query->latest()->paginate(10)->withQueryString();

        return view('admin.nikah.nikah-verifications', compact('profiles'));
    }

    public function approve(NikahProfile $profile)
    {
        abort_unless($profile->payment_status === 'confirmed', 403, 'Payment must be confirmed before verification.');

        $profile->update(['verification_status' => 'verified', 'rejection_reason' => null]);
        return back()->with('status', 'Profile approved.');
    }

    public function reject(Request $request, NikahProfile $profile)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $profile->update(['verification_status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        return back()->with('status', 'Profile rejected.');
    }
}
