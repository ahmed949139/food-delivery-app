<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\DeliveryMen;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AssignmentNotification;
use App\Services\GeoService;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
        ]);

        $restaurant = Restaurant::findOrFail($request->restaurant_id);

        // Zone validation
        if (!$restaurant->isWithinDeliveryZone($request->delivery_latitude, $request->delivery_longitude)) {
            return response()->json([
                'error' => 'Delivery address is outside of the delivery zone'
            ], 422);
        }

        // Find nearest available delivery men
        $deliveryMen = DeliveryMen::where('is_available', true)
            ->get()
            ->sortBy(function ($man) use ($request) {
                $geo = new GeoService();
                return $geo->haversineDistance(
                    $request->delivery_latitude,
                    $request->delivery_longitude,
                    $man->latitude,
                    $man->longitude
                );
            })
            ->first();

        if (!$deliveryMen) {
            return response()->json([
                'error' => 'Delivery men are not available at the moment!'
            ], 422);
        }

        $order = Order::create([
            'customer_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
            'delivery_latitude' => $request->delivery_latitude,
            'delivery_longitude' => $request->delivery_longitude,
            'status' => 'pending',
        ]);

        $assignment = OrderAssignment::create([
            'order_id' => $order->id,
            'delivery_men_id' => $deliveryMen->user_id,
        ]);

        // Notify delivery men
        $deliveryMen->notify(new AssignmentNotification($order));

        return response()->json($order->load('assignment'), 201);
    }
}
