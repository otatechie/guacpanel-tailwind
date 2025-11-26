<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolename = 'user';
        $role = Role::where('name', $rolename)->first();
        if ($role === null) {
            Role::create(['name' => $rolename]);
        }
    }
}
