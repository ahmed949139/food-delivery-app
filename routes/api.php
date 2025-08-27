<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\DeliveryZoneController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryAssignmentController;
use App\Http\Controllers\NotificationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Restaurants
    Route::middleware('role:restaurant_owner')->group(function () {
        Route::get('/restaurants', [RestaurantController::class, 'index']);
        Route::post('/restaurants', [RestaurantController::class, 'store']);

        // Delivery Zones
        Route::post('/restaurant/{id}/zones', [DeliveryZoneController::class, 'store']);
    });

    // Orders
    Route::middleware('role:customer')->group(function () {
        Route::post('/order', [OrderController::class, 'store']);
    });

    // Delivery Assignments
    Route::middleware('role:delivery_men')->group(function () {
        Route::get('/assignments', [DeliveryAssignmentController::class, 'index']);
        Route::post('/assignment/{id}/respond', [DeliveryAssignmentController::class, 'respond']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notification/{id}/read', [NotificationController::class, 'markAsRead']);
    });
});