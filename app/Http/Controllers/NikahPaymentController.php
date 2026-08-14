<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NikahPaymentController extends Controller
{
    public function show()
    {
        $profile = Auth::user()->nikahProfile;

        if (!$profile) {
            return redirect()->route('nikah.create');
        }
        return view('nikah.payment', compact('profile'));
    }

    public function store(Request $request)
    {
        $profile = Auth::user()->nikahProfile;
        abort_unless($profile, 404);

        // Don't allow resubmission if already confirmed
        if ($profile->payment_status === 'confirmed') {
            return redirect()->route('nikah.show')->with('status', 'Payment already confirmed.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:jazzcash,bank_transfer'],
            'payment_reference' => ['required', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $validated['payment_screenshot'] = $request->file('payment_screenshot')->store('nikah/payments', 'private');
        $validated['payment_status'] = 'submitted';
        $validated['payment_rejection_reason'] = null;
        $validated['payment_amount'] = setting('nikah_verification_fee', config('services.nikah.verification_fee'));

        $profile->update($validated);

        \App\Models\User::role('admin')->each(function ($admin) use ($profile) {
            try {
                $admin->notify(new \App\Notifications\NewNikahPaymentSubmitted($profile));
            } catch (\Throwable $e) {
                \Log::error('NewNikahPaymentSubmitted notification failed: ' . $e->getMessage());
            }
        });

        return redirect()->route('nikah.show')->with('status', 'Payment proof submitted! Our team will confirm it shortly.');
    }
}
