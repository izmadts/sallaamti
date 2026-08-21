<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Auth\Concerns\RegistersMinimalUsers;
use App\Http\Controllers\Auth\Concerns\ResolvesSocialLogin;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeMail;
use App\Rules\ValidPhoneNumber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

// The mobile app's account system — an app account is a web account, so
// every method here ends the same way the web equivalents do (createMinimalUser
// / ResolvesSocialLogin's resolveSocialUser) but issues a Sanctum bearer
// token instead of a session, since a mobile client can't share PHP session
// state across app restarts.
class AuthController extends Controller
{
    use RegistersMinimalUsers, ResolvesSocialLogin;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', new ValidPhoneNumber(), 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (blank($validated['email'] ?? null) && blank($validated['phone'] ?? null)) {
            throw ValidationException::withMessages(['email' => 'An email address or phone number is required.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'provider' => 'password',
        ]);
        $user->assignRole('member');
        event(new Registered($user));

        return $this->tokenResponse($user, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = str_contains($validated['login'], '@') ? 'email' : 'phone';
        $user = User::where($field, $validated['login'])->first();

        if (!$user || !$user->password || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages(['login' => 'Those credentials do not match an account.']);
        }

        if ($user->isDeactivated()) {
            $user->reactivate();
        }

        return $this->tokenResponse($user);
    }

    public function otpRequest(Request $request): JsonResponse
    {
        $user = User::where('phone', $request->input('phone'))->first();
        $purpose = $user ? 'login' : 'registration';

        $request->validate([
            'phone' => ['required', 'string', 'max:20', new ValidPhoneNumber()],
            'email' => ['required', 'email', 'max:255'],
            'name' => [$purpose === 'registration' ? 'required' : 'nullable', 'string', 'max:255'],
        ]);

        $otp = OtpCode::generateFor($request->phone, $purpose, $user?->id);

        Notification::route('mail', $request->email)->notify(new OtpCodeMail($otp->code));

        return response()->json(['purpose' => $purpose]);
    }

    public function otpVerify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20', new ValidPhoneNumber()],
            'code' => ['required', 'digits:6'],
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('phone', $validated['phone'])->first();
        $purpose = $user ? 'login' : 'registration';

        if (!OtpCode::verify($validated['phone'], $validated['code'], $purpose)) {
            throw ValidationException::withMessages(['code' => 'That code is invalid or has expired.']);
        }

        if (!$user) {
            if (blank($validated['name'] ?? null)) {
                throw ValidationException::withMessages(['name' => 'Your name is required to finish registering.']);
            }
            $user = $this->createMinimalUser($validated['name'], $validated['email'], $validated['phone'], provider: 'whatsapp');
        }

        if (!$user->email) {
            $user->email = $validated['email'];
        }
        if ($user->email === $validated['email'] && !$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();

        if ($user->isDeactivated()) {
            $user->reactivate();
        }

        return $this->tokenResponse($user, $purpose === 'registration' ? 201 : 200);
    }

    public function socialGoogle(Request $request): JsonResponse
    {
        $request->validate(['id_token' => ['required', 'string']]);

        $info = Http::get('https://oauth2.googleapis.com/tokeninfo', ['id_token' => $request->id_token])->json();

        $allowedAudiences = config('services.google.mobile_client_ids', []);

        if (!$info || !isset($info['sub']) || (!empty($allowedAudiences) && !in_array($info['aud'] ?? null, $allowedAudiences, true))) {
            throw ValidationException::withMessages(['id_token' => 'Could not verify this Google sign-in.']);
        }

        $user = $this->resolveSocialUser('google', $info['sub'], $info['name'] ?? 'Sallaamti User', $info['email'] ?? null);

        return $this->tokenResponse($user, $user->wasRecentlyCreated ? 201 : 200);
    }

    public function socialFacebook(Request $request): JsonResponse
    {
        $request->validate(['access_token' => ['required', 'string']]);

        $info = Http::get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email',
            'access_token' => $request->access_token,
        ])->json();

        if (!$info || !isset($info['id'])) {
            throw ValidationException::withMessages(['access_token' => 'Could not verify this Facebook sign-in.']);
        }

        $user = $this->resolveSocialUser('facebook', $info['id'], $info['name'] ?? 'Sallaamti User', $info['email'] ?? null);

        return $this->tokenResponse($user, $user->wasRecentlyCreated ? 201 : 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => new UserResource($request->user())]);
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        $token = $user->createToken('sallaamti-app')->plainTextToken;

        // A freshly created model's in-memory attributes don't reflect
        // column defaults applied at the DB level (e.g. the module-enabled
        // flags default to true in the migration but aren't in $fillable
        // passed to create()) — refresh so the response matches what /me
        // would return a moment later instead of showing stale nulls/false.
        $user->refresh();

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], $status);
    }
}
