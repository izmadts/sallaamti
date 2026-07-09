<?php

namespace App\Http\Controllers;

use App\Models\VolunteerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    public function create()
    {
        return view('volunteer.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:volunteer_applications,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:volunteer_applications,phone'],
            'city' => ['nullable', 'string', 'max:100'],
            'area_of_interest' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = Auth::id();

        VolunteerApplication::create($validated);

        session()->flash('conversion_event', 'volunteer_registered');

        return back()->with('status', 'Thank you! Your volunteer application has been received.');
    }
}
