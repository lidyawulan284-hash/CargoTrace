<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShipmentApiAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operator;

    protected User $customer1;

    protected User $customer2;

    protected Carrier $carrier;

    protected Location $origin;

    protected Location $destination;

    protected Shipment $shipment1;

    protected Shipment $shipment2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->customer1 = User::factory()->create(['role' => 'customer']);
        $this->customer2 = User::factory()->create(['role' => 'customer']);

        $this->carrier = Carrier::create(['code' => 'MSK', 'name' => 'Maersk', 'service_type' => 'SEA_FREIGHT']);
        $this->origin = Location::create(['code' => 'IDJKT', 'name' => 'Jakarta', 'city' => 'Jakarta', 'country' => 'Indonesia', 'latitude' => -6.1, 'longitude' => 106.8]);
        $this->destination = Location::create(['code' => 'NLRTM', 'name' => 'Rotterdam', 'city' => 'Rotterdam', 'country' => 'Netherlands', 'latitude' => 51.9, 'longitude' => 4.4]);

        $this->shipment1 = Shipment::create([
            'shipment_number' => 'CT-CUST1-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(10),
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $this->shipment2 = Shipment::create([
            'shipment_number' => 'CT-CUST2-002',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(12),
            'status' => Shipment::STATUS_BOOKED,
        ]);
    }

    public function test_customer_can_only_view_own_shipment(): void
    {
        Sanctum::actingAs($this->customer1);

        // Can view own shipment
        $response1 = $this->getJson("/api/shipments/{$this->shipment1->id}");
        $response1->assertStatus(200)
            ->assertJsonPath('data.shipment_number', 'CT-CUST1-001');

        // Cannot view other customer's shipment (Slide 13: 403 Forbidden)
        $response2 = $this->getJson("/api/shipments/{$this->shipment2->id}");
        $response2->assertStatus(403);
    }

    public function test_customer_cannot_create_shipment_via_api(): void
    {
        Sanctum::actingAs($this->customer1);

        $payload = [
            'shipment_number' => 'CT-ILLEGAL',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'GENERAL_CARGO',
            'total_weight_kg' => 1000,
            'total_volume_cbm' => 2,
            'expected_departure' => now()->toIso8601String(),
            'expected_arrival' => now()->addDays(5)->toIso8601String(),
        ];

        $response = $this->postJson('/api/shipments', $payload);
        $response->assertStatus(403);
    }

    public function test_operator_can_create_shipment_via_api(): void
    {
        Sanctum::actingAs($this->operator);

        $payload = [
            'shipment_number' => 'CT-OPS-VALID',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'ELECTRONICS',
            'total_weight_kg' => 2500,
            'total_volume_cbm' => 5,
            'expected_departure' => now()->toIso8601String(),
            'expected_arrival' => now()->addDays(7)->toIso8601String(),
        ];

        $response = $this->postJson('/api/shipments', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('data.shipment_number', 'CT-OPS-VALID');
    }

    public function test_public_tracking_endpoint_is_accessible_without_auth(): void
    {
        $response = $this->getJson("/api/public/track/{$this->shipment1->shipment_number}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.shipment_number', 'CT-CUST1-001');
    }

    public function test_tracking_api_returns_spatial_coordinates_for_origin_destination_and_current_fix(): void
    {
        Sanctum::actingAs($this->customer1);

        $response = $this->getJson("/api/shipments/{$this->shipment1->id}/tracking");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.shipment_number', 'CT-CUST1-001')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'shipment_number',
                    'current_status',
                    'is_delayed',
                    'delay_hours',
                    'delay_severity',
                    'origin' => ['name', 'city', 'latitude', 'longitude'],
                    'destination' => ['name', 'city', 'latitude', 'longitude'],
                    'current_coordinates',
                    'events',
                ],
            ]);
    }

    public function test_dashboard_telematics_coordinates_endpoint_returns_json(): void
    {
        $this->actingAs($this->operator);

        $response = $this->getJson("/shipments/{$this->shipment1->id}/coordinates");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.shipment_number', 'CT-CUST1-001')
            ->assertJsonPath('data.origin.name', 'Jakarta')
            ->assertJsonPath('data.destination.name', 'Rotterdam');
    }

    public function test_dashboard_telematics_coordinates_endpoint_rejects_unauthorized_customer(): void
    {
        $this->actingAs($this->customer2);

        // customer2 tries to access customer1's coordinates
        $response = $this->getJson("/shipments/{$this->shipment1->id}/coordinates");

        $response->assertStatus(403);
    }
}
