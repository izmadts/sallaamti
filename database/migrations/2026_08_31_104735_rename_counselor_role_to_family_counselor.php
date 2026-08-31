<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// The 'counselor' role (Family Counseling staff) shares a name with the
// unrelated 'matchmaker' role (Nikah Counselors), which caused confusion
// when talking about "the counselor". Renaming the role record in place
// (rather than reassigning it) keeps every existing model_has_roles
// assignment intact, since those reference role_id, not the name.
return new class extends Migration
{
    public function up(): void
    {
        Role::where('name', 'counselor')->update(['name' => 'family_counselor']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Role::where('name', 'family_counselor')->update(['name' => 'counselor']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
