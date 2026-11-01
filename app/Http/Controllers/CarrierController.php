<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function index(): View
    {
        $carriers = Carrier::withCount('shipments')->orderBy('name')->paginate(15);
        return view('carriers.index', compact('carriers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:120'],
            'service_type' => ['required', 'string', 'in:SEA_FREIGHT,AIR_FREIGHT,ROAD_FREIGHT,MULTIMODAL'],
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

        return redirect()->route('carriers.index')->with('success', 'Carrier added successfully.');
    }
}
