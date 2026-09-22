<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => 'manual_in',
            'quantity' => 10,
            'balance_after' => 10,
            'reference' => 'factory:'.fake()->unique()->uuid(),
        ];
    }
}
