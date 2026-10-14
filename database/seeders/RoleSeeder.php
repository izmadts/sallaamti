<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        // Full Gate::before bypass, same as 'admin' (see AppServiceProvider),
        // but protected from being touched (role changed, deactivated,
        // deleted) by a plain admin — only another super_admin, or the
        // account itself, can do that. See UserManagementController.
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'family_counselor']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'member']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'blogger']);
        Role::firstOrCreate(['name' => 'donor']);
        Role::firstOrCreate(['name' => 'volunteer']);
        Role::firstOrCreate(['name' => 'matchmaker']);

    }
}
