<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NikahProfile;
use App\Services\ImageOptimizer;
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

    public function bulkConfirm(Request $request)
    {
        $request->validate(['profile_ids' => ['required', 'array'], 'profile_ids.*' => ['integer', 'exists:nikah_profiles,id']]);

        $profiles = NikahProfile::whereIn('id', $request->profile_ids)
            ->where('payment_status', 'submitted')
            ->get();

        foreach ($profiles as $profile) {
            $profile->update(['payment_status' => 'confirmed', 'payment_confirmed_at' => now()]);

            try {
                $profile->user->notify(new \App\Notifications\NikahPaymentConfirmed());
            } catch (\Throwable $e) {
                \Log::error('NikahPaymentConfirmed notification failed: ' . $e->getMessage());
            }

            if ($profile->verification_status === 'pending') {
                \App\Models\User::role('admin')->each(function ($admin) use ($profile) {
                    try {
                        $admin->notify(new \App\Notifications\NewNikahProfilePendingVerification($profile));
                    } catch (\Throwable $e) {
                        \Log::error('NewNikahProfilePendingVerification notification failed: ' . $e->getMessage());
                    }
                });
            }
        }

        return back()->with('status', "{$profiles->count()} payment(s) confirmed.");
    }

    public function confirm(NikahProfile $profile)
    {
        // Mirror bulkConfirm()'s own guard: only a payment actually awaiting
        // review can be confirmed. Without this, a double-click / two admin
        // tabs re-sends the confirmation notification (and the "pending
        // verification" broadcast to every admin) on every click, and a
        // stale link could silently re-confirm a profile that was already
        // rejected with no new proof submitted.
        if ($profile->payment_status !== 'submitted') {
            return back()->with('error', "This payment is already {$profile->payment_status} — nothing to confirm.");
        }

        $profile->update(['payment_status' => 'confirmed', 'payment_confirmed_at' => now()]);

        try {
            $profile->user->notify(new \App\Notifications\NikahPaymentConfirmed());
        } catch (\Throwable $e) {
            \Log::error('NikahPaymentConfirmed notification failed: ' . $e->getMessage());
        }

        if ($profile->verification_status === 'pending') {
            \App\Models\User::role('admin')->each(function ($admin) use ($profile) {
                try {
                    $admin->notify(new \App\Notifications\NewNikahProfilePendingVerification($profile));
                } catch (\Throwable $e) {
                    \Log::error('NewNikahProfilePendingVerification notification failed: ' . $e->getMessage());
                }
            });
        }

        return back()->with('status', 'Payment confirmed.');
    }

    // Many members find it easier to just send their JazzCash/bank receipt
    // straight to us on WhatsApp or in person than to fill out the online
    // upload form. This records exactly that — admin enters what they were
    // told/shown and confirms the payment on the member's behalf, with the
    // same downstream effects (notify member, notify admins for review) as
    // a normal online submission.
    public function recordOffline(Request $request, NikahProfile $profile)
    {
        if ($profile->payment_status === 'confirmed') {
            return back()->with('error', 'Payment is already confirmed for this profile.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:whatsapp,cash,jazzcash,bank_transfer,other'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('payment_screenshot')) {
            $validated['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');
        }

        $validated['payment_status'] = 'confirmed';
        $validated['payment_confirmed_at'] = now();
        $validated['payment_amount'] = $profile->payment_amount ?: setting('nikah_verification_fee', config('services.nikah.verification_fee'));
        $validated['payment_rejection_reason'] = null;

        \DB::transaction(function () use ($profile, $validated) {
            $profile->update($validated);

            $profile->moderationNotes()->create([
                'admin_id' => auth()->id(),
                'note' => 'Payment recorded manually by admin — received via ' . $validated['payment_method'] . '.',
            ]);
        });

        try {
            $profile->user->notify(new \App\Notifications\NikahPaymentConfirmed());
        } catch (\Throwable $e) {
            \Log::error('NikahPaymentConfirmed notification failed: ' . $e->getMessage());
        }

        if ($profile->verification_status === 'pending') {
            \App\Models\User::role('admin')->each(function ($admin) use ($profile) {
                try {
                    $admin->notify(new \App\Notifications\NewNikahProfilePendingVerification($profile));
                } catch (\Throwable $e) {
                    \Log::error('NewNikahProfilePendingVerification notification failed: ' . $e->getMessage());
                }
            });
        }

        return back()->with('status', 'Payment recorded and confirmed for ' . ($profile->user?->name ?? 'the member') . '.');
    }

    // Admin discretion, case by case (e.g. a widow/divorcee re-entering
    // the platform) — not an automatic rule off marital_status. Reuses the
    // normal payment_status/amount/method columns (set to confirmed/0/
    // 'waived') so every existing payment_status === 'confirmed' check
    // across the app — browse eligibility, canInteract(), etc. — treats a
    // waived profile exactly like a paid one, with zero other code changes.
    public function waive(Request $request, NikahProfile $profile)
    {
        if ($profile->payment_status === 'confirmed') {
            return back()->with('error', 'Payment is already confirmed for this profile.');
        }

        $validated = $request->validate([
            'fee_waived_reason' => ['nullable', 'string', 'max:255'],
        ]);

        \DB::transaction(function () use ($profile, $validated) {
            $profile->update([
                'payment_status' => 'confirmed',
                'payment_amount' => 0,
                'payment_method' => 'waived',
                'payment_confirmed_at' => now(),
                'payment_rejection_reason' => null,
                'fee_waived' => true,
                'fee_waived_by' => auth()->id(),
                'fee_waived_reason' => $validated['fee_waived_reason'] ?? null,
            ]);

            $profile->moderationNotes()->create([
                'admin_id' => auth()->id(),
                'note' => 'Verification fee waived by admin' . (!empty($validated['fee_waived_reason']) ? ' — ' . $validated['fee_waived_reason'] : '') . '.',
            ]);
        });

        try {
            $profile->user->notify(new \App\Notifications\NikahPaymentConfirmed());
        } catch (\Throwable $e) {
            \Log::error('NikahPaymentConfirmed notification failed: ' . $e->getMessage());
        }

        if ($profile->verification_status === 'pending') {
            \App\Models\User::role('admin')->each(function ($admin) use ($profile) {
                try {
                    $admin->notify(new \App\Notifications\NewNikahProfilePendingVerification($profile));
                } catch (\Throwable $e) {
                    \Log::error('NewNikahProfilePendingVerification notification failed: ' . $e->getMessage());
                }
            });
        }

        return back()->with('status', 'Verification fee waived for ' . ($profile->user?->name ?? 'the member') . '.');
    }

    public function reject(Request $request, NikahProfile $profile)
    {
        $request->validate(['payment_rejection_reason' => 'required|string|max:500']);

        $updates = [
            'payment_status' => 'rejected',
            'payment_rejection_reason' => $request->payment_rejection_reason,
        ];

        // If this payment was previously confirmed and the profile already went
        // through verification off the back of it, a retroactive rejection (e.g.
        // fraud discovered later) must pull the profile back out of the public
        // browse results — verified status can't outlive its own payment.
        if ($profile->verification_status === 'verified') {
            $updates['verification_status'] = 'rejected';
            $updates['rejection_reason'] = 'Payment confirmation was revoked: ' . $request->payment_rejection_reason;
        }

        $profile->update($updates);

        return back()->with('status', 'Payment rejected. User will need to resubmit.');
    }
}
