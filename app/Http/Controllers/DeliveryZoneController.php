<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryZone;
use App\Models\Restaurant;

class DeliveryZoneController extends Controller
{
    public function store(Request $request, $restaurantId)
    {
        $request->validate([
            'type'            => 'required|in:polygon,radius',
            'polygon'         => 'nullable|array',
            'center_latitude' => 'nullable|numeric',
            'center_longitude'=> 'nullable|numeric',
            'radius'          => 'nullable|numeric',
        ]);

        // Check Restaurant Id is valid
        $restaurant_exists = Restaurant::where('id', $restaurantId)->first();

        if (!$restaurant_exists) {
            return response()->json(['message' => 'Invalid Restaurant ID!'], 404);
        }

        $zone = DeliveryZone::create([
            'restaurant_id'   => $restaurantId,
            'type'            => $request->type,
            'polygon'         => $request->polygon ? json_encode($request->polygon) : null,
            'center_latitude' => $request->center_latitude,
            'center_longitude'=> $request->center_longitude,
            'radius'          => $request->radius,
        ]);

        return response()->json($zone, 201);
    }
}
