<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $metrics = $this->shipmentService->getDashboardMetrics($user);

        // Recent shipments
        $recentShipments = Shipment::query()
            ->with(['carrier', 'origin', 'destination', 'customer', 'latestTrackingEvent', 'items', 'exceptions'])
            ->when(! $user->canManageShipments(), fn ($q) => $q->where('customer_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Shipments available for interactive Leaflet dashboard telematics map
        $mapShipments = Shipment::query()
            ->with(['carrier', 'origin', 'destination', 'customer', 'latestTrackingEvent', 'items'])
            ->when(! $user->canManageShipments(), fn ($q) => $q->where('customer_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        // Recent tracking activity feed
        $recentEvents = TrackingEvent::query()
            ->with(['shipment', 'creator'])
            ->when(! $user->canManageShipments(), function ($q) use ($user) {
                $q->whereHas('shipment', fn ($sq) => $sq->where('customer_id', $user->id));
            })
            ->orderBy('event_time', 'desc')
            ->take(6)
            ->get();

        // Active exceptions needing attention
        $activeExceptions = ShipmentException::query()
            ->with(['shipment'])
            ->whereIn('status', [ShipmentException::STATUS_OPEN, ShipmentException::STATUS_INVESTIGATING])
            ->when(! $user->canManageShipments(), function ($q) use ($user) {
                $q->whereHas('shipment', fn ($sq) => $sq->where('customer_id', $user->id));
            })
            ->orderBy('detected_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'metrics',
            'recentShipments',
            'mapShipments',
            'recentEvents',
            'activeExceptions'
        ));
    }
}
