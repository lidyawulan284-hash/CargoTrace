<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExternalGeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodeApiController extends Controller
{
    public function __construct(
        protected ExternalGeocodingService $geocodingService
    ) {}

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:150'],
        ]);

        $result = $this->geocodingService->geocode($validated['q']);

        return response()->json([
            'success' => $result['status'] === 'success',
            'data' => $result,
        ]);
    }
}
