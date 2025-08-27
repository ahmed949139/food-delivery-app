<?php

namespace Database\Factories;

use App\Models\DeliveryMen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryMen>
 */
class DeliveryMenFactory extends Factory
{
    protected $model = DeliveryMen::class;

    public function definition()
    {
        return [
            'user_id'      => User::factory()->create(['role' => 'delivery_men'])->id,
            'latitude'     => $this->faker->latitude(),
            'longitude'    => $this->faker->longitude(),
            'is_available' => true,
        ];
    }
}
