<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\CityController;
use App\Http\Controllers\web\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
Route::resource('cities', CityController::class)->only(['index','store','destroy']);



