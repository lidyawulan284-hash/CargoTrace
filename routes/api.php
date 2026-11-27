<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierApiController;
use App\Http\Controllers\Api\ExceptionApiController;
use App\Http\Controllers\Api\GeocodeApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\PublicTrackingApiController;
use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\Api\TrackingApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CargoTrace Automotive REST API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Quick Tracking Lookup Endpoint (Controller-based for route:cache support)
Route::get('/public/track/{number}', [PublicTrackingApiController::class, 'show']);

// Authenticated Endpoints (Bearer Token via Laravel Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Shipments CRUD & Tracking
    Route::get('/shipments', [ShipmentApiController::class, 'index']);
    Route::post('/shipments', [ShipmentApiController::class, 'store'])->middleware('role:admin,operator');
    Route::get('/shipments/{id}', [ShipmentApiController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{id}', [ShipmentApiController::class, 'update'])->middleware('role:admin,operator');
    Route::delete('/shipments/{id}', [ShipmentApiController::class, 'destroy'])->middleware('role:admin');

    Route::get('/shipments/{id}/tracking', [TrackingApiController::class, 'index']);
    Route::post('/shipments/{id}/tracking', [TrackingApiController::class, 'store'])->middleware('role:admin,operator');

    // Exceptions
    Route::get('/shipments/{id}/exceptions', [ExceptionApiController::class, 'index']);
    Route::post('/shipments/{id}/exceptions', [ExceptionApiController::class, 'store'])->middleware('role:admin,operator');
    Route::patch('/exceptions/{id}', [ExceptionApiController::class, 'resolve'])->middleware('role:admin,operator');

    // Carriers, Locations & External Geocoding Lookup
    Route::get('/carriers', [CarrierApiController::class, 'index']);
    Route::post('/carriers', [CarrierApiController::class, 'store'])->middleware('role:admin');

    Route::get('/locations', [LocationApiController::class, 'index']);
    Route::post('/locations', [LocationApiController::class, 'store'])->middleware('role:admin');
    Route::get('/geocode', [GeocodeApiController::class, 'lookup'])->middleware('role:admin,operator');
});
