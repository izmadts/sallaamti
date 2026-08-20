<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // nikah.view/nikah.manage unlock the FULL admin moderation console
    // (approve/reject, payment confirmation, destroy, CNIC/photo/contact
    // visible everywhere) — too broad for "browse profiles without contact
    // details, request access per-profile." Matchmaker access is now scoped
    // by a dedicated 'matchmaker' role middleware + its own routes instead.
    public function up(): void
    {
        $role = Role::where('name', 'matchmaker')->first();
        $role?->revokePermissionTo(['nikah.view', 'nikah.manage']);
    }

    public function down(): void
    {
        $role = Role::where('name', 'matchmaker')->first();
        $role?->givePermissionTo(['nikah.view', 'nikah.manage']);
    }
};
