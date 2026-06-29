<?php

namespace App\Http\Controllers;

use App\Models\NikahProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NikahProfileController extends Controller
{
    public function create()
    {
        // If profile already exists, redirect to edit instead
        if (Auth::user()->nikahProfile) {
            return redirect()->route('nikah.show');
        }

        return view('nikah.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateProfile($request);
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('cnic_front_image')) {
            $validated['cnic_front_image'] = $request->file('cnic_front_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('cnic_back_image')) {
            $validated['cnic_back_image'] = $request->file('cnic_back_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('nikah/photos', 'private');
        }

        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        $validated['payment_amount'] = config('services.nikah.verification_fee');

        $profile = NikahProfile::create($validated);

        return redirect()->route('nikah.payment')->with('status', 'Profile submitted! Please complete the verification fee payment to proceed.');
    }

    public function show()
    {
        $profile = Auth::user()->nikahProfile;

        if (!$profile) {
            return redirect()->route('nikah.create');
        }

        return view('nikah.show', compact('profile'));
    }

    public function edit()
    {
        $profile = Auth::user()->nikahProfile;

        if (!$profile) {
            return redirect()->route('nikah.create');
        }

        return view('nikah.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->nikahProfile;
        $validated = $this->validateProfile($request, $profile);
        $validated['allow_photo_sharing'] = $request->has('allow_photo_sharing');
        
        if ($request->hasFile('cnic_front_image')) {
            $validated['cnic_front_image'] = $request->file('cnic_front_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('cnic_back_image')) {
            $validated['cnic_back_image'] = $request->file('cnic_back_image')->store('nikah/cnic', 'private');
        }
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('nikah/photos', 'private');
        }

        $profile->update($validated);

        return redirect()->route('nikah.show')->with('status', 'Profile updated successfully.');
    }

    private function validateProfile(Request $request, ?NikahProfile $profile = null): array
    {
        return $request->validate([
            'age' => ['required', 'integer', 'min:18', 'max:70'],
            'height' => ['nullable', 'string', 'max:20'],
            'marital_status' => ['required', 'string', 'in:never_married,divorced,widowed,married'],
            'sect' => ['nullable', 'string', 'max:100'],
            'caste' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'family_type' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_contact' => ['required', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'about' => ['nullable', 'string', 'max:2000'],
            'expectations' => ['nullable', 'string', 'max:2000'],
            'cnic_number' => [$profile?->cnic_number ? 'nullable' : 'required', 'string', 'max:20'],
            'cnic_front_image' => [$profile?->cnic_front_image ? 'nullable' : 'required', 'image', 'max:4096'],
            'cnic_back_image' => [$profile?->cnic_back_image ? 'nullable' : 'required', 'image', 'max:4096'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'allow_photo_sharing' => ['nullable', 'boolean'],
            'visibility' => ['required', 'in:public,private'],
        ]);
    }
    public function browse(Request $request)
    {
        $myProfile = Auth::user()->nikahProfile;

        if (!$myProfile) {
            return redirect()->route('nikah.create')->with('status', 'Create your profile first to browse matches.');
        }

        $query = NikahProfile::where('verification_status', 'verified')
            ->where('is_active', true)
            ->where('visibility', 'public')
            ->where('user_id', '!=', Auth::id())
            ->whereHas('user', function ($q) {
                $q->where('gender', '!=', Auth::user()->gender);
            });

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('min_age')) {
            $query->where('age', '>=', $request->min_age);
        }
        if ($request->filled('max_age')) {
            $query->where('age', '<=', $request->max_age);
        }

        $profiles = $query->latest()->paginate(9)->withQueryString();

        $sentInterestIds = $myProfile->sentInterests()->pluck('receiver_profile_id')->toArray();

        return view('nikah.browse', compact('profiles', 'sentInterestIds'));
    }
}
