<?php

namespace App\Services;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterKriteriaWawancara;
use App\Models\User;
use App\Models\Wawancara;
use App\Models\WawancaraDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WawancaraService
{
    public function __construct(
        protected SpmbStatusService $spmbStatusService
    ) {}

    /**
     * Get paginated or filtered queue of candidates eligible for interview.
     */
    public function getAntrian(
        ?string $search = null,
        ?int $jurusanId = null,
        ?string $statusWawancara = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = CalonSiswa::query()
            ->with(['user', 'jurusan', 'programBelajar', 'sekolahAsal', 'wawancara.pewawancara', 'dokumenPendaftaran'])
            ->where(function (Builder $q) {
                // Calon siswa who are in MENUNGGU_WAWANCARA or have existing wawancara records
                $q->where('status_spmb', SpmbStatus::MENUNGGU_WAWANCARA->value)
                  ->orWhere('status_spmb', SpmbStatus::SUDAH_DIWAWANCARA->value)
                  ->orWhereHas('wawancara');
            });

        // Filter by keyword search (Nama, No Pendaftaran, NISN, Asal Sekolah)
        if (! empty($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('sekolah_asal_text', 'like', "%{$search}%")
                  ->orWhereHas('sekolahAsal', function (Builder $sq) use ($search) {
                      $sq->where('nama_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Jurusan
        if (! empty($jurusanId)) {
            $query->where('jurusan_id', $jurusanId);
        }

        // Filter by interview status
        if (! empty($statusWawancara)) {
            if ($statusWawancara === 'BELUM') {
                $query->whereDoesntHave('wawancara', function (Builder $wq) {
                    $wq->whereIn('status', [Wawancara::STATUS_PROSES, Wawancara::STATUS_SELESAI]);
                })->where('status_spmb', SpmbStatus::MENUNGGU_WAWANCARA->value);
            } elseif ($statusWawancara === Wawancara::STATUS_PROSES) {
                $query->whereHas('wawancara', function (Builder $wq) {
                    $wq->where('status', Wawancara::STATUS_PROSES);
                });
            } elseif ($statusWawancara === Wawancara::STATUS_SELESAI) {
                $query->whereHas('wawancara', function (Builder $wq) {
                    $wq->where('status', Wawancara::STATUS_SELESAI);
                });
            }
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Get completed interviews (Riwayat Wawancara).
     */
    public function getRiwayat(
        ?string $search = null,
        ?int $jurusanId = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Wawancara::query()
            ->with(['calonSiswa.jurusan', 'calonSiswa.programBelajar', 'calonSiswa.sekolahAsal', 'pewawancara', 'details.kriteria'])
            ->where('status', Wawancara::STATUS_SELESAI);

        if (! empty($search)) {
            $query->whereHas('calonSiswa', function (Builder $q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if (! empty($jurusanId)) {
            $query->whereHas('calonSiswa', function (Builder $q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId);
            });
        }

        return $query->latest('tanggal_wawancara')->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Get active criteria grouped by type (siswa / orang_tua).
     */
    public function getRubrikKriteria(): Collection
    {
        return MasterKriteriaWawancara::aktif()
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Find or create an active interview session for a candidate.
     */
    public function getOrCreateWawancara(CalonSiswa $calonSiswa, User $pewawancara): Wawancara
    {
        $wawancara = Wawancara::with(['details.kriteria', 'pewawancara'])
            ->where('calon_siswa_id', $calonSiswa->id)
            ->latest('id')
            ->first();

        if (! $wawancara) {
            $wawancara = Wawancara::create([
                'calon_siswa_id' => $calonSiswa->id,
                'pewawancara_id' => $pewawancara->id,
                'tanggal_wawancara' => now()->toDateString(),
                'status' => Wawancara::STATUS_MENUNGGU,
            ]);
            $wawancara->load(['details.kriteria', 'pewawancara']);
        }

        return $wawancara;
    }

    /**
     * Save interview as Draft without changing SPMB status to SUDAH_DIWAWANCARA.
     */
    public function saveDraft(CalonSiswa $calonSiswa, array $data, User $pewawancara): Wawancara
    {
        return DB::transaction(function () use ($calonSiswa, $data, $pewawancara) {
            $wawancara = Wawancara::firstOrNew(['calon_siswa_id' => $calonSiswa->id]);

            $wawancara->pewawancara_id = $pewawancara->id;
            $wawancara->tanggal_wawancara = $data['tanggal_wawancara'] ?? now()->toDateString();
            $wawancara->status = Wawancara::STATUS_PROSES;
            $wawancara->catatan_umum = $data['catatan_umum'] ?? null;
            $wawancara->catatan_orang_tua = $data['catatan_orang_tua'] ?? null;
            $wawancara->save();

            $this->syncDetails($wawancara, $data['penilaian'] ?? []);

            if (function_exists('activity')) {
                activity('wawancara')
                    ->performedOn($wawancara)
                    ->causedBy($pewawancara)
                    ->withProperties([
                        'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                        'nama_siswa' => $calonSiswa->nama_lengkap,
                        'status' => Wawancara::STATUS_PROSES,
                    ])
                    ->log("Draft penilaian wawancara disimpan untuk {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
            }

            return $wawancara->load(['details.kriteria', 'pewawancara']);
        });
    }

    /**
     * Finalize interview, save all criteria, and transition candidate to SUDAH_DIWAWANCARA.
     */
    public function selesaiWawancara(CalonSiswa $calonSiswa, array $data, User $pewawancara): Wawancara
    {
        return DB::transaction(function () use ($calonSiswa, $data, $pewawancara) {
            $wawancara = Wawancara::firstOrNew(['calon_siswa_id' => $calonSiswa->id]);

            $wawancara->pewawancara_id = $pewawancara->id;
            $wawancara->tanggal_wawancara = $data['tanggal_wawancara'] ?? now()->toDateString();
            $wawancara->status = Wawancara::STATUS_SELESAI;
            $wawancara->catatan_umum = $data['catatan_umum'] ?? null;
            $wawancara->catatan_orang_tua = $data['catatan_orang_tua'] ?? null;
            $wawancara->save();

            $this->syncDetails($wawancara, $data['penilaian'] ?? []);

            // Transition candidate SPMB status if currently MENUNGGU_WAWANCARA
            $currentStatus = is_string($calonSiswa->status_spmb)
                ? SpmbStatus::from($calonSiswa->status_spmb)
                : $calonSiswa->status_spmb;

            if ($currentStatus === SpmbStatus::MENUNGGU_WAWANCARA) {
                $this->spmbStatusService->changeStatus(
                    calonSiswa: $calonSiswa,
                    targetStatus: SpmbStatus::SUDAH_DIWAWANCARA,
                    alasan: 'Wawancara seleksi selesai dilaksanakan oleh pewawancara',
                    catatan: $data['catatan_umum'] ?? 'Penilaian wawancara selesai diinput.',
                    changedBy: $pewawancara
                );
            }

            if (function_exists('activity')) {
                activity('wawancara')
                    ->performedOn($wawancara)
                    ->causedBy($pewawancara)
                    ->withProperties([
                        'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                        'nama_siswa' => $calonSiswa->nama_lengkap,
                        'status' => Wawancara::STATUS_SELESAI,
                    ])
                    ->log("Wawancara diselesaikan untuk {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}).");
            }

            return $wawancara->load(['details.kriteria', 'pewawancara']);
        });
    }

    /**
     * Sync criteria evaluation rows into wawancara_detail.
     */
    protected function syncDetails(Wawancara $wawancara, array $penilaianList): void
    {
        foreach ($penilaianList as $item) {
            if (! isset($item['kriteria_id'])) {
                continue;
            }

            $nilai = (int) ($item['nilai'] ?? 0);
            $warna = $item['warna'] ?? $this->deriveColorFromScore($nilai);
            $indikator = $item['indikator'] ?? $this->deriveIndicatorFromScore($nilai);

            WawancaraDetail::updateOrCreate(
                [
                    'wawancara_id' => $wawancara->id,
                    'kriteria_id' => $item['kriteria_id'],
                ],
                [
                    'indikator' => $indikator,
                    'nilai' => $nilai,
                    'warna' => in_array($warna, [WawancaraDetail::WARNA_HIJAU, WawancaraDetail::WARNA_ORANYE, WawancaraDetail::WARNA_MERAH], true)
                        ? $warna
                        : WawancaraDetail::WARNA_HIJAU,
                    'catatan' => $item['catatan'] ?? null,
                ]
            );
        }
    }

    /**
     * Helper to derive color indicator from score if not explicitly selected.
     */
    public function deriveColorFromScore(int $score): string
    {
        if ($score >= 75) {
            return WawancaraDetail::WARNA_HIJAU;
        }

        if ($score >= 60) {
            return WawancaraDetail::WARNA_ORANYE;
        }

        return WawancaraDetail::WARNA_MERAH;
    }

    /**
     * Helper to derive indicator label from score.
     */
    public function deriveIndicatorFromScore(int $score): string
    {
        if ($score >= 85) {
            return 'Sangat Baik';
        }

        if ($score >= 75) {
            return 'Baik';
        }

        if ($score >= 60) {
            return 'Cukup';
        }

        return 'Kurang';
    }

    /**
     * Get interview statistics for dashboards.
     */
    public function getStatistics(?User $pewawancara = null): array
    {
        // 1. Antrian Menunggu: Calon Siswa with MENUNGGU_WAWANCARA status without finished/in-progress interview
        $antrianMenunggu = CalonSiswa::where('status_spmb', SpmbStatus::MENUNGGU_WAWANCARA->value)
            ->whereDoesntHave('wawancara', function (Builder $q) {
                $q->whereIn('status', [Wawancara::STATUS_PROSES, Wawancara::STATUS_SELESAI]);
            })
            ->count();

        // 2. Sedang Diwawancara / Draft
        $sedangProsesQuery = Wawancara::where('status', Wawancara::STATUS_PROSES);
        if ($pewawancara && ! $pewawancara->isAdmin()) {
            $sedangProsesQuery->where('pewawancara_id', $pewawancara->id);
        }
        $sedangProses = $sedangProsesQuery->count();

        // 3. Selesai Wawancara
        $selesaiQuery = Wawancara::where('status', Wawancara::STATUS_SELESAI);
        if ($pewawancara && ! $pewawancara->isAdmin()) {
            $selesaiQuery->where('pewawancara_id', $pewawancara->id);
        }
        $selesai = $selesaiQuery->count();

        // 4. Kriteria Aktif
        $kriteriaAktif = MasterKriteriaWawancara::aktif()->count();

        return [
            'antrian_menunggu' => $antrianMenunggu,
            'sedang_proses' => $sedangProses,
            'selesai' => $selesai,
            'kriteria_aktif' => $kriteriaAktif,
        ];
    }
}
