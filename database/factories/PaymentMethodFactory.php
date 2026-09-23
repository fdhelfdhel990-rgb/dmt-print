<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['qris', 'bank_transfer', 'cash']),
            'name' => fake()->words(2, true),
            'is_active' => true,
            'instructions' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
