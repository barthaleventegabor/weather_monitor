<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;


class DashboardController extends Controller
{
    public function index()
    {
        $cities = City::with(['weatherMeasurements' => function($q){
            $q->latest()->limit(10);
        }])->get();

        $citySeries = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'country' => $city->country,
                'labels' => $city->weatherMeasurements
                    ->pluck('created_at')
                    ->map(fn ($dt) => $dt?->format('Y-m-d H:i'))
                    ->values(),
                'temps' => $city->weatherMeasurements
                    ->pluck('temperature')
                    ->map(fn ($t) => (float) $t)
                    ->values(),
            ];
        })->values();

        return view('dashboard', compact('cities', 'citySeries'));
    }
}
