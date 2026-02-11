<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeocodingService;
use App\Models\City;

class CityController extends Controller
{
    protected $geocoding;
    public function __construct(GeocodingService $geocoding)
    {
        $this->geocoding = $geocoding;
    }

    public function index()
    {
        $cities = City::all();
        return response()->json($cities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'country' => 'nullable|string',
        ]);

        $coords = $this->geocoding->getCoordinates($request->name);

        $city = City::create([
            'name' => $request->name,
            'country' => $request->country,
            'latitude' => $coords['latitude'] ?? null,
            'longitude' => $coords['longitude'] ?? null,
        ]);

        return response()->json([
            'message' => 'City created successfully',
            'city' => $city
        ], 201); 
    }
 
}
