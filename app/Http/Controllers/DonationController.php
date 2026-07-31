<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\DonationSubmitted;
use App\Mail\AdminNewDonation;

class DonationController extends Controller
{
    public function create()
    {
        return view('donate.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'amount'              => ['required', 'numeric', 'min:100'],
        'payment_method'      => ['required', 'in:bank_transfer,international'],
        'payment_screenshot'  => ['nullable', 'image', 'max:4096'],
        'donor_name'          => ['nullable', 'string', 'max:255'],
        'donor_email'         => ['nullable', 'email', 'max:255'],
        'donor_phone'         => ['nullable', 'string', 'max:30'],
        'cause'               => ['nullable', 'string', 'max:100'],
        'message'             => ['nullable', 'string', 'max:1000'],
        'is_anonymous'        => ['nullable', 'boolean'],
        'payment_reference'   => ['nullable', 'string', 'max:255'],
    ]);

    $validated['user_id']      = Auth::id();
    $validated['payment_status'] = 'submitted';
    $validated['is_anonymous'] = $request->has('is_anonymous');
    $validated['cause']        = $request->input('cause', 'general');
    $validated['payment_reference'] = $request->input('payment_reference', 'pending-admin-verify');

    if ($request->hasFile('payment_screenshot')) {
        $validated['payment_screenshot'] = $request->file('payment_screenshot')
            ->store('donations/screenshots', 'private');
    }

    $donation = Donation::create($validated);

    // 1. Notify the donor
    if (Auth::check()) {
        Auth::user()->notify(new \App\Notifications\DonationReceived($donation));
    } elseif ($request->filled('donor_email')) {
        // Guest donor — send mail directly
        try {
            \Mail::to($request->donor_email)
                ->send(new \App\Mail\GuestDonationReceived($donation));
        } catch (\Exception $e) {
            // Silently fail — don't break the flow if mail fails
            \Log::error('Guest donation email failed: ' . $e->getMessage());
        }
    }

    // 2. Notify all admins
    \App\Models\User::role('admin')->each(function ($admin) use ($donation) {
        $admin->notify(new \App\Notifications\NewDonationReceived($donation));
    });

    return redirect()->route('donate.thank-you')
        ->with('status', 'JazakAllah Khair! Your donation has been submitted. We will verify within 24 hours.');
}
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'donor_name' => ['required', 'string', 'max:255'],
    //         'email' => ['nullable', 'email', 'max:255'],
    //         'phone' => ['nullable', 'string', 'max:30'],
    //         'amount' => ['required', 'numeric', 'min:1'],
    //         'purpose' => ['nullable', 'string', 'max:100'],
    //         'payment_method' => ['required', 'in:jazzcash,easypaisa,bank_transfer'],
    //         'payment_reference' => ['required', 'string', 'max:100'],
    //         'payment_screenshot' => ['nullable', 'image', 'max:4096'],
    //     ]);

    //     if ($request->hasFile('payment_screenshot')) {
    //         $validated['payment_screenshot'] = $request->file('payment_screenshot')->store('donations', 'private');
    //     }
    //     $validated['donation_number'] = Donation::generateNumber();
    //     $validated['user_id'] = Auth::id(); // null for guests

    //     $donation = Donation::create($validated);

    //     try {
    //         if (!empty($donation->email)) {
    //             Mail::to($donation->email)->send(new DonationSubmitted($donation));
    //         }
    //         if (!empty(config('mail.admin_email'))) {
    //             Mail::to(config('mail.admin_email'))->send(new AdminNewDonation($donation));
    //         }
    //     } catch (\Throwable $e) {
    //         \Log::error('Donation submission email failed: ' . $e->getMessage());
    //     }

    //     session()->flash('conversion_event', 'donation_submitted');
    //     session()->flash('conversion_event_data', ['value' => (float) $donation->amount, 'currency' => 'PKR']);

    //     return redirect()->route('donate.thank-you', $donation);
    // }

    public function thankYou(Donation $donation)
    {
        return view('donate.thank-you', compact('donation'));
    }

    public function myDonations()
    {
        $donations = Auth::user()->donations()->latest()->get();
        return view('donate.my', compact('donations'));
    }

    public function screenshot(Donation $donation)
    {
        $path = $donation->payment_screenshot;
        abort_unless($path && Storage::disk('private')->exists($path), 404);

        $user = Auth::user();
        abort_unless($donation->user_id === $user->id || $user->hasRole('admin'), 403);

        return Storage::disk('private')->response($path);
    }
}
