<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\WeatherMeasurementController;
use App\Http\Controllers\api\CityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Weather measurement routes
Route::get('/weathers', [WeatherMeasurementController::class, 'index']);
Route::get('/weather/{cityId}', [WeatherMeasurementController::class, 'indexByCity']);

// City routes
Route::get('/cities', [CityController::class, 'index']);
Route::post('/storecity', [CityController::class, 'store']);

