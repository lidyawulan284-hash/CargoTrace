<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrackingService
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    /**
     * Add a tracking event to a shipment with strict validation and business rules.
     */
    public function addEvent(Shipment $shipment, array $data, ?User $actor = null): TrackingEvent
    {
        // Rule 1: Validate chronological order (Slide 7)
        $eventTime = Carbon::parse($data['event_time'] ?? now());
        $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();

        if ($latestEvent && $eventTime->lessThan($latestEvent->event_time)) {
            throw ValidationException::withMessages([
                'event_time' => ["Event timestamp cannot precede the most recent event ({$latestEvent->event_time->toDateTimeString()})."],
            ]);
        }

        // Rule 2: Status transition validation (Slide 6)
        $targetStatus = $data['status'] ?? $shipment->status;
        $isTransitioning = ($targetStatus !== $shipment->status);

        if ($isTransitioning) {
            if ($shipment->isTerminal()) {
                throw ValidationException::withMessages([
                    'status' => ["Cannot transition shipment '{$shipment->shipment_number}' from terminal status '{$shipment->status}'."],
                ]);
            }

            if (! $shipment->canTransitionTo($targetStatus)) {
                $allowed = implode(', ', Shipment::TRANSITIONS[$shipment->status] ?? []);
                throw ValidationException::withMessages([
                    'status' => ["Invalid status transition from '{$shipment->status}' to '{$targetStatus}'. Allowed transitions: [{$allowed}]."],
                ]);
            }
        }

        return DB::transaction(function () use ($shipment, $data, $actor, $eventTime, $targetStatus, $isTransitioning) {
            $oldStatus = $shipment->status;

            // Create tracking event
            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => $data['event_type'] ?? 'TRANSIT_UPDATE',
                'status' => $targetStatus,
                'location_name' => $data['location_name'],
                'location_id' => $data['location_id'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'event_time' => $eventTime,
                'description' => $data['description'],
                'is_exception' => $data['is_exception'] ?? false,
                'created_by' => $actor?->id,
            ]);

            // Update shipment status if changed
            if ($isTransitioning) {
                $shipment->status = $targetStatus;

                if ($targetStatus === Shipment::STATUS_IN_TRANSIT && ! $shipment->actual_departure) {
                    $shipment->actual_departure = $eventTime;
                }

                if ($targetStatus === Shipment::STATUS_DELIVERED) {
                    $shipment->actual_arrival = $eventTime;
                }

                $shipment->save();

                // Re-evaluate delay
                $this->etaService->evaluateDelay($shipment);

                // Send notification to customer
                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'title' => "Shipment {$shipment->shipment_number} Status Updated",
                    'message' => "Your shipment status is now '{$targetStatus}' at {$event->location_name}.",
                    'type' => 'STATUS_UPDATE',
                    'link' => route('shipments.show', $shipment->id),
                ]);

                // Audit log
                AuditLogService::log(
                    action: 'STATUS_TRANSITION',
                    entityType: 'Shipment',
                    entityId: $shipment->id,
                    oldValues: ['status' => $oldStatus],
                    newValues: ['status' => $targetStatus, 'location' => $event->location_name]
                );
            }

            return $event;
        });
    }
}
