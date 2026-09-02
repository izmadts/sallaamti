<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MatchmakerApplication;
use App\Models\MatchmakingTimelineEvent;
use App\Models\NikahPackage;
use App\Models\User;
use App\Notifications\MatchmakerClientHired;
use App\Notifications\NewPackagePaymentSubmitted;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Web mirror of Api\V1\NikahHireCounselorController — same bridge from
// self-service (a member with their own NikahProfile) into the
// counselor-assisted flow, just session-authenticated and redirect-based
// instead of Sanctum + JSON. Both render into the same nikah/payment page,
// which is where a member chooses between paying the one-time self-service
// fee themselves or hiring a Nikah Counselor and paying for a package
// instead — see NikahPaymentController::show() for the branching.
class NikahHireCounselorController extends Controller
{
    private function myProfile()
    {
        $profile = Auth::user()->nikahProfile;
        abort_unless($profile, 404, 'Create your Nikah profile first.');
        return $profile;
    }

    public function hire(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => ['required', 'exists:users,id'],
        ]);

        $counselor = User::findOrFail($validated['counselor_id']);
        abort_unless($counselor->hasRole('matchmaker'), 422, 'That account is not a Nikah Counselor.');

        $profile = $this->myProfile();
        $user = Auth::user();

        $lead = Lead::where('nikah_profile_id', $profile->id)->first();

        if ($lead) {
            $lead->update(['assigned_to' => $counselor->id]);
        } else {
            $lead = Lead::create([
                'name' => $user->name,
                'gender' => $user->gender,
                'phone' => $user->phone,
                'email' => $user->email,
                'looking_for' => 'self',
                'source' => 'other',
                'assigned_to' => $counselor->id,
                'nikah_profile_id' => $profile->id,
                'created_by' => $user->id,
            ]);
        }

        MatchmakingTimelineEvent::log($lead, $profile, 'counselor_hired', "{$user->name} chose {$counselor->name} as their Nikah Counselor from their account.");

        try {
            $counselor->notify(new MatchmakerClientHired($lead));
        } catch (\Throwable $e) {
            \Log::error('MatchmakerClientHired notification failed: ' . $e->getMessage());
        }

        return back()->with('status', "You've hired {$counselor->name} as your Nikah Counselor — choose a package below to get started.");
    }

    public function submitPackage(Request $request)
    {
        $profile = $this->myProfile();
        $lead = Lead::where('nikah_profile_id', $profile->id)->whereNotNull('assigned_to')->first();
        abort_unless($lead, 409, 'Hire a Nikah Counselor first.');
        abort_if($lead->package_payment_status === 'submitted', 422, 'A package payment is already awaiting review.');

        $validated = $request->validate([
            'nikah_package_id' => ['required', 'exists:nikah_packages,id'],
            'payment_method' => ['required', 'in:jazzcash,bank_transfer,easypaisa'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_screenshot' => ['required', 'image', 'max:4096'],
        ]);

        $package = NikahPackage::active()->findOrFail($validated['nikah_package_id']);
        abort_if($package->isOneTime(), 422, 'That package is not a Nikah counseling package.');

        $screenshotPath = ImageOptimizer::store($request->file('payment_screenshot'), 'nikah/payments', 'private');

        $lead->update([
            'pending_package_id' => $package->id,
            'package_payment_method' => $validated['payment_method'],
            'package_payment_reference' => $validated['payment_reference'] ?? null,
            'package_payment_screenshot' => $screenshotPath,
            'package_payment_status' => 'submitted',
            'package_payment_rejection_reason' => null,
        ]);

        MatchmakingTimelineEvent::log($lead, $profile, 'package_payment_submitted', "Client selected the {$package->name} package and submitted payment proof.");

        User::role('admin')->each(function ($admin) use ($lead) {
            try {
                $admin->notify(new NewPackagePaymentSubmitted($lead));
            } catch (\Throwable $e) {
                \Log::error('NewPackagePaymentSubmitted notification failed: ' . $e->getMessage());
            }
        });

        return redirect()->route('nikah.payment')->with('status', 'Payment proof submitted! Our team will confirm it shortly.');
    }
}
