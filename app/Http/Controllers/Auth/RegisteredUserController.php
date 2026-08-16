<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    use RegistersMinimalUsers;

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $identifier = trim($request->identifier);
        $isEmail = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);

        $request->validate([
            'identifier' => [
                $isEmail ? 'email' : 'string',
                Rule::unique(User::class, $isEmail ? 'email' : 'phone'),
            ],
        ]);

        $user = $this->createMinimalUser(
            $request->name,
            $isEmail ? strtolower($identifier) : null,
            $isEmail ? null : $identifier,
            $request->password
        );

        // Interest checkboxes on the registration form — same columns the
        // profile page's "Your Interests" section and the topbar toggle
        // read/write, so whichever the member picks here is already in
        // effect on their very first dashboard view.
        $user->update([
            'nikah_module_enabled' => $request->boolean('nikah_module_enabled'),
            'quran_module_enabled' => $request->boolean('quran_module_enabled'),
            'counseling_module_enabled' => $request->boolean('counseling_module_enabled'),
        ]);

        Auth::login($user);

        session()->flash('conversion_event', 'user_registered');

        // `intended()` still wins first — e.g. someone who registered from a
        // shared Nikah profile link must land back on that exact page, not
        // get diverted here. This default only kicks in when there's no
        // intended URL waiting.
        return redirect()->intended($this->postRegistrationRoute($user));
    }

    // A single interest picked at signup is a strong enough signal to skip
    // the dashboard entirely and drop the member straight into that
    // module's next step; picking none, two, or all three is ambiguous, so
    // the dashboard (with only the chosen modules visible) is the safer
    // landing spot.
    protected function postRegistrationRoute(User $user): string
    {
        $selected = collect([
            'nikah' => $user->nikah_module_enabled,
            'quran' => $user->quran_module_enabled,
            'counseling' => $user->counseling_module_enabled,
        ])->filter()->keys();

        if ($selected->count() !== 1) {
            return route('dashboard');
        }

        return match ($selected->first()) {
            'nikah' => route('nikah.create'),
            'quran' => route('courses.index'),
            'counseling' => route('counseling.book.start'),
            default => route('dashboard'),
        };
    }
}
