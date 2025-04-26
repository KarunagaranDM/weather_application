<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/', [WeatherController::class, 'dashboard'])->name('dashboard');
Route::get('/weather-by-coords', [WeatherController::class, 'getByCoordinates'])->name('weather.by-coords');
