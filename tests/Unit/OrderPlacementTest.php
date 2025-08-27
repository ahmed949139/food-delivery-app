<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\DeliveryZone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function order_is_valid_if_inside_polygon_zone()
    {
        $restaurant = Restaurant::factory()->create();

        DeliveryZone::factory()->create([
            'restaurant_id' => $restaurant->id,
            'type' => 'polygon',
            'polygon' => [
                ['lat' => 23.810, 'lng' => 90.410],
                ['lat' => 23.815, 'lng' => 90.410],
                ['lat' => 23.815, 'lng' => 90.420],
                ['lat' => 23.810, 'lng' => 90.420],
            ],
        ]);

        $order = Order::factory()->create([
            'restaurant_id' => $restaurant->id,
            'delivery_latitude' => 23.812,
            'delivery_longitude' => 90.415,
        ]);

        $this->assertTrue($restaurant->isWithinDeliveryZone(
            $order->delivery_latitude,
            $order->delivery_longitude
        ));
    }

    #[Test]
    public function order_is_invalid_if_outside_polygon_zone()
    {
        $restaurant = Restaurant::factory()->create();

        DeliveryZone::factory()->create([
            'restaurant_id' => $restaurant->id,
            'type' => 'polygon',
            'polygon' => [
                ['lat' => 23.810, 'lng' => 90.410],
                ['lat' => 23.815, 'lng' => 90.410],
                ['lat' => 23.815, 'lng' => 90.420],
                ['lat' => 23.810, 'lng' => 90.420],
            ],
        ]);

        $order = Order::factory()->create([
            'restaurant_id' => $restaurant->id,
            'delivery_latitude' => 23.820,
            'delivery_longitude' => 90.430,
        ]);

        $this->assertFalse($restaurant->isWithinDeliveryZone(
            $order->delivery_latitude,
            $order->delivery_longitude
        ));
    }    

    #[Test]
    public function order_is_valid_if_inside_radius_zone()
    {
        $restaurant = Restaurant::factory()->create();

        DeliveryZone::factory()->create([
            'restaurant_id' => $restaurant->id,
            'type' => 'radius',
            'radius' => 5, // km
            'center_latitude' => 23.8103,
            'center_longitude' => 90.4125,
        ]);

        $order = Order::factory()->create([
            'restaurant_id' => $restaurant->id,
            'delivery_latitude' => 23.8110,
            'delivery_longitude' => 90.4130,
        ]);

        $this->assertTrue($restaurant->isWithinDeliveryZone(
            $order->delivery_latitude,
            $order->delivery_longitude
        ));
    }

    #[Test]
    public function order_is_invalid_if_outside_radius_zone()
    {
        $restaurant = Restaurant::factory()->create();

        DeliveryZone::factory()->create([
            'restaurant_id' => $restaurant->id,
            'type' => 'radius',
            'radius' => 1, // km
            'center_latitude' => 23.8103,
            'center_longitude' => 90.4125,
        ]);

        $order = Order::factory()->create([
            'restaurant_id' => $restaurant->id,
            'delivery_latitude' => 24.0000,
            'delivery_longitude' => 90.5000,
        ]);

        $this->assertFalse($restaurant->isWithinDeliveryZone(
            $order->delivery_latitude,
            $order->delivery_longitude
        ));
    }
}
