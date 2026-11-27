<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\EtaService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $etaService = app(EtaService::class);

        // 1. Seed Users (Demo Accounts for RBAC Evaluation)
        $admin = User::firstOrCreate(['email' => 'admin@cargotrace.test'], [
            'name' => 'System Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+62 811 0000 001',
            'company_name' => 'CargoTrace Automotive Operations',
        ]);

        $operator = User::firstOrCreate(['email' => 'operator@cargotrace.test'], [
            'name' => 'Ro-Ro & Air Freight Dispatcher',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'phone' => '+62 812 0000 002',
            'company_name' => 'CargoTrace Vehicle Terminal Desk',
        ]);

        $customer1 = User::firstOrCreate(['email' => 'customer@cargotrace.test'], [
            'name' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+62 813 0000 003',
            'company_name' => 'PT Nusantara EV & Mobility',
        ]);

        $customer2 = User::firstOrCreate(['email' => 'motorsport@cargotrace.test'], [
            'name' => 'Siti Rahmawati',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+62 814 0000 004',
            'company_name' => 'Asia Motorsport & Superbike Imports',
        ]);

        // 2. Seed Realistic Automotive Carriers (Ro-Ro Sea & Air Freight)
        $carriers = [
            [
                'code' => 'WWL',
                'name' => 'Wallenius Wilhelmsen',
                'service_type' => 'RORO_SEA',
                'contact_email' => 'roro-ops@walleniuswilhelmsen.com',
                'contact_phone' => '+47 67 58 40 00',
                'website' => 'https://www.walleniuswilhelmsen.com',
                'is_active' => true,
            ],
            [
                'code' => 'NYK',
                'name' => 'NYK Line (Ro-Ro Car Carrier)',
                'service_type' => 'RORO_SEA',
                'contact_email' => 'autocarrier@nyk.com',
                'contact_phone' => '+81 3 3284 5151',
                'website' => 'https://www.nyk.com',
                'is_active' => true,
            ],
            [
                'code' => 'CV',
                'name' => 'Cargolux (Motorsport & VIP Air)',
                'service_type' => 'AIR_FREIGHT',
                'contact_email' => 'motorsport@cargolux.com',
                'contact_phone' => '+352 4211 1',
                'website' => 'https://www.cargolux.com',
                'is_active' => true,
            ],
            [
                'code' => 'QR',
                'name' => 'Qatar Airways Cargo',
                'service_type' => 'AIR_FREIGHT',
                'contact_email' => 'qrauto@qrcargo.com',
                'contact_phone' => '+974 4022 0000',
                'website' => 'https://www.qrcargo.com',
                'is_active' => true,
            ],
        ];

        $carrierModels = [];
        foreach ($carriers as $c) {
            $carrierModels[$c['code']] = Carrier::updateOrCreate(['code' => $c['code']], $c);
        }

        // 3. Seed Major Automotive Hub Locations (Southeast Asia <-> Europe)
        $locations = [
            [
                'code' => 'IDTPP',
                'name' => 'Jakarta / Tanjung Priok Car Terminal (IKT)',
                'type' => 'PORT',
                'address' => 'Jl. Sindang Laut No.101, Tanjung Priok',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'country_code' => 'ID',
                'latitude' => -6.0967000,
                'longitude' => 106.8858000,
            ],
            [
                'code' => 'THLCH',
                'name' => 'Bangkok / Laem Chabang Ro-Ro Terminal',
                'type' => 'PORT',
                'address' => 'Laem Chabang Port Terminal A1, Chonburi',
                'city' => 'Laem Chabang',
                'country' => 'Thailand',
                'country_code' => 'TH',
                'latitude' => 13.0819000,
                'longitude' => 100.8833000,
            ],
            [
                'code' => 'JPNRT',
                'name' => 'Tokyo / Narita Air Cargo Hub',
                'type' => 'AIRPORT',
                'address' => '1-1 Furugome, Narita International Cargo Area',
                'city' => 'Tokyo / Narita',
                'country' => 'Japan',
                'country_code' => 'JP',
                'latitude' => 35.7719867,
                'longitude' => 140.3928501,
            ],
            [
                'code' => 'NLRTM',
                'name' => 'Rotterdam Ro-Ro & EV Hub',
                'type' => 'PORT',
                'address' => 'Maasvlakte Plaza 6, Port of Rotterdam',
                'city' => 'Rotterdam',
                'country' => 'Netherlands',
                'country_code' => 'NL',
                'latitude' => 51.9054440,
                'longitude' => 4.4666370,
            ],
            [
                'code' => 'BEANR',
                'name' => 'Antwerp Automotive Terminal',
                'type' => 'PORT',
                'address' => 'Haven 1333, Left Bank Ro-Ro Terminal',
                'city' => 'Antwerp',
                'country' => 'Belgium',
                'country_code' => 'BE',
                'latitude' => 51.2667000,
                'longitude' => 4.3333000,
            ],
            [
                'code' => 'DEFRA',
                'name' => 'Frankfurt Airport CargoCity Motorsport Hub',
                'type' => 'AIRPORT',
                'address' => 'CargoCity South, Frankfurt Airport',
                'city' => 'Frankfurt',
                'country' => 'Germany',
                'country_code' => 'DE',
                'latitude' => 50.0379326,
                'longitude' => 8.5621518,
            ],
            [
                'code' => 'SGSIN',
                'name' => 'Singapore PSA Automotive Transshipment Hub',
                'type' => 'PORT',
                'address' => 'Pasir Panjang Auto Terminal Berth 4',
                'city' => 'Singapore',
                'country' => 'Singapore',
                'country_code' => 'SG',
                'latitude' => 1.2640000,
                'longitude' => 103.8220000,
            ],
        ];

        $locationModels = [];
        foreach ($locations as $l) {
            $locationModels[$l['code']] = Location::updateOrCreate(['code' => $l['code']], $l);
        }

        // 4. Seed Automotive Shipments & Vehicle Consignments
        // Shipment 1: CBU EV Fleet via Ro-Ro Sea (Jakarta -> Rotterdam) with EU Homologation / Lithium DG Hold (+18h)
        $shipment1 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-RORO-8841'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['WWL']->id,
                'origin_id' => $locationModels['IDTPP']->id,
                'destination_id' => $locationModels['NLRTM']->id,
                'cargo_type' => ShipmentItem::CATEGORY_CBU_EV,
                'total_weight_kg' => 39600.00,
                'total_volume_cbm' => 285.00,
                'expected_departure' => Carbon::now()->subDays(6)->setTime(8, 0),
                'expected_arrival' => Carbon::now()->addDays(14)->setTime(18, 0),
                'actual_departure' => Carbon::now()->subDays(6)->setTime(9, 15),
                'estimated_arrival' => Carbon::now()->addDays(15)->setTime(12, 0),
                'status' => Shipment::STATUS_IN_TRANSIT,
                'notes' => 'Pure Car & Truck Carrier MV Thermopylae. CBU EV consignment with UN3480 lithium battery monitoring.',
            ]
        );

        ShipmentItem::updateOrCreate(['vin_number' => 'KMHKN81EFPU884101'], [
            'shipment_id' => $shipment1->id,
            'item_description' => 'Hyundai Ioniq 5 CBU (Long Range AWD)',
            'vehicle_model' => 'Hyundai Ioniq 5 CBU',
            'cargo_category' => ShipmentItem::CATEGORY_CBU_EV,
            'quantity' => 12,
            'weight_kg' => 25200.00,
            'declared_value' => 648000.00,
            'dimensions' => '463x189x160 cm / unit',
            'hs_code' => '8703.80.10',
        ]);
        ShipmentItem::updateOrCreate(['vin_number' => 'KMHK381GFPU884102'], [
            'shipment_id' => $shipment1->id,
            'item_description' => 'Hyundai Kona Electric CBU (EU Spec)',
            'vehicle_model' => 'Hyundai Kona Electric CBU',
            'cargo_category' => ShipmentItem::CATEGORY_CBU_EV,
            'quantity' => 8,
            'weight_kg' => 14400.00,
            'declared_value' => 336000.00,
            'dimensions' => '435x182x157 cm / unit',
            'hs_code' => '8703.80.10',
        ]);

        if ($shipment1->trackingEvents()->count() === 0) {
            TrackingEvent::create([
                'shipment_id' => $shipment1->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => 'Jakarta / Tanjung Priok Car Terminal (IKT)',
                'location_id' => $locationModels['IDTPP']->id,
                'latitude' => -6.0967,
                'longitude' => 106.8858,
                'event_time' => Carbon::now()->subDays(7)->setTime(10, 0),
                'description' => 'Ro-Ro export booking confirmed and VIN manifest registered.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment1->id,
                'event_type' => 'PICKED_UP',
                'status' => Shipment::STATUS_PICKED_UP,
                'location_name' => 'Tanjung Priok Ro-Ro Ramp 2',
                'location_id' => $locationModels['IDTPP']->id,
                'latitude' => -6.0967,
                'longitude' => 106.8858,
                'event_time' => Carbon::now()->subDays(6)->setTime(7, 30),
                'description' => '20 CBU EV units driven aboard MV Thermopylae deck 5 and lashed.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment1->id,
                'event_type' => 'DEPARTED',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Java Sea Maritime Corridor',
                'latitude' => -3.5000,
                'longitude' => 107.8000,
                'event_time' => Carbon::now()->subDays(5)->setTime(14, 0),
                'description' => 'Ro-Ro vessel departed Tanjung Priok toward Singapore automotive hub.',
                'created_by' => $operator->id,
            ]);
            $sgEvent = TrackingEvent::create([
                'shipment_id' => $shipment1->id,
                'event_type' => 'CUSTOMS_INSPECTION',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Singapore PSA Automotive Transshipment Hub',
                'location_id' => $locationModels['SGSIN']->id,
                'latitude' => 1.2640,
                'longitude' => 103.8220,
                'event_time' => Carbon::now()->subDays(3)->setTime(18, 0),
                'description' => 'Transit verification flagged for EU homologation & UN3480 lithium battery compliance.',
                'is_exception' => true,
                'created_by' => $operator->id,
            ]);

            ShipmentException::create([
                'shipment_id' => $shipment1->id,
                'tracking_event_id' => $sgEvent->id,
                'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
                'severity' => ShipmentException::SEVERITY_MEDIUM,
                'status' => ShipmentException::STATUS_OPEN,
                'description' => 'Verifikasi dokumen Homologasi EU & sertifikat baterai lithium (Dangerous Goods)',
                'delay_impact_hours' => 18,
                'detected_at' => Carbon::now()->subDays(3)->setTime(19, 0),
            ]);
        }
        $etaService->evaluateDelay($shipment1);

        // Shipment 2: Motorsport & Superbike Air Charter (Tokyo/Narita -> Frankfurt)
        $shipment2 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-AIR-9102'],
            [
                'customer_id' => $customer2->id,
                'carrier_id' => $carrierModels['CV']->id,
                'origin_id' => $locationModels['JPNRT']->id,
                'destination_id' => $locationModels['DEFRA']->id,
                'cargo_type' => ShipmentItem::CATEGORY_MOTORSPORT_AIR,
                'total_weight_kg' => 3242.00,
                'total_volume_cbm' => 28.50,
                'expected_departure' => Carbon::now()->subDays(2)->setTime(14, 0),
                'expected_arrival' => Carbon::now()->addDays(1)->setTime(16, 0),
                'actual_departure' => Carbon::now()->subDays(2)->setTime(16, 30),
                'estimated_arrival' => Carbon::now()->addDays(2)->setTime(6, 0),
                'status' => Shipment::STATUS_IN_TRANSIT,
                'notes' => 'Cargolux Boeing 747-8F nose-loader charter for motorsport prototypes & superbikes.',
            ]
        );

        ShipmentItem::updateOrCreate(['vin_number' => 'JTMPRT01X26910201'], [
            'shipment_id' => $shipment2->id,
            'item_description' => 'Prototype EV Chassis (Track Telemetry Spec)',
            'vehicle_model' => 'Prototype EV Chassis',
            'cargo_category' => ShipmentItem::CATEGORY_MOTORSPORT_AIR,
            'quantity' => 2,
            'weight_kg' => 2450.00,
            'declared_value' => 980000.00,
            'dimensions' => '485x200x115 cm / unit',
            'hs_code' => '8703.80.90',
        ]);
        ShipmentItem::updateOrCreate(['vin_number' => 'ZDM14BPW5PB910202'], [
            'shipment_id' => $shipment2->id,
            'item_description' => 'Ducati Panigale V4 (Crated Air Pallet)',
            'vehicle_model' => 'Ducati Panigale V4',
            'cargo_category' => ShipmentItem::CATEGORY_MOTORSPORT_AIR,
            'quantity' => 4,
            'weight_kg' => 792.00,
            'declared_value' => 148000.00,
            'dimensions' => '215x85x120 cm / crate',
            'hs_code' => '8711.50.00',
        ]);

        if ($shipment2->trackingEvents()->count() === 0) {
            TrackingEvent::create([
                'shipment_id' => $shipment2->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => 'Tokyo / Narita Air Cargo Hub',
                'location_id' => $locationModels['JPNRT']->id,
                'latitude' => 35.7720,
                'longitude' => 140.3929,
                'event_time' => Carbon::now()->subDays(3)->setTime(8, 0),
                'description' => 'Air Charter Master Airway Bill confirmed with Cargolux Motorsport Desk.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment2->id,
                'event_type' => 'DEPARTED',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Tokyo / Narita Runway 34L',
                'location_id' => $locationModels['JPNRT']->id,
                'latitude' => 35.7720,
                'longitude' => 140.3929,
                'event_time' => Carbon::now()->subDays(2)->setTime(16, 30),
                'description' => 'Flight CV-7942 departed Narita; high-altitude turbulence reroute over Central Asia.',
                'is_exception' => true,
                'created_by' => $operator->id,
            ]);

            ShipmentException::create([
                'shipment_id' => $shipment2->id,
                'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
                'severity' => ShipmentException::SEVERITY_MEDIUM,
                'status' => ShipmentException::STATUS_INVESTIGATING,
                'description' => 'Jetstream severe turbulence forced southern corridor detour, adding 14 flight and crew hours.',
                'delay_impact_hours' => 14,
                'detected_at' => Carbon::now()->subDays(1)->setTime(6, 0),
            ]);
        }
        $etaService->evaluateDelay($shipment2);

        // Shipment 3: Delivered Luxury Vehicles (Bangkok / Laem Chabang -> Antwerp)
        $shipment3 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-RORO-4412'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['NYK']->id,
                'origin_id' => $locationModels['THLCH']->id,
                'destination_id' => $locationModels['BEANR']->id,
                'cargo_type' => ShipmentItem::CATEGORY_LUXURY_CAR,
                'total_weight_kg' => 19550.00,
                'total_volume_cbm' => 142.00,
                'expected_departure' => Carbon::now()->subDays(18)->setTime(6, 0),
                'expected_arrival' => Carbon::now()->subDays(1)->setTime(15, 0),
                'actual_departure' => Carbon::now()->subDays(18)->setTime(6, 30),
                'actual_arrival' => Carbon::now()->subDays(1)->setTime(14, 15),
                'estimated_arrival' => Carbon::now()->subDays(1)->setTime(15, 0),
                'status' => Shipment::STATUS_DELIVERED,
                'notes' => 'NYK Ro-Ro Vessel Venus Leader. Delivered cleanly to Antwerp Left Bank Auto Terminal.',
            ]
        );

        ShipmentItem::updateOrCreate(['vin_number' => 'WP0ZZZ9Y2PSA44121'], [
            'shipment_id' => $shipment3->id,
            'item_description' => 'Porsche Taycan Turbo S (CBU)',
            'vehicle_model' => 'Porsche Taycan Turbo S',
            'cargo_category' => ShipmentItem::CATEGORY_LUXURY_CAR,
            'quantity' => 4,
            'weight_kg' => 9200.00,
            'declared_value' => 760000.00,
            'hs_code' => '8703.80.10',
        ]);
        ShipmentItem::updateOrCreate(['vin_number' => 'WBS43AZ060CM44122'], [
            'shipment_id' => $shipment3->id,
            'item_description' => 'BMW M4 Competition Coupe',
            'vehicle_model' => 'BMW M4 Competition CBU',
            'cargo_category' => ShipmentItem::CATEGORY_LUXURY_CAR,
            'quantity' => 6,
            'weight_kg' => 10350.00,
            'declared_value' => 540000.00,
            'hs_code' => '8703.23.90',
        ]);

        if ($shipment3->trackingEvents()->count() === 0) {
            TrackingEvent::create([
                'shipment_id' => $shipment3->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => 'Bangkok / Laem Chabang Ro-Ro Terminal',
                'location_id' => $locationModels['THLCH']->id,
                'latitude' => 13.0819,
                'longitude' => 100.8833,
                'event_time' => Carbon::now()->subDays(19)->setTime(12, 0),
                'description' => 'Vehicle export clearance and VIN pre-delivery inspection completed.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment3->id,
                'event_type' => 'DEPARTED',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Gulf of Thailand Maritime Lane',
                'latitude' => 10.5000,
                'longitude' => 101.5000,
                'event_time' => Carbon::now()->subDays(18)->setTime(6, 30),
                'description' => 'MV Venus Leader cleared Laem Chabang fairway en route to Europe.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment3->id,
                'event_type' => 'DELIVERED',
                'status' => Shipment::STATUS_DELIVERED,
                'location_name' => 'Antwerp Automotive Terminal',
                'location_id' => $locationModels['BEANR']->id,
                'latitude' => 51.2667,
                'longitude' => 4.3333,
                'event_time' => Carbon::now()->subDays(1)->setTime(14, 15),
                'description' => 'All 10 luxury CBU units discharged, PDI inspected, and signed off by consignee.',
                'created_by' => $operator->id,
            ]);
        }
        $etaService->evaluateDelay($shipment3);

        // Shipment 4: Booked CKD & EV Battery Powertrain Air Freight (Jakarta -> Frankfurt)
        $shipment4 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-AIR-1099'],
            [
                'customer_id' => $customer2->id,
                'carrier_id' => $carrierModels['QR']->id,
                'origin_id' => $locationModels['IDTPP']->id,
                'destination_id' => $locationModels['DEFRA']->id,
                'cargo_type' => ShipmentItem::CATEGORY_CKD_PARTS,
                'total_weight_kg' => 6800.00,
                'total_volume_cbm' => 22.00,
                'expected_departure' => Carbon::now()->addDays(2)->setTime(9, 0),
                'expected_arrival' => Carbon::now()->addDays(4)->setTime(16, 0),
                'status' => Shipment::STATUS_BOOKED,
                'notes' => 'Qatar Airways Cargo Boeing 777F main-deck allocation for EV powertrain CKD kits.',
            ]
        );

        ShipmentItem::updateOrCreate(['vin_number' => 'CKD-EV800V-2026-1099'], [
            'shipment_id' => $shipment4->id,
            'item_description' => '800V EV Silicon Carbide Inverter & CKD Subframe Kits',
            'vehicle_model' => 'EV 800V Powertrain CKD Kit',
            'cargo_category' => ShipmentItem::CATEGORY_CKD_PARTS,
            'quantity' => 24,
            'weight_kg' => 6800.00,
            'declared_value' => 215000.00,
            'hs_code' => '8507.60.00',
        ]);

        if ($shipment4->trackingEvents()->count() === 0) {
            TrackingEvent::create([
                'shipment_id' => $shipment4->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => 'Jakarta / Tanjung Priok Car Terminal (IKT)',
                'location_id' => $locationModels['IDTPP']->id,
                'latitude' => -6.0967,
                'longitude' => 106.8858,
                'event_time' => Carbon::now()->subHours(12),
                'description' => 'Automotive CKD air dispatch order scheduled.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment4->id,
                'event_type' => 'BOOKED',
                'status' => Shipment::STATUS_BOOKED,
                'location_name' => 'Jakarta Air Freight Staging Hub',
                'location_id' => $locationModels['IDTPP']->id,
                'latitude' => -6.1256,
                'longitude' => 106.6558,
                'event_time' => Carbon::now()->subHours(4),
                'description' => 'Main-deck pallet space confirmed with Qatar Airways Cargo.',
                'created_by' => $operator->id,
            ]);
        }
        $etaService->evaluateDelay($shipment4);

        // Shipment 5: Resolved Port Congestion Case (Laem Chabang -> Rotterdam)
        $shipment5 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-RORO-7721'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['WWL']->id,
                'origin_id' => $locationModels['THLCH']->id,
                'destination_id' => $locationModels['NLRTM']->id,
                'cargo_type' => ShipmentItem::CATEGORY_CBU_EV,
                'total_weight_kg' => 21850.00,
                'total_volume_cbm' => 155.00,
                'expected_departure' => Carbon::now()->subDays(8)->setTime(11, 0),
                'expected_arrival' => Carbon::now()->addDays(12)->setTime(17, 0),
                'actual_departure' => Carbon::now()->subDays(8)->setTime(17, 0),
                'estimated_arrival' => Carbon::now()->addDays(12)->setTime(23, 0),
                'status' => Shipment::STATUS_IN_TRANSIT,
                'notes' => '10 CBU EV units onboard MV Morning Lily.',
            ]
        );

        ShipmentItem::updateOrCreate(['vin_number' => 'LGXCE6CB9R0772101'], [
            'shipment_id' => $shipment5->id,
            'item_description' => 'BYD Seal Performance AWD CBU',
            'vehicle_model' => 'BYD Seal Performance AWD',
            'cargo_category' => ShipmentItem::CATEGORY_CBU_EV,
            'quantity' => 10,
            'weight_kg' => 21850.00,
            'declared_value' => 490000.00,
            'hs_code' => '8703.80.10',
        ]);

        if ($shipment5->trackingEvents()->count() === 0) {
            TrackingEvent::create([
                'shipment_id' => $shipment5->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => 'Bangkok / Laem Chabang Ro-Ro Terminal',
                'location_id' => $locationModels['THLCH']->id,
                'latitude' => 13.0819,
                'longitude' => 100.8833,
                'event_time' => Carbon::now()->subDays(9),
                'description' => 'EV state-of-charge verified at 45% for safe maritime Ro-Ro carriage.',
                'created_by' => $operator->id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment5->id,
                'event_type' => 'DEPARTED',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Malacca Strait Ro-Ro Corridor',
                'latitude' => 2.5000,
                'longitude' => 101.2000,
                'event_time' => Carbon::now()->subDays(8),
                'description' => 'Vessel cleared congestion queue and steaming west toward Suez.',
                'created_by' => $operator->id,
            ]);

            ShipmentException::create([
                'shipment_id' => $shipment5->id,
                'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
                'severity' => ShipmentException::SEVERITY_LOW,
                'status' => ShipmentException::STATUS_RESOLVED,
                'description' => 'Antrean sandar kapal Ro-Ro di pelabuhan transit',
                'delay_impact_hours' => 6,
                'detected_at' => Carbon::now()->subDays(9)->setTime(14, 0),
                'resolved_at' => Carbon::now()->subDays(8)->setTime(10, 0),
                'resolved_by' => $operator->id,
                'resolution_notes' => 'Terminal allocated priority Ro-Ro berth window; vessel resumed schedule.',
            ]);
        }
        $etaService->evaluateDelay($shipment5);

        // Shipment 6: High Severity Vehicle Unloading Inspection Exception (Antwerp -> Jakarta)
        $shipment6 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-RORO-5530'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['NYK']->id,
                'origin_id' => $locationModels['BEANR']->id,
                'destination_id' => $locationModels['IDTPP']->id,
                'cargo_type' => ShipmentItem::CATEGORY_LUXURY_CAR,
                'total_weight_kg' => 4528.00,
                'total_volume_cbm' => 46.00,
                'expected_departure' => Carbon::now()->subDays(15)->setTime(10, 0),
                'expected_arrival' => Carbon::now()->addDays(2)->setTime(12, 0),
                'actual_departure' => Carbon::now()->subDays(15)->setTime(11, 0),
                'estimated_arrival' => Carbon::now()->addDays(3)->setTime(16, 0),
                'status' => Shipment::STATUS_IN_TRANSIT,
                'notes' => 'European luxury coupe & superbike consignment bound for Jakarta.',
            ]
        );

        ShipmentItem::updateOrCreate(['vin_number' => 'W1K7X8JB9PA553001'], [
            'shipment_id' => $shipment6->id,
            'item_description' => 'Mercedes-AMG GT 63 Coupe',
            'vehicle_model' => 'Mercedes-AMG GT 63 Coupe',
            'cargo_category' => ShipmentItem::CATEGORY_LUXURY_CAR,
            'quantity' => 2,
            'weight_kg' => 3940.00,
            'declared_value' => 410000.00,
            'hs_code' => '8703.24.00',
        ]);
        ShipmentItem::updateOrCreate(['vin_number' => 'ZDMFA01BXPB553002'], [
            'shipment_id' => $shipment6->id,
            'item_description' => 'Ducati Streetfighter V4 SP2',
            'vehicle_model' => 'Ducati Streetfighter V4 SP2',
            'cargo_category' => ShipmentItem::CATEGORY_MOTORSPORT_AIR,
            'quantity' => 3,
            'weight_kg' => 588.00,
            'declared_value' => 114000.00,
            'hs_code' => '8711.50.00',
        ]);

        if ($shipment6->trackingEvents()->count() === 0) {
            TrackingEvent::create([
                'shipment_id' => $shipment6->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => 'Antwerp Automotive Terminal',
                'location_id' => $locationModels['BEANR']->id,
                'latitude' => 51.2667,
                'longitude' => 4.3333,
                'event_time' => Carbon::now()->subDays(16),
                'description' => 'Vehicles cleared European export inspection.',
                'created_by' => $operator->id,
            ]);
            $singaporeStop = TrackingEvent::create([
                'shipment_id' => $shipment6->id,
                'event_type' => 'DAMAGE_INSPECTION',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Singapore PSA Automotive Transshipment Hub',
                'location_id' => $locationModels['SGSIN']->id,
                'latitude' => 1.2640,
                'longitude' => 103.8220,
                'event_time' => Carbon::now()->subHours(18),
                'description' => 'Transshipment deck inspection requested prior to final feeder voyage to Jakarta.',
                'is_exception' => true,
                'created_by' => $operator->id,
            ]);

            ShipmentException::create([
                'shipment_id' => $shipment6->id,
                'tracking_event_id' => $singaporeStop->id,
                'exception_type' => ShipmentException::TYPE_DAMAGED,
                'severity' => ShipmentException::SEVERITY_HIGH,
                'status' => ShipmentException::STATUS_OPEN,
                'description' => 'Inspeksi goresan minor pada bumper saat proses unloading',
                'delay_impact_hours' => 28,
                'detected_at' => Carbon::now()->subHours(16),
            ]);
        }
        $etaService->evaluateDelay($shipment6);

        // 5. Seed In-App Notifications
        if (Notification::count() === 0) {
            Notification::create([
                'user_id' => $customer1->id,
                'title' => 'EU Homologation & Battery Hold on CT-2026-RORO-8841',
                'message' => 'Verifikasi dokumen Homologasi EU & sertifikat baterai lithium (Dangerous Goods). Estimated delay +18h.',
                'type' => 'EXCEPTION_OPENED',
                'link' => route('shipments.show', $shipment1->id),
                'created_at' => Carbon::now()->subDays(3)->setTime(19, 5),
            ]);
            Notification::create([
                'user_id' => $customer1->id,
                'title' => 'Bumper Inspection Flagged on CT-2026-RORO-5530',
                'message' => 'Inspeksi goresan minor pada bumper saat proses unloading at Singapore PSA transshipment.',
                'type' => 'EXCEPTION_OPENED',
                'link' => route('shipments.show', $shipment6->id),
                'created_at' => Carbon::now()->subHours(15),
            ]);
            Notification::create([
                'user_id' => $customer1->id,
                'title' => 'Luxury CBU Consignment CT-2026-RORO-4412 Delivered',
                'message' => 'Porsche Taycan Turbo S & BMW M4 Competition units delivered at Antwerp Automotive Terminal on schedule.',
                'type' => 'STATUS_UPDATE',
                'link' => route('shipments.show', $shipment3->id),
                'read_at' => Carbon::now()->subHours(2),
                'created_at' => Carbon::now()->subDays(1)->setTime(14, 20),
            ]);
        }

        // 6. Seed Audit Logs
        if (AuditLog::count() === 0) {
            AuditLog::create([
                'user_id' => $admin->id,
                'user_name' => $admin->name,
                'action' => 'SYSTEM_INIT',
                'entity_type' => 'System',
                'entity_id' => 1,
                'old_values' => null,
                'new_values' => ['domain' => 'Global Automotive & Vehicle Shipment Visibility', 'delay_threshold_hours' => config('cargotrace.delay_threshold_hours')],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'DatabaseSeeder',
                'created_at' => Carbon::now()->subDays(14),
            ]);
            AuditLog::create([
                'user_id' => $operator->id,
                'user_name' => $operator->name,
                'action' => 'CREATE_EXCEPTION',
                'entity_type' => 'ShipmentException',
                'entity_id' => 1,
                'old_values' => null,
                'new_values' => ['shipment' => 'CT-2026-RORO-8841', 'type' => 'CUSTOMS_HOLD', 'impact_hours' => 18],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'CargoTrace Dispatch Console',
                'created_at' => Carbon::now()->subDays(3)->setTime(19, 0),
            ]);
        }
    }
}
