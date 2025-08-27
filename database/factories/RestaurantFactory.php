<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition()
    {
        return [
            'owner_id' => User::factory()->create(['role' => 'restaurant_owner'])->id,
            'name'     => $this->faker->company(),
            'address'  => $this->faker->address(),
        ];
    }
}
