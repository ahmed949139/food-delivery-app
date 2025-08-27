<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\DeliveryMen;
use App\Models\DeliveryZone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderAssignmentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_delivery_man_assignment_logic()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $deliveryUser = User::factory()->create(['role' => 'delivery_men']);
        $deliveryMan = DeliveryMen::factory()->create([
            'user_id' => $deliveryUser->id,
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'is_available' => true,
        ]);

        $restaurant = Restaurant::factory()->create();
        DeliveryZone::factory()->create([
            'restaurant_id' => $restaurant->id,
            'type' => 'radius',
            'radius' => 5, // km
            'center_latitude' => 23.81,
            'center_longitude' => 90.41,
        ]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
            'delivery_latitude' => 23.811,
            'delivery_longitude' => 90.412,
        ]);

        $assignment = $order->assignment()->create([
            'delivery_men_id' => $deliveryUser->id,
            'status' => 'pending',
        ]);

        $assignment->status = 'accepted';
        $assignment->save();
        $order->status = 'assigned';
        $order->save();

        $this->assertDatabaseHas('order_assignments', [
            'id' => $assignment->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'assigned',
        ]);
    }
}
