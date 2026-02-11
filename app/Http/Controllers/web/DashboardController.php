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
            $q->latest()->limit(1);
        }])->get();

        return view('dashboard', compact('cities'));
    }
}
