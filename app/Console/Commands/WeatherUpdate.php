<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use App\Models\WeatherMeasurement;
use App\Services\WeatherService;
use App\Services\GeocodingService;

class WeatherUpdate extends Command
{
    protected $signature = 'weather:update';
    protected $description = 'Update weather for all cities';

    public function handle()
    {
        $cities = City::all();

        foreach ($cities as $city) {
  
            if (!$city->latitude || !$city->longitude) {
                $coords = GeocodingService::getCoordinates($city->name);
                if ($coords) {
                    $city->update($coords);
                } else {
                    $this->warn("Cannot find coordinates: {$city->name}");
                    continue;
                }
            }

            $temp = WeatherService::getCurrentTemperature([
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ]);

            if ($temp !== null) {
                WeatherMeasurement::create([
                    'city_id' => $city->id,
                    'temperature' => $temp,
                ]);
                $this->info("Updated: {$city->name} - {$temp}°C");
            } else {
                $this->warn("Failed to get temperature: {$city->name}");
            }
        }

        $this->info('All measurements updated!');
    }
}
