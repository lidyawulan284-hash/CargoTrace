<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierApiController;
use App\Http\Controllers\Api\ExceptionApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\Api\TrackingApiController;
use App\Models\Shipment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CargoTrace REST API Routes (Slides 12, 13)
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Quick Tracking Lookup Endpoint (Helpful for recruiters and demo testing)
Route::get('/public/track/{number}', function (string $number) {
    $shipment = Shipment::with([
        'customer:id,name,company_name',
        'carrier:id,name,code',
        'origin:id,name,city,country,latitude,longitude',
        'destination:id,name,city,country,latitude,longitude',
        'items',
        'trackingEvents' => fn($q) => $q->orderBy('event_time', 'asc'),
        'exceptions' => fn($q) => $q->where('status', '!=', 'RESOLVED')
    ])->where('shipment_number', $number)->first();

    if (!$shipment) {
        return response()->json([
            'success' => false,
            'message' => "Shipment '{$number}' not found.",
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $shipment,
    ]);
});

// Authenticated Endpoints (Bearer Token via Laravel Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Shipments CRUD & Tracking (Slide 12)
    Route::get('/shipments', [ShipmentApiController::class, 'index']);
    Route::post('/shipments', [ShipmentApiController::class, 'store']);
    Route::get('/shipments/{id}', [ShipmentApiController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{id}', [ShipmentApiController::class, 'update']);
    Route::delete('/shipments/{id}', [ShipmentApiController::class, 'destroy']);

    Route::get('/shipments/{id}/tracking', [TrackingApiController::class, 'index']);
    Route::post('/shipments/{id}/tracking', [TrackingApiController::class, 'store']);

    // Exceptions (Slide 12)
    Route::get('/shipments/{id}/exceptions', [ExceptionApiController::class, 'index']);
    Route::post('/shipments/{id}/exceptions', [ExceptionApiController::class, 'store']);
    Route::patch('/exceptions/{id}', [ExceptionApiController::class, 'resolve']);

    // Carriers & Locations (Slide 12)
    Route::get('/carriers', [CarrierApiController::class, 'index']);
    Route::post('/carriers', [CarrierApiController::class, 'store']);

    Route::get('/locations', [LocationApiController::class, 'index']);
    Route::post('/locations', [LocationApiController::class, 'store']);
});
