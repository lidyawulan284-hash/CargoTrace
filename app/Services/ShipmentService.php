<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShipmentService
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    /**
     * Generate unique tracking code e.g. CT-2026-8719
     */
    public function generateShipmentNumber(): string
    {
        do {
            $code = 'CT-'.date('Y').'-'.strtoupper(Str::random(4)).rand(10, 99);
        } while (Shipment::where('shipment_number', $code)->exists());

        return $code;
    }

    /**
     * Create a new shipment with items and initial tracking event.
     */
    public function createShipment(array $data, ?User $actor = null): Shipment
    {
        return DB::transaction(function () use ($data, $actor) {
            $shipmentNumber = $data['shipment_number'] ?? $this->generateShipmentNumber();

            $expectedDeparture = Carbon::parse($data['expected_departure']);
            $expectedArrival = Carbon::parse($data['expected_arrival']);
            $estimatedArrival = isset($data['estimated_arrival'])
                ? Carbon::parse($data['estimated_arrival'])
                : $expectedArrival->copy();

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $data['customer_id'],
                'carrier_id' => $data['carrier_id'],
                'origin_id' => $data['origin_id'],
                'destination_id' => $data['destination_id'],
                'cargo_type' => $data['cargo_type'] ?? 'GENERAL_CARGO',
                'total_weight_kg' => $data['total_weight_kg'] ?? 0,
                'total_volume_cbm' => $data['total_volume_cbm'] ?? 0,
                'expected_departure' => $expectedDeparture,
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $estimatedArrival,
                'status' => Shipment::STATUS_CREATED,
                'notes' => $data['notes'] ?? null,
            ]);

            // Add cargo items if provided
            if (! empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (! empty($item['item_description'])) {
                        ShipmentItem::create([
                            'shipment_id' => $shipment->id,
                            'item_description' => $item['item_description'],
                            'quantity' => $item['quantity'] ?? 1,
                            'weight_kg' => $item['weight_kg'] ?? 0,
                            'dimensions' => $item['dimensions'] ?? null,
                            'hs_code' => $item['hs_code'] ?? null,
                        ]);
                    }
                }
            }

            // Create initial tracking event at origin
            $origin = Location::find($shipment->origin_id);
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => $origin ? $origin->name : 'Origin Logistics Center',
                'location_id' => $shipment->origin_id,
                'latitude' => $origin?->latitude,
                'longitude' => $origin?->longitude,
                'event_time' => now(),
                'description' => 'Shipment booking created in CargoTrace system.',
                'created_by' => $actor?->id,
            ]);

            // Evaluate delay
            $this->etaService->evaluateDelay($shipment);

            // Audit Log
            AuditLogService::log(
                action: 'CREATE',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: null,
                newValues: ['shipment_number' => $shipment->shipment_number, 'customer_id' => $shipment->customer_id]
            );

            return $shipment->fresh(['customer', 'carrier', 'origin', 'destination', 'items', 'trackingEvents']);
        });
    }

    /**
     * Filter and search shipments with server-side pagination (Slide 10).
     */
    public function searchShipments(array $filters, ?User $forUser = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Shipment::query()
            ->with(['customer', 'carrier', 'origin', 'destination', 'latestTrackingEvent']);

        if ($forUser && ! $forUser->canManageShipments()) {
            $query->where('customer_id', $forUser->id);
        }

        // Search by shipment number or notes
        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function (Builder $q) use ($term) {
                $q->where('shipment_number', 'LIKE', "%{$term}%")
                    ->orWhere('notes', 'LIKE', "%{$term}%")
                    ->orWhereHas('customer', function (Builder $cq) use ($term) {
                        $cq->where('name', 'LIKE', "%{$term}%")
                            ->orWhere('company_name', 'LIKE', "%{$term}%");
                    });
            });
        }

        // Filter by status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by carrier
        if (! empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        // Filter by delay
        if (isset($filters['is_delayed']) && $filters['is_delayed'] !== '') {
            $query->where('is_delayed', (bool) $filters['is_delayed']);
        }

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('expected_departure', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('expected_arrival', '<=', $filters['date_to']);
        }

        // Sort
        $sortField = in_array($filters['sort_by'] ?? 'created_at', [
            'created_at', 'shipment_number', 'status', 'expected_arrival', 'delay_hours',
        ]) ? $filters['sort_by'] ?? 'created_at' : 'created_at';

        $sortDirection = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    /**
     * Calculate dashboard statistics (Slide 10).
     */
    public function getDashboardMetrics(?User $forUser = null): array
    {
        $baseQuery = Shipment::query();
        if ($forUser && ! $forUser->canManageShipments()) {
            $baseQuery->where('customer_id', $forUser->id);
        }

        $totalShipments = (clone $baseQuery)->count();
        $inTransit = (clone $baseQuery)->where('status', Shipment::STATUS_IN_TRANSIT)->count();
        $delivered = (clone $baseQuery)->where('status', Shipment::STATUS_DELIVERED)->count();
        $delayed = (clone $baseQuery)->where('is_delayed', true)->count();

        $openExceptions = ShipmentException::whereIn('status', [
            ShipmentException::STATUS_OPEN,
            ShipmentException::STATUS_INVESTIGATING,
        ])
            ->when($forUser && ! $forUser->canManageShipments(), function ($q) use ($forUser) {
                $q->whereHas('shipment', function ($sq) use ($forUser) {
                    $sq->where('customer_id', $forUser->id);
                });
            })
            ->count();

        // On-time delivery rate KPI
        $completedCount = (clone $baseQuery)->whereIn('status', [Shipment::STATUS_DELIVERED])->count();
        $onTimeCompleted = (clone $baseQuery)->where('status', Shipment::STATUS_DELIVERED)->where('is_delayed', false)->count();
        $onTimeRate = $completedCount > 0 ? round(($onTimeCompleted / $completedCount) * 100, 1) : 100.0;

        // Shipments by status for Chart.js
        $statuses = [
            Shipment::STATUS_CREATED => 0,
            Shipment::STATUS_BOOKED => 0,
            Shipment::STATUS_PICKED_UP => 0,
            Shipment::STATUS_IN_TRANSIT => 0,
            Shipment::STATUS_DELIVERED => 0,
            Shipment::STATUS_CANCELLED => 0,
        ];

        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        foreach ($statusCounts as $s => $cnt) {
            if (isset($statuses[$s])) {
                $statuses[$s] = $cnt;
            }
        }

        // Shipments by carrier for Chart.js
        $carrierCounts = Carrier::query()
            ->withCount(['shipments' => function ($q) use ($forUser) {
                if ($forUser && ! $forUser->canManageShipments()) {
                    $q->where('customer_id', $forUser->id);
                }
            }])
            ->get()
            ->map(fn ($c) => ['name' => $c->name, 'count' => $c->shipments_count]);

        return [
            'total_shipments' => $totalShipments,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'delayed' => $delayed,
            'open_exceptions' => $openExceptions,
            'on_time_rate' => $onTimeRate,
            'delay_rate' => $totalShipments > 0 ? round(($delayed / $totalShipments) * 100, 1) : 0.0,
            'status_chart' => $statuses,
            'carrier_chart' => $carrierCounts,
        ];
    }
}
