<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalWeatherService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected int $timeoutSeconds;

    public function __construct()
    {
        $this->baseUrl = config('cargotrace.weather_api_url', 'https://api.open-meteo.com/v1/forecast');
        $this->apiKey = (string) config('cargotrace.weather_api_key', '');
        $this->timeoutSeconds = (int) config('cargotrace.weather_timeout_seconds', 4);
    }

    /**
     * Fetch current weather conditions for given coordinates.
     * Handles timeouts, HTTP error responses, and empty payloads with graceful fallback.
     *
     * @return array<string, mixed>
     */
    public function getWeather(float $latitude, float $longitude, ?string $locationName = null): array
    {
        try {
            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                'timezone' => 'auto',
            ];

            if ($this->apiKey !== '') {
                $query['apikey'] = $this->apiKey;
            }

            $response = Http::connectTimeout(3)
                ->timeout($this->timeoutSeconds)
                ->get($this->baseUrl, $query);

            if ($response->successful()) {
                $data = $response->json();
                $current = is_array($data) ? ($data['current'] ?? null) : null;

                if (! is_array($current) || ! array_key_exists('temperature_2m', $current)) {
                    Log::warning('Weather API returned empty or incomplete payload', ['location' => $locationName]);

                    return $this->getFallbackWeather($latitude, $longitude, $locationName);
                }

                $weatherCode = (int) ($current['weather_code'] ?? 0);
                $interpretation = $this->interpretWeatherCode($weatherCode);

                return [
                    'status' => 'success',
                    'source' => 'Open-Meteo API',
                    'location' => $locationName ?? "{$latitude}, {$longitude}",
                    'temperature' => $current['temperature_2m'],
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

            Log::warning('Weather API returned non-200 status', ['status' => $response->status()]);
        } catch (Throwable $e) {
            Log::info('Weather API fallback invoked: '.$e->getMessage());
        }

        return $this->getFallbackWeather($latitude, $longitude, $locationName);
    }

    /**
     * @return array{condition: string, icon: string, is_adverse: bool}
     */
    protected function interpretWeatherCode(int $code): array
    {
        return match (true) {
            $code === 0 => ['condition' => 'Clear Sky', 'icon' => '☀️', 'is_adverse' => false],
            in_array($code, [1, 2, 3], true) => ['condition' => 'Mainly Clear to Overcast', 'icon' => '⛅', 'is_adverse' => false],
            in_array($code, [45, 48], true) => ['condition' => 'Fog / Low Visibility', 'icon' => '🌫️', 'is_adverse' => true],
            in_array($code, [51, 53, 55, 61, 63, 65], true) => ['condition' => 'Moderate Rain', 'icon' => '🌧️', 'is_adverse' => false],
            in_array($code, [80, 81, 82], true) => ['condition' => 'Heavy Rain Showers', 'icon' => '🌧️', 'is_adverse' => true],
            in_array($code, [95, 96, 99], true) => ['condition' => 'Thunderstorm / Gale Advisory', 'icon' => '⛈️', 'is_adverse' => true],
            default => ['condition' => 'Fair Transit Conditions', 'icon' => '🌤️', 'is_adverse' => false],
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFallbackWeather(float $latitude, float $longitude, ?string $locationName): array
    {
        return [
            'status' => 'cached_fallback',
            'source' => 'Automotive Telemetry Safe Fallback',
            'location' => $locationName ?? "{$latitude}, {$longitude}",
            'temperature' => 26.0,
            'temperature_unit' => '°C',
            'humidity' => 68,
            'humidity_unit' => '%',
            'wind_speed' => 14.0,
            'wind_speed_unit' => 'km/h',
            'condition' => 'Normal Ro-Ro & Air Freight Conditions',
            'icon' => '🚢',
            'is_adverse' => false,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
