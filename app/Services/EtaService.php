<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Evaluate and update delay metrics on a shipment.
     * Deterministic rule implementation per Slide 8 & Slide 23.
     */
    public function evaluateDelay(Shipment $shipment): array
    {
        $expected = Carbon::parse($shipment->expected_arrival);
        $estimated = $shipment->estimated_arrival
            ? Carbon::parse($shipment->estimated_arrival)
            : $expected->copy();

        // Calculate discrepancy in hours
        $delayHours = 0;
        $isDelayed = false;
        $severity = 'NONE';
        $reason = 'Shipment is on schedule.';

        if ($estimated->greaterThan($expected)) {
            $delayHours = (int) ceil($expected->diffInMinutes($estimated) / 60);
            $isDelayed = true;

            // Slide 8 Rules:
            // 0 - 6 hours: LOW
            // >6 - 24 hours: MEDIUM
            // >24 hours: HIGH
            if ($delayHours <= 6) {
                $severity = 'LOW';
                $reason = "Minor operational delay ({$delayHours} hours).";
            } elseif ($delayHours <= 24) {
                $severity = 'MEDIUM';
                $reason = "Moderate delay ({$delayHours} hours), schedule adjustment required.";
            } else {
                $severity = 'HIGH';
                $reason = "Critical transit delay ({$delayHours} hours), escalation notified.";
            }
        } elseif (! $shipment->isTerminal() && now()->greaterThan($expected)) {
            // Shipment is past expected arrival but not delivered
            $delayHours = (int) ceil($expected->diffInMinutes(now()) / 60);
            $isDelayed = true;
            $estimated = now();

            if ($delayHours <= 6) {
                $severity = 'LOW';
            } elseif ($delayHours <= 24) {
                $severity = 'MEDIUM';
            } else {
                $severity = 'HIGH';
            }
            $reason = "Shipment overdue by {$delayHours} hours without delivery confirmation.";
        }

        // Apply changes to shipment
        $shipment->estimated_arrival = $estimated;
        $shipment->is_delayed = $isDelayed;
        $shipment->delay_hours = $delayHours;
        $shipment->delay_severity = $severity;
        $shipment->save();

        return [
            'is_delayed' => $isDelayed,
            'delay_hours' => $delayHours,
            'delay_severity' => $severity,
            'expected_arrival' => $expected->toIso8601String(),
            'estimated_arrival' => $estimated->toIso8601String(),
            'reason' => $reason,
        ];
    }

    /**
     * Adjust ETA when an exception occurs.
     */
    public function applyExceptionDelay(Shipment $shipment, int $impactHours, string $reason): void
    {
        $currentEstimated = $shipment->estimated_arrival
            ? Carbon::parse($shipment->estimated_arrival)
            : Carbon::parse($shipment->expected_arrival);

        $newEstimated = $currentEstimated->copy()->addHours($impactHours);
        $shipment->estimated_arrival = $newEstimated;
        $shipment->save();

        $this->evaluateDelay($shipment);
    }
}
