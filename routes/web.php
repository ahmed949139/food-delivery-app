<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ZoneController;

Route::get('/', function () {
    return view('welcome');
});

// Admin UI to draw zones using Leaflet.js
Route::get('/zones', [ZoneController::class, 'index']);
Route::post('/zones', [ZoneController::class, 'store']);
Route::get('/zones/list', [ZoneController::class, 'list']);
