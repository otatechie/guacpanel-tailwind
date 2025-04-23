<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create superuser
        $superuser = User::create([
            'name' => 'Ota',
            'email' => 'ota@example.com',
            'password' => bcrypt('password'),
        ]);
        $superuser->assignRole('superuser');

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('user');
    }
} 