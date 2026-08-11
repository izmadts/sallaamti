<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: real visitors never see or fill this field, bots that
        // auto-fill every input do. Pretend success so the bot doesn't
        // learn to skip the field next time.
        if ($request->filled('website')) {
            return back()->with('contact_success', 'Thank you! Your message has been received. We will get back to you soon.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'subject_type' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $contactMessage = ContactMessage::create($validated);

        $adminEmail = setting('site_email');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new ContactMessageReceived($contactMessage));
            } catch (\Throwable $e) {
                \Log::error('Contact message email failed: ' . $e->getMessage());
            }
        }

        return back()->with('contact_success', 'Thank you! Your message has been received. We will get back to you soon.');
    }
}
