<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\EtaService;
use App\Services\ExceptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
