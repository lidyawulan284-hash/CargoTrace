<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarrierApiController extends Controller
{
    public function index(): JsonResponse
    {
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $carriers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Admin privileges required.',
            ], 403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:120'],
            'service_type' => ['required', 'string', 'in:SEA_FREIGHT,AIR_FREIGHT,ROAD_FREIGHT,MULTIMODAL'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $carrier = Carrier::create($validated);

        AuditLogService::log(
            action: 'CREATE',
            entityType: 'Carrier',
            entityId: $carrier->id,
            oldValues: null,
            newValues: $carrier->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Carrier created successfully.',
            'data' => $carrier,
        ], 201);
    }
}
