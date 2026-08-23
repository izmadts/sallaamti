<?php

namespace App\Http\Controllers;

use App\Services\ImageOptimizer;
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

        // Lazily catches both a profile created after the switch was
        // flipped off and one already sitting unpaid from before — same
        // approach as age auto-deriving from date_of_birth on save, no
        // bulk migration needed when admin toggles the setting.
        if ($profile->payment_status !== 'confirmed' && !setting('nikah_payment_required', true)) {
            $profile->update([
                'payment_status' => 'confirmed',
                'payment_amount' => 0,
                'payment_method' => 'waived',
                'payment_confirmed_at' => now(),
                'payment_rejection_reason' => null,
                'fee_waived' => true,
                'fee_waived_reason' => 'Payment disabled site-wide',
            ]);

            return redirect()->route('nikah.show')->with('status', __('db.No verification fee is required right now — your profile is ready.'));
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
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $validated['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');
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
