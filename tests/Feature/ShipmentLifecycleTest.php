<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\TrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShipmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;

    protected Carrier $carrier;

    protected Location $origin;

    protected Location $destination;

    protected TrackingService $trackingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->carrier = Carrier::create([
            'code' => 'MSK',
            'name' => 'Maersk',
            'service_type' => 'SEA_FREIGHT',
        ]);

        $this->origin = Location::create([
            'code' => 'IDJKT',
            'name' => 'Tanjung Priok',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.1,
            'longitude' => 106.8,
        ]);

        $this->destination = Location::create([
            'code' => 'NLRTM',
            'name' => 'Rotterdam',
            'city' => 'Rotterdam',
            'country' => 'Netherlands',
            'latitude' => 51.9,
            'longitude' => 4.4,
        ]);

        $this->trackingService = app(TrackingService::class);
    }

    public function test_valid_shipment_lifecycle_progression(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-001',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'GENERAL_CARGO',
            'total_weight_kg' => 5000,
            'expected_departure' => now()->addDay(),
            'expected_arrival' => now()->addDays(10),
            'status' => Shipment::STATUS_CREATED,
        ]);

        // Transition 1: CREATED -> BOOKED
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'BOOKING_CONFIRMED',
            'status' => Shipment::STATUS_BOOKED,
            'location_name' => 'Port of Tanjung Priok',
            'event_time' => now()->addHours(1),
            'description' => 'Space secured on vessel.',
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_BOOKED, $shipment->fresh()->status);

        // Transition 2: BOOKED -> PICKED_UP
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'PICKED_UP',
            'status' => Shipment::STATUS_PICKED_UP,
            'location_name' => 'Port of Tanjung Priok',
            'event_time' => now()->addHours(2),
            'description' => 'Container gate in.',
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_PICKED_UP, $shipment->fresh()->status);

        // Transition 3: PICKED_UP -> IN_TRANSIT
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'DEPARTED',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Java Sea',
            'event_time' => now()->addHours(3),
            'description' => 'Vessel departed berth.',
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->fresh()->status);

        // Transition 4: IN_TRANSIT -> DELIVERED
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'DELIVERED',
            'status' => Shipment::STATUS_DELIVERED,
            'location_name' => 'Port of Rotterdam',
            'event_time' => now()->addHours(4),
            'description' => 'Delivered to consignee.',
        ], $this->operator);

        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->fresh()->status);
        $this->assertTrue($shipment->fresh()->isTerminal());
    }

    public function test_illegal_status_transition_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        $shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-002',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => Shipment::STATUS_CREATED,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(5),
        ]);

        // Trying to skip directly from CREATED to DELIVERED is illegal (Slide 6)
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'DELIVERED',
            'status' => Shipment::STATUS_DELIVERED,
            'location_name' => 'Port of Rotterdam',
            'event_time' => now(),
            'description' => 'Direct delivery attempt.',
        ], $this->operator);
    }

    public function test_terminal_status_cannot_be_reverted(): void
    {
        $this->expectException(ValidationException::class);

        $shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-003',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => Shipment::STATUS_DELIVERED,
            'expected_departure' => now()->subDays(5),
            'expected_arrival' => now()->subDay(),
        ]);

        // Attempting to revert from terminal status DELIVERED back to IN_TRANSIT (Slide 6)
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'IN_TRANSIT',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Port of Rotterdam',
            'event_time' => now(),
            'description' => 'Attempting illegal backward status reversion.',
        ], $this->operator);
    }

    public function test_api_rejects_reverting_delivered_shipment_to_in_transit_and_preserves_append_only_events(): void
    {
        Sanctum::actingAs($this->operator);

        $shipment = Shipment::create([
            'shipment_number' => 'CT-TERM-API-001',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => Shipment::STATUS_DELIVERED,
            'expected_departure' => now()->subDays(10),
            'expected_arrival' => now()->subDay(),
        ]);

        $response = $this->postJson("/api/shipments/{$shipment->id}/tracking", [
            'event_type' => 'IN_TRANSIT',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Antwerp Automotive Terminal',
            'event_time' => now()->toDateTimeString(),
            'description' => 'Illegal backward transition via API.',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors('status');

        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->fresh()->status);
        $this->assertEquals(0, $shipment->trackingEvents()->count());
    }

    public function test_tracking_events_are_append_only_chronological_and_audited(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'CT-APPEND-001',
            'customer_id' => $this->operator->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'status' => Shipment::STATUS_CREATED,
            'expected_departure' => now()->addDay(),
            'expected_arrival' => now()->addDays(12),
        ]);

        $t1 = now()->addHours(2);
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'BOOKED',
            'status' => Shipment::STATUS_BOOKED,
            'location_name' => 'Jakarta / Tanjung Priok Car Terminal',
            'event_time' => $t1,
            'description' => 'Ro-Ro booking confirmed.',
        ], $this->operator);

        $this->assertDatabaseHas(AuditLog::class, [
            'action' => 'STATUS_TRANSITION',
            'entity_type' => 'Shipment',
            'entity_id' => $shipment->id,
        ]);

        // Reject backdated event preceding latest checkpoint
        $this->expectException(ValidationException::class);
        $this->trackingService->addEvent($shipment, [
            'event_type' => 'PICKED_UP',
            'status' => Shipment::STATUS_PICKED_UP,
            'location_name' => 'Jakarta / Tanjung Priok Car Terminal',
            'event_time' => $t1->copy()->subHour(),
            'description' => 'Out-of-order timestamp event.',
        ], $this->operator);
    }
}
