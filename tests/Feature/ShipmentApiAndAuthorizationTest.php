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
}
