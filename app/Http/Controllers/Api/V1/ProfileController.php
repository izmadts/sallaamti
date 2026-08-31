<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Rules\ValidPhoneNumber;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// The mobile-app side of ProfileController (web). Same fields/validation
// as ProfileUpdateRequest and updateModules() - just JSON in, JSON out,
// and no username/public_bio/password/PIN/deactivation yet (those stay
// web-only for now; nothing here precludes adding them later).
class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['user' => new UserResource($request->user())]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', new ValidPhoneNumber(), Rule::unique('users')->ignore($user->id)],
            'gender' => ['nullable', 'in:male,female'],
            'city' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $user->fill($validated);

        if ($request->hasFile('avatar')) {
            $user->avatar = ImageOptimizer::store($request->file('avatar'), 'avatars', 'private', maxDimension: 512);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Same two-way city sync as the web ProfileController — see
        // feedback-cross-profile-field-sync: whichever side already has a
        // value, the other backfills from it without clobbering anything.
        if ($user->isDirty('city') && $user->city) {
            $profile = $user->nikahProfile;
            if ($profile && !$profile->city) {
                $profile->update(['city' => $user->city]);
            }
        }

        return response()->json(['user' => new UserResource($user->fresh())]);
    }

    public function updateModules(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'nikah_module_enabled' => $request->boolean('nikah_module_enabled'),
            'quran_module_enabled' => $request->boolean('quran_module_enabled'),
            'counseling_module_enabled' => $request->boolean('counseling_module_enabled'),
            'skills_module_enabled' => $request->boolean('skills_module_enabled'),
        ]);

        return response()->json(['user' => new UserResource($user->fresh())]);
    }
}
