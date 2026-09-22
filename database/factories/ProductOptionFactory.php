<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductOption>
 */
class ProductOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'name' => fake()->randomElement(['Ukuran', 'Bahan', 'Finishing']), 'is_required' => false, 'is_active' => true, 'sort_order' => 0];
    }
}
