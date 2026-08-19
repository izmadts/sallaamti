<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'nikahProfile')
            ->withCount(['enrollments', 'counselingBookings'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('provider')) {
            $request->provider === 'unknown'
                ? $query->whereNull('provider')
                : $query->where('provider', $request->provider);
        }

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
