<?php

namespace App\Http\Controllers;

use App\Models\NikahPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class NikahPhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $profile = Auth::user()->nikahProfile;
        abort_unless($profile, 403, 'No Nikah profile found.');

        // Max 5 photos
        abort_if($profile->photos()->count() >= 5, 422, 'Maximum 5 photos allowed.');

        // Store on PRIVATE disk — never public
        $path = $request->file('photo')->store('nikah/photos', 'private');

        $photo = $profile->photos()->create([
            'path' => $path,
            'is_primary' => $profile->photos()->count() === 0, // first photo = primary
        ]);

        return back()->with('status', 'Photo uploaded successfully.');
    }
    ////////////////////Old Store
    // public function store(Request $request)
    // {
    //     $profile = Auth::user()->nikahProfile;
    //     abort_unless($profile, 404);

    //     // Max 5 photos
    //     abort_if($profile->photos()->count() >= 5, 422, 'Maximum 5 photos allowed.');

    //     $request->validate([
    //         'photos.*' => ['required', 'image', 'max:4096'],
    //     ]);

    //     foreach ($request->file('photos', []) as $index => $file) {
    //         $path = $file->store('nikah/photos', 'private');
    //         $isFirst = $profile->photos()->count() === 0 && $index === 0;

    //         NikahPhoto::create([
    //             'nikah_profile_id' => $profile->id,
    //             'path' => $path,
    //             'is_primary' => $isFirst,
    //             'order' => $profile->photos()->max('order') + 1,
    //         ]);
    //     }

    //     return back()->with('status', 'Photos uploaded successfully.');
    // }

    public function destroy(NikahPhoto $photo)
    {
        abort_unless($photo->profile->user_id === Auth::id(), 403);

        Storage::disk('private')->delete($photo->path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        // If deleted primary, set next photo as primary
        if ($wasPrimary) {
            $next = $photo->profile->photos()->first();
            $next?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Photo removed.');
    }

    public function setPrimary(NikahPhoto $photo)
    {
        abort_unless($photo->profile->user_id === Auth::id(), 403);

        // Remove current primary
        $photo->profile->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back()->with('status', 'Primary photo updated.');
    }
    ///////////// Old Show
    // public function show(NikahPhoto $photo)
    // {
    //     $user = Auth::user();
    //     $myProfile = $user->nikahProfile;

    //     // Admin sees all
    //     if ($user->hasRole('admin')) {
    //         return Storage::disk('private')->response($photo->path);
    //     }

    //     // Owner sees their own
    //     if ($photo->profile->user_id === $user->id) {
    //         return Storage::disk('private')->response($photo->path);
    //     }

    //     // Others only see if photo-sharing is enabled AND mutually accepted interest exists
    //     abort_unless($photo->profile->allow_photo_sharing, 403);
    //     abort_unless($myProfile, 403);

    //     $hasAccepted = \App\Models\NikahInterest::where(function ($q) use ($myProfile, $photo) {
    //         $q->where('sender_profile_id', $myProfile->id)
    //             ->where('receiver_profile_id', $photo->nikah_profile_id)
    //             ->where('status', 'accepted');
    //     })->orWhere(function ($q) use ($myProfile, $photo) {
    //         $q->where('receiver_profile_id', $myProfile->id)
    //             ->where('sender_profile_id', $photo->nikah_profile_id)
    //             ->where('status', 'accepted');
    //     })->exists();

    //     abort_unless($hasAccepted, 403);

    //     return Storage::disk('private')->response($photo->path);
    // }
    public function show(NikahPhoto $photo)
    {
        $requestingUser = Auth::user();
        $requestingProfile = $requestingUser?->nikahProfile;
        $ownerProfile = $photo->nikahProfile;

        // Owner can always see their own photos
        if ($photo->nikahProfile->user_id === $requestingUser?->id) {
            return $this->servePhoto($photo);
        }

        // Block if requesting user has no Nikah profile
        abort_unless($requestingProfile, 403, 'You need a Nikah profile to view photos.');

        // Check if blocked
        $isBlocked = \App\Models\NikahBlock::where(function ($q) use ($requestingProfile, $ownerProfile) {
            $q->where('blocker_profile_id', $requestingProfile->id)
                ->where('blocked_profile_id', $ownerProfile->id);
        })->orWhere(function ($q) use ($requestingProfile, $ownerProfile) {
            $q->where('blocker_profile_id', $ownerProfile->id)
                ->where('blocked_profile_id', $requestingProfile->id);
        })->exists();

        abort_if($isBlocked, 403, 'Access denied.');

        // Photos only visible after MUTUAL acceptance
        $hasAccepted = \App\Models\NikahInterest::where(function ($q) use ($requestingProfile, $ownerProfile) {
            $q->where('sender_profile_id', $requestingProfile->id)
                ->where('receiver_profile_id', $ownerProfile->id);
        })->orWhere(function ($q) use ($requestingProfile, $ownerProfile) {
            $q->where('sender_profile_id', $ownerProfile->id)
                ->where('receiver_profile_id', $requestingProfile->id);
        })->where('status', 'accepted')->exists();

        abort_unless($hasAccepted, 403, 'Photos are only visible after mutual acceptance.');

        return $this->servePhoto($photo);
    }

    private function servePhoto(NikahPhoto $photo): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_unless(
            \Storage::disk('private')->exists($photo->path),
            404,
            'Photo not found.'
        );

        $mimeType = \Storage::disk('private')->mimeType($photo->path);

        return \Storage::disk('private')->response(
            $photo->path,
            basename($photo->path),
            ['Content-Type' => $mimeType]
        );
    }
}
