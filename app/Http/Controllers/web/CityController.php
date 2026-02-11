<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Services\GeocodingService;

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
        return view('cities', compact('cities'));
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'country' => 'required|string', 
    ]);

    $coords = $this->geocoding->getCoordinates($request->name);

    if (!$coords) {
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['name' => 'Invalid city name, could not find coordinates.']);
    }

    $exists = City::where('name', $request->name)
                  ->where('country', $request->country)
                  ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['name' => 'This city and country combination already exists.']);
    }

    $city = City::create([
        'name' => $request->name,
        'country' => $request->country,
        'latitude' => $coords['latitude'],
        'longitude' => $coords['longitude'],
    ]);

    return redirect()->back()->with('success', 'City created successfully!');
}


    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->back()->with('success', 'City deleted successfully!');
    }
}
