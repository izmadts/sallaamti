<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // A narrower permission than nikah.manage — lets matchmaker help a
    // walk-in create their account + profile (in-person, with consent,
    // CNIC included) without also unlocking the broader moderation console
    // (approve/reject, payments, destroy, browsing other members' CNIC/
    // photo/contact) that nikah.manage grants.
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'nikah.create-profile', 'guard_name' => 'web']);

        $role = Role::where('name', 'matchmaker')->first();
        $role?->givePermissionTo('nikah.create-profile');
    }

    public function down(): void
    {
        $role = Role::where('name', 'matchmaker')->first();
        $role?->revokePermissionTo('nikah.create-profile');

        Permission::where('name', 'nikah.create-profile')->delete();
    }
};
