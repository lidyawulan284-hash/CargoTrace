<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

        $this->carrier = Carrier::create(['code' => 'WWL', 'name' => 'Wallenius Wilhelmsen', 'service_type' => 'RORO_SEA']);
        $this->origin = Location::create(['code' => 'IDTPP', 'name' => 'Jakarta / Tanjung Priok Car Terminal (IKT)', 'city' => 'Jakarta', 'country' => 'Indonesia', 'latitude' => -6.0967, 'longitude' => 106.8858]);
        $this->destination = Location::create(['code' => 'NLRTM', 'name' => 'Rotterdam Ro-Ro & EV Hub', 'city' => 'Rotterdam', 'country' => 'Netherlands', 'latitude' => 51.9054, 'longitude' => 4.4666]);

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
            ->assertJsonPath('data.origin.city', 'Jakarta')
            ->assertJsonPath('data.destination.city', 'Rotterdam');
    }

    public function test_dashboard_telematics_coordinates_endpoint_rejects_unauthorized_customer(): void
    {
        $this->actingAs($this->customer2);

        // customer2 tries to access customer1's coordinates
        $response = $this->getJson("/shipments/{$this->shipment1->id}/coordinates");

        $response->assertStatus(403);
    }

    public function test_customer_cannot_view_other_customers_shipment_via_web(): void
    {
        $this->actingAs($this->customer2);

        $this->get("/shipments/{$this->shipment1->id}")
            ->assertStatus(403);
    }

    public function test_operator_cannot_access_admin_only_resources(): void
    {
        Sanctum::actingAs($this->operator);

        $this->deleteJson("/api/shipments/{$this->shipment1->id}")
            ->assertStatus(403);

        $this->postJson('/api/carriers', [
            'code' => 'HOEGH',
            'name' => 'Hoegh Autoliners',
            'service_type' => 'RORO_SEA',
        ])->assertStatus(403);

        $this->actingAs($this->operator)
            ->get('/users')
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->get('/audit-logs')
            ->assertStatus(403);
    }

    public function test_operator_can_create_automotive_shipment_with_vehicle_items_and_search_by_vin(): void
    {
        Sanctum::actingAs($this->operator);

        $payload = [
            'shipment_number' => 'CT-2026-RORO-TEST99',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'CBU_EV',
            'total_weight_kg' => 4200,
            'total_volume_cbm' => 30,
            'expected_departure' => now()->toIso8601String(),
            'expected_arrival' => now()->addDays(14)->toIso8601String(),
            'items' => [
                [
                    'vehicle_model' => 'Hyundai Ioniq 5 CBU',
                    'vin_number' => 'KMHKN81EFPU999901',
                    'cargo_category' => 'CBU_EV',
                    'quantity' => 2,
                    'weight_kg' => 4200,
                    'declared_value' => 108000,
                ],
            ],
        ];

        $this->postJson('/api/shipments', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.items.0.vin_number', 'KMHKN81EFPU999901')
            ->assertJsonPath('data.items.0.cargo_category', 'CBU_EV');

        // Duplicate VIN must be rejected with 422
        $payload['shipment_number'] = 'CT-2026-RORO-DUPVIN';
        $this->postJson('/api/shipments', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('items.0.vin_number');

        // Search by VIN returns only the matching shipment
        $this->getJson('/api/shipments?search=KMHKN81EFPU999901')
            ->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.shipment_number', 'CT-2026-RORO-TEST99');
    }

    public function test_shipment_api_update_validates_schedule_and_admin_can_delete(): void
    {
        Sanctum::actingAs($this->operator);

        // Invalid update: expected_arrival before expected_departure -> 422
        $this->patchJson("/api/shipments/{$this->shipment1->id}", [
            'expected_departure' => now()->addDays(10)->toIso8601String(),
            'expected_arrival' => now()->addDays(5)->toIso8601String(),
        ])->assertStatus(422)
            ->assertJsonValidationErrors('expected_arrival');

        // Valid update by operator -> 200
        $this->patchJson("/api/shipments/{$this->shipment1->id}", [
            'notes' => 'Updated EU homologation dossier attached.',
        ])->assertStatus(200)
            ->assertJsonPath('data.notes', 'Updated EU homologation dossier attached.');

        // Admin can soft-delete shipment -> 200
        Sanctum::actingAs($this->admin);
        $this->deleteJson("/api/shipments/{$this->shipment2->id}")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('shipments', ['id' => $this->shipment2->id]);
    }

    public function test_exception_api_lifecycle_and_authorization(): void
    {
        // Customer cannot flag exceptions -> 403
        Sanctum::actingAs($this->customer1);
        $this->postJson("/api/shipments/{$this->shipment1->id}/exceptions", [
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => 'HIGH',
            'description' => 'Unauthorized exception creation.',
            'delay_impact_hours' => 12,
        ])->assertStatus(403);

        // Operator flags exception -> 201
        Sanctum::actingAs($this->operator);
        $created = $this->postJson("/api/shipments/{$this->shipment1->id}/exceptions", [
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => 'HIGH',
            'description' => 'EU homologation inspection hold.',
            'delay_impact_hours' => 18,
        ]);
        $created->assertStatus(201)
            ->assertJsonPath('data.status', 'OPEN');

        $exceptionId = $created->json('data.id');

        // Customer owning shipment can view its exceptions -> 200
        Sanctum::actingAs($this->customer1);
        $this->getJson("/api/shipments/{$this->shipment1->id}/exceptions")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Other customer cannot view exceptions -> 403
        Sanctum::actingAs($this->customer2);
        $this->getJson("/api/shipments/{$this->shipment1->id}/exceptions")
            ->assertStatus(403);

        // Operator transitions OPEN -> INVESTIGATING -> RESOLVED
        Sanctum::actingAs($this->operator);
        $this->patchJson("/api/exceptions/{$exceptionId}", [
            'status' => 'INVESTIGATING',
            'resolution_notes' => 'Broker reviewing UN3480 battery documentation.',
        ])->assertStatus(200)
            ->assertJsonPath('data.status', 'INVESTIGATING');

        $this->patchJson("/api/exceptions/{$exceptionId}", [
            'status' => 'RESOLVED',
            'resolution_notes' => 'Customs clearance certificate approved and released.',
        ])->assertStatus(200)
            ->assertJsonPath('data.status', 'RESOLVED');
    }

    public function test_shipments_api_sorting_filtering_and_summary_delay_explanation(): void
    {
        Http::fake([
            '*' => Http::response(['current' => ['temperature_2m' => 24.0, 'weather_code' => 0, 'wind_speed_10m' => 10]], 200),
        ]);

        Sanctum::actingAs($this->admin);

        // Sorting ascending by shipment_number
        $this->getJson('/api/shipments?sort_by=shipment_number&sort_dir=asc&per_page=1')
            ->assertStatus(200)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.shipment_number', 'CT-CUST1-001');

        // Show shipment returns deterministic delay reason and current location
        $this->getJson("/api/shipments/{$this->shipment1->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'summary' => [
                    'current_location',
                    'sla_threshold_hours',
                    'delay_reason',
                    'delay_evaluation' => ['expected_arrival', 'estimated_arrival', 'reason'],
                ],
                'weather',
            ]);
    }

    public function test_admin_can_manage_carriers_and_locations_and_in_use_records_are_protected(): void
    {
        $unusedCarrier = Carrier::create(['code' => 'HOEGH', 'name' => 'Hoegh Autoliners', 'service_type' => 'RORO_SEA']);
        $unusedLocation = Location::create([
            'code' => 'DEBRV',
            'name' => 'Bremerhaven Auto Terminal',
            'type' => 'PORT',
            'city' => 'Bremerhaven',
            'country' => 'Germany',
            'latitude' => 53.5625,
            'longitude' => 8.5567,
        ]);

        // Operator cannot update or delete carriers/locations -> 403
        $this->actingAs($this->operator)
            ->patch("/carriers/{$unusedCarrier->id}", [
                'code' => 'HOEGH',
                'name' => 'Unauthorized Rename',
                'service_type' => 'RORO_SEA',
            ])->assertStatus(403);

        // Admin updates carrier and location
        $this->actingAs($this->admin)
            ->patch("/carriers/{$unusedCarrier->id}", [
                'code' => 'HOEGH',
                'name' => 'Hoegh Autoliners AS',
                'service_type' => 'RORO_SEA',
                'is_active' => 1,
            ])->assertRedirect(route('carriers.index'));
        $this->assertDatabaseHas('carriers', ['id' => $unusedCarrier->id, 'name' => 'Hoegh Autoliners AS']);

        $this->actingAs($this->admin)
            ->patch("/locations/{$unusedLocation->id}", [
                'code' => 'DEBRV',
                'name' => 'Bremerhaven Ro-Ro Terminal',
                'type' => 'PORT',
                'city' => 'Bremerhaven',
                'country' => 'Germany',
                'latitude' => 53.5625,
                'longitude' => 8.5567,
            ])->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', ['id' => $unusedLocation->id, 'name' => 'Bremerhaven Ro-Ro Terminal']);

        // Referenced carrier/location cannot be deleted
        $this->actingAs($this->admin)
            ->delete("/carriers/{$this->carrier->id}")
            ->assertRedirect(route('carriers.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('carriers', ['id' => $this->carrier->id]);

        $this->actingAs($this->admin)
            ->delete("/locations/{$this->origin->id}")
            ->assertRedirect(route('locations.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('locations', ['id' => $this->origin->id]);

        // Unreferenced carrier/location can be deleted
        $this->actingAs($this->admin)
            ->delete("/carriers/{$unusedCarrier->id}")
            ->assertRedirect(route('carriers.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('carriers', ['id' => $unusedCarrier->id]);

        $this->actingAs($this->admin)
            ->delete("/locations/{$unusedLocation->id}")
            ->assertRedirect(route('locations.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('locations', ['id' => $unusedLocation->id]);
    }

    public function test_geocode_api_endpoint_is_role_protected_and_returns_coordinates(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'lat' => '1.2640000',
                    'lon' => '103.8220000',
                    'display_name' => 'Singapore PSA Terminal',
                    'address' => ['city' => 'Singapore', 'country' => 'Singapore', 'country_code' => 'sg'],
                ],
            ], 200),
        ]);

        // Customer is blocked -> 403
        Sanctum::actingAs($this->customer1);
        $this->getJson('/api/geocode?q=Singapore')
            ->assertStatus(403);

        // Operator receives geocoded coordinates -> 200
        Sanctum::actingAs($this->operator);
        $this->getJson('/api/geocode?q=Singapore')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.latitude', 1.264)
            ->assertJsonPath('data.longitude', 103.822);
    }

    public function test_settings_pages_profile_update_and_audit_log_csv_export(): void
    {
        // Settings tabs render cleanly
        foreach (['general', 'profile', 'account', 'notifications', 'language', 'security', 'preferences'] as $tab) {
            $this->actingAs($this->admin)
                ->get(route('settings.index', ['tab' => $tab]))
                ->assertStatus(200)
                ->assertDontSee('ui.');
        }

        // Profile update persists and logs audit event
        $this->actingAs($this->operator)
            ->patch(route('settings.profile.update'), [
                'name' => 'Updated Dispatch Operator',
                'company_name' => 'CargoTrace Ro-Ro Desk',
                'phone' => '+62 812 9999 0000',
            ])->assertRedirect(route('settings.index', ['tab' => 'profile']));

        $this->assertDatabaseHas('users', [
            'id' => $this->operator->id,
            'name' => 'Updated Dispatch Operator',
            'company_name' => 'CargoTrace Ro-Ro Desk',
        ]);

        // Audit logs CSV export is Admin-only
        $this->actingAs($this->operator)
            ->get(route('audit_logs.export'))
            ->assertStatus(403);

        $export = $this->actingAs($this->admin)
            ->get(route('audit_logs.export'));
        $export->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $export->headers->get('content-type'));
    }
}
