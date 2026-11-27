<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Services\ExternalWeatherService;
use App\Services\ShipmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShipmentApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected ExternalWeatherService $weatherService
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Shipment::class);
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

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->can('create', Shipment::class)) {
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
            'items.*.vehicle_model' => ['nullable', 'string', 'max:150'],
            'items.*.item_description' => ['required_without:items.*.vehicle_model', 'nullable', 'string', 'max:200'],
            'items.*.vin_number' => ['nullable', 'string', 'max:50', 'distinct', 'unique:shipment_items,vin_number'],
            'items.*.cargo_category' => ['nullable', 'string', Rule::in(ShipmentItem::CATEGORIES)],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0'],
            'items.*.declared_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.dimensions' => ['nullable', 'string', 'max:60'],
            'items.*.hs_code' => ['nullable', 'string', 'max:30'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Shipment created successfully.',
            'data' => $shipment,
        ], 201);
    }

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
            'exceptions.resolver',
        ])->find($id);

        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if (! $user->can('view', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to view this shipment.',
            ], 403);
        }

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
            'summary' => $this->shipmentService->getShipmentSummary($shipment),
            'weather' => $weather,
        ]);
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();

        $shipment = Shipment::find($id);
        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if (! $user->can('update', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
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

        $effectiveDeparture = isset($validated['expected_departure'])
            ? Carbon::parse($validated['expected_departure'])
            : Carbon::parse($shipment->expected_departure);
        $effectiveArrival = isset($validated['expected_arrival'])
            ? Carbon::parse($validated['expected_arrival'])
            : Carbon::parse($shipment->expected_arrival);

        if ($effectiveArrival->lessThanOrEqualTo($effectiveDeparture)) {
            throw ValidationException::withMessages([
                'expected_arrival' => ['The expected arrival must be a date after expected departure.'],
            ]);
        }

        $updated = $this->shipmentService->updateShipment($shipment, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipment updated successfully.',
            'data' => $updated,
        ]);
    }

    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();

        $shipment = Shipment::find($id);
        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if (! $user->can('delete', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Admin privileges required to delete shipments.',
            ], 403);
        }

        $this->shipmentService->deleteShipment($shipment);

        return response()->json([
            'success' => true,
            'message' => 'Shipment deleted successfully.',
        ]);
    }
}
