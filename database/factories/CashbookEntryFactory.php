<?php

namespace Database\Factories;

use App\Models\CashbookEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashbookEntry>
 */
class CashbookEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'direction' => 'in',
            'category' => 'payment',
            'amount' => fake()->numberBetween(50000, 500000),
            'reference' => 'factory:'.fake()->unique()->uuid(),
        ];
    }
}
