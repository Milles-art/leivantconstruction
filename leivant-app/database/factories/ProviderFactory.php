<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'region_id' => Region::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(['Material Suppliers', 'Equipment Rental', 'Architects', 'Engineers', 'Contractors', 'Skilled Labour', 'Site Support', 'Electrical & Plumbing']),
            'phone' => '+255'.fake()->numberBetween(600000000, 799999999),
            'email' => fake()->companyEmail(),
            'location' => fake()->city(),
            'description' => fake()->sentence(18),
            'rating' => fake()->randomFloat(1, 3.8, 5.0),
            'is_verified' => true,
            'is_active' => true,
        ];
    }
}
