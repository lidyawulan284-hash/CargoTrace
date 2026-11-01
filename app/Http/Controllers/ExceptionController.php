<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403);
        }

        $validated = $request->validate([
            'exception_type' => ['required', 'string', 'in:' . implode(',', ShipmentException::TYPES)],
            'severity' => ['required', 'string', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'description' => ['required', 'string'],
            'delay_impact_hours' => ['nullable', 'integer', 'min:0'],
            'detected_at' => ['nullable', 'date'],
        ]);

        $this->exceptionService->createException($shipment, $validated, $user);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('warning', 'Shipment exception flagged. Estimated delay has been recalculated.');
    }

    public function resolve(Request $request, ShipmentException $exception): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403);
        }

        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:5'],
        ]);

        $this->exceptionService->resolveException($exception, $validated, $user);

        return redirect()->route('shipments.show', $exception->shipment_id)
            ->with('success', 'Exception resolved and cleared from active alarms.');
    }
}
