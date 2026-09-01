<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\PaginatesApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Services\ImageOptimizer;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// The mobile side of the web's TestimonialController, plus a read endpoint
// the web doesn't need (it renders approved testimonials into the public
// /testimonial page directly). Same review gate, including sending an
// already-approved testimonial back to pending when its author edits it.
class TestimonialController extends Controller
{
    use PaginatesApiResponses;

    private function payload(Testimonial $testimonial): array
    {
        return [
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'location' => $testimonial->location,
            'content' => $testimonial->content,
            'rating' => $testimonial->rating,
            'photo_url' => $testimonial->photo ? Storage::url($testimonial->photo) : null,
            'status' => $testimonial->status,
            'rejection_reason' => $testimonial->rejection_reason,
            'created_at' => $testimonial->created_at?->toIso8601String(),
        ];
    }

    /** Approved testimonials — what the public /testimonial page shows. */
    public function index(): JsonResponse
    {
        $testimonials = Testimonial::published()->orderBy('order')->paginate(15);

        return response()->json(
            $this->paginated($testimonials, 'testimonials', fn (Testimonial $t) => $this->payload($t))
        );
    }

    /** The member's own, across every status. */
    public function mine(Request $request): JsonResponse
    {
        $testimonials = Testimonial::where('user_id', $request->user()->id)->latest()->paginate(15);

        return response()->json(
            $this->paginated($testimonials, 'testimonials', fn (Testimonial $t) => $this->payload($t))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateTestimonial($request);

        $validated['user_id'] = $request->user()->id;
        $validated['is_active'] = true;
        $validated['status'] = 'pending';
        $validated['order'] = Testimonial::max('order') + 1;

        if ($request->hasFile('photo')) {
            $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'testimonials', 'public', maxDimension: 600, quality: 82);
        }

        $testimonial = Testimonial::create($validated);

        return response()->json([
            'testimonial' => $this->payload($testimonial),
            'message' => __('db.Thank you for sharing! Your testimonial will appear once our team reviews it.'),
        ], 201);
    }

    public function update(Request $request, Testimonial $testimonial): JsonResponse
    {
        $this->authorizeOwner($testimonial, $request);

        $validated = $this->validateTestimonial($request);

        // Editing an approved testimonial sends it back for review, same as
        // web — otherwise approval could be won once and the text swapped.
        if ($testimonial->status === 'approved') {
            $validated['status'] = 'pending';
        }

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) {
                Storage::disk('public')->delete($testimonial->photo);
            }
            $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'testimonials', 'public', maxDimension: 600, quality: 82);
        }

        $testimonial->update($validated);

        return response()->json([
            'testimonial' => $this->payload($testimonial),
            'message' => __('db.Testimonial updated.'),
        ]);
    }

    public function destroy(Testimonial $testimonial, Request $request): JsonResponse
    {
        $this->authorizeOwner($testimonial, $request);

        if ($testimonial->photo) {
            Storage::disk('public')->delete($testimonial->photo);
        }

        $testimonial->delete();

        return response()->json(['status' => 'ok', 'message' => __('db.Testimonial deleted.')]);
    }

    private function authorizeOwner(Testimonial $testimonial, Request $request): void
    {
        abort_unless((int) $testimonial->user_id === (int) $request->user()->id, 403);
    }

    private function validateTestimonial(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['content'] = HtmlSanitizer::cleanAuthoredText($validated['content']);

        return $validated;
    }
}
