<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\CityController;
use App\Http\Controllers\web\DashboardController;

Route::get('/', [CityController::class, 'index']);
Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');


