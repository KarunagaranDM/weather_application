<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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

            $history = $this->getSimulatedHistoricalData(
                $currentWeather['coord']['lat'] ?? null,
                $currentWeather['coord']['lon'] ?? null
            );

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

        if (!is_numeric($lat) || !is_numeric($lon)) {
            return response()->json(['error' => 'Invalid coordinates provided'], 400);
        }

        try {
            $reverseGeocodeResponse = $client->get("http://api.openweathermap.org/geo/1.0/reverse", [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'limit' => 1,
                    'appid' => $apiKey
                ]
            ]);

            $locationData = json_decode($reverseGeocodeResponse->getBody(), true);

            if (empty($locationData)) {
                throw new \Exception('No location found for these coordinates');
            }

            $locationName = $locationData[0]['name'] ?? $locationData[0]['local_names']['en'] ?? "Unknown Location";

            $weatherResponse = $client->get("https://api.openweathermap.org/data/2.5/weather", [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'units' => 'metric',
                    'appid' => $apiKey
                ]
            ]);

            $weatherData = json_decode($weatherResponse->getBody(), true);

            return response()->json([
                'current' => $weatherData,
                'history' => $this->getSimulatedHistoricalData($lat, $lon),
                'location' => $locationName
            ]);
        } catch (\Exception $e) {
            Log::error("Weather by coordinates error: " . $e->getMessage());

            try {
                $weatherResponse = $client->get("https://api.openweathermap.org/data/2.5/weather", [
                    'query' => [
                        'lat' => $lat,
                        'lon' => $lon,
                        'units' => 'metric',
                        'appid' => $apiKey
                    ]
                ]);

                $weatherData = json_decode($weatherResponse->getBody(), true);

                return response()->json([
                    'current' => $weatherData,
                    'history' => $this->getSimulatedHistoricalData($lat, $lon),
                    'location' => $weatherData['name'] ?? "Current Location"
                ]);
            } catch (\Exception $fallbackError) {
                Log::error("Weather fallback failed: " . $fallbackError->getMessage());
                return response()->json([
                    'error' => 'Unable to fetch weather data for your location',
                    'details' => $fallbackError->getMessage()
                ], 500);
            }
        }
    }

    private function getHistoricalWeatherData($lat, $lon, $date)
    {
        $client = new Client();
        $apiKey = env('OPENWEATHER_API_KEY');

        $dt = Carbon::parse($date)->timestamp;

        try {
            $response = $client->get("https://api.openweathermap.org/data/3.0/onecall/day_summary", [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'date' => $date,
                    'units' => 'metric',
                    'appid' => $apiKey,
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return [
                'temp' => $data['temperature']['day'],
                'humidity' => $data['humidity'],
                'wind_speed' => $data['wind_speed'],
                'pressure' => $data['pressure'],
            ];
        } catch (\Exception $e) {
            Log::error("Historical Weather API Error: " . $e->getMessage());
            return null;
        }
    }

    private function getSimulatedHistoricalData($lat = null, $lon = null)
    {
        $history = [];
        $today = now();

        //default
        if (!$lat || !$lon) {
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

        for ($i = 1; $i <= 5; $i++) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $weatherData = $this->getHistoricalWeatherData($lat, $lon, $date);

            if ($weatherData) {
                $history[] = [
                    'date' => $date,
                    'temp' => $weatherData['temp'],
                    'humidity' => $weatherData['humidity'],
                    'wind_speed' => $weatherData['wind_speed'],
                    'pressure' => $weatherData['pressure'],
                ];
            } else {
                $history[] = [
                    'date' => $date,
                    'temp' => rand(15, 30),
                    'humidity' => rand(30, 90),
                    'wind_speed' => rand(0, 20),
                    'pressure' => rand(990, 1030),
                ];
            }
        }

        return $history;
    }
}
