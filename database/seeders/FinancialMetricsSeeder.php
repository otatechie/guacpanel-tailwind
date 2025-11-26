<?php

namespace Database\Seeders;

use App\Models\FinancialMetric;
use Faker\Factory;
use Illuminate\Database\Seeder;

class FinancialMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $startDate = now()->subDays(100);

        // Disable Scout syncing during seeding to avoid Typesense configuration errors
        FinancialMetric::withoutSyncingToSearch(function () use ($faker, $startDate) {
            for ($i = 0; $i < 100; $i++) {
                FinancialMetric::create([
                    'date'        => $startDate->copy()->addDays($i),
                    'category'    => $faker->randomElement(['sales', 'marketing', 'operations', 'investment']),
                    'amount'      => $faker->randomFloat(2, 1000, 100000),
                    'type'        => $faker->randomElement(['income', 'expense']),
                    'description' => $faker->sentence(),
                ]);
            }
        });
    }
}
