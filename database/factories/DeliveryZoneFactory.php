<?php

namespace Database\Factories;

use App\Models\DeliveryZone;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryZone>
 */
class DeliveryZoneFactory extends Factory
{
    protected $model = DeliveryZone::class;

    public function definition()
    {
        return [
            'restaurant_id'   => Restaurant::factory(),
            'type'            => 'radius',
            'center_latitude' => $this->faker->latitude(),
            'center_longitude'=> $this->faker->longitude(),
            'radius'          => 5, // km
            'polygon'         => null,
        ];
    }
}
