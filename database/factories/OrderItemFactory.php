<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(10000, 100000);

        return ['order_id' => Order::factory(), 'product_id' => Product::factory(), 'product_name' => fake()->words(3, true), 'sku' => fake()->bothify('DMT-??-##'), 'unit' => 'pcs', 'quantity' => 1, 'base_price' => $price, 'options_total' => 0, 'unit_estimate' => $price, 'subtotal' => $price];
    }
}
