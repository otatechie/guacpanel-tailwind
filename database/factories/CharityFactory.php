<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Charity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Charity>
 */
class CharityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'business_digital_address' => $this->faker->address,
            'address' => $this->faker->address,
            'email_address' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'region' => $this->faker->randomElement(['Ahafo', 'Ashanti', 'Bono', 'Bono East', 'Central', 'Eastern', 'Greater Accra', 'Northern', 'North East', 'Western', 'Western North', 'Oti', 'Savannah', 'Upper East', 'Upper West', 'Volta']),
            'mission_statement' => $this->faker->paragraph,
            'established_date' => $this->faker->date,
            'ghana_card_number' => Str::random(10),
            'business_number' => Str::random(10),
            'twitter' => $this->faker->userName,
            'facebook' => $this->faker->userName,
            'instagram' => $this->faker->userName,
            'website' => $this->faker->url,
            'linkedin' => $this->faker->userName,
            'account_status' => 1,
            'charity_category' => $this->faker->randomElement(['Animals & Pets', 'Business & Entrepreneurship', 'Charity & Nonprofit', 'Community Projects', 'Creative Arts & Music', 'Disaster Relief', 'Education', 'Emergency', 'Environment & Conservation', 'Events & Experiences', 'Faith & Church', 'Family & Parenting', 'Housing & Rent', 'Legal & Advocacy', 'Medical & Health', 'Memorial & Tribute', 'Miscellaneous', 'Natural Disaster', 'Other', 'Poverty Alleviation', 'Religious', 'Science & Technology', 'Sports & Recreation', 'Youth & Children', 'Zoological']),
            'logo' => $this->faker->imageUrl,
            'business_files' => json_encode([$this->faker->url]),
            'user_id' => $this->getUniqueUserId(),
        ];
    }

    private function getUniqueUserId()
    {
        do {
            $user = User::inRandomOrder()->where('charity_affiliate', true)->first();
        } while (Charity::where('user_id', $user->id)->exists());

        return $user->id;
    }
}
