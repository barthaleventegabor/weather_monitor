<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\WeatherMeasurementController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Weather measurement routes
Route::get('/weather/{city_id}', [WeatherMeasurementController::class, 'indexByCity']);



