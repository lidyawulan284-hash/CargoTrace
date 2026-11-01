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
     * Record a new shipment exception and update shipment ETA.
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

            // Add delay impact to shipment ETA
            if ($impactHours > 0) {
                $this->etaService->applyExceptionDelay($shipment, $impactHours, $data['description']);
            } else {
                $this->etaService->evaluateDelay($shipment);
            }

            // In-app alert for customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'title' => "Transit Exception Alert: {$exception->exception_type}",
                'message' => "An incident was flagged on shipment {$shipment->shipment_number}: {$exception->description}. Estimated delay impact: +{$impactHours} hrs.",
                'type' => 'EXCEPTION_OPENED',
                'link' => route('shipments.show', $shipment->id),
            ]);

            // Audit log
            AuditLogService::log(
                action: 'CREATE_EXCEPTION',
                entityType: 'ShipmentException',
                entityId: $exception->id,
                oldValues: null,
                newValues: [
                    'shipment_number' => $shipment->shipment_number,
                    'type' => $exception->exception_type,
                    'severity' => $exception->severity,
                    'impact_hours' => $impactHours,
                ]
            );

            return $exception;
        });
    }

    /**
     * Resolve an open exception.
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
                'resolution_notes' => $data['resolution_notes'] ?? 'Exception cleared by operational team.',
            ]);

            $shipment = $exception->shipment;

            // Re-evaluate delay now that exception is cleared
            $this->etaService->evaluateDelay($shipment);

            // Notify customer
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
                newValues: ['status' => ShipmentException::STATUS_RESOLVED, 'notes' => $exception->resolution_notes]
            );

            return $exception;
        });
    }
}
