<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalWeatherService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('cargotrace.weather_api_url', 'https://api.open-meteo.com/v1/forecast');
    }

    /**
     * Fetch current weather conditions for given coordinates.
     * Uses Open-Meteo with fallback to synthetic safe response on failure.
     */
    public function getWeather(float $latitude, float $longitude, ?string $locationName = null): array
    {
        try {
            $response = Http::timeout(4)
                ->get($this->baseUrl, [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                    'timezone' => 'auto',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $current = $data['current'] ?? [];

                $weatherCode = $current['weather_code'] ?? 0;
                $interpretation = $this->interpretWeatherCode($weatherCode);

                return [
                    'status' => 'success',
                    'source' => 'Open-Meteo API',
                    'location' => $locationName ?? "{$latitude}, {$longitude}",
                    'temperature' => $current['temperature_2m'] ?? 28,
                    'temperature_unit' => '°C',
                    'humidity' => $current['relative_humidity_2m'] ?? 75,
                    'humidity_unit' => '%',
                    'wind_speed' => $current['wind_speed_10m'] ?? 12,
                    'wind_speed_unit' => 'km/h',
                    'condition' => $interpretation['condition'],
                    'icon' => $interpretation['icon'],
                    'is_adverse' => $interpretation['is_adverse'],
                    'timestamp' => $current['time'] ?? now()->toIso8601String(),
                ];
            }

            Log::warning('Weather API returned non-200', ['status' => $response->status()]);
        } catch (Throwable $e) {
            Log::info('Weather API fallback invoked: '.$e->getMessage());
        }

        // Graceful fallback (Slide 9: Tangani timeout, error response, dan data kosong)
        return $this->getFallbackWeather($latitude, $longitude, $locationName);
    }

    protected function interpretWeatherCode(int $code): array
    {
        return match (true) {
            $code === 0 => ['condition' => 'Clear Sky', 'icon' => '☀️', 'is_adverse' => false],
            in_array($code, [1, 2, 3]) => ['condition' => 'Mainly Clear to Overcast', 'icon' => '⛅', 'is_adverse' => false],
            in_array($code, [45, 48]) => ['condition' => 'Fog / Low Visibility', 'icon' => '🌫️', 'is_adverse' => true],
            in_array($code, [51, 53, 55, 61, 63, 65]) => ['condition' => 'Moderate Rain', 'icon' => '🌧️', 'is_adverse' => false],
            in_array($code, [80, 81, 82]) => ['condition' => 'Heavy Rain Showers', 'icon' => '🌧️', 'is_adverse' => true],
            in_array($code, [95, 96, 99]) => ['condition' => 'Thunderstorm / Gale Advisory', 'icon' => '⛈️', 'is_adverse' => true],
            default => ['condition' => 'Fair Logistics Conditions', 'icon' => '🌤️', 'is_adverse' => false],
        };
    }

    protected function getFallbackWeather(float $latitude, float $longitude, ?string $locationName): array
    {
        return [
            'status' => 'cached_fallback',
            'source' => 'Logistics Telemetry Safe Fallback',
            'location' => $locationName ?? "{$latitude}, {$longitude}",
            'temperature' => 28.5,
            'temperature_unit' => '°C',
            'humidity' => 70,
            'humidity_unit' => '%',
            'wind_speed' => 14.2,
            'wind_speed_unit' => 'km/h',
            'condition' => 'Normal Maritime Transit Conditions',
            'icon' => '🚢',
            'is_adverse' => false,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
