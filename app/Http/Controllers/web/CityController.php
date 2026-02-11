<?php

namespace App\Http\Controllers\web;

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
        $cities = City::with('weatherMeasurements')->get();
        return view('cities.index', compact('cities'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'country' => 'nullable|string',
        ]);


        $coords = $this->geocoding->getCoordinates($request->name);

        if (!$coords) {
 
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'A megadott város nem található.']);
        }

        City::create([
            'name' => $request->name,
            'country' => $request->country,
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],
        ]);

        return redirect()->back()->with('success', 'City added successfully!');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->back()->with('success', 'City deleted successfully!');
    }
}
