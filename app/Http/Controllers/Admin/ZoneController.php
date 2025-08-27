<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryZone;
use App\Models\Restaurant;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        return view('admin.zones', compact('restaurants'));
    }

    public function list()
    {
        $zones = DeliveryZone::with('restaurant')->get()->map(function ($z) {
            return [
                'id' => $z->id,
                'restaurant_id' => $z->restaurant_id,
                'restaurant_name' => optional($z->restaurant)->name,
                'type' => $z->type,
                'polygon' => $z->polygon,
                'center_latitude' => $z->center_latitude,
                'center_longitude' => $z->center_longitude,
                'radius' => $z->radius, // km
            ];
        });

        return response()->json($zones);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'restaurant_id'    => ['required', Rule::exists('restaurants', 'id')],
            'type'             => ['required', Rule::in(['polygon', 'radius'])],
            'polygon'          => ['nullable', 'array'],
            'polygon.*'        => ['array'],
            'center_latitude'  => 'nullable|numeric',
            'center_longitude' => 'nullable|numeric',
            'radius'           => 'nullable|numeric',
        ]);

        if ($data['type'] === 'polygon' && empty($data['polygon'])) {
            return response()->json(['message' => 'Polygon is required for polygon type'], 422);
        }

        if ($data['type'] === 'radius' && (empty($data['center_latitude']) || empty($data['center_longitude']) || empty($data['radius']))) {
            return response()->json(['message' => 'center and radius are required for radius type'], 422);
        }

        $zone = DeliveryZone::create([
            'restaurant_id'   => $data['restaurant_id'],
            'type'            => $data['type'],
            'polygon'         => $data['type'] === 'polygon' ? $data['polygon'] : null,
            'center_latitude' => $data['type'] === 'radius' ? $data['center_latitude'] : null,
            'center_longitude'=> $data['type'] === 'radius' ? $data['center_longitude'] : null,
            'radius'          => $data['type'] === 'radius' ? $data['radius'] : null,
        ]);

        return response()->json(['message' => 'Zone created', 'zone' => $zone], 201);
    }
}
