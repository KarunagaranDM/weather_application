<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function dashboard(Request $request)
    {
        $client = new Client();
        $apiKey = env('OPENWEATHER_API_KEY');
        $location = $request->input('location', 'Chennai');

        try {
            $currentResponse = $client->get("https://api.openweathermap.org/data/2.5/weather?q={$location}&units=metric&appid={$apiKey}");
            $currentWeather = json_decode($currentResponse->getBody(), true);

            $history = $this->getSimulatedHistoricalData();

            return view('dashboard', [
                'current' => $currentWeather,
                'history' => $history,
                'location' => $location
            ]);
        } catch (\Exception $e) {
            Log::error("Weather API error: " . $e->getMessage());

            $currentResponse = $client->get("https://api.openweathermap.org/data/2.5/weather?q=Chennai&units=metric&appid={$apiKey}");
            $currentWeather = json_decode($currentResponse->getBody(), true);

            return view('dashboard', [
                'current' => $currentWeather,
                'history' => $this->getSimulatedHistoricalData(),
                'location' => 'Chennai',
                'error' => 'The requested location was not found. Showing default weather for Chennai.'
            ]);
        }
    }

    public function getByCoordinates(Request $request)
    {
        $client = new Client();
        $apiKey = env('OPENWEATHER_API_KEY');

        $lat = $request->input('lat');
        $lon = $request->input('lon');

        try {
            $currentResponse = $client->get("https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units=metric&appid={$apiKey}");
            $currentWeather = json_decode($currentResponse->getBody(), true);

            return response()->json([
                'current' => $currentWeather,
                'history' => $this->getSimulatedHistoricalData(),
                'location' => $currentWeather['name']
            ]);
        } catch (\Exception $e) {
            Log::error("Weather API error: " . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch weather data'], 500);
        }
    }

    private function getSimulatedHistoricalData()
    {
        $history = [];
        $today = now();

        for ($i = 1; $i <= 5; $i++) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $history[] = [
                'date' => $date,
                'temp' => rand(15, 30),
                'humidity' => rand(30, 90),
                'wind_speed' => rand(0, 20),
                'pressure' => rand(990, 1030),
            ];
        }

        return $history;
    }
}
