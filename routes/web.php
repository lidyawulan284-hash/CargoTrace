<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - CargoTrace Platform
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. DEFAULT ROUTE: URL '/' WAJIB MENAMPILKAN HALAMAN LOGIN (TUJUAN UTAMA)
// =========================================================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login.post');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

// =========================================================================
// 2. PUBLIC SHIPMENT TRACKING ("Lacak Tanpa Login")
// =========================================================================
Route::get('/tracking', [PublicTrackingController::class, 'index'])->name('tracking.public');
Route::get('/public-tracking', [PublicTrackingController::class, 'index'])->name('home');

// =========================================================================
// 3. REGISTRATION (GUEST ONLY)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Demo One-Click Login for Fast Grading & Interviews (Slide 19)
Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('demo.login');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Slide 10)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create')->middleware('role:admin,operator');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store')->middleware('role:admin,operator');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit')->middleware('role:admin,operator');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update')->middleware('role:admin,operator');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy')->middleware('role:admin');

    // Tracking Event Submissions (Slide 7)
    Route::get('/shipments/{shipment}/coordinates', [TrackingController::class, 'coordinates'])->name('shipments.coordinates');
    Route::post('/shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('tracking.store')->middleware('role:admin,operator');

    // Exceptions & Incident Resolutions (Slide 8)
    Route::post('/shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('exceptions.store')->middleware('role:admin,operator');
    Route::patch('/exceptions/{exception}/resolve', [ExceptionController::class, 'resolve'])->name('exceptions.resolve')->middleware('role:admin,operator');

    // Carriers
    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store')->middleware('role:admin');

    // Locations
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store')->middleware('role:admin');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    // Audit Logs (Admin Only - Slide 10)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index')->middleware('role:admin');
});
