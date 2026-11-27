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
     * Create a new vehicle shipment with cargo items and initial origin tracking event.
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

            $items = is_array($data['items'] ?? null) ? $data['items'] : [];
            $computedWeight = collect($items)->sum(fn ($item) => (float) ($item['weight_kg'] ?? 0));
            $totalWeight = (float) ($data['total_weight_kg'] ?? 0);
            if ($totalWeight <= 0 && $computedWeight > 0) {
                $totalWeight = $computedWeight;
            }

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $data['customer_id'],
                'carrier_id' => $data['carrier_id'],
                'origin_id' => $data['origin_id'],
                'destination_id' => $data['destination_id'],
                'cargo_type' => $data['cargo_type'] ?? ShipmentItem::CATEGORY_CBU_EV,
                'total_weight_kg' => $totalWeight,
                'total_volume_cbm' => $data['total_volume_cbm'] ?? 0,
                'expected_departure' => $expectedDeparture,
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $estimatedArrival,
                'status' => Shipment::STATUS_CREATED,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $vehicleModel = $item['vehicle_model'] ?? null;
                $description = $item['item_description'] ?? $vehicleModel;
                if (! empty($description)) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'item_description' => $description,
                        'vehicle_model' => $vehicleModel ?? $description,
                        'vin_number' => ! empty($item['vin_number']) ? strtoupper(trim($item['vin_number'])) : null,
                        'cargo_category' => $item['cargo_category'] ?? ($data['cargo_type'] ?? ShipmentItem::CATEGORY_CBU_EV),
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'weight_kg' => (float) ($item['weight_kg'] ?? 0),
                        'declared_value' => (float) ($item['declared_value'] ?? 0),
                        'dimensions' => $item['dimensions'] ?? null,
                        'hs_code' => $item['hs_code'] ?? null,
                    ]);
                }
            }

            $origin = Location::find($shipment->origin_id);
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => 'CREATED',
                'status' => Shipment::STATUS_CREATED,
                'location_name' => $origin ? $origin->name : 'Origin Automotive Terminal',
                'location_id' => $shipment->origin_id,
                'latitude' => $origin?->latitude,
                'longitude' => $origin?->longitude,
                'event_time' => now(),
                'description' => 'Vehicle shipment booking created and manifest registered in CargoTrace.',
                'created_by' => $actor?->id,
            ]);

            $this->etaService->evaluateDelay($shipment);

            AuditLogService::log(
                action: 'CREATE',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: null,
                newValues: ['shipment_number' => $shipment->shipment_number, 'customer_id' => $shipment->customer_id],
                actor: $actor
            );

            return $shipment->fresh(['customer', 'carrier', 'origin', 'destination', 'items', 'trackingEvents']);
        });
    }

    /**
     * Update shipment metadata and recalculate ETA metrics.
     */
    public function updateShipment(Shipment $shipment, array $data): Shipment
    {
        return DB::transaction(function () use ($shipment, $data) {
            $oldValues = $shipment->only([
                'carrier_id', 'cargo_type', 'total_weight_kg', 'total_volume_cbm',
                'expected_departure', 'expected_arrival', 'notes',
            ]);

            $shipment->update($data);
            $this->etaService->evaluateDelay($shipment);

            AuditLogService::log(
                action: 'UPDATE',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: $oldValues,
                newValues: $shipment->only(array_keys($oldValues))
            );

            return $shipment->fresh(['customer', 'carrier', 'origin', 'destination', 'items']);
        });
    }

    /**
     * Soft-delete a shipment and record governance audit trail.
     */
    public function deleteShipment(Shipment $shipment): void
    {
        DB::transaction(function () use ($shipment) {
            $oldValues = $shipment->only(['id', 'shipment_number', 'status', 'customer_id']);
            $shipment->delete();

            AuditLogService::log(
                action: 'DELETE',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: $oldValues,
                newValues: null
            );
        });
    }

    /**
     * Calculate vehicle manifest summary and delay evaluation for a shipment.
     *
     * @return array<string, mixed>
     */
    public function getShipmentSummary(Shipment $shipment): array
    {
        $shipment->loadMissing(['items', 'exceptions', 'latestTrackingEvent', 'origin']);
        $delayEvaluation = $this->etaService->explainDelay($shipment);
        $currentCoords = $shipment->getCurrentCoordinates();

        return [
            'total_vehicles' => (int) $shipment->items->sum('quantity'),
            'total_declared_value' => (float) $shipment->items->sum('declared_value'),
            'total_weight_kg' => (float) ($shipment->total_weight_kg ?: $shipment->items->sum('weight_kg')),
            'open_exceptions_count' => $shipment->exceptions->whereIn('status', [
                ShipmentException::STATUS_OPEN,
                ShipmentException::STATUS_INVESTIGATING,
            ])->count(),
            'current_location' => $currentCoords['location'] ?? $shipment->origin?->name,
            'sla_threshold_hours' => $delayEvaluation['sla_threshold_hours'],
            'exceeds_sla_threshold' => $delayEvaluation['exceeds_sla_threshold'],
            'delay_reason' => $delayEvaluation['reason'],
            'delay_evaluation' => $delayEvaluation,
        ];
    }

    /**
     * Filter and search shipments with server-side pagination.
     */
    public function searchShipments(array $filters, ?User $forUser = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Shipment::query()
            ->with(['customer', 'carrier', 'origin', 'destination', 'latestTrackingEvent', 'items']);

        if ($forUser && ! $forUser->canManageShipments()) {
            $query->where('customer_id', $forUser->id);
        }

        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function (Builder $q) use ($term) {
                $q->where('shipment_number', 'LIKE', "%{$term}%")
                    ->orWhere('notes', 'LIKE', "%{$term}%")
                    ->orWhereHas('customer', function (Builder $cq) use ($term) {
                        $cq->where('name', 'LIKE', "%{$term}%")
                            ->orWhere('company_name', 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('items', function (Builder $iq) use ($term) {
                        $iq->where('vehicle_model', 'LIKE', "%{$term}%")
                            ->orWhere('vin_number', 'LIKE', "%{$term}%")
                            ->orWhere('item_description', 'LIKE', "%{$term}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        if (! empty($filters['origin_id'])) {
            $query->where('origin_id', $filters['origin_id']);
        }

        if (! empty($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        if (isset($filters['is_delayed']) && $filters['is_delayed'] !== '') {
            $query->where('is_delayed', (bool) $filters['is_delayed']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('expected_departure', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('expected_arrival', '<=', $filters['date_to']);
        }

        $sortField = in_array($filters['sort_by'] ?? 'created_at', [
            'created_at', 'shipment_number', 'status', 'expected_arrival', 'delay_hours',
        ], true) ? ($filters['sort_by'] ?? 'created_at') : 'created_at';

        $sortDirection = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage)->withQueryString();
    }

    /**
     * Calculate live dashboard KPI statistics directly from database queries.
     *
     * @return array<string, mixed>
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

        $completedCount = (clone $baseQuery)->where('status', Shipment::STATUS_DELIVERED)->count();
        $onTimeCompleted = (clone $baseQuery)->where('status', Shipment::STATUS_DELIVERED)->where('is_delayed', false)->count();
        $onTimeRate = $completedCount > 0 ? round(($onTimeCompleted / $completedCount) * 100, 1) : 100.0;

        $totalVehicles = (int) ShipmentItem::query()
            ->when($forUser && ! $forUser->canManageShipments(), function ($q) use ($forUser) {
                $q->whereHas('shipment', fn ($sq) => $sq->where('customer_id', $forUser->id));
            })
            ->sum('quantity');

        $totalDeclaredValue = (float) ShipmentItem::query()
            ->when($forUser && ! $forUser->canManageShipments(), function ($q) use ($forUser) {
                $q->whereHas('shipment', fn ($sq) => $sq->where('customer_id', $forUser->id));
            })
            ->sum('declared_value');

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
                $statuses[$s] = (int) $cnt;
            }
        }

        $carrierCounts = Carrier::query()
            ->withCount(['shipments' => function ($q) use ($forUser) {
                if ($forUser && ! $forUser->canManageShipments()) {
                    $q->where('customer_id', $forUser->id);
                }
            }])
            ->get()
            ->map(fn ($c) => ['name' => $c->name, 'count' => $c->shipments_count]);

        $shipmentsWithExceptions = (clone $baseQuery)->whereHas('exceptions')->count();
        $exceptionRate = $totalShipments > 0 ? round(($shipmentsWithExceptions / $totalShipments) * 100, 1) : 0.0;

        return [
            'total_shipments' => $totalShipments,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'delayed' => $delayed,
            'open_exceptions' => $openExceptions,
            'on_time_rate' => $onTimeRate,
            'delay_rate' => $totalShipments > 0 ? round(($delayed / $totalShipments) * 100, 1) : 0.0,
            'exception_rate' => $exceptionRate,
            'total_vehicles' => $totalVehicles,
            'total_declared_value' => $totalDeclaredValue,
            'status_chart' => $statuses,
            'carrier_chart' => $carrierCounts,
        ];
    }
}
