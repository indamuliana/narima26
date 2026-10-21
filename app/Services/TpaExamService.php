<?php

namespace App\Services;

use App\Models\CalonSiswa;
use App\Models\TpaJawabanSiswa;
use App\Models\TpaPengaturan;
use App\Models\TpaSesiSiswa;
use App\Models\TpaSoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TpaExamService
{
    /**
     * Dapatkan atau inisialisasi sesi ujian siswa dengan pengacakan nomor soal.
     */
    public function getOrCreateSesi(CalonSiswa $calonSiswa): TpaSesiSiswa
    {
        $sesi = TpaSesiSiswa::where('calon_siswa_id', $calonSiswa->id)->first();

        if ($sesi) {
            return $sesi;
        }

        return DB::transaction(function () use ($calonSiswa) {
            // Acak urutan butir soal per babak
            $soalLiterasiIds = TpaSoal::literasi()->aktif()->inRandomOrder()->pluck('id')->all();
            $soalNumerasiIds = TpaSoal::numerasi()->aktif()->inRandomOrder()->pluck('id')->all();

            $sesi = TpaSesiSiswa::create([
                'calon_siswa_id' => $calonSiswa->id,
                'status' => 'belum_mulai',
                'urutan_soal_literasi' => $soalLiterasiIds,
                'urutan_soal_numerasi' => $soalNumerasiIds,
                'ip_address' => request()->ip(),
            ]);

            // Inisialisasi baris jawaban kosong untuk integritas kueri
            $allSoalIds = array_merge($soalLiterasiIds, $soalNumerasiIds);
            $rows = [];
            $now = now();
            foreach ($allSoalIds as $idSoal) {
                $rows[] = [
                    'tpa_sesi_siswa_id' => $sesi->id,
                    'tpa_soal_id' => $idSoal,
                    'jawaban_dipilih' => null,
                    'is_ragu' => false,
                    'is_benar' => false,
                    'poin_diperoleh' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($rows)) {
                TpaJawabanSiswa::insert($rows);
            }

            return $sesi;
        });
    }

    /**
     * Mulai babak ujian (Babak 1 Literasi atau Babak 2 Numerasi).
     */
    public function mulaiBabak(TpaSesiSiswa $sesi, string $babak): void
    {
        if ($babak === 'literasi' && $sesi->status === 'belum_mulai') {
            $sesi->update([
                'status' => 'babak_1',
                'babak_1_mulai' => now(),
            ]);
        } elseif ($babak === 'numerasi' && in_array($sesi->status, ['transisi', 'babak_1'])) {
            $sesi->update([
                'status' => 'babak_2',
                'babak_1_selesai' => $sesi->babak_1_selesai ?? now(),
                'babak_2_mulai' => now(),
            ]);
        }
    }

    /**
     * Hitung sisa waktu pengerjaan babak saat ini dalam satuan detik (server-authoritative).
     */
    public function getSisaWaktuDetik(TpaSesiSiswa $sesi, TpaPengaturan $pengaturan): int
    {
        $now = Carbon::now();

        if ($sesi->status === 'babak_1' && $sesi->babak_1_mulai) {
            $durasiDetik = $pengaturan->durasi_literasi_menit * 60;
            $selesaiTarget = Carbon::parse($sesi->babak_1_mulai)->addSeconds($durasiDetik);
            return max(0, $now->diffInSeconds($selesaiTarget, false));
        }

        if ($sesi->status === 'babak_2' && $sesi->babak_2_mulai) {
            $durasiDetik = $pengaturan->durasi_numerasi_menit * 60;
            $selesaiTarget = Carbon::parse($sesi->babak_2_mulai)->addSeconds($durasiDetik);
            return max(0, $now->diffInSeconds($selesaiTarget, false));
        }

        return 0;
    }

    /**
     * Simpan jawaban dan status ragu-ragu butir soal.
     */
    public function simpanJawaban(TpaSesiSiswa $sesi, int $soalId, ?string $jawaban, bool $isRagu): TpaJawabanSiswa
    {
        $jawabanRecord = TpaJawabanSiswa::firstOrNew([
            'tpa_sesi_siswa_id' => $sesi->id,
            'tpa_soal_id' => $soalId,
        ]);

        $jawabanRecord->jawaban_dipilih = $jawaban ? strtoupper(trim($jawaban)) : null;
        $jawabanRecord->is_ragu = $isRagu;
        $jawabanRecord->save();

        return $jawabanRecord;
    }

    /**
     * Selesaikan Babak 1 dan kunci jawaban.
     */
    public function selesaikanBabak1(TpaSesiSiswa $sesi): void
    {
        if ($sesi->status === 'babak_1') {
            $sesi->update([
                'status' => 'transisi',
                'babak_1_selesai' => now(),
            ]);
        }
    }

    /**
     * Selesaikan Babak 2, kunci keseluruhan ujian, dan hitung skor akhir.
     */
    public function selesaikanBabak2(TpaSesiSiswa $sesi): void
    {
        $sesi->update([
            'status' => 'selesai',
            'babak_2_selesai' => now(),
        ]);

        $sesi->hitungSkor();
    }

    /**
     * Diskualifikasi sesi pengerjaan karena pelanggaran melebihi batas.
     */
    public function diskualifikasi(TpaSesiSiswa $sesi, string $alasan): void
    {
        $sesi->catatPelanggaran($alasan);
        $sesi->update([
            'status' => 'didiskualifikasi',
            'babak_1_selesai' => $sesi->babak_1_selesai ?? now(),
            'babak_2_selesai' => now(),
        ]);

        $sesi->hitungSkor();
    }
}
