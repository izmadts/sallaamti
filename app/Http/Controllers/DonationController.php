<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\ImageOptimizer;
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
    // Honeypot: real visitors never see or fill this field, bots that
    // auto-fill every input do. Pretend success so the bot doesn't learn
    // to skip the field next time.
    if ($request->filled('website')) {
        return redirect()->route('donate.create')
            ->with('status', 'JazakAllah Khair! Your donation has been submitted. We will verify within 24 hours.');
    }

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
    // QA finding: this was missing entirely — donation_number has no DB
    // default, so every submission was throwing a QueryException before
    // ever reaching the redirect below. Donation::generateNumber() already
    // existed (used by the old, now-dead commented-out store() above) but
    // the live rewrite dropped the call.
    $validated['donation_number'] = Donation::generateNumber();
    // QA finding: the form fields are donor_email/donor_phone, but the
    // model's $fillable columns are email/phone — passing the *_email/
    // *_phone keys straight through silently dropped them on every guest
    // donation (mass-assignment ignores non-fillable keys rather than
    // erroring), so guest donors' contact info was never actually saved.
    $validated['email'] = $validated['donor_email'] ?? null;
    $validated['phone'] = $validated['donor_phone'] ?? null;
    unset($validated['donor_email'], $validated['donor_phone']);

    if ($request->hasFile('payment_screenshot')) {
        $validated['payment_screenshot'] = ImageOptimizer::store($request->file('payment_screenshot'), 'donations/screenshots', 'private');
    }

    $donation = Donation::create($validated);

    // 1. Notify the donor
    if (Auth::check()) {
        Auth::user()->notify(new \App\Notifications\DonationReceived($donation));
    } elseif ($request->filled('donor_email')) {
        // Guest donor — send mail directly
        try {
            \Mail::to($request->donor_email)
                ->send(new DonationSubmitted($donation));
        } catch (\Throwable $e) {
            // Silently fail — don't break the flow if mail fails
            \Log::error('Guest donation email failed: ' . $e->getMessage());
        }
    }

    // 2. Notify all admins
    \App\Models\User::role('admin')->each(function ($admin) use ($donation) {
        $admin->notify(new \App\Notifications\NewDonationReceived($donation));
    });

    // The route requires {donation} — omitting it here threw a
    // UrlGenerationException on every real submission (QA finding: this
    // was a live 500 on the happy path, not just a redirect nicety).
    session()->put("donation_thankyou_{$donation->id}", true);

    return redirect()->route('donate.thank-you', $donation)
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

    // Security audit finding: this had no ownership check at all — donation
    // IDs are sequential/enumerable, and while the view currently renders
    // no donor fields, that's a landmine for the day someone adds one. The
    // session flag covers the (also-supported) guest-donor case where
    // there's no account to check ownership against.
    public function thankYou(Donation $donation)
    {
        // get(), not pull() — a page refresh on the thank-you page is normal
        // and shouldn't 404 just because the flag was already read once.
        $justDonated = session()->get("donation_thankyou_{$donation->id}", false);
        $owns = Auth::check() && (Auth::id() === $donation->user_id || Auth::user()->hasRole('admin'));

        abort_unless($justDonated || $owns, 404);

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
