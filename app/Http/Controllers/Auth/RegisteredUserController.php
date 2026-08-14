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

        Auth::login($user);

        session()->flash('conversion_event', 'user_registered');

        return redirect(route('dashboard', absolute: false));
    }
}
