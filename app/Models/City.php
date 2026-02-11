<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function measurements()
    {
        return $this->hasMany(WeatherMeasurement::class);
    }
}
