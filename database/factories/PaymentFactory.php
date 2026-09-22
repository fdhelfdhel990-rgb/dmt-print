<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'method' => fake()->randomElement(['qris', 'bank_transfer', 'cash']),
            'payment_type' => fake()->randomElement(['down_payment', 'full', 'settlement']),
            'amount' => fake()->numberBetween(50000, 500000),
            'status' => 'pending',
        ];
    }
}
