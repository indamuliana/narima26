<?php

use App\Models\CalonSiswa;
use App\Models\MasterBiaya;
use App\Models\MasterProgram;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $unggulan = MasterProgram::where('kode', 'UGG')->first();
        if (! $unggulan) {
            return;
        }
        $programId = $unggulan->id;

        // 1. Tambah/update master biaya Biaya Asrama untuk Program Unggulan
        $biayaAsrama = MasterBiaya::updateOrCreate(
            ['kode_biaya' => 'ASR-UGG'],
            [
                'nama_biaya' => 'Biaya Asrama',
                'kategori' => 'ASRAMA',
                'program_id' => $programId,
                'gelombang_id' => null, // Berlaku sama untuk semua gelombang
                'jenis_kelamin' => null,
                'nominal' => 1500000,
                'tipe_nominal' => 'tetap',
                'wajib' => true,
                'aktif' => true,
                'keterangan' => 'Biaya Asrama bagi calon siswa program Unggulan (wajib, sama untuk setiap gelombang)',
            ]
        );

        // 2. Sinkronkan tagihan daftar ulang calon siswa Unggulan yang sudah diterbitkan
        // dan belum ada pembayaran (belum diverifikasi/lunas)
        $tagihanUnggulanList = Tagihan::where('jenis_tagihan', Tagihan::JENIS_DAFTAR_ULANG)
            ->whereHas('calonSiswa.program', function ($q) {
                $q->where('kode', 'UGG')->orWhere('nama', 'like', '%Unggulan%');
            })
            ->whereDoesntHave('pembayaran')
            ->get();

        foreach ($tagihanUnggulanList as $tagihan) {
            $hasAsrama = $tagihan->details()->where('kode_biaya_snapshot', 'ASR-UGG')->exists();
            if (! $hasAsrama) {
                TagihanDetail::create([
                    'tagihan_id' => $tagihan->id,
                    'kode_biaya_snapshot' => $biayaAsrama->kode_biaya,
                    'nama_biaya_snapshot' => $biayaAsrama->nama_biaya,
                    'kategori_snapshot' => 'ASRAMA',
                    'nominal_snapshot' => $biayaAsrama->nominal,
                    'jumlah' => 1,
                    'subtotal' => $biayaAsrama->nominal,
                ]);

                $newTotalBruto = (float) $tagihan->details()->sum('subtotal');
                $newTotalNetto = max(0, $newTotalBruto - (float) $tagihan->total_diskon);

                $tagihan->update([
                    'total_bruto' => $newTotalBruto,
                    'total_netto' => $newTotalNetto,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MasterBiaya::where('kode_biaya', 'ASR-UGG')->delete();
    }
};
