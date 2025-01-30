<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Charity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'amount' => $this->faker->randomFloat(2, 1, 10000),
            'email' => $this->faker->unique()->safeEmail,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'trans_id' => $this->faker->uuid,
            'ref_id' => $this->faker->uuid,
            'campaign_id' => Campaign::inRandomOrder()->first()->id,
        ];
    }
}
