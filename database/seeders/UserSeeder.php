<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Disable Scout syncing during seeding to avoid Typesense configuration errors
        User::withoutSyncingToSearch(function () {
            $superuser = User::create([
                'name' => 'Ota',
                'email' => 'ota@example.com',
                'password' => bcrypt('password'),
            ]);
            $superuser->assignRole('superuser');

            $user = User::create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('user');
        });
    }
} 