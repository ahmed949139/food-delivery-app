<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'customer_id'       => User::factory()->create(['role' => 'customer'])->id,
            'restaurant_id'     => Restaurant::factory(),
            'delivery_latitude' => $this->faker->latitude(),
            'delivery_longitude'=> $this->faker->longitude(),
            'status'            => 'pending',
        ];
    }
}
