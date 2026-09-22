<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(10000, 500000);

        return ['order_number' => 'DMT-'.now()->format('ymd').'-'.fake()->unique()->bothify('??##'), 'customer_id' => Customer::factory(), 'public_token' => Str::random(64), 'status' => 'pending_review', 'estimated_subtotal' => $subtotal, 'shipping_cost' => 0, 'estimated_total' => $subtotal, 'amount_paid' => 0, 'fulfillment_method' => 'pickup', 'customer_approved_at' => now()];
    }
}
