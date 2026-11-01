<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::withCount(['outboundShipments', 'inboundShipments'])->orderBy('country')->orderBy('city')->paginate(15);
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:locations,code'],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'in:PORT,AIRPORT,WAREHOUSE,CUSTOMS_FACILITY,HUB'],
            'address' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $location = Location::create($validated);

        AuditLogService::log(
            action: 'CREATE',
            entityType: 'Location',
            entityId: $location->id,
            oldValues: null,
            newValues: $location->toArray()
        );

        return redirect()->route('locations.index')->with('success', 'Location added successfully.');
    }
}
