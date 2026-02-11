<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City; 
use Carbon\Carbon;
use App\Models\WeatherMeasurement;

class WeatherMeasurementController extends Controller
{
    public function index(){
        $measurements = WeatherMeasurement::with('city')->latest()->get();
        return response()->json($measurements);
    }

    public function indexByCity($cityId){
        $measurements = WeatherMeasurement::where('city_id', $cityId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($measurements->isEmpty()) {
            return response()->json(['message' => 'There are no measurements for this city.'], 404);
        }

        return response()->json($measurements);
    }

}
