<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExceptionApiController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    /**
     * Get exceptions for a shipment (Slide 12)
     */
    public function index(Request $request, int|string $shipmentId): JsonResponse
    {
        $user = $request->user();

        $shipment = Shipment::find($shipmentId);
        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if (! $user->canManageShipments() && $shipment->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        }

        $exceptions = $shipment->exceptions()
            ->with(['resolver', 'trackingEvent'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exceptions,
        ]);
    }

    /**
     * Create a new exception for a shipment (Slide 12)
     */
    public function store(Request $request, int|string $shipmentId): JsonResponse
    {
        $user = $request->user();
        if (! $user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only Operators and Admins can log exceptions.',
            ], 403);
        }

        $shipment = Shipment::find($shipmentId);
        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        $validated = $request->validate([
            'exception_type' => ['required', 'string', 'in:'.implode(',', ShipmentException::TYPES)],
            'severity' => ['required', 'string', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'description' => ['required', 'string'],
            'delay_impact_hours' => ['nullable', 'integer', 'min:0'],
            'tracking_event_id' => ['nullable', 'exists:tracking_events,id'],
            'detected_at' => ['nullable', 'date'],
        ]);

        $exception = $this->exceptionService->createException($shipment, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Shipment exception recorded successfully.',
            'data' => $exception,
        ], 201);
    }

    /**
     * Resolve an exception (Slide 12)
     */
    public function resolve(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only Operators and Admins can resolve exceptions.',
            ], 403);
        }

        $exception = ShipmentException::find($id);
        if (! $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Exception record not found.',
            ], 404);
        }

        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:5'],
        ]);

        $updated = $this->exceptionService->resolveException($exception, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Exception marked as RESOLVED.',
            'data' => $updated,
        ]);
    }
}
