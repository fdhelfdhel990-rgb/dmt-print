<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::factory();

        return [
            'subject_type' => Order::class,
            'subject_id' => $order,
            'event' => 'factory.created',
            'properties' => [],
        ];
    }
}
