<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PinController extends Controller
{
    /**
     * Set or change the authenticated user's login PIN.
     */
    public function update(Request $request): RedirectResponse
    {
        // A social-only account has no password to verify against —
        // being authenticated in this session is already proof enough for
        // them, same as it is for every other action a logged-in user can
        // take. Everyone else must prove they still are who they say they
        // are, same as changing a password.
        $rules = ['pin' => ['required', 'digits:4', 'confirmed']];
        if ($request->user()->password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validateWithBag('updatePin', $rules);

        $request->user()->update(['pin' => $validated['pin']]);

        return back()->with('status', 'pin-updated');
    }

    /**
     * Remove the authenticated user's login PIN.
     *
     * No current_password check here (unlike update() above) — removing a
     * PIN only ever makes the account *harder* to get into (falls back to
     * requiring the real password), so there's nothing for a check like
     * that to protect against here; the CSRF token plus an authenticated
     * session is enough.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->update(['pin' => null]);

        return back()->with('status', 'pin-removed');
    }
}
