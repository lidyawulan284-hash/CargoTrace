<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\ExternalWeatherService;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected ExternalWeatherService $weatherService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();

        $shipments = $this->shipmentService->searchShipments(
            filters: $request->all(),
            forUser: $user,
            perPage: 12
        );

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('shipments.index', compact('shipments', 'carriers', 'locations'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403, 'Unauthorized. Only logistics operators or administrators can create shipments.');
        }

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $suggestedNumber = $this->shipmentService->generateShipmentNumber();

        return view('shipments.create', compact('carriers', 'locations', 'customers', 'suggestedNumber'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403);
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
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.item_description' => ['nullable', 'string', 'max:200'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.dimensions' => ['nullable', 'string'],
            'items.*.hs_code' => ['nullable', 'string'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $user);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', "Shipment {$shipment->shipment_number} successfully registered!");
    }

    public function show(Shipment $shipment): View
    {
        $user = Auth::user();

        // Customer access control (Slide 5, 13)
        if (!$user->canManageShipments() && $shipment->customer_id !== $user->id) {
            abort(403, 'Forbidden. You do not have access to this shipment.');
        }

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.creator',
            'exceptions.resolver'
        ]);

        // Weather integration for current location (Slide 9)
        $coords = $shipment->getCurrentCoordinates();
        $weather = null;
        if ($coords) {
            $weather = $this->weatherService->getWeather(
                latitude: $coords['lat'],
                longitude: $coords['lng'],
                locationName: $coords['location']
            );
        }

        // Prepare Map GeoJSON / points array for Leaflet.js
        $mapPoints = [];

        // Origin point
        if ($shipment->origin) {
            $mapPoints[] = [
                'type' => 'origin',
                'name' => 'Origin: ' . $shipment->origin->name,
                'city' => $shipment->origin->city . ', ' . $shipment->origin->country,
                'lat' => (float) $shipment->origin->latitude,
                'lng' => (float) $shipment->origin->longitude,
                'time' => $shipment->expected_departure->format('M d, Y H:i'),
            ];
        }

        // Tracking waypoints
        foreach ($shipment->trackingEvents as $event) {
            if ($event->latitude && $event->longitude) {
                $mapPoints[] = [
                    'type' => $event->is_exception ? 'exception' : 'waypoint',
                    'name' => $event->event_type . ' - ' . $event->location_name,
                    'city' => $event->location_name,
                    'lat' => (float) $event->latitude,
                    'lng' => (float) $event->longitude,
                    'time' => $event->event_time->format('M d, Y H:i'),
                    'desc' => $event->description,
                ];
            }
        }

        // Destination point
        if ($shipment->destination) {
            $mapPoints[] = [
                'type' => 'destination',
                'name' => 'Destination: ' . $shipment->destination->name,
                'city' => $shipment->destination->city . ', ' . $shipment->destination->country,
                'lat' => (float) $shipment->destination->latitude,
                'lng' => (float) $shipment->destination->longitude,
                'time' => $shipment->expected_arrival->format('M d, Y H:i'),
            ];
        }

        $allowedTransitions = Shipment::TRANSITIONS[$shipment->status] ?? [];
        $locations = Location::orderBy('name')->get();
        $exceptionTypes = ShipmentException::TYPES;

        return view('shipments.show', compact(
            'shipment',
            'weather',
            'mapPoints',
            'allowedTransitions',
            'locations',
            'exceptionTypes'
        ));
    }

    public function edit(Shipment $shipment): View
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403);
        }

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('shipments.edit', compact('shipment', 'carriers', 'locations'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403);
        }

        $validated = $request->validate([
            'carrier_id' => ['required', 'exists:carriers,id'],
            'cargo_type' => ['required', 'string', 'max:60'],
            'total_weight_kg' => ['required', 'numeric', 'min:0'],
            'total_volume_cbm' => ['required', 'numeric', 'min:0'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date'],
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

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Shipment details updated.');
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized. Admin role required to delete shipments.');
        }

        $old = $shipment->toArray();
        $shipment->delete();

        AuditLogService::log(
            action: 'DELETE',
            entityType: 'Shipment',
            entityId: $shipment->id,
            oldValues: $old,
            newValues: null
        );

        return redirect()->route('shipments.index')
            ->with('success', "Shipment {$shipment->shipment_number} deleted successfully.");
    }
}
