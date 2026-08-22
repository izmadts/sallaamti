<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        // Pending submissions need attention first, then everything else in
        // its normal display order.
        $testimonials = Testimonial::with('user')
            ->orderByRaw("status = 'pending' desc")
            ->orderBy('order')
            ->get();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'content'  => ['required', 'string'],
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = Testimonial::max('order') + 1;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial added.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'content'  => ['required', 'string'],
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) Storage::disk('public')->delete($testimonial->photo);
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->photo) Storage::disk('public')->delete($testimonial->photo);
        $testimonial->delete();
        return back()->with('status', 'Testimonial deleted.');
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        return back()->with('status', 'Updated.');
    }

    public function approve(Testimonial $testimonial)
    {
        $testimonial->update(['status' => 'approved', 'is_active' => true, 'rejection_reason' => null]);

        return back()->with('status', 'Testimonial approved and published.');
    }

    public function reject(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:500']]);

        $testimonial->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return back()->with('status', 'Testimonial rejected.');
    }
}
