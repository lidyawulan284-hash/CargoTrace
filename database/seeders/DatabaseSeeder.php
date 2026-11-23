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
        $etaService = new EtaService;

        // 1. Seed Users (Slide 5, 18, 19: Demo Accounts)
        $admin = User::firstOrCreate(['email' => 'admin@cargotrace.test'], [
            'name' => 'System Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+62 811 0000 001',
            'company_name' => 'CargoTrace Global Operations',
        ]);

        $operator = User::firstOrCreate(['email' => 'operator@cargotrace.test'], [
            'name' => 'Logistics Dispatcher Operator',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'phone' => '+62 812 0000 002',
            'company_name' => 'CargoTrace Port Terminal Operations',
        ]);

        $customer1 = User::firstOrCreate(['email' => 'customer@cargotrace.test'], [
            'name' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+62 813 0000 003',
            'company_name' => 'PT Nusantara Global Trade',
        ]);

        $customer2 = User::firstOrCreate(['email' => 'astra_parts@example.com'], [
            'name' => 'Siti Rahmawati',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+62 814 0000 004',
            'company_name' => 'PT Astra Mandiri Components',
        ]);

        // 2. Seed Carriers (Slide 11 & 14)
        $carriers = [
            [
                'code' => 'MSK',
                'name' => 'Maersk Line A/S',
                'service_type' => 'SEA_FREIGHT',
                'contact_email' => 'support@maersk.com',
                'contact_phone' => '+45 33 63 33 63',
                'website' => 'https://www.maersk.com',
                'is_active' => true,
            ],
            [
                'code' => 'HAPAG',
                'name' => 'Hapag-Lloyd AG',
                'service_type' => 'SEA_FREIGHT',
                'contact_email' => 'operations@hlag.com',
                'contact_phone' => '+49 40 3001-0',
                'website' => 'https://www.hapag-lloyd.com',
                'is_active' => true,
            ],
            [
                'code' => 'ONE',
                'name' => 'Ocean Network Express',
                'service_type' => 'SEA_FREIGHT',
                'contact_email' => 'service@one-line.com',
                'contact_phone' => '+65 6890 1800',
                'website' => 'https://www.one-line.com',
                'is_active' => true,
            ],
            [
                'code' => 'DHL',
                'name' => 'DHL Global Forwarding',
                'service_type' => 'AIR_FREIGHT',
                'contact_email' => 'airfreight@dhl.com',
                'contact_phone' => '+49 228 182-0',
                'website' => 'https://www.dhl.com',
                'is_active' => true,
            ],
            [
                'code' => 'FEDEX',
                'name' => 'FedEx Trade Networks',
                'service_type' => 'AIR_FREIGHT',
                'contact_email' => 'customs@fedex.com',
                'contact_phone' => '+1 800 463 3339',
                'website' => 'https://www.fedex.com',
                'is_active' => true,
            ],
            [
                'code' => 'SICEPAT',
                'name' => 'SiCepat Intermodal Logistics',
                'service_type' => 'ROAD_FREIGHT',
                'contact_email' => 'cargo@sicepat.com',
                'contact_phone' => '+62 21 5020 0050',
                'website' => 'https://www.sicepat.com',
                'is_active' => true,
            ],
        ];

        $carrierModels = [];
        foreach ($carriers as $c) {
            $carrierModels[$c['code']] = Carrier::updateOrCreate(['code' => $c['code']], $c);
        }

        // 3. Seed Locations (Slide 9 & 11)
        $locations = [
            [
                'code' => 'IDJKT',
                'name' => 'Port of Tanjung Priok',
                'type' => 'PORT',
                'address' => 'Jl. Pasoso No.1, Tanjung Priok',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'country_code' => 'ID',
                'latitude' => -6.1033000,
                'longitude' => 106.8833000,
            ],
            [
                'code' => 'SGSIN',
                'name' => 'Port of Singapore (PSA Pasir Panjang)',
                'type' => 'PORT',
                'address' => '7B Keppel Road, Tanjong Pagar Complex',
                'city' => 'Singapore',
                'country' => 'Singapore',
                'country_code' => 'SG',
                'latitude' => 1.2902700,
                'longitude' => 103.8519590,
            ],
            [
                'code' => 'NLRTM',
                'name' => 'Port of Rotterdam (Maasvlakte 2)',
                'type' => 'PORT',
                'address' => 'Europaweg 902',
                'city' => 'Rotterdam',
                'country' => 'Netherlands',
                'country_code' => 'NL',
                'latitude' => 51.9054440,
                'longitude' => 4.4666370,
            ],
            [
                'code' => 'DEHAM',
                'name' => 'Port of Hamburg (Burchardkai)',
                'type' => 'PORT',
                'address' => 'Waltershofer Damm',
                'city' => 'Hamburg',
                'country' => 'Germany',
                'country_code' => 'DE',
                'latitude' => 53.5437640,
                'longitude' => 9.9672320,
            ],
            [
                'code' => 'USLAX',
                'name' => 'Port of Los Angeles / Long Beach',
                'type' => 'PORT',
                'address' => '425 S. Palos Verdes St',
                'city' => 'Los Angeles',
                'country' => 'United States',
                'country_code' => 'US',
                'latitude' => 33.7431840,
                'longitude' => -118.2672540,
            ],
            [
                'code' => 'AUMLB',
                'name' => 'Port of Melbourne (Webb Dock)',
                'type' => 'PORT',
                'address' => 'Todd Road, Port Melbourne',
                'city' => 'Melbourne',
                'country' => 'Australia',
                'country_code' => 'AU',
                'latitude' => -37.8136280,
                'longitude' => 144.9630580,
            ],
            [
                'code' => 'CNSHA',
                'name' => 'Port of Shanghai (Yangshan Deep-Water)',
                'type' => 'PORT',
                'address' => 'Yangshan Island Harbor Blvd',
                'city' => 'Shanghai',
                'country' => 'China',
                'country_code' => 'CN',
                'latitude' => 31.2304160,
                'longitude' => 121.4737010,
            ],
            [
                'code' => 'JPTYO',
                'name' => 'Port of Tokyo Container Terminal',
                'type' => 'PORT',
                'address' => 'Aomi Wharf, Koto City',
                'city' => 'Tokyo',
                'country' => 'Japan',
                'country_code' => 'JP',
                'latitude' => 35.6528320,
                'longitude' => 139.8394780,
            ],
        ];

        $locationModels = [];
        foreach ($locations as $l) {
            $locationModels[$l['code']] = Location::updateOrCreate(['code' => $l['code']], $l);
        }

        // 4. Seed Shipments & Scenarios (Slide 23: Group Scenarios)

        // Scenario 1: Group A (Southeast Asia -> Europe, delay threshold 12h, customs hold)
        $shipment1 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-SEA-8841'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['MSK']->id,
                'origin_id' => $locationModels['IDJKT']->id,
                'destination_id' => $locationModels['NLRTM']->id,
                'cargo_type' => 'ELECTRONICS',
                'total_weight_kg' => 42500.00,
                'total_volume_cbm' => 68.00,
                'expected_departure' => Carbon::now()->subDays(6)->setTime(8, 0),
                'expected_arrival' => Carbon::now()->addDays(14)->setTime(18, 0),
                'actual_departure' => Carbon::now()->subDays(6)->setTime(9, 30),
                'estimated_arrival' => Carbon::now()->addDays(15)->setTime(12, 0), // Shifted by +18h due to customs hold
                'status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => true,
                'delay_hours' => 18,
                'delay_severity' => 'MEDIUM',
                'notes' => 'Container CMAU-9182301 with high-precision semiconductors. Require continuous cold storage monitoring.',
            ]
        );

        // Scenario 1 Items
        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_description' => 'Automotive Microcontroller Units (MCUs)',
            'quantity' => 12000,
            'weight_kg' => 24000.00,
            'dimensions' => '120x80x160 cm x 20 pallets',
            'hs_code' => '8542.31.00',
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_description' => 'Lithium-ion Battery Energy Storage Modules',
            'quantity' => 600,
            'weight_kg' => 18500.00,
            'dimensions' => '100x120x100 cm x 10 pallets',
            'hs_code' => '8507.60.00',
        ]);

        // Scenario 1 Checkpoints (Slide 7)
        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => 'CREATED',
            'status' => Shipment::STATUS_CREATED,
            'location_name' => 'Jakarta Export Terminal Hub',
            'location_id' => $locationModels['IDJKT']->id,
            'latitude' => -6.1033,
            'longitude' => 106.8833,
            'event_time' => Carbon::now()->subDays(7)->setTime(10, 0),
            'description' => 'Export booking confirmed and customs export manifest registered.',
            'created_by' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => 'PICKED_UP',
            'status' => Shipment::STATUS_PICKED_UP,
            'location_name' => 'Port of Tanjung Priok Container Yard',
            'location_id' => $locationModels['IDJKT']->id,
            'latitude' => -6.1033,
            'longitude' => 106.8833,
            'event_time' => Carbon::now()->subDays(6)->setTime(7, 30),
            'description' => 'Container gate-in inspected and loaded onto vessel MV Maersk Nusantara.',
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
            'description' => 'Vessel departed Tanjung Priok; steaming northbound towards Singapore strait.',
            'created_by' => $operator->id,
        ]);
        $sgEvent = TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => 'ARRIVED',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Port of Singapore (Pasir Panjang Terminal)',
            'location_id' => $locationModels['SGSIN']->id,
            'latitude' => 1.29027,
            'longitude' => 103.85196,
            'event_time' => Carbon::now()->subDays(3)->setTime(18, 0),
            'description' => 'Vessel berthed for transshipment feeder transfer. Random inspection flagged.',
            'is_exception' => true,
            'created_by' => $operator->id,
        ]);

        // Scenario 1 Exception (Slide 8: CUSTOMS_HOLD, 18 hrs delay impact)
        ShipmentException::create([
            'shipment_id' => $shipment1->id,
            'tracking_event_id' => $sgEvent->id,
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => ShipmentException::SEVERITY_MEDIUM,
            'status' => ShipmentException::STATUS_OPEN,
            'description' => 'Singapore Customs authorities selected consignment for secondary non-intrusive radiological scanning.',
            'delay_impact_hours' => 18,
            'detected_at' => Carbon::now()->subDays(3)->setTime(19, 0),
        ]);

        // Scenario 2: Group B (Asia -> North America, delay threshold 18h, weather delay)
        $shipment2 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-AIR-9102'],
            [
                'customer_id' => $customer2->id,
                'carrier_id' => $carrierModels['DHL']->id,
                'origin_id' => $locationModels['CNSHA']->id,
                'destination_id' => $locationModels['USLAX']->id,
                'cargo_type' => 'PHARMACEUTICALS',
                'total_weight_kg' => 3800.00,
                'total_volume_cbm' => 14.50,
                'expected_departure' => Carbon::now()->subDays(2)->setTime(14, 0),
                'expected_arrival' => Carbon::now()->addDays(2)->setTime(22, 0),
                'actual_departure' => Carbon::now()->subDays(2)->setTime(16, 45),
                'estimated_arrival' => Carbon::now()->addDays(3)->setTime(10, 0), // Delayed +12h by weather
                'status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => true,
                'delay_hours' => 12,
                'delay_severity' => 'MEDIUM',
                'notes' => 'Vaccine active cold box containers. Dry ice replenished at Shanghai hub.',
            ]
        );

        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'item_description' => 'Sterile Diagnostic Assay Reagents',
            'quantity' => 1500,
            'weight_kg' => 3800.00,
            'hs_code' => '3002.15.00',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'event_type' => 'CREATED',
            'status' => Shipment::STATUS_CREATED,
            'location_name' => 'Shanghai Pudong Logistics Free Zone',
            'location_id' => $locationModels['CNSHA']->id,
            'latitude' => 31.2304,
            'longitude' => 121.4737,
            'event_time' => Carbon::now()->subDays(3)->setTime(8, 0),
            'description' => 'Air cargo airway bill master booked with DHL Aviation.',
            'created_by' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'event_type' => 'DEPARTED',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Shanghai Pudong International Runway',
            'location_id' => $locationModels['CNSHA']->id,
            'latitude' => 31.1443,
            'longitude' => 121.8083,
            'event_time' => Carbon::now()->subDays(2)->setTime(16, 45),
            'description' => 'Flight D0-819 departed Shanghai; route altered over North Pacific due to gale warnings.',
            'is_exception' => true,
            'created_by' => $operator->id,
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment2->id,
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => ShipmentException::SEVERITY_MEDIUM,
            'status' => ShipmentException::STATUS_INVESTIGATING,
            'description' => 'North Pacific winter cyclone forced polar routing detour, adding 12 flight and crew rest hours.',
            'delay_impact_hours' => 12,
            'detected_at' => Carbon::now()->subDays(1)->setTime(6, 0),
        ]);

        // Scenario 3: Delivered Consignment (Singapore -> Melbourne, On-Time 100% SLA)
        $shipment3 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-SEA-4412'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['ONE']->id,
                'origin_id' => $locationModels['SGSIN']->id,
                'destination_id' => $locationModels['AUMLB']->id,
                'cargo_type' => 'GENERAL_CARGO',
                'total_weight_kg' => 28400.00,
                'total_volume_cbm' => 52.00,
                'expected_departure' => Carbon::now()->subDays(12)->setTime(6, 0),
                'expected_arrival' => Carbon::now()->subDays(1)->setTime(15, 0),
                'actual_departure' => Carbon::now()->subDays(12)->setTime(6, 30),
                'actual_arrival' => Carbon::now()->subDays(1)->setTime(14, 15),
                'estimated_arrival' => Carbon::now()->subDays(1)->setTime(15, 0),
                'status' => Shipment::STATUS_DELIVERED,
                'is_delayed' => false,
                'delay_hours' => 0,
                'delay_severity' => 'NONE',
                'notes' => 'Commercial furniture and consumer retail equipment delivered safely to Port Melbourne depot.',
            ]
        );

        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'item_description' => 'Precision Ergonomic Office Workstations',
            'quantity' => 280,
            'weight_kg' => 28400.00,
            'hs_code' => '9403.10.00',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'event_type' => 'CREATED',
            'status' => Shipment::STATUS_CREATED,
            'location_name' => 'Pasir Panjang Terminal Gate',
            'location_id' => $locationModels['SGSIN']->id,
            'latitude' => 1.29027,
            'longitude' => 103.85196,
            'event_time' => Carbon::now()->subDays(13)->setTime(12, 0),
            'description' => 'Container release issued.',
            'created_by' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'event_type' => 'DEPARTED',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Singapore Strait Maritime Gate',
            'latitude' => 1.2400,
            'longitude' => 103.9500,
            'event_time' => Carbon::now()->subDays(12)->setTime(6, 30),
            'description' => 'Vessel ONE Commitment cleared port limits.',
            'created_by' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'event_type' => 'DELIVERED',
            'status' => Shipment::STATUS_DELIVERED,
            'location_name' => 'Port of Melbourne Webb Dock East',
            'location_id' => $locationModels['AUMLB']->id,
            'latitude' => -37.8136,
            'longitude' => 144.9631,
            'event_time' => Carbon::now()->subDays(1)->setTime(14, 15),
            'description' => 'Container offloaded, customs duty settled, consignee signed receipt.',
            'created_by' => $operator->id,
        ]);

        // Scenario 4: Early Stage Booking (Jakarta -> Surabaya)
        $shipment4 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-ROD-1099'],
            [
                'customer_id' => $customer2->id,
                'carrier_id' => $carrierModels['SICEPAT']->id,
                'origin_id' => $locationModels['IDJKT']->id,
                'destination_id' => $locationModels['SGSIN']->id,
                'cargo_type' => 'INDUSTRIAL_PARTS',
                'total_weight_kg' => 9500.00,
                'total_volume_cbm' => 18.00,
                'expected_departure' => Carbon::now()->addDays(2)->setTime(9, 0),
                'expected_arrival' => Carbon::now()->addDays(6)->setTime(16, 0),
                'status' => Shipment::STATUS_BOOKED,
                'is_delayed' => false,
                'delay_hours' => 0,
                'delay_severity' => 'NONE',
                'notes' => 'Hydraulic pumps and CNC tooling spares.',
            ]
        );

        TrackingEvent::create([
            'shipment_id' => $shipment4->id,
            'event_type' => 'CREATED',
            'status' => Shipment::STATUS_CREATED,
            'location_name' => 'Jakarta Marunda Logistics Park',
            'location_id' => $locationModels['IDJKT']->id,
            'latitude' => -6.1033,
            'longitude' => 106.8833,
            'event_time' => Carbon::now()->subHours(12),
            'description' => 'Intermodal dispatch order scheduled.',
            'created_by' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $shipment4->id,
            'event_type' => 'BOOKED',
            'status' => Shipment::STATUS_BOOKED,
            'location_name' => 'Port of Tanjung Priok Berth 104',
            'location_id' => $locationModels['IDJKT']->id,
            'latitude' => -6.1033,
            'longitude' => 106.8833,
            'event_time' => Carbon::now()->subHours(4),
            'description' => 'Vessel space reservation confirmed by carrier.',
            'created_by' => $operator->id,
        ]);

        // Scenario 5: Resolved Exception Case (Tokyo -> Hamburg)
        $shipment5 = Shipment::updateOrCreate(
            ['shipment_number' => 'CT-2026-SEA-7721'],
            [
                'customer_id' => $customer1->id,
                'carrier_id' => $carrierModels['HAPAG']->id,
                'origin_id' => $locationModels['JPTYO']->id,
                'destination_id' => $locationModels['DEHAM']->id,
                'cargo_type' => 'PERISHABLES',
                'total_weight_kg' => 19800.00,
                'total_volume_cbm' => 32.00,
                'expected_departure' => Carbon::now()->subDays(8)->setTime(11, 0),
                'expected_arrival' => Carbon::now()->addDays(12)->setTime(17, 0),
                'actual_departure' => Carbon::now()->subDays(8)->setTime(12, 0),
                'estimated_arrival' => Carbon::now()->addDays(12)->setTime(23, 0),
                'status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => true,
                'delay_hours' => 6,
                'delay_severity' => 'LOW',
                'notes' => 'Controlled atmosphere reefer container with premium fruit export.',
            ]
        );

        TrackingEvent::create([
            'shipment_id' => $shipment5->id,
            'event_type' => 'CREATED',
            'status' => Shipment::STATUS_CREATED,
            'location_name' => 'Tokyo Ohi Terminal',
            'location_id' => $locationModels['JPTYO']->id,
            'latitude' => 35.6528,
            'longitude' => 139.8395,
            'event_time' => Carbon::now()->subDays(9),
            'description' => 'Cold chain pre-trip inspection verified at -2C.',
            'created_by' => $operator->id,
        ]);
        TrackingEvent::create([
            'shipment_id' => $shipment5->id,
            'event_type' => 'DEPARTED',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Tokyo Bay Outer Anchorage',
            'latitude' => 35.5000,
            'longitude' => 139.9000,
            'event_time' => Carbon::now()->subDays(8),
            'description' => 'Vessel Hapag Berlin underway towards Suez canal route.',
            'created_by' => $operator->id,
        ]);

        $resolvedExc = ShipmentException::create([
            'shipment_id' => $shipment5->id,
            'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
            'severity' => ShipmentException::SEVERITY_LOW,
            'status' => ShipmentException::STATUS_RESOLVED,
            'description' => 'Initial berth congestion in Tokyo delayed departure by 6 hours.',
            'delay_impact_hours' => 6,
            'detected_at' => Carbon::now()->subDays(9)->setTime(14, 0),
            'resolved_at' => Carbon::now()->subDays(8)->setTime(10, 0),
            'resolved_by' => $operator->id,
            'resolution_notes' => 'Port authority allocated secondary gantry crane; vessel expedited loading without further backlog.',
        ]);

        // 5. Seed In-App Notifications (Slide 10)
        Notification::create([
            'user_id' => $customer1->id,
            'title' => 'Customs Hold Alert on Consignment CT-2026-SEA-8841',
            'message' => 'Singapore port authorities flagged shipment for secondary scanning. Estimated delay +18 hrs.',
            'type' => 'EXCEPTION_OPENED',
            'link' => route('shipments.show', $shipment1->id),
            'created_at' => Carbon::now()->subDays(3)->setTime(19, 5),
        ]);
        Notification::create([
            'user_id' => $customer1->id,
            'title' => 'Shipment CT-2026-SEA-4412 Successfully Delivered!',
            'message' => 'Your shipment has been received in Melbourne on schedule. POD available.',
            'type' => 'STATUS_UPDATE',
            'link' => route('shipments.show', $shipment3->id),
            'read_at' => Carbon::now()->subHours(2),
            'created_at' => Carbon::now()->subDays(1)->setTime(14, 20),
        ]);
        Notification::create([
            'user_id' => $customer1->id,
            'title' => 'Port Congestion Cleared for CT-2026-SEA-7721',
            'message' => 'Departure berth cleared; container is now underway on MV Hapag Berlin.',
            'type' => 'EXCEPTION_RESOLVED',
            'link' => route('shipments.show', $shipment5->id),
            'read_at' => Carbon::now()->subDays(8),
            'created_at' => Carbon::now()->subDays(8)->setTime(10, 5),
        ]);

        // 6. Seed Security Audit Logs (Slide 10)
        AuditLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'action' => 'SYSTEM_INIT',
            'entity_type' => 'System',
            'entity_id' => 1,
            'old_values' => null,
            'new_values' => ['message' => 'CargoTrace system initialized with baseline seeds'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder Script',
            'created_at' => Carbon::now()->subDays(14),
        ]);
        AuditLog::create([
            'user_id' => $operator->id,
            'user_name' => $operator->name,
            'action' => 'CREATE_EXCEPTION',
            'entity_type' => 'ShipmentException',
            'entity_id' => 1,
            'old_values' => null,
            'new_values' => ['shipment' => 'CT-2026-SEA-8841', 'type' => 'CUSTOMS_HOLD', 'impact_hours' => 18],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => Carbon::now()->subDays(3)->setTime(19, 0),
        ]);
    }
}
