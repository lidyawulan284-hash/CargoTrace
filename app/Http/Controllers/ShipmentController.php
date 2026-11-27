<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\User;
use App\Services\ExternalWeatherService;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected ExternalWeatherService $weatherService
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Shipment::class);
        $user = Auth::user();

        $shipments = $this->shipmentService->searchShipments(
            filters: $request->all(),
            forUser: $user,
            perPage: 12
        );

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        $statsQuery = Shipment::query();
        if (! $user->canManageShipments()) {
            $statsQuery->where('customer_id', $user->id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'booked' => (clone $statsQuery)->whereIn('status', [Shipment::STATUS_CREATED, Shipment::STATUS_BOOKED])->count(),
            'in_transit' => (clone $statsQuery)->whereIn('status', [Shipment::STATUS_PICKED_UP, Shipment::STATUS_IN_TRANSIT])->count(),
            'delivered' => (clone $statsQuery)->where('status', Shipment::STATUS_DELIVERED)->count(),
            'exceptions' => (clone $statsQuery)->whereHas('exceptions', fn ($q) => $q->whereIn('status', [
                ShipmentException::STATUS_OPEN,
                ShipmentException::STATUS_INVESTIGATING,
            ]))->count(),
        ];

        return view('shipments.index', compact('shipments', 'carriers', 'locations', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('create', Shipment::class);

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $suggestedNumber = $this->shipmentService->generateShipmentNumber();

        return view('shipments.create', compact('carriers', 'locations', 'customers', 'suggestedNumber'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Shipment::class);
        $user = Auth::user();

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
            'items.*.vehicle_model' => ['nullable', 'string', 'max:150'],
            'items.*.item_description' => ['nullable', 'string', 'max:200'],
            'items.*.vin_number' => ['nullable', 'string', 'max:50', 'distinct', 'unique:shipment_items,vin_number'],
            'items.*.cargo_category' => ['nullable', 'string', Rule::in(ShipmentItem::CATEGORIES)],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.declared_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.dimensions' => ['nullable', 'string', 'max:60'],
            'items.*.hs_code' => ['nullable', 'string', 'max:30'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $user);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', __('ui.flash.shipment_created', ['number' => $shipment->shipment_number]));
    }

    public function show(Shipment $shipment): View
    {
        Gate::authorize('view', $shipment);

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.creator',
            'exceptions.resolver',
        ]);

        $summary = $this->shipmentService->getShipmentSummary($shipment);

        $coords = $shipment->getCurrentCoordinates();
        $weather = null;
        if ($coords) {
            $weather = $this->weatherService->getWeather(
                latitude: $coords['lat'],
                longitude: $coords['lng'],
                locationName: $coords['location']
            );
        }

        $mapPoints = [];
        if ($shipment->origin) {
            $mapPoints[] = [
                'type' => 'origin',
                'name' => 'Origin: '.$shipment->origin->name,
                'city' => $shipment->origin->city.', '.$shipment->origin->country,
                'status' => 'ORIGIN_HUB',
                'lat' => (float) $shipment->origin->latitude,
                'lng' => (float) $shipment->origin->longitude,
                'time' => $shipment->expected_departure->format('M d, Y H:i'),
            ];
        }

        foreach ($shipment->trackingEvents as $event) {
            if ($event->latitude && $event->longitude) {
                $mapPoints[] = [
                    'type' => $event->is_exception ? 'exception' : 'waypoint',
                    'name' => $event->event_type.' - '.$event->location_name,
                    'city' => $event->location_name,
                    'status' => $event->status,
                    'lat' => (float) $event->latitude,
                    'lng' => (float) $event->longitude,
                    'time' => $event->event_time->format('M d, Y H:i'),
                    'desc' => $event->description,
                ];
            }
        }

        if ($shipment->destination) {
            $mapPoints[] = [
                'type' => 'destination',
                'name' => 'Destination: '.$shipment->destination->name,
                'city' => $shipment->destination->city.', '.$shipment->destination->country,
                'status' => 'DESTINATION_HUB',
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
            'summary',
            'weather',
            'mapPoints',
            'allowedTransitions',
            'locations',
            'exceptionTypes'
        ));
    }

    public function edit(Shipment $shipment): View
    {
        Gate::authorize('update', $shipment);

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('shipments.edit', compact('shipment', 'carriers', 'locations'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        Gate::authorize('update', $shipment);

        $validated = $request->validate([
            'carrier_id' => ['required', 'exists:carriers,id'],
            'cargo_type' => ['required', 'string', 'max:60'],
            'total_weight_kg' => ['required', 'numeric', 'min:0'],
            'total_volume_cbm' => ['required', 'numeric', 'min:0'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->shipmentService->updateShipment($shipment, $validated);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', __('ui.flash.shipment_updated'));
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        Gate::authorize('delete', $shipment);

        $number = $shipment->shipment_number;
        $this->shipmentService->deleteShipment($shipment);

        return redirect()->route('shipments.index')
            ->with('success', __('ui.flash.shipment_deleted', ['number' => $number]));
    }
}
