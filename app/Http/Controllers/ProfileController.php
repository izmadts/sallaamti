<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->hasFile('avatar')) {
            $request->user()->avatar = ImageOptimizer::store($request->file('avatar'), 'avatars', 'private', maxDimension: 512);
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Keep the account and Nikah profile's city in sync both ways — if
        // they have a Nikah profile with no city of its own yet, carry this
        // over instead of leaving it blank there.
        if ($request->user()->city && $nikahProfile = $request->user()->nikahProfile) {
            if (!$nikahProfile->city) {
                $nikahProfile->update(['city' => $request->user()->city]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Which modules (Nikah / Quran / Family Support) show up in this
    // member's nav and dashboard. Doesn't restrict access to anything they
    // already have — e.g. an existing Nikah profile stays fully manageable
    // even if they later uncheck it here, they just won't see it promoted
    // in their menu.
    public function updateModules(Request $request): RedirectResponse
    {
        $request->user()->update([
            'nikah_module_enabled' => $request->boolean('nikah_module_enabled'),
            'quran_module_enabled' => $request->boolean('quran_module_enabled'),
            'counseling_module_enabled' => $request->boolean('counseling_module_enabled'),
        ]);

        return Redirect::route('profile.edit')->with('status', 'modules-updated');
    }

    /**
     * Deactivate the user's account. Not an immediate hard delete — the
     * account and its data stay intact for 30 days so the member can
     * change their mind just by logging back in (see AuthenticatedSessionController),
     * after which SendAccountDeletionReminders / PurgeDeactivatedAccounts
     * permanently erases anything still deactivated.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->deactivate();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Your account has been deactivated. Your data will be kept safe for 30 days — log back in anytime during that window to reactivate it. After 30 days it will be permanently deleted.');
    }
}
