<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personalisation>
 */
class PersonalisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_name' => fake()->company(),
            'app_logo' => null,
            'app_logo_dark' => null,
            'favicon' => null,
            'copyright_text' => fake()->sentence(3),
        ];
    }
}
