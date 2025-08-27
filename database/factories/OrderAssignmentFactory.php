<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderAssignment>
 */
class OrderAssignmentFactory extends Factory
{
    protected $model = OrderAssignment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'delivery_men_id' => User::factory()->state(['role' => 'delivery_men']),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
