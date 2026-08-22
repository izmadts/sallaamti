<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function create()
    {
        return view('testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateTestimonial($request);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;
        $validated['status'] = 'pending';
        $validated['order'] = Testimonial::max('order') + 1;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        Testimonial::create($validated);

        return redirect()->route('testimonials.mine')
            ->with('status', 'Thank you for sharing! Your testimonial will appear once our team reviews it.');
    }

    // A member's own submissions across every status — the only place a
    // pending/rejected testimonial is visible to anyone but staff.
    public function mine()
    {
        $testimonials = Testimonial::where('user_id', Auth::id())->latest()->paginate(15);

        return view('testimonials.mine', compact('testimonials'));
    }

    public function edit(Testimonial $testimonial)
    {
        $this->authorizeOwner($testimonial);

        return view('testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $this->authorizeOwner($testimonial);

        $validated = $this->validateTestimonial($request);

        // Editing an already-approved testimonial sends it back for review
        // — otherwise the approval gate is pointless (get one harmless
        // testimonial approved, then silently rewrite it into anything).
        if ($testimonial->status === 'approved') {
            $validated['status'] = 'pending';
        }

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) {
                Storage::disk('public')->delete($testimonial->photo);
            }
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return redirect()->route('testimonials.mine')->with('status', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->authorizeOwner($testimonial);

        if ($testimonial->photo) {
            Storage::disk('public')->delete($testimonial->photo);
        }
        $testimonial->delete();

        return back()->with('status', 'Testimonial deleted.');
    }

    private function authorizeOwner(Testimonial $testimonial): void
    {
        abort_unless($testimonial->user_id === Auth::id(), 403);
    }

    private function validateTestimonial(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
