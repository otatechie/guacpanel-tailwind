<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Charity;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionRoleSeeder::class,
            PhoneBrandSeeder::class,
            PhoneVariantSeeder::class,
            PhoneModelSeeder::class,
        ]);
    }
}
