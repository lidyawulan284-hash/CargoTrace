<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Services\AuditLogService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Carrier::class);

        $query = Carrier::query()
            ->withCount('shipments')
            ->with(['shipments' => fn ($q) => $q->with(['origin:id,city,code', 'destination:id,city,code'])->latest()->take(6)]);

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('contact_email', 'like', "%{$term}%");
            });
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', (string) $request->input('service_type'));
        }

        if ($request->filled('status') && in_array($request->input('status'), ['1', '0'], true)) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $carriers = $query->orderBy('name')->paginate(12)->withQueryString();

        $stats = [
            'total' => Carrier::count(),
            'active' => Carrier::where('is_active', true)->count(),
            'roro' => Carrier::whereIn('service_type', ['RORO_SEA', 'SEA_FREIGHT'])->count(),
            'air' => Carrier::where('service_type', 'AIR_FREIGHT')->count(),
        ];

        return view('carriers.index', compact('carriers', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Carrier::class);

        $allowedModes = array_keys(config('cargotrace.carrier_service_types', [
            'RORO_SEA' => '', 'AIR_FREIGHT' => '', 'SEA_FREIGHT' => '', 'ROAD_FREIGHT' => '', 'MULTIMODAL' => '',
        ]));

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:120'],
            'service_type' => ['required', 'string', Rule::in($allowedModes)],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url'],
        ]);

        $carrier = Carrier::create($validated);

        AuditLogService::log(
            action: 'CREATE',
            entityType: 'Carrier',
            entityId: $carrier->id,
            oldValues: null,
            newValues: $carrier->toArray()
        );

        return redirect()->route('carriers.index')->with('success', __('ui.flash.carrier_added'));
    }

    public function update(Request $request, Carrier $carrier): RedirectResponse
    {
        Gate::authorize('update', $carrier);

        $allowedModes = array_keys(config('cargotrace.carrier_service_types', [
            'RORO_SEA' => '', 'AIR_FREIGHT' => '', 'SEA_FREIGHT' => '', 'ROAD_FREIGHT' => '', 'MULTIMODAL' => '',
        ]));

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('carriers', 'code')->ignore($carrier->id)],
            'name' => ['required', 'string', 'max:120'],
            'service_type' => ['required', 'string', Rule::in($allowedModes)],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $old = $carrier->only(['code', 'name', 'service_type', 'contact_email', 'contact_phone', 'website', 'is_active']);
        $carrier->update($validated);

        AuditLogService::log(
            action: 'UPDATE',
            entityType: 'Carrier',
            entityId: $carrier->id,
            oldValues: $old,
            newValues: $carrier->only(array_keys($old))
        );

        return redirect()->route('carriers.index')->with('success', __('ui.flash.carrier_updated'));
    }

    public function destroy(Carrier $carrier): RedirectResponse
    {
        Gate::authorize('delete', $carrier);

        if ($carrier->shipments()->withTrashed()->exists()) {
            return redirect()->route('carriers.index')
                ->with('error', __('ui.flash.carrier_in_use'));
        }

        try {
            $old = $carrier->only(['id', 'code', 'name', 'service_type']);
            $carrier->delete();

            AuditLogService::log(
                action: 'DELETE',
                entityType: 'Carrier',
                entityId: $carrier->id,
                oldValues: $old,
                newValues: null
            );
        } catch (QueryException) {
            return redirect()->route('carriers.index')
                ->with('error', __('ui.flash.carrier_in_use'));
        }

        return redirect()->route('carriers.index')->with('success', __('ui.flash.carrier_deleted'));
    }
}
