<?php

use App\Http\Controllers\Api\GeocodeApiController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - CargoTrace Automotive Logistics Platform
|--------------------------------------------------------------------------
*/

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login.post');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/tracking', [PublicTrackingController::class, 'index'])->name('tracking.public');
Route::get('/public-tracking', [PublicTrackingController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('demo.login');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create')->middleware('role:admin,operator');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store')->middleware('role:admin,operator');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit')->middleware('role:admin,operator');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update')->middleware('role:admin,operator');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy')->middleware('role:admin');

    Route::get('/shipments/{shipment}/coordinates', [TrackingController::class, 'coordinates'])->name('shipments.coordinates');
    Route::post('/shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('tracking.store')->middleware('role:admin,operator');

    Route::post('/shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('exceptions.store')->middleware('role:admin,operator');
    Route::patch('/exceptions/{exception}/investigate', [ExceptionController::class, 'investigate'])->name('exceptions.investigate')->middleware('role:admin,operator');
    Route::patch('/exceptions/{exception}/resolve', [ExceptionController::class, 'resolve'])->name('exceptions.resolve')->middleware('role:admin,operator');

    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store')->middleware('role:admin');
    Route::patch('/carriers/{carrier}', [CarrierController::class, 'update'])->name('carriers.update')->middleware('role:admin');
    Route::delete('/carriers/{carrier}', [CarrierController::class, 'destroy'])->name('carriers.destroy')->middleware('role:admin');

    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store')->middleware('role:admin');
    Route::patch('/locations/{location}', [LocationController::class, 'update'])->name('locations.update')->middleware('role:admin');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy')->middleware('role:admin');
    Route::get('/geocode', [GeocodeApiController::class, 'lookup'])->name('geocode.lookup')->middleware('role:admin,operator');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    // Admin Governance: Users & Audit Logs
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('role:admin');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('role:admin');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('role:admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('role:admin');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index')->middleware('role:admin');
    Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit_logs.export')->middleware('role:admin');

    // System & Account Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::patch('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
});
