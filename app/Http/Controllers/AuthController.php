<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DeliveryMen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,restaurant_owner,delivery_men,customer',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // If delivery men create profile
        if ($user->role === 'delivery_men') {
            DeliveryMen::create([
                'user_id'      => $user->id,
                'latitude'     => $request->latitude ?? null,
                'longitude'    => $request->longitude ?? null,
                'is_available' => true,
            ]);
        }

        $data = [];
        $data["token"] = $user->createToken('food-delivery')->plainTextToken;
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["message"] = "User registered successfully";

        return response()->json($data, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $data = [];
        $data["token"] = $user->createToken('food-delivery')->plainTextToken;
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["message"] = "User logged in successfully";

        return response()->json($data, 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out',
        ]);
    }    
}
