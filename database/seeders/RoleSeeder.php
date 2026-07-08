<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'counselor']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'member']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'blogger']);
        Role::firstOrCreate(['name' => 'donor']);
        Role::firstOrCreate(['name' => 'volunteer']);

    }
}
