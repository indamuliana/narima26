<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    /**
     * Get list of provinces.
     * Route: /api/internal/wilayah/provinsi
     */
    public function provinsi(): JsonResponse
    {
        $provinsi = MasterProvinsi::orderBy('nama')->get(['id', 'kode', 'nama']);

        return response()->json([
            'success' => true,
            'data' => $provinsi,
        ]);
    }

    /**
     * Get regencies / cities by province ID.
     * Route: /api/internal/wilayah/kabupaten/{id}
     */
    public function kabupaten(int $id): JsonResponse
    {
        $kabupaten = MasterKabupaten::where('provinsi_id', $id)
            ->orderBy('nama')
            ->get(['id', 'provinsi_id', 'kode', 'nama']);

        return response()->json([
            'success' => true,
            'data' => $kabupaten,
        ]);
    }

    /**
     * Get districts by regency/city ID.
     * Route: /api/internal/wilayah/kecamatan/{id}
     */
    public function kecamatan(int $id): JsonResponse
    {
        $kecamatan = MasterKecamatan::where('kabupaten_id', $id)
            ->orderBy('nama')
            ->get(['id', 'kabupaten_id', 'kode', 'nama']);

        return response()->json([
            'success' => true,
            'data' => $kecamatan,
        ]);
    }

    /**
     * Get villages by district ID.
     * Route: /api/internal/wilayah/desa/{id}
     */
    public function desa(int $id): JsonResponse
    {
        $desa = MasterDesa::where('kecamatan_id', $id)
            ->orderBy('nama')
            ->get(['id', 'kecamatan_id', 'kode', 'nama', 'kode_pos']);

        return response()->json([
            'success' => true,
            'data' => $desa,
        ]);
    }
}
