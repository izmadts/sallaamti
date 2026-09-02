<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\NikahPackage;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NikahPaymentController extends Controller
{
    public function show(Request $request)
    {
        $profile = Auth::user()->nikahProfile;

        if (!$profile) {
            return redirect()->route('nikah.create');
        }

        // Lazily catches a profile sitting unpaid from before admin
        // disabled payment globally or set up a 100% marital-status
        // discount (both folded into applicableVerificationFee() itself)
        // — same approach as age auto-deriving from date_of_birth on
        // save, no bulk migration needed either way.
        if ($profile->payment_status !== 'confirmed' && $profile->applicableVerificationFee() <= 0) {
            $profile->update([
                'payment_status' => 'confirmed',
                'payment_amount' => 0,
                'payment_method' => 'waived',
                'payment_confirmed_at' => now(),
                'payment_rejection_reason' => null,
                'fee_waived' => true,
                'fee_waived_reason' => !setting('nikah_payment_required', true) ? 'Payment disabled site-wide' : 'Marital status discount (100%)',
            ]);

            return redirect()->route('dashboard')->with('status', __('db.No verification fee is required right now — your profile is ready.'));
        }

        // Once a counselor is hired, the page pivots entirely to the
        // package-payment state (see nikah/payment.blade.php) — the
        // self-service fee card and the counselor list are mutually
        // exclusive with "already hired someone" from that point on.
        $lead = Lead::where('nikah_profile_id', $profile->id)->whereNotNull('assigned_to')->with('assignedTo', 'nikahPackage')->first();

        $counselors = null;
        $packages = null;

        if ($lead) {
            $packages = NikahPackage::active()->ordered()->get()->reject->isOneTime()->values();
        } else {
            $city = trim((string) $request->query('city', ''));
            $gender = $request->query('gender');

            $counselors = MatchmakerApplication::where('status', 'certified')
                ->with('user')
                ->get()
                ->filter(fn (MatchmakerApplication $app) => $app->user && $app->user->hasRole('matchmaker'))
                ->when($city !== '', fn ($apps) => $apps->filter(
                    fn (MatchmakerApplication $app) => $app->user->city && str_contains(strtolower($app->user->city), strtolower($city))
                ))
                ->when(in_array($gender, ['male', 'female'], true), fn ($apps) => $apps->filter(
                    fn (MatchmakerApplication $app) => $app->user->gender === $gender
                ))
                ->values();
        }

        return view('nikah.payment', compact('profile', 'lead', 'counselors', 'packages'));
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
        $validated['payment_amount'] = $profile->applicableVerificationFee();

        $profile->update($validated);

        \App\Models\User::role('admin')->each(function ($admin) use ($profile) {
            try {
                $admin->notify(new \App\Notifications\NewNikahPaymentSubmitted($profile));
            } catch (\Throwable $e) {
                \Log::error('NewNikahPaymentSubmitted notification failed: ' . $e->getMessage());
            }
        });

        return redirect()->route('dashboard')->with('status', 'Payment proof submitted! Our team will confirm it shortly.');
    }
}
