<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankFactory>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_name' => $this->faker->company,
            'bank_account_number' => $this->faker->bankAccountNumber,
            'bank_account_name' => $this->faker->name,
            'mobile_money_provider' => $this->faker->company,
            'mobile_money_number' => $this->faker->phoneNumber,
            'mobile_money_name' => $this->faker->name,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
