<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\AuditLogService;
use App\Services\ExternalWeatherService;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected ExternalWeatherService $weatherService
    ) {}

    /**
     * List shipments with server-side filtering, sorting and pagination (Slide 12, 13)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $shipments = $this->shipmentService->searchShipments(
            filters: $request->all(),
            forUser: $user,
            perPage: min((int) ($request->get('per_page', 15)), 100)
        );

        return response()->json([
            'success' => true,
            'data' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ]);
    }

    /**
     * Store new shipment (Slide 12)
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Operators or Admins can create shipments.',
            ], 403);
        }

        $validated = $request->validate([
            'shipment_number' => ['nullable', 'string', 'max:50', 'unique:shipments,shipment_number'],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'exists:locations,id'],
            'destination_id' => ['required', 'different:origin_id', 'exists:locations,id'],
            'cargo_type' => ['required', 'string', 'max:60'],
            'total_weight_kg' => ['required', 'numeric', 'min:0'],
            'total_volume_cbm' => ['required', 'numeric', 'min:0'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_arrival' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.item_description' => ['required', 'string', 'max:200'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0'],
            'items.*.dimensions' => ['nullable', 'string'],
            'items.*.hs_code' => ['nullable', 'string'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Shipment created successfully.',
            'data' => $shipment,
        ], 201);
    }

    /**
     * Get shipment details with tracking events, items, and weather data (Slide 12)
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();

        $shipment = Shipment::with([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.creator',
            'exceptions.resolver'
        ])->find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        // Authorization check (Slide 13)
        if (!$user->canManageShipments() && $shipment->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to view this shipment.',
            ], 403);
        }

        // Fetch live weather for current coordinates (Slide 9)
        $coords = $shipment->getCurrentCoordinates();
        $weather = null;
        if ($coords) {
            $weather = $this->weatherService->getWeather(
                latitude: $coords['lat'],
                longitude: $coords['lng'],
                locationName: $coords['location']
            );
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
            'weather' => $weather,
        ]);
    }

    /**
     * Update shipment (Slide 12)
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        $validated = $request->validate([
            'carrier_id' => ['sometimes', 'exists:carriers,id'],
            'cargo_type' => ['sometimes', 'string', 'max:60'],
            'total_weight_kg' => ['sometimes', 'numeric', 'min:0'],
            'total_volume_cbm' => ['sometimes', 'numeric', 'min:0'],
            'expected_departure' => ['sometimes', 'date'],
            'expected_arrival' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $shipment->toArray();
        $shipment->update($validated);

        AuditLogService::log(
            action: 'UPDATE',
            entityType: 'Shipment',
            entityId: $shipment->id,
            oldValues: $old,
            newValues: $shipment->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Shipment updated successfully.',
            'data' => $shipment,
        ]);
    }

    /**
     * Delete shipment (Slide 12 - Admin only)
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Admin privileges required to delete shipments.',
            ], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        $old = $shipment->toArray();
        $shipment->delete();

        AuditLogService::log(
            action: 'DELETE',
            entityType: 'Shipment',
            entityId: (int)$id,
            oldValues: $old,
            newValues: null
        );

        return response()->json([
            'success' => true,
            'message' => 'Shipment deleted successfully.',
        ]);
    }
}
