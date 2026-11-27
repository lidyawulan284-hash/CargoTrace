<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Deterministically calculate delay evaluation details without mutating database state.
     *
     * @return array<string, mixed>
     */
    public function explainDelay(Shipment $shipment): array
    {
        $expected = Carbon::parse($shipment->expected_arrival);
        $estimated = $shipment->estimated_arrival
            ? Carbon::parse($shipment->estimated_arrival)
            : $expected->copy();

        $thresholdHours = (int) config('cargotrace.delay_threshold_hours', 12);
        $lowMaxHours = (int) config('cargotrace.delay_severity_thresholds.low_max_hours', 6);
        $mediumMaxHours = (int) config('cargotrace.delay_severity_thresholds.medium_max_hours', 24);

        $delayHours = 0;
        $isDelayed = false;
        $severity = 'NONE';
        $reason = 'Shipment is on schedule (estimated arrival is within expected baseline).';

        if ($estimated->greaterThan($expected)) {
            $delayHours = (int) ceil($expected->diffInMinutes($estimated) / 60);
            $isDelayed = $delayHours > 0;
        } elseif (! $shipment->isTerminal() && now()->greaterThan($expected)) {
            $delayHours = (int) ceil($expected->diffInMinutes(now()) / 60);
            $isDelayed = $delayHours > 0;
            $estimated = now();
        }

        if ($isDelayed) {
            if ($delayHours <= $lowMaxHours) {
                $severity = 'LOW';
                $reason = "Minor operational variance (+{$delayHours}h vs expected arrival; LOW tier <= {$lowMaxHours}h).";
            } elseif ($delayHours <= $mediumMaxHours) {
                $severity = 'MEDIUM';
                $reason = "Moderate transit delay (+{$delayHours}h vs expected arrival; MEDIUM tier >{$lowMaxHours}h-{$mediumMaxHours}h).";
            } else {
                $severity = 'HIGH';
                $reason = "Critical transit delay (+{$delayHours}h vs expected arrival; HIGH tier >{$mediumMaxHours}h).";
            }

            if ($delayHours > $thresholdHours) {
                $reason .= " Exceeds {$thresholdHours}h group SLA delay threshold.";
            }
        }

        return [
            'is_delayed' => $isDelayed,
            'delay_hours' => $delayHours,
            'delay_severity' => $severity,
            'sla_threshold_hours' => $thresholdHours,
            'exceeds_sla_threshold' => $delayHours > $thresholdHours,
            'severity_tiers' => [
                'low_max_hours' => $lowMaxHours,
                'medium_max_hours' => $mediumMaxHours,
            ],
            'expected_arrival' => $expected->toIso8601String(),
            'estimated_arrival' => $estimated->toIso8601String(),
            'reason' => $reason,
        ];
    }

    /**
     * Evaluate and persist delay metrics on a shipment deterministically.
     *
     * @return array<string, mixed>
     */
    public function evaluateDelay(Shipment $shipment): array
    {
        $evaluation = $this->explainDelay($shipment);

        $shipment->estimated_arrival = Carbon::parse($evaluation['estimated_arrival']);
        $shipment->is_delayed = $evaluation['is_delayed'];
        $shipment->delay_hours = $evaluation['delay_hours'];
        $shipment->delay_severity = $evaluation['delay_severity'];
        $shipment->save();

        return $evaluation;
    }

    /**
     * Adjust ETA forward when an operational exception occurs.
     */
    public function applyExceptionDelay(Shipment $shipment, int $impactHours, string $reason): void
    {
        $currentEstimated = $shipment->estimated_arrival
            ? Carbon::parse($shipment->estimated_arrival)
            : Carbon::parse($shipment->expected_arrival);

        $shipment->estimated_arrival = $currentEstimated->copy()->addHours($impactHours);
        $shipment->save();

        $this->evaluateDelay($shipment);
    }
}
