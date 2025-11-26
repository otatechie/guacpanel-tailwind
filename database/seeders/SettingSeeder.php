<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'passwordless_login'        => false,
            'password_expiry'           => false,
            'two_factor_authentication' => false,
        ]);
    }
}
