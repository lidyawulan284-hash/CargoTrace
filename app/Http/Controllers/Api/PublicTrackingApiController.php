<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\EtaService;
use Illuminate\Http\JsonResponse;

class PublicTrackingApiController extends Controller
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    public function show(string $number): JsonResponse
    {
        $shipment = Shipment::with([
            'customer:id,name,company_name',
            'carrier:id,name,code,service_type',
            'origin:id,code,name,city,country,latitude,longitude',
            'destination:id,code,name,city,country,latitude,longitude',
            'latestTrackingEvent',
            'items',
            'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc'),
            'exceptions' => fn ($q) => $q->where('status', '!=', 'RESOLVED'),
        ])->where('shipment_number', $number)->first();

        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => "Shipment '{$number}' not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
            'current_location' => $shipment->getCurrentCoordinates(),
            'delay_evaluation' => $this->etaService->explainDelay($shipment),
        ]);
    }
}
