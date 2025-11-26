<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Disable Scout syncing during seeding to avoid Typesense configuration errors
        User::withoutSyncingToSearch(function () {
            if (config('seeders.users.superAdmin.enabled')) {
                $seededSuperAdminEmail = config('seeders.users.superAdmin.email');
                $superuser = User::where('email', '=', $seededSuperAdminEmail)->first();
                if ($superuser === null) {
                    $superuser = User::updateOrCreate([
                        'name'              => config('seeders.users.superAdmin.name'),
                        'email'             => $seededSuperAdminEmail,
                        'password'          => bcrypt(config('seeders.users.superAdmin.password')),
                        'email_verified_at' => now(),
                    ]);
                    $superuser->assignRole(config('seeders.users.superAdmin.role'));
                }
            }

            if (config('seeders.users.regular.enabled')) {
                $seededRegularEmail = config('seeders.users.regular.email');
                $regularuser = User::where('email', '=', $seededRegularEmail)->first();
                if ($regularuser === null) {
                    $regularuser = User::updateOrCreate([
                        'name'              => config('seeders.users.regular.name'),
                        'email'             => $seededRegularEmail,
                        'password'          => bcrypt(config('seeders.users.regular.password')),
                        'email_verified_at' => now(),
                    ]);
                    $regularuser->assignRole(config('seeders.users.regular.role'));
                }
            }
        });
    }
}
