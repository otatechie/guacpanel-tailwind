<?php

namespace Database\Factories;

use App\Models\Personalisation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalisationFactory extends Factory
{
    protected $model = Personalisation::class;

    public function definition(): array
    {
        return [
            'app_name' => $this->faker->company,
            'timezone' => 'UTC',
            'copyright_text' => '© ' . date('Y'),
            'app_logo' => null,
            'favicon' => null,
        ];
    }
} 