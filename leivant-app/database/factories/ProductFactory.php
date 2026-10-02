<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement(['Concrete Mixer', 'Plate Compactor', 'Angle Grinder', 'Scaffolding Set', 'Site Generator']).' '.fake()->unique()->numberBetween(10, 999);

        return [
            'category_id' => Category::factory(),
            'provider_id' => Provider::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(22),
            'price' => fake()->numberBetween(45000, 2500000),
            'unit' => fake()->randomElement(['item', 'set', 'unit', 'kit']),
            'stock' => fake()->numberBetween(1, 45),
            'region' => fake()->randomElement(['Dar es Salaam', 'Dodoma', 'Mwanza', 'Arusha', 'Morogoro']),
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => fake()->numberBetween(10000, 180000),
            'availability_status' => fake()->randomElement(['available', 'limited', 'booked']),
            'equipment_condition' => fake()->randomElement(['New', 'Good working condition', 'Site-tested']),
            'is_featured' => fake()->boolean(35),
            'is_active' => true,
        ];
    }
}
