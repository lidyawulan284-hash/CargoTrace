<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function coordinates(Request $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('view', $shipment);

        $shipment->load(['origin', 'destination', 'latestTrackingEvent', 'carrier', 'customer', 'items']);
        $events = $shipment->trackingEvents()
            ->with(['location', 'creator'])
            ->orderBy('event_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'status' => $shipment->status,
                'carrier' => $shipment->carrier?->name,
                'carrier_code' => $shipment->carrier?->code,
                'service_type' => $shipment->carrier?->service_type,
                'customer' => $shipment->customer?->company_name ?? $shipment->customer?->name,
                'cargo_type' => $shipment->cargo_type,
                'vehicle_count' => (int) $shipment->items->sum('quantity'),
                'is_delayed' => (bool) $shipment->is_delayed,
                'delay_hours' => (int) $shipment->delay_hours,
                'delay_severity' => $shipment->delay_severity,
                'expected_departure' => $shipment->expected_departure?->format('M d, Y H:i'),
                'expected_arrival' => $shipment->expected_arrival?->format('M d, Y H:i'),
                'estimated_arrival' => $shipment->estimated_arrival?->format('M d, Y H:i'),
                'origin' => [
                    'name' => $shipment->origin?->name,
                    'city' => $shipment->origin?->city,
                    'country' => $shipment->origin?->country,
                    'lat' => (float) $shipment->origin?->latitude,
                    'lng' => (float) $shipment->origin?->longitude,
                ],
                'destination' => [
                    'name' => $shipment->destination?->name,
                    'city' => $shipment->destination?->city,
                    'country' => $shipment->destination?->country,
                    'lat' => (float) $shipment->destination?->latitude,
                    'lng' => (float) $shipment->destination?->longitude,
                ],
                'current' => $shipment->getCurrentCoordinates(),
                'events' => $events->map(fn ($e) => [
                    'id' => $e->id,
                    'location_name' => $e->location_name,
                    'status' => $e->status,
                    'event_type' => $e->event_type,
                    'event_time' => $e->event_time->format('M d, Y H:i'),
                    'description' => $e->description,
                    'is_exception' => (bool) $e->is_exception,
                    'lat' => $e->latitude !== null ? (float) $e->latitude : null,
                    'lng' => $e->longitude !== null ? (float) $e->longitude : null,
                ]),
            ],
        ]);
    }

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        Gate::authorize('update', $shipment);
        $user = Auth::user();

        $validated = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED,CANCELLED'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['required', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'event_time' => ['required', 'date'],
            'description' => ['required', 'string'],
            'is_exception' => ['nullable', 'boolean'],
        ]);

        $validated['is_exception'] = $request->boolean('is_exception');

        if ((empty($validated['latitude']) || empty($validated['longitude'])) && ! empty($validated['location_id'])) {
            $loc = Location::find($validated['location_id']);
            if ($loc) {
                $validated['latitude'] = $loc->latitude;
                $validated['longitude'] = $loc->longitude;
            }
        }

        $this->trackingService->addEvent($shipment, $validated, $user);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', __('ui.flash.checkpoint_logged'));
    }
}
