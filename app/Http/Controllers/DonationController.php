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
            'donor_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:1'],
            'purpose' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'in:jazzcash,easypaisa,bank_transfer'],
            'payment_reference' => ['required', 'string', 'max:100'],
            'payment_screenshot' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('payment_screenshot')) {
            $validated['payment_screenshot'] = $request->file('payment_screenshot')->store('donations', 'private');
        }
        $validated['donation_number'] = Donation::generateNumber();
        $validated['user_id'] = Auth::id(); // null for guests

        $donation = Donation::create($validated);

        try {
            if (!empty($donation->email)) {
                Mail::to($donation->email)->send(new DonationSubmitted($donation));
            }
            if (!empty(config('mail.admin_email'))) {
                Mail::to(config('mail.admin_email'))->send(new AdminNewDonation($donation));
            }
        } catch (\Throwable $e) {
            \Log::error('Donation submission email failed: ' . $e->getMessage());
        }

        session()->flash('conversion_event', 'donation_submitted');
        session()->flash('conversion_event_data', ['value' => (float) $donation->amount, 'currency' => 'PKR']);

        return redirect()->route('donate.thank-you', $donation);
    }

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
