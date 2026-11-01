<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403);
        }

        $validated = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['required', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'event_time' => ['required', 'date'],
            'description' => ['required', 'string'],
        ]);

        // Auto-fill coordinates from location if not explicitly provided
        if ((empty($validated['latitude']) || empty($validated['longitude'])) && !empty($validated['location_id'])) {
            $loc = Location::find($validated['location_id']);
            if ($loc) {
                $validated['latitude'] = $loc->latitude;
                $validated['longitude'] = $loc->longitude;
            }
        }

        $this->trackingService->addEvent($shipment, $validated, $user);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', 'Tracking checkpoint logged successfully.');
    }
}
