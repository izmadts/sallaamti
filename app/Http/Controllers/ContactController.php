<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // For now just redirect back with success
        // Later: send email via Mail::to('info@sallaamti.com')->send(...)
        return back()->with('contact_success', 'Thank you! Your message has been received. We will get back to you soon.');
    }
}
