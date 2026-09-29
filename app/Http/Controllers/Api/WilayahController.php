<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
use Database\Seeders\WilayahIndonesiaSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WilayahController extends Controller
{
    protected const API_BASE_URL = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    /**
     * Helper to format wilayah name cleanly (e.g. "KABUPATEN GARUT" -> "Kabupaten Garut").
     */
    protected function formatName(string $name): string
    {
        $name = trim($name);
        $formatted = ucwords(strtolower($name));

        // Format common abbreviations
        $replacements = [
            'Dki ' => 'DKI ',
            'Di ' => 'DI ',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $formatted);
    }

    /**
     * Get list of provinces.
     * Route: /api/internal/wilayah/provinsi
     */
    public function provinsi(): JsonResponse
    {
        try {
            if (MasterProvinsi::count() < 38) {
                (new WilayahIndonesiaSeeder())->run();
            }
        } catch (Throwable $e) {
            Log::warning('WilayahController: Failed auto-seeding provinces: ' . $e->getMessage());
        }

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
        $provinsi = MasterProvinsi::find($id);

        if (!$provinsi) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan.',
                'data' => [],
            ], 404);
        }

        // Check if regencies already exist in local database
        $kabupaten = MasterKabupaten::where('provinsi_id', $id)
            ->orderBy('nama')
            ->get(['id', 'provinsi_id', 'kode', 'nama']);

        // In Indonesia, provinces have between 5 and 38 regencies/cities.
        // For Jawa Barat (kode 32), there are exactly 27 regencies/cities.
        $needsSync = $kabupaten->isEmpty() || ($provinsi->kode === '32' && $kabupaten->count() < 27) || $kabupaten->count() < 5;

        if ($needsSync) {
            try {
                $response = Http::timeout(6)->get(self::API_BASE_URL . "/regencies/{$provinsi->kode}.json");

                if ($response->successful()) {
                    $items = $response->json();
                    if (is_array($items) && !empty($items)) {
                        foreach ($items as $item) {
                            $name = $this->formatName($item['name'] ?? '');
                            if (!empty($item['id']) && !empty($name)) {
                                $existing = MasterKabupaten::where('provinsi_id', $id)
                                    ->where(function ($q) use ($item, $name) {
                                        $q->where('kode', (string)$item['id'])
                                          ->orWhereRaw('LOWER(nama) = ?', [strtolower($name)]);
                                    })->first();

                                if ($existing) {
                                    $existing->update([
                                        'kode' => (string)$item['id'],
                                        'nama' => $name,
                                    ]);
                                } else {
                                    MasterKabupaten::create([
                                        'provinsi_id' => $id,
                                        'kode' => (string)$item['id'],
                                        'nama' => $name,
                                    ]);
                                }
                            }
                        }

                        $kabupaten = MasterKabupaten::where('provinsi_id', $id)
                            ->orderBy('nama')
                            ->get(['id', 'provinsi_id', 'kode', 'nama']);
                    }
                }
            } catch (Throwable $e) {
                Log::warning("WilayahController: Failed fetching kabupaten for prov {$provinsi->kode}: " . $e->getMessage());
            }
        }

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
        $kabupaten = MasterKabupaten::find($id);

        if (!$kabupaten) {
            return response()->json([
                'success' => false,
                'message' => 'Kabupaten / Kota tidak ditemukan.',
                'data' => [],
            ], 404);
        }

        // Check if districts already exist in local database
        $kecamatan = MasterKecamatan::where('kabupaten_id', $id)
            ->orderBy('nama')
            ->get(['id', 'kabupaten_id', 'kode', 'nama']);

        // For Garut (kode 3205), there are 42 districts. Most regencies have >= 3 districts.
        $needsSync = $kecamatan->isEmpty() || ($kabupaten->kode === '3205' && $kecamatan->count() < 42) || $kecamatan->count() < 3;

        if ($needsSync) {
            try {
                $response = Http::timeout(6)->get(self::API_BASE_URL . "/districts/{$kabupaten->kode}.json");

                if ($response->successful()) {
                    $items = $response->json();
                    if (is_array($items) && !empty($items)) {
                        foreach ($items as $item) {
                            $name = $this->formatName($item['name'] ?? '');
                            if (!empty($item['id']) && !empty($name)) {
                                $existing = MasterKecamatan::where('kabupaten_id', $id)
                                    ->where(function ($q) use ($item, $name) {
                                        $q->where('kode', (string)$item['id'])
                                          ->orWhereRaw('LOWER(nama) = ?', [strtolower($name)]);
                                    })->first();

                                if ($existing) {
                                    $existing->update([
                                        'kode' => (string)$item['id'],
                                        'nama' => $name,
                                    ]);
                                } else {
                                    MasterKecamatan::create([
                                        'kabupaten_id' => $id,
                                        'kode' => (string)$item['id'],
                                        'nama' => $name,
                                    ]);
                                }
                            }
                        }

                        $kecamatan = MasterKecamatan::where('kabupaten_id', $id)
                            ->orderBy('nama')
                            ->get(['id', 'kabupaten_id', 'kode', 'nama']);
                    }
                }
            } catch (Throwable $e) {
                Log::warning("WilayahController: Failed fetching kecamatan for kab {$kabupaten->kode}: " . $e->getMessage());
            }
        }

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
        $kecamatan = MasterKecamatan::find($id);

        if (!$kecamatan) {
            return response()->json([
                'success' => false,
                'message' => 'Kecamatan tidak ditemukan.',
                'data' => [],
            ], 404);
        }

        // Check if villages already exist in local database
        $desa = MasterDesa::where('kecamatan_id', $id)
            ->orderBy('nama')
            ->get(['id', 'kecamatan_id', 'kode', 'nama', 'kode_pos']);

        // Most Indonesian districts have at least 3-10 villages
        $needsSync = $desa->isEmpty() || $desa->count() < 3;

        if ($needsSync) {
            try {
                // If kecamatan kode is not standard (e.g. legacy mock code), attempt to auto-heal from parent kabupaten districts
                $kodeKecamatan = $kecamatan->kode;
                $response = Http::timeout(6)->get(self::API_BASE_URL . "/villages/{$kodeKecamatan}.json");

                // Self-healing fallback if 404 or failed
                if (!$response->successful() && $kecamatan->kabupaten) {
                    $districtsRes = Http::timeout(6)->get(self::API_BASE_URL . "/districts/{$kecamatan->kabupaten->kode}.json");
                    if ($districtsRes->successful()) {
                        $distList = $districtsRes->json();
                        if (is_array($distList)) {
                            foreach ($distList as $d) {
                                if (strcasecmp($this->formatName($d['name'] ?? ''), $kecamatan->nama) === 0) {
                                    $kodeKecamatan = (string)$d['id'];
                                    $kecamatan->update(['kode' => $kodeKecamatan]);
                                    $response = Http::timeout(6)->get(self::API_BASE_URL . "/villages/{$kodeKecamatan}.json");
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($response->successful()) {
                    $items = $response->json();
                    if (is_array($items) && !empty($items)) {
                        foreach ($items as $item) {
                            $name = $this->formatName($item['name'] ?? '');
                            if (!empty($item['id']) && !empty($name)) {
                                $existing = MasterDesa::where('kecamatan_id', $id)
                                    ->where(function ($q) use ($item, $name) {
                                        $q->where('kode', (string)$item['id'])
                                          ->orWhereRaw('LOWER(nama) = ?', [strtolower($name)]);
                                    })->first();

                                if ($existing) {
                                    $existing->update([
                                        'kode' => (string)$item['id'],
                                        'nama' => $name,
                                        'kode_pos' => $item['kode_pos'] ?? $existing->kode_pos,
                                    ]);
                                } else {
                                    MasterDesa::create([
                                        'kecamatan_id' => $id,
                                        'kode' => (string)$item['id'],
                                        'nama' => $name,
                                        'kode_pos' => $item['kode_pos'] ?? null,
                                    ]);
                                }
                            }
                        }

                        $desa = MasterDesa::where('kecamatan_id', $id)
                            ->orderBy('nama')
                            ->get(['id', 'kecamatan_id', 'kode', 'nama', 'kode_pos']);
                    }
                }
            } catch (Throwable $e) {
                Log::warning("WilayahController: Failed fetching desa for kec {$kecamatan->kode}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data' => $desa,
        ]);
    }
}
