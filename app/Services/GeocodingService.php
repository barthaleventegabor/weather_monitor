<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public static function getCoordinates(string $cityName)
    {
        $response = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $cityName,
            'count' => 1,
        ]);

        if (isset($response['results'][0])) {
            return [
                'latitude' => $response['results'][0]['latitude'],
                'longitude' => $response['results'][0]['longitude'],
            ];
        }

        return null;
    }
}
