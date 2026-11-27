<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalGeocodingService
{
    protected string $baseUrl;

    protected string $userAgent;

    protected int $timeoutSeconds;

    public function __construct()
    {
        $this->baseUrl = (string) config('cargotrace.geocoding_api_url', 'https://nominatim.openstreetmap.org/search');
        $this->userAgent = (string) config('cargotrace.geocoding_user_agent', 'CargoTrace/1.0');
        $this->timeoutSeconds = (int) config('cargotrace.geocoding_timeout_seconds', 4);
    }

    /**
     * Resolve geographic coordinates (latitude/longitude) from a port, airport, or city name.
     * Handles HTTP errors, empty payloads, and network timeouts gracefully.
     *
     * @return array<string, mixed>
     */
    public function geocode(string $query): array
    {
        $trimmed = trim($query);
        if ($trimmed === '') {
            return $this->fallback($trimmed, 'Empty geocoding query provided.');
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'application/json',
            ])
                ->connectTimeout(3)
                ->timeout($this->timeoutSeconds)
                ->get($this->baseUrl, [
                    'q' => $trimmed,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1,
                ]);

            if ($response->successful()) {
                $payload = $response->json();

                if (! is_array($payload) || empty($payload[0]) || ! isset($payload[0]['lat'], $payload[0]['lon'])) {
                    Log::warning('Geocoding API returned empty or unparseable result', ['query' => $trimmed]);

                    return $this->fallback($trimmed, 'Location coordinates not found from geocoding provider.');
                }

                $first = $payload[0];
                $address = is_array($first['address'] ?? null) ? $first['address'] : [];

                return [
                    'status' => 'success',
                    'source' => 'OpenStreetMap Nominatim API',
                    'query' => $trimmed,
                    'display_name' => (string) ($first['display_name'] ?? $trimmed),
                    'latitude' => round((float) $first['lat'], 7),
                    'longitude' => round((float) $first['lon'], 7),
                    'city' => $address['city'] ?? $address['town'] ?? $address['state'] ?? null,
                    'country' => $address['country'] ?? null,
                    'country_code' => isset($address['country_code']) ? strtoupper((string) $address['country_code']) : null,
                ];
            }

            Log::warning('Geocoding API returned non-200 status', [
                'query' => $trimmed,
                'status' => $response->status(),
            ]);

            return $this->fallback($trimmed, "Geocoding provider responded with HTTP {$response->status()}.");
        } catch (Throwable $e) {
            Log::info('Geocoding API fallback invoked: '.$e->getMessage(), ['query' => $trimmed]);

            return $this->fallback($trimmed, 'Geocoding service temporarily unavailable (timeout or network error).');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function fallback(string $query, string $message): array
    {
        return [
            'status' => 'fallback',
            'source' => 'CargoTrace Safe Fallback',
            'query' => $query,
            'display_name' => $query,
            'latitude' => null,
            'longitude' => null,
            'city' => null,
            'country' => null,
            'country_code' => null,
            'message' => $message,
        ];
    }
}
