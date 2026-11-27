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
     * Append a historical tracking event to a shipment with strict validation and business rules.
     */
    public function addEvent(Shipment $shipment, array $data, ?User $actor = null): TrackingEvent
    {
        $eventTime = Carbon::parse($data['event_time'] ?? now());
        $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();

        // Rule 1: Enforce strictly chronological append-only timeline
        if ($latestEvent && $eventTime->lessThan($latestEvent->event_time)) {
            throw ValidationException::withMessages([
                'event_time' => ["Event timestamp cannot precede the most recent event ({$latestEvent->event_time->toDateTimeString()})."],
            ]);
        }

        // Rule 2: Enforce finite state machine transitions & immutable terminal states
        $targetStatus = $data['status'] ?? $shipment->status;
        $isTransitioning = ($targetStatus !== $shipment->status);

        if ($shipment->isTerminal()) {
            throw ValidationException::withMessages([
                'status' => ["Cannot modify or revert shipment '{$shipment->shipment_number}' from terminal status '{$shipment->status}'."],
            ]);
        }

        if ($isTransitioning && ! $shipment->canTransitionTo($targetStatus)) {
            $allowed = implode(', ', Shipment::TRANSITIONS[$shipment->status] ?? []);
            throw ValidationException::withMessages([
                'status' => ["Invalid status transition from '{$shipment->status}' to '{$targetStatus}'. Allowed transitions: [{$allowed}]."],
            ]);
        }

        return DB::transaction(function () use ($shipment, $data, $actor, $eventTime, $targetStatus, $isTransitioning) {
            $oldStatus = $shipment->status;

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
                'is_exception' => (bool) ($data['is_exception'] ?? false),
                'created_by' => $actor?->id,
            ]);

            if ($isTransitioning) {
                $shipment->status = $targetStatus;

                if ($targetStatus === Shipment::STATUS_IN_TRANSIT && ! $shipment->actual_departure) {
                    $shipment->actual_departure = $eventTime;
                }

                if ($targetStatus === Shipment::STATUS_DELIVERED) {
                    $shipment->actual_arrival = $eventTime;
                }

                $shipment->save();

                $this->etaService->evaluateDelay($shipment);

                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'title' => "Vehicle Shipment {$shipment->shipment_number} Status Updated",
                    'message' => "Your consignment status transitioned to '{$targetStatus}' at {$event->location_name}.",
                    'type' => 'STATUS_UPDATE',
                    'link' => route('shipments.show', $shipment->id),
                ]);

                AuditLogService::log(
                    action: 'STATUS_TRANSITION',
                    entityType: 'Shipment',
                    entityId: $shipment->id,
                    oldValues: ['status' => $oldStatus],
                    newValues: ['status' => $targetStatus, 'location' => $event->location_name],
                    actor: $actor
                );
            }

            AuditLogService::log(
                action: 'CREATE_TRACKING_EVENT',
                entityType: 'TrackingEvent',
                entityId: $event->id,
                oldValues: null,
                newValues: [
                    'shipment_number' => $shipment->shipment_number,
                    'event_type' => $event->event_type,
                    'status' => $event->status,
                    'location_name' => $event->location_name,
                    'event_time' => $event->event_time->toIso8601String(),
                ],
                actor: $actor
            );

            return $event;
        });
    }
}
