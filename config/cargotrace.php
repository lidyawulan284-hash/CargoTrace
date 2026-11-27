<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CargoTrace Global Automotive Logistics Settings
    |--------------------------------------------------------------------------
    | Deterministic business rules and group parameters for global automotive
    | Ro-Ro sea & air freight visibility.
    */

    // Default group delay threshold in hours (configurable via DELAY_THRESHOLD_HOURS)
    'delay_threshold_hours' => (int) env('DELAY_THRESHOLD_HOURS', 12),

    // Deterministic delay severity tier boundaries (hours)
    'delay_severity_thresholds' => [
        'low_max_hours' => (int) env('DELAY_LOW_MAX_HOURS', 6),
        'medium_max_hours' => (int) env('DELAY_MEDIUM_MAX_HOURS', 24),
    ],

    // External Weather API configuration
    'weather_api_url' => env('CARGOTRACE_WEATHER_API_URL', 'https://api.open-meteo.com/v1/forecast'),
    'weather_api_key' => env('CARGOTRACE_WEATHER_API_KEY', ''),
    'weather_timeout_seconds' => (int) env('CARGOTRACE_WEATHER_TIMEOUT', 4),

    // External Geocoding API configuration (OpenStreetMap Nominatim)
    'geocoding_api_url' => env('CARGOTRACE_GEOCODING_API_URL', 'https://nominatim.openstreetmap.org/search'),
    'geocoding_user_agent' => env('CARGOTRACE_GEOCODING_USER_AGENT', 'CargoTrace/1.0 (Educational Logistics Portfolio)'),
    'geocoding_timeout_seconds' => (int) env('CARGOTRACE_GEOCODING_TIMEOUT', 4),

    // Demo 1-click evaluator login toggle (disable in strict production if needed)
    'demo_login_enabled' => (bool) env('CARGOTRACE_DEMO_LOGIN_ENABLED', true),

    // Supported automotive cargo categories
    'cargo_types' => [
        'CBU_EV' => 'Completely Built-Up Electric Vehicles (CBU EV)',
        'LUXURY_CAR' => 'Luxury & Exotic Vehicles (CBU)',
        'MOTORSPORT_AIR' => 'Motorsport Prototypes & Race Cars (Air Charter)',
        'CKD_PARTS' => 'CKD Kits & EV Lithium Powertrain Components',
    ],

    // Carrier freight service modes
    'carrier_service_types' => [
        'RORO_SEA' => 'Ro-Ro Ocean Car Carrier',
        'AIR_FREIGHT' => 'Air Cargo Charter (Motorsport / VIP)',
        'SEA_FREIGHT' => 'Containerized Maritime Freight',
        'ROAD_FREIGHT' => 'Enclosed Auto Transporter',
        'MULTIMODAL' => 'Multimodal Vehicle Logistics',
    ],

    // Exception types & default automotive delay impact estimates
    'exception_defaults' => [
        'CUSTOMS_HOLD' => [
            'severity' => 'HIGH',
            'estimated_delay_hours' => 24,
            'label' => 'Customs & EU Homologation Hold',
        ],
        'PORT_CONGESTION' => [
            'severity' => 'MEDIUM',
            'estimated_delay_hours' => 14,
            'label' => 'Ro-Ro Terminal Berth Congestion',
        ],
        'WEATHER_DELAY' => [
            'severity' => 'MEDIUM',
            'estimated_delay_hours' => 18,
            'label' => 'Maritime Swell / Aviation Weather Reroute',
        ],
        'DAMAGED' => [
            'severity' => 'HIGH',
            'estimated_delay_hours' => 30,
            'label' => 'Vehicle Transit Body / Chassis Inspection',
        ],
        'MISROUTED' => [
            'severity' => 'HIGH',
            'estimated_delay_hours' => 36,
            'label' => 'Hub Transshipment Misrouting',
        ],
        'DOCUMENTATION_ISSUE' => [
            'severity' => 'LOW',
            'estimated_delay_hours' => 6,
            'label' => 'UN3480 Lithium Battery / COO Paperwork Issue',
        ],
    ],
];
