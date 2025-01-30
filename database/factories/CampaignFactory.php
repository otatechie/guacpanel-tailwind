<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Charity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(rand(5, 8), true),
            'slug' => $this->faker->slug,
            'cover_photo' => 'sample_cover.jpg', // Adjust if you need to upload real files
            'youtube_video_link' => 'https://www.youtube.com/watch?v=' . Str::random(10),
            'category' => $this->faker->randomElement([
                'Animal Welfare',
                'Arts and Culture',
                'Children and Youth',
                'Community Development',
                'Disaster Relief',
                'Economic Empowerment',
                'Education',
                'Elderly Care',
                'Environment',
                'Gender Equality',
                'Health and Wellness',
                'Human Rights',
                'Hunger and Nutrition',
                'Mental Health',
                'Poverty Alleviation',
                'Research and Innovation',
                'Sports and Recreation',
                'Technology and Innovation',
                'Veteran Support',
                'Water and Sanitation',
                'Women Empowerment'
            ]),
            'city' => $this->faker->randomElement([
                'Ahafo',
                'Ashanti',
                'Bono',
                'Bono East',
                'Central',
                'Eastern',
                'Greater Accra',
                'Northern',
                'North East',
                'Western',
                'Western North',
                'Oti',
                'Savannah',
                'Upper East',
                'Upper West',
                'Volta',
            ]),
            'description' => $this->faker->paragraph,
            'goal_amount' => $this->faker->numberBetween(1000, 100000),
            'amount_raised' => function (array $attributes) {
                return $this->faker->numberBetween(0, $attributes['goal_amount']);
            },
            'end_date' => $this->faker->dateTimeBetween('now', '+2 months')->format('Y-m-d H:i:s'),
            'charity_id' => Charity::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(['active', 'completed', 'archived']),
        ];
    }
}
