<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\EtaService;
use App\Services\ExceptionService;
use App\Services\ExternalGeocodingService;
use App\Services\ExternalWeatherService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EtaDelayEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected EtaService $etaService;

    protected ExceptionService $exceptionService;

    protected Shipment $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->etaService = app(EtaService::class);
        $this->exceptionService = app(ExceptionService::class);

        $user = User::factory()->create();
        $carrier = Carrier::create(['code' => 'DHL', 'name' => 'DHL', 'service_type' => 'AIR_FREIGHT']);
        $origin = Location::create(['code' => 'CNSHA', 'name' => 'Shanghai', 'city' => 'Shanghai', 'country' => 'China', 'latitude' => 31.2, 'longitude' => 121.4]);
        $dest = Location::create(['code' => 'USLAX', 'name' => 'Los Angeles', 'city' => 'LA', 'country' => 'USA', 'latitude' => 33.7, 'longitude' => -118.2]);

        $this->shipment = Shipment::create([
            'shipment_number' => 'CT-ETA-TEST',
            'customer_id' => $user->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(5),
            'estimated_arrival' => now()->addDays(5),
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);
    }

    public function test_nominal_schedule_is_not_delayed(): void
    {
        $eval = $this->etaService->evaluateDelay($this->shipment);

        $this->assertFalse($eval['is_delayed']);
        $this->assertEquals(0, $eval['delay_hours']);
        $this->assertEquals('NONE', $eval['delay_severity']);
    }

    public function test_delay_severity_thresholds_match_rubric(): void
    {
        // 1. Test LOW severity (3 hours delay <= 6h)
        $this->shipment->estimated_arrival = Carbon::parse($this->shipment->expected_arrival)->copy()->addHours(3);
        $evalLow = $this->etaService->evaluateDelay($this->shipment);
        $this->assertTrue($evalLow['is_delayed']);
        $this->assertEquals('LOW', $evalLow['delay_severity']);

        // 2. Test MEDIUM severity (18 hours delay: > 6h and <= 24h)
        $this->shipment->estimated_arrival = Carbon::parse($this->shipment->expected_arrival)->copy()->addHours(18);
        $evalMed = $this->etaService->evaluateDelay($this->shipment);
        $this->assertTrue($evalMed['is_delayed']);
        $this->assertEquals('MEDIUM', $evalMed['delay_severity']);

        // 3. Test HIGH severity (36 hours delay: > 24h)
        $this->shipment->estimated_arrival = Carbon::parse($this->shipment->expected_arrival)->copy()->addHours(36);
        $evalHigh = $this->etaService->evaluateDelay($this->shipment);
        $this->assertTrue($evalHigh['is_delayed']);
        $this->assertEquals('HIGH', $evalHigh['delay_severity']);
    }

    public function test_exception_impact_updates_shipment_delay(): void
    {
        // Flagging an exception with +20 hours impact (Slide 8)
        $exception = $this->exceptionService->createException($this->shipment, [
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => ShipmentException::SEVERITY_MEDIUM,
            'description' => 'Typhoon alert near Luzon Strait.',
            'delay_impact_hours' => 20,
        ]);

        $this->shipment->refresh();
        $this->assertTrue($this->shipment->is_delayed);
        $this->assertEquals(20, $this->shipment->delay_hours);
        $this->assertEquals('MEDIUM', $this->shipment->delay_severity);
    }

    public function test_configurable_sla_threshold_and_exception_lifecycle(): void
    {
        config(['cargotrace.delay_threshold_hours' => 12]);

        $operator = User::factory()->create(['role' => 'operator']);

        $exception = $this->exceptionService->createException($this->shipment, [
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'description' => 'Verifikasi dokumen Homologasi EU & sertifikat baterai lithium (Dangerous Goods)',
            'delay_impact_hours' => 16,
        ], $operator);

        $this->assertEquals(ShipmentException::STATUS_OPEN, $exception->status);
        $this->assertNotNull($exception->detected_at);
        $this->assertNull($exception->resolved_at);

        $eval = $this->etaService->evaluateDelay($this->shipment->fresh());
        $this->assertEquals(12, $eval['sla_threshold_hours']);
        $this->assertTrue($eval['exceeds_sla_threshold']);
        $this->assertEquals('MEDIUM', $eval['delay_severity']);

        $investigating = $this->exceptionService->investigateException($exception, 'Submitting UN3480 test summary.', $operator);
        $this->assertEquals(ShipmentException::STATUS_INVESTIGATING, $investigating->status);

        $resolved = $this->exceptionService->resolveException($investigating, [
            'resolution_notes' => 'EU Homologation & UN3480 certificates cleared by customs.',
        ], $operator);

        $this->assertEquals(ShipmentException::STATUS_RESOLVED, $resolved->status);
        $this->assertNotNull($resolved->resolved_at);
        $this->assertEquals($operator->id, $resolved->resolved_by);
        $this->assertGreaterThanOrEqual(3, Notification::where('user_id', $this->shipment->customer_id)->count());
        $this->assertDatabaseHas(AuditLog::class, [
            'action' => 'RESOLVE_EXCEPTION',
            'entity_type' => 'ShipmentException',
            'entity_id' => $exception->id,
        ]);
    }

    public function test_external_weather_service_handles_timeout_errors_and_empty_payload_gracefully(): void
    {
        $weatherService = app(ExternalWeatherService::class);

        Http::fakeSequence()
            ->pushStatus(500)
            ->push([], 200)
            ->push([
                'current' => [
                    'temperature_2m' => 14.5,
                    'relative_humidity_2m' => 72,
                    'weather_code' => 95,
                    'wind_speed_10m' => 38.2,
                    'time' => '2026-11-25T12:00',
                ],
            ], 200)
            ->pushFailedConnection('Connection timed out');

        // 1. HTTP 500 error -> graceful fallback
        $errorResult = $weatherService->getWeather(-6.0967, 106.8858, 'Jakarta / Tanjung Priok');
        $this->assertEquals('cached_fallback', $errorResult['status']);
        $this->assertEquals('Jakarta / Tanjung Priok', $errorResult['location']);

        // 2. Empty JSON payload -> graceful fallback
        $emptyResult = $weatherService->getWeather(50.0379, 8.5621, 'Frankfurt CargoCity');
        $this->assertEquals('cached_fallback', $emptyResult['status']);

        // 3. Valid Open-Meteo payload -> live telemetry
        $liveResult = $weatherService->getWeather(51.9054, 4.4666, 'Rotterdam Ro-Ro & EV Hub');
        $this->assertEquals('success', $liveResult['status']);
        $this->assertEquals(14.5, $liveResult['temperature']);
        $this->assertTrue($liveResult['is_adverse']);

        // 4. Connection timeout -> graceful fallback
        $timeoutResult = $weatherService->getWeather(51.9054, 4.4666, 'Rotterdam Ro-Ro & EV Hub');
        $this->assertEquals('cached_fallback', $timeoutResult['status']);
        $this->assertEquals('Rotterdam Ro-Ro & EV Hub', $timeoutResult['location']);
    }

    public function test_external_geocoding_service_handles_success_errors_empty_payload_and_timeout(): void
    {
        $geocodingService = app(ExternalGeocodingService::class);

        Http::fakeSequence()
            ->push([
                [
                    'lat' => '51.9054440',
                    'lon' => '4.4666370',
                    'display_name' => 'Rotterdam, South Holland, Netherlands',
                    'address' => [
                        'city' => 'Rotterdam',
                        'country' => 'Netherlands',
                        'country_code' => 'nl',
                    ],
                ],
            ], 200)
            ->pushStatus(503)
            ->push([], 200)
            ->pushFailedConnection('Geocoding request timed out');

        // 1. Valid Nominatim response
        $ok = $geocodingService->geocode('Rotterdam, Netherlands');
        $this->assertEquals('success', $ok['status']);
        $this->assertEquals(51.905444, $ok['latitude']);
        $this->assertEquals(4.466637, $ok['longitude']);
        $this->assertEquals('NL', $ok['country_code']);

        // 2. HTTP 503 error -> graceful fallback
        $error = $geocodingService->geocode('Antwerp, Belgium');
        $this->assertEquals('fallback', $error['status']);
        $this->assertNull($error['latitude']);

        // 3. Empty result -> graceful fallback
        $empty = $geocodingService->geocode('Nonexistent Port XYZ');
        $this->assertEquals('fallback', $empty['status']);
        $this->assertNull($empty['longitude']);

        // 4. Connection timeout -> graceful fallback
        $timeout = $geocodingService->geocode('Frankfurt, Germany');
        $this->assertEquals('fallback', $timeout['status']);
        $this->assertStringContainsString('unavailable', strtolower($timeout['message']));
    }
}
