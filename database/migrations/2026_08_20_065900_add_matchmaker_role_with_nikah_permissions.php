<?php

use App\Support\PermissionCatalog;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // A staff role scoped to just Nikah profile creation/moderation —
        // reuses the same granular nikah.* permissions an admin could hand
        // out manually via the Roles page, just pre-wired so it works the
        // moment it's assigned.
        PermissionCatalog::ensureSeeded();

        $role = Role::firstOrCreate(['name' => 'matchmaker', 'guard_name' => 'web']);
        $role->givePermissionTo(['nikah.view', 'nikah.manage']);
    }

    public function down(): void
    {
        Role::where('name', 'matchmaker')->delete();
    }
};
