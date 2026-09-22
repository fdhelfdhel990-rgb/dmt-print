<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'sku' => 'DMT-'.fake()->unique()->bothify('??-#####'),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'base_price' => fake()->numberBetween(5000, 500000),
            'unit' => fake()->randomElement(['pcs', 'lembar', 'box', 'set']),
            'minimum_order' => 1,
            'stock_on_hand' => 100,
            'stock_minimum' => 5,
            'production_estimate' => '1-2 hari',
            'tone' => fake()->randomElement(['blue', 'cyan', 'navy', 'yellow']),
            'tag' => null,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
