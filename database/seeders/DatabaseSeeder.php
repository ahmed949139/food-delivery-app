<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\DeliveryZone;
use App\Models\DeliveryMen;
use App\Models\Order;
use App\Models\OrderAssignment;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Restaurant Owner
        $owner = User::factory()->create([
            'name' => 'Restaurant Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_owner',
        ]);

        $restaurant = Restaurant::factory()->create([
            'owner_id' => $owner->id,
            'name' => "Pizza Hut",
        ]);

        $zone = DeliveryZone::factory()->create([
            'restaurant_id' => $restaurant->id,
            'type' => 'radius',
            'radius' => 5,
            'center_latitude' => 23.8103,
            'center_longitude' => 90.4125,
        ]);

        // Delivery Men
        $deliveryMenUser = User::factory()->create([
            'name' => 'Delivery Man',
            'email' => 'delivery@example.com',
            'password' => bcrypt('password'),
            'role' => 'delivery_men',
        ]);

        $deliveryMen = DeliveryMen::factory()->create([
            'user_id' => $deliveryMenUser->id,
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'is_available' => true,
        ]);

        // Customer
        $customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $order = Order::factory()->create([
            'restaurant_id' => $restaurant->id,
            'customer_id' => $customer->id,
            'delivery_latitude' => 23.8120,
            'delivery_longitude' => 90.4130,
        ]);

        // Order Assignment
        OrderAssignment::factory()->create([
            'order_id' => $order->id,
            'delivery_men_id' => $deliveryMen->id,
            'status' => 'pending',
        ]);
    }
}
