<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidPhoneNumber;
use App\Services\ImageOptimizer;
use App\Support\PermissionCatalog;
use App\Support\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = UserFilter::apply(
            User::with('roles', 'nikahProfile')->withCount(['enrollments', 'counselingBookings'])->latest(),
            $request
        );

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load('roles', 'nikahProfile');
        $roles = Role::all();
        $allRoles = Role::all();

        return view('admin.users.show', compact('user', 'roles', 'allRoles'));
    }

    // Same fields/rules a member can edit on their own profile (see
    // ProfileUpdateRequest) — admin just edits them on the member's behalf,
    // e.g. fixing a typo'd email that's locking someone out.
    public function updateDetails(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone' => [
                'nullable', 'string', 'max:20', new ValidPhoneNumber(),
                Rule::unique(User::class)->ignore($user->id),
            ],
            'gender' => ['nullable', 'in:male,female'],
            'city' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $emailChanged = $user->email !== ($validated['email'] ?? null);

        $user->fill($validated);

        if ($request->hasFile('avatar')) {
            $user->avatar = ImageOptimizer::store($request->file('avatar'), 'avatars', 'private', maxDimension: 512);
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Same both-directions-if-empty sync ProfileController uses when a
        // member edits their own city.
        if ($user->city && $nikahProfile = $user->nikahProfile) {
            if (!$nikahProfile->city) {
                $nikahProfile->update(['city' => $user->city]);
            }
        }

        return back()->with('status', 'Details updated for ' . $user->name . '.');
    }

    // Doesn't set or reveal a password directly — sends the same reset
    // email a member would trigger themselves from "Forgot your password",
    // so admin never has to know or type someone else's credential.
    public function sendPasswordReset(User $user)
    {
        if (!$user->email) {
            return back()->with('error', "{$user->name} has no email on file to send a reset link to.");
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', "Password reset link sent to {$user->email}.")
            : back()->with('error', 'Could not send the reset link: ' . __($status));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        // Prevent removing your own admin role
        if ($user->id === auth()->id() && !in_array('admin', $request->roles)) {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $user->syncRoles($request->roles);

        return back()->with('status', 'Roles updated for ' . $user->name);
    }

    public function toggleActive(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('status', $user->name . ' has been ' . ($user->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }

    public function roles()
    {
        PermissionCatalog::ensureSeeded();

        $roles = Role::withCount('users')->with('permissions')->get();

        return view('admin.users.roles', [
            'roles' => $roles,
            'resources' => PermissionCatalog::RESOURCES,
            'actions' => PermissionCatalog::ACTIONS,
        ]);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:roles,name'],
        ]);

        Role::create(['name' => Str::lower($request->name)]);

        return back()->with('status', 'Role created.');
    }

    // The 'admin' role is intentionally not editable here — it's a full
    // bypass for every permission check (see AppServiceProvider's
    // Gate::before), so toggling individual permissions for it would look
    // like it does something and actually do nothing.
    public function updatePermissions(Request $request, Role $role)
    {
        abort_if($role->name === 'admin', 422, "The admin role already has full access — it can't be restricted.");

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', PermissionCatalog::all())],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('status', "Permissions updated for {$role->name}.");
    }
}
