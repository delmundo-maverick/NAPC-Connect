<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /** Provinces of a region. NCR has none, so it returns an empty list. */
    public function provinces(Request $request): JsonResponse
    {
        $region = (string) $request->query('region');

        $items = Province::where('region_code', $region)
            ->orderBy('name')
            ->get(['code', 'name']);

        return response()->json($items)->header('Cache-Control', 'private, max-age=600');
    }

    /**
     * Cities and municipalities.
     * In a region with provinces, a province is required.
     * Independent cities (no province) are listed under every province of their region.
     */
    public function cities(Request $request): JsonResponse
    {
        $region = (string) $request->query('region');
        $province = (string) $request->query('province');

        $hasProvinces = Province::where('region_code', $region)->exists();

        if ($hasProvinces && $province === '') {
            return response()->json([]);
        }

        $items = City::where('region_code', $region)
            ->when($hasProvinces, fn($q) => $q->where(
                fn($w) => $w->where('province_code', $province)->orWhereNull('province_code')
            ))
            ->orderBy('name')
            ->get(['code', 'name']);

        return response()->json($items)->header('Cache-Control', 'private, max-age=600');
    }
}
