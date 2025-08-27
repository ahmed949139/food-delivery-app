<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = Restaurant::where('owner_id', $request->user()->id)->get();
        return response()->json($restaurants);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $restaurant = Restaurant::create([
            'owner_id' => $request->user()->id,
            'name'     => $request->name,
            'address'  => $request->address,
        ]);

        return response()->json($restaurant, 201);
    }
}
