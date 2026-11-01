<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
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

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully.',
            'data' => $location,
        ], 201);
    }
}
