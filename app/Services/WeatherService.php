<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public static function getCurrentTemperature(array $coordinates)
    {
        $lat = $coordinates['latitude'];
        $lon = $coordinates['longitude'];

        $response = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $lat,
            'longitude' => $lon,
            'current_weather' => true
        ]);

        if ($response->successful() && isset($response['current_weather']['temperature'])) {
            return $response['current_weather']['temperature'];
        }

        return null;
    }
}
