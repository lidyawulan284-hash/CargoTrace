<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingApiController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    /**
     * Get tracking history and timeline for a shipment (Slide 12)
     */
    public function index(Request $request, int|string $shipmentId): JsonResponse
    {
        $user = $request->user();

        $shipment = Shipment::with(['origin', 'destination'])->find($shipmentId);
        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if (! $user->canManageShipments() && $shipment->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        }

        $events = $shipment->trackingEvents()
            ->with(['location', 'creator'])
            ->orderBy('event_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shipment_number' => $shipment->shipment_number,
                'current_status' => $shipment->status,
                'is_delayed' => $shipment->is_delayed,
                'delay_hours' => $shipment->delay_hours,
                'delay_severity' => $shipment->delay_severity,
                'origin' => [
                    'id' => $shipment->origin?->id,
                    'name' => $shipment->origin?->name,
                    'city' => $shipment->origin?->city,
                    'country' => $shipment->origin?->country,
                    'latitude' => (float) $shipment->origin?->latitude,
                    'longitude' => (float) $shipment->origin?->longitude,
                ],
                'destination' => [
                    'id' => $shipment->destination?->id,
                    'name' => $shipment->destination?->name,
                    'city' => $shipment->destination?->city,
                    'country' => $shipment->destination?->country,
                    'latitude' => (float) $shipment->destination?->latitude,
                    'longitude' => (float) $shipment->destination?->longitude,
                ],
                'current_coordinates' => $shipment->getCurrentCoordinates(),
                'events' => $events,
            ],
        ]);
    }

    /**
     * Record a new tracking event (Slide 12)
     */
    public function store(Request $request, int|string $shipmentId): JsonResponse
    {
        $user = $request->user();
        if (! $user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only Operators and Admins can add tracking events.',
            ], 403);
        }

        $shipment = Shipment::find($shipmentId);
        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        $validated = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'location_name' => ['required', 'string', 'max:150'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'event_time' => ['required', 'date'],
            'description' => ['required', 'string'],
            'is_exception' => ['nullable', 'boolean'],
        ]);

        $event = $this->trackingService->addEvent($shipment, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Tracking event logged successfully.',
            'data' => $event,
        ], 201);
    }
}
