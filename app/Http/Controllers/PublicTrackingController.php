<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\ExternalWeatherService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTrackingController extends Controller
{
    public function __construct(
        protected ExternalWeatherService $weatherService
    ) {}

    public function index(Request $request): View
    {
        $trackingNumber = trim($request->get('tracking_number', ''));
        $shipment = null;
        $weather = null;
        $mapPoints = [];

        if (!empty($trackingNumber)) {
            $shipment = Shipment::with([
                'carrier',
                'origin',
                'destination',
                'items',
                'trackingEvents' => fn($q) => $q->orderBy('event_time', 'asc'),
                'exceptions' => fn($q) => $q->where('status', '!=', 'RESOLVED')
            ])->where('shipment_number', $trackingNumber)->first();

            if ($shipment) {
                $coords = $shipment->getCurrentCoordinates();
                if ($coords) {
                    $weather = $this->weatherService->getWeather(
                        latitude: $coords['lat'],
                        longitude: $coords['lng'],
                        locationName: $coords['location']
                    );
                }

                // Map points
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
            }
        }

        // Suggested sample tracking numbers from database
        $sampleShipments = Shipment::orderBy('created_at', 'desc')->take(4)->get();

        return view('welcome', compact('shipment', 'trackingNumber', 'weather', 'mapPoints', 'sampleShipments'));
    }
}
