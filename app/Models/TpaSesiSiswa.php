<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TpaSesiSiswa extends Model
{
    protected $table = 'tpa_sesi_siswa';

    protected $fillable = [
        'calon_siswa_id',
        'status',
        'babak_1_mulai',
        'babak_1_selesai',
        'babak_2_mulai',
        'babak_2_selesai',
        'urutan_soal_literasi',
        'urutan_soal_numerasi',
        'skor_literasi',
        'skor_numerasi',
        'total_skor',
        'benar_literasi',
        'salah_literasi',
        'kosong_literasi',
        'benar_numerasi',
        'salah_numerasi',
        'kosong_numerasi',
        'jumlah_pelanggaran',
        'log_pelanggaran',
        'ip_address',
    ];

    protected $casts = [
        'babak_1_mulai' => 'datetime',
        'babak_1_selesai' => 'datetime',
        'babak_2_mulai' => 'datetime',
        'babak_2_selesai' => 'datetime',
        'urutan_soal_literasi' => 'array',
        'urutan_soal_numerasi' => 'array',
        'skor_literasi' => 'decimal:2',
        'skor_numerasi' => 'decimal:2',
        'total_skor' => 'decimal:2',
        'benar_literasi' => 'integer',
        'salah_literasi' => 'integer',
        'kosong_literasi' => 'integer',
        'benar_numerasi' => 'integer',
        'salah_numerasi' => 'integer',
        'kosong_numerasi' => 'integer',
        'jumlah_pelanggaran' => 'integer',
        'log_pelanggaran' => 'array',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(TpaJawabanSiswa::class, 'tpa_sesi_siswa_id');
    }

    /**
     * Hitung ulang skor literasi, numerasi, dan total secara akurat berdasarkan kunci dan bobot soal.
     */
    public function hitungSkor(): void
    {
        $this->loadMissing(['jawabanSiswa.soal']);

        $skorLit = 0.0;
        $benarLit = 0;
        $salahLit = 0;
        $kosongLit = 0;

        $skorNum = 0.0;
        $benarNum = 0;
        $salahNum = 0;
        $kosongNum = 0;

        $litIds = $this->urutan_soal_literasi ?? [];
        $numIds = $this->urutan_soal_numerasi ?? [];

        $jawabanMap = $this->jawabanSiswa->keyBy('tpa_soal_id');

        // Kalkulasi Literasi
        foreach ($litIds as $idSoal) {
            $jawaban = $jawabanMap->get($idSoal);
            $soal = $jawaban?->soal ?? TpaSoal::find($idSoal);
            if (! $soal) continue;

            $dipilih = $jawaban?->jawaban_dipilih;
            if (empty($dipilih)) {
                $kosongLit++;
            } elseif (strtoupper($dipilih) === strtoupper($soal->kunci_jawaban)) {
                $benarLit++;
                $skorLit += (float) $soal->bobot;
                if ($jawaban) {
                    $jawaban->update(['is_benar' => true, 'poin_diperoleh' => $soal->bobot]);
                }
            } else {
                $salahLit++;
                if ($jawaban) {
                    $jawaban->update(['is_benar' => false, 'poin_diperoleh' => 0]);
                }
            }
        }

        // Kalkulasi Numerasi
        foreach ($numIds as $idSoal) {
            $jawaban = $jawabanMap->get($idSoal);
            $soal = $jawaban?->soal ?? TpaSoal::find($idSoal);
            if (! $soal) continue;

            $dipilih = $jawaban?->jawaban_dipilih;
            if (empty($dipilih)) {
                $kosongNum++;
            } elseif (strtoupper($dipilih) === strtoupper($soal->kunci_jawaban)) {
                $benarNum++;
                $skorNum += (float) $soal->bobot;
                if ($jawaban) {
                    $jawaban->update(['is_benar' => true, 'poin_diperoleh' => $soal->bobot]);
                }
            } else {
                $salahNum++;
                if ($jawaban) {
                    $jawaban->update(['is_benar' => false, 'poin_diperoleh' => 0]);
                }
            }
        }

        $this->update([
            'skor_literasi' => $skorLit,
            'skor_numerasi' => $skorNum,
            'total_skor' => $skorLit + $skorNum,
            'benar_literasi' => $benarLit,
            'salah_literasi' => $salahLit,
            'kosong_literasi' => $kosongLit,
            'benar_numerasi' => $benarNum,
            'salah_numerasi' => $salahNum,
            'kosong_numerasi' => $kosongNum,
        ]);
    }

    /**
     * Catat log pelanggaran saat siswa beralih tab browser atau meminimalkan jendela.
     */
    public function catatPelanggaran(string $alasan): int
    {
        $log = $this->log_pelanggaran ?? [];
        $log[] = [
            'waktu' => Carbon::now()->toIso8601String(),
            'alasan' => $alasan,
            'ip' => request()->ip(),
        ];

        $count = $this->jumlah_pelanggaran + 1;
        $this->update([
            'jumlah_pelanggaran' => $count,
            'log_pelanggaran' => $log,
        ]);

        return $count;
    }
}
