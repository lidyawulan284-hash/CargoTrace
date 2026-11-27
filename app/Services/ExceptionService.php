<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExceptionService
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    /**
     * Record a new automotive shipment exception (OPEN) and update shipment ETA.
     */
    public function createException(Shipment $shipment, array $data, ?User $actor = null): ShipmentException
    {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $impactHours = (int) ($data['delay_impact_hours'] ?? 0);

            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'tracking_event_id' => $data['tracking_event_id'] ?? null,
                'exception_type' => $data['exception_type'],
                'severity' => $data['severity'] ?? 'MEDIUM',
                'status' => ShipmentException::STATUS_OPEN,
                'description' => $data['description'],
                'delay_impact_hours' => $impactHours,
                'detected_at' => $data['detected_at'] ?? now(),
            ]);

            if ($impactHours > 0) {
                $this->etaService->applyExceptionDelay($shipment, $impactHours, $data['description']);
            } else {
                $this->etaService->evaluateDelay($shipment);
            }

            Notification::create([
                'user_id' => $shipment->customer_id,
                'title' => "Vehicle Transit Exception: {$exception->exception_type}",
                'message' => "Incident flagged on {$shipment->shipment_number}: {$exception->description} (+{$impactHours}h ETA impact).",
                'type' => 'EXCEPTION_OPENED',
                'link' => route('shipments.show', $shipment->id),
            ]);

            AuditLogService::log(
                action: 'CREATE_EXCEPTION',
                entityType: 'ShipmentException',
                entityId: $exception->id,
                oldValues: null,
                newValues: [
                    'shipment_number' => $shipment->shipment_number,
                    'type' => $exception->exception_type,
                    'severity' => $exception->severity,
                    'status' => ShipmentException::STATUS_OPEN,
                    'impact_hours' => $impactHours,
                ],
                actor: $actor
            );

            return $exception;
        });
    }

    /**
     * Transition an OPEN exception to INVESTIGATING.
     */
    public function investigateException(ShipmentException $exception, ?string $notes = null, ?User $actor = null): ShipmentException
    {
        if ($exception->status !== ShipmentException::STATUS_OPEN) {
            throw ValidationException::withMessages([
                'status' => ["Only OPEN exceptions can move to INVESTIGATING (current: {$exception->status})."],
            ]);
        }

        return DB::transaction(function () use ($exception, $notes, $actor) {
            $oldStatus = $exception->status;

            $exception->update([
                'status' => ShipmentException::STATUS_INVESTIGATING,
                'resolution_notes' => $notes ?: $exception->resolution_notes,
            ]);

            $shipment = $exception->shipment;

            Notification::create([
                'user_id' => $shipment->customer_id,
                'title' => "Exception Under Investigation: {$shipment->shipment_number}",
                'message' => "Operations team is actively investigating {$exception->exception_type} on your consignment.",
                'type' => 'EXCEPTION_INVESTIGATING',
                'link' => route('shipments.show', $shipment->id),
            ]);

            AuditLogService::log(
                action: 'INVESTIGATE_EXCEPTION',
                entityType: 'ShipmentException',
                entityId: $exception->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => ShipmentException::STATUS_INVESTIGATING, 'notes' => $notes],
                actor: $actor
            );

            return $exception->fresh();
        });
    }

    /**
     * Resolve an OPEN or INVESTIGATING exception.
     */
    public function resolveException(ShipmentException $exception, array $data, ?User $actor = null): ShipmentException
    {
        if ($exception->isResolved()) {
            throw ValidationException::withMessages([
                'status' => ['This exception has already been marked as resolved.'],
            ]);
        }

        return DB::transaction(function () use ($exception, $data, $actor) {
            $oldStatus = $exception->status;

            $exception->update([
                'status' => ShipmentException::STATUS_RESOLVED,
                'resolved_at' => now(),
                'resolved_by' => $actor?->id,
                'resolution_notes' => $data['resolution_notes'] ?? 'Exception cleared by automotive operations team.',
            ]);

            $shipment = $exception->shipment;
            $this->etaService->evaluateDelay($shipment);

            Notification::create([
                'user_id' => $shipment->customer_id,
                'title' => "Exception Resolved for {$shipment->shipment_number}",
                'message' => "The incident ({$exception->exception_type}) has been resolved: {$exception->resolution_notes}",
                'type' => 'EXCEPTION_RESOLVED',
                'link' => route('shipments.show', $shipment->id),
            ]);

            AuditLogService::log(
                action: 'RESOLVE_EXCEPTION',
                entityType: 'ShipmentException',
                entityId: $exception->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => ShipmentException::STATUS_RESOLVED, 'notes' => $exception->resolution_notes],
                actor: $actor
            );

            return $exception->fresh();
        });
    }

    /**
     * Update exception lifecycle state uniformly (INVESTIGATING or RESOLVED).
     */
    public function updateStatus(ShipmentException $exception, string $targetStatus, array $data, ?User $actor = null): ShipmentException
    {
        return match ($targetStatus) {
            ShipmentException::STATUS_INVESTIGATING => $this->investigateException(
                $exception,
                $data['resolution_notes'] ?? $data['notes'] ?? null,
                $actor
            ),
            ShipmentException::STATUS_RESOLVED => $this->resolveException($exception, $data, $actor),
            default => throw ValidationException::withMessages([
                'status' => ["Invalid target exception status '{$targetStatus}'."],
            ]),
        };
    }
}
