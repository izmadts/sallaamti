<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Support\ModuleRedirects;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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

        // A value that's neither a clean email nor a clean phone number (e.g.
        // an email and a phone number typed into the one field together)
        // used to fall through to a bare 'string' rule and get saved as-is
        // into whichever column it wasn't recognized as an email for.
        if (! $isEmail) {
            $identifier = preg_replace('/[\s\-]/', '', $identifier);
        }

        Validator::make(['identifier' => $identifier], [
            'identifier' => [
                $isEmail ? 'email' : new ValidPhoneNumber(),
                Rule::unique(User::class, $isEmail ? 'email' : 'phone'),
            ],
        ])->validate();

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
            'quran_module_enabled' => $request->boolean('quran_module_enabled') || $request->input('module') === 'quran_live',
            'counseling_module_enabled' => $request->boolean('counseling_module_enabled'),
            'skills_module_enabled' => $request->boolean('skills_module_enabled'),
        ]);

        Auth::login($user);

        session()->flash('conversion_event', 'user_registered');

        // An explicit ?module= from a homepage card click wins outright —
        // it's the deliberate thing they just tapped, and redirect()->
        // intended() checks session('url.intended') BEFORE even looking at
        // its fallback argument, so a stale intended URL left over from
        // some earlier unrelated page visit (e.g. hitting /dashboard while
        // logged out at some point) would otherwise silently override it.
        // Only when there's no module param do we fall back to intended()
        // — e.g. someone who registered from a shared Nikah profile link
        // should still land back on that exact page — then the checkbox
        // heuristic as the last resort for anyone who landed on /register
        // directly.
        $moduleTarget = ModuleRedirects::resolve($request->input('module'));

        return $moduleTarget
            ? redirect($moduleTarget)
            : redirect()->intended($this->postRegistrationRoute($user));
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
            'skills' => $user->skills_module_enabled,
        ])->filter()->keys();

        if ($selected->count() !== 1) {
            return route('dashboard');
        }

        return match ($selected->first()) {
            'nikah' => route('nikah.create'),
            'quran' => route('courses.index'),
            'counseling' => route('counseling.book.start'),
            'skills' => route('skills.index'),
            default => route('dashboard'),
        };
    }
}
