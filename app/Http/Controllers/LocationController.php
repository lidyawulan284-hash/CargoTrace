<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\AuditLogService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Location::class);

        $query = Location::withCount(['outboundShipments', 'inboundShipments']);

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                    ->orWhere('country', 'like', "%{$term}%");
            });
        }

        if ($request->filled('country')) {
            $query->where('country', (string) $request->input('country'));
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->input('type'));
        }

        $locations = (clone $query)
            ->orderBy('country')
            ->orderBy('city')
            ->paginate(15)
            ->withQueryString();

        $mapLocations = (clone $query)
            ->orderBy('name')
            ->get()
            ->map(fn (Location $l) => [
                'id' => $l->id,
                'code' => $l->code,
                'name' => $l->name,
                'type' => $l->type,
                'city' => $l->city,
                'country' => $l->country,
                'lat' => (float) $l->latitude,
                'lng' => (float) $l->longitude,
                'outbound' => (int) $l->outbound_shipments_count,
                'inbound' => (int) $l->inbound_shipments_count,
            ])
            ->values();

        $countries = Location::query()->select('country')->distinct()->orderBy('country')->pluck('country');

        $stats = [
            'total' => Location::count(),
            'active' => Location::where(fn ($q) => $q->has('outboundShipments')->orHas('inboundShipments'))->count(),
            'countries' => $countries->count(),
            'hubs' => Location::whereIn('type', ['HUB', 'AIRPORT', 'WAREHOUSE'])->count(),
        ];

        return view('locations.index', compact('locations', 'mapLocations', 'countries', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Location::class);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:locations,code'],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'in:PORT,AIRPORT,WAREHOUSE,CUSTOMS_FACILITY,HUB'],
            'address' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'country_code' => ['nullable', 'string', 'max:10'],
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

        return redirect()->route('locations.index')->with('success', __('ui.flash.location_added'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        Gate::authorize('update', $location);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('locations', 'code')->ignore($location->id)],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'in:PORT,AIRPORT,WAREHOUSE,CUSTOMS_FACILITY,HUB'],
            'address' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $old = $location->only(['code', 'name', 'type', 'city', 'country', 'latitude', 'longitude']);
        $location->update($validated);

        AuditLogService::log(
            action: 'UPDATE',
            entityType: 'Location',
            entityId: $location->id,
            oldValues: $old,
            newValues: $location->only(array_keys($old))
        );

        return redirect()->route('locations.index')->with('success', __('ui.flash.location_updated'));
    }

    public function destroy(Location $location): RedirectResponse
    {
        Gate::authorize('delete', $location);

        if ($location->outboundShipments()->withTrashed()->exists() || $location->inboundShipments()->withTrashed()->exists()) {
            return redirect()->route('locations.index')
                ->with('error', __('ui.flash.location_in_use'));
        }

        try {
            $old = $location->only(['id', 'code', 'name', 'city', 'country']);
            $location->delete();

            AuditLogService::log(
                action: 'DELETE',
                entityType: 'Location',
                entityId: $location->id,
                oldValues: $old,
                newValues: null
            );
        } catch (QueryException) {
            return redirect()->route('locations.index')
                ->with('error', __('ui.flash.location_in_use'));
        }

        return redirect()->route('locations.index')->with('success', __('ui.flash.location_deleted'));
    }
}
