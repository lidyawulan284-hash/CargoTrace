<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CargoTrace Global Settings
    |--------------------------------------------------------------------------
    | Deterministic business rules and group scenarios (Slides 8, 20, 23)
    */

    // Default delay threshold in hours before marking shipment as heavily delayed
    'delay_threshold_hours' => (int) env('DELAY_THRESHOLD_HOURS', 18),

    // Weather API endpoint
    'weather_api_url' => env('CARGOTRACE_WEATHER_API_URL', 'https://api.open-meteo.com/v1/forecast'),

    // Supported cargo types
    'cargo_types' => [
        'ELECTRONICS' => 'High-Tech Electronics & Semiconductors',
        'PERISHABLES' => 'Cold Chain & Perishables',
        'INDUSTRIAL_PARTS' => 'Industrial Machinery & Automotive',
        'GENERAL_CARGO' => 'General Dry Cargo',
        'TEXTILES' => 'Apparel & Textiles',
        'PHARMACEUTICALS' => 'Pharmaceuticals & Medical Supplies',
    ],

    // Exception types & default delay impact estimates
    'exception_defaults' => [
        'CUSTOMS_HOLD' => [
            'severity' => 'HIGH',
            'estimated_delay_hours' => 24,
            'label' => 'Customs Clearance Inspection Hold',
        ],
        'WEATHER_DELAY' => [
            'severity' => 'MEDIUM',
            'estimated_delay_hours' => 18,
            'label' => 'Severe Weather / Typhoon Alert',
        ],
        'PORT_CONGESTION' => [
            'severity' => 'MEDIUM',
            'estimated_delay_hours' => 12,
            'label' => 'Berth Congestion & Container Yard Delay',
        ],
        'DAMAGED' => [
            'severity' => 'HIGH',
            'estimated_delay_hours' => 36,
            'label' => 'Cargo Damage Inspection Protocol',
        ],
        'MISROUTED' => [
            'severity' => 'HIGH',
            'estimated_delay_hours' => 48,
            'label' => 'Intermodal Misrouting & Re-dispatch',
        ],
        'DOCUMENTATION_ISSUE' => [
            'severity' => 'LOW',
            'estimated_delay_hours' => 8,
            'label' => 'Bill of Lading / Invoice Discrepancy',
        ],
    ],
];
