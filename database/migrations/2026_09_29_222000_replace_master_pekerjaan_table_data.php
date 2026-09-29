<?php

use App\Models\DataOrangtua;
use App\Models\MasterPekerjaan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pekerjaanList = [
            'BELUM/TIDAK BEKERJA',
            'MENGURUS RUMAH TANGGA',
            'PELAJAR/MAHASISWA',
            'PENSIUNAN',
            'PEGAWAI NEGERI SIPIL (PNS)',
            'TENTARA NASIONAL INDONESIA (TNI)',
            'KEPOLISIAN RI (POLRI)',
            'PERDAGANGAN',
            'PETANI/PERKEBUNAN',
            'PETERNAK',
            'NELAYAN/PERIKANAN',
            'INDUSTRI',
            'KONSTRUKSI',
            'TRANSPORTASI',
            'KARYAWAN SWASTA',
            'KARYAWAN BUMN',
            'KARYAWAN BUMD',
            'KARYAWAN HONORER',
            'BURUH HARIAN LEPAS',
            'BURUH TANI/PERKEBUNAN',
            'BURUH NELAYAN/PERIKANAN',
            'BURUH PETERNAKAN',
            'PEMBANTU RUMAH TANGGA',
            'TUKANG CUKUR',
            'TUKANG LISTRIK',
            'TUKANG BATU',
            'TUKANG KAYU',
            'TUKANG SOL SEPATU',
            'TUKANG LAS/PANDAI BESI',
            'TUKANG JAHIT',
            'TUKANG GIGI',
            'PENATA RIAS',
            'PENATA BUSANA',
            'PENATA RAMBUT',
            'MEKANIK',
            'SENIMAN',
            'TABIB',
            'PARAJI',
            'PERANCANG BUSANA',
            'PENTERJEMAH',
            'IMAM MASJID',
            'PENDETA',
            'PASTOR',
            'WARTAWAN',
            'USTADZ/MUBALIGH',
            'JURU MASAK',
            'PROMOTOR ACARA',
            'ANGGOTA DPR-RI',
            'ANGGOTA DPD',
            'ANGGOTA BPK',
            'PRESIDEN',
            'WAKIL PRESIDEN',
            'ANGGOTA MAHKAMAH KONSTITUSI',
            'ANGGOTA KABINET KEMENTERIAN',
            'DUTA BESAR',
            'GUBERNUR',
            'WAKIL GUBERNUR',
            'BUPATI',
            'WAKIL BUPATI',
            'WALIKOTA',
            'WAKIL WALIKOTA',
            'ANGGOTA DPRD PROVINSI',
            'ANGGOTA DPRD KABUPATEN/KOTA',
            'DOSEN',
            'GURU',
            'PILOT',
            'PENGACARA',
            'NOTARIS',
            'ARSITEK',
            'AKUNTAN',
            'KONSULTAN',
            'DOKTER',
            'BIDAN',
            'PERAWAT',
            'APOTEKER',
            'PSIKIATER/PSIKOLOG',
            'PENYIAR TELEVISI',
            'PENYIAR RADIO',
            'PELAUT',
            'PENELITI',
            'SOPIR',
            'PIALANG',
            'PARANORMAL',
            'PEDAGANG',
            'PERANGKAT DESA',
            'KEPALA DESA',
            'BIARAWATI',
            'WIRASWASTA',
            'LAINNYA',
        ];

        // 1. Insert or update the new 89 records
        $newMap = [];
        foreach ($pekerjaanList as $nama) {
            $model = MasterPekerjaan::withTrashed()->where('nama', $nama)->first();
            if ($model) {
                if ($model->trashed()) {
                    $model->restore();
                }
                $model->update(['nama' => $nama, 'aktif' => true]);
            } else {
                $model = MasterPekerjaan::create([
                    'nama' => $nama,
                    'aktif' => true,
                ]);
            }
            $newMap[strtoupper($nama)] = $model->id;
        }

        // 2. Remap legacy references in data_orangtua
        $legacyMappings = [
            'PNS' => 'PEGAWAI NEGERI SIPIL (PNS)',
            'TNI/POLRI' => 'TENTARA NASIONAL INDONESIA (TNI)',
            'KARYAWAN SWASTA' => 'KARYAWAN SWASTA',
            'WIRASWASTA' => 'WIRASWASTA',
            'PETANI' => 'PETANI/PERKEBUNAN',
            'BURUH' => 'BURUH HARIAN LEPAS',
            'GURU' => 'GURU',
            'PEDAGANG' => 'PEDAGANG',
            'TIDAK BEKERJA' => 'BELUM/TIDAK BEKERJA',
            'LAINNYA' => 'LAINNYA',
        ];

        $oldPekerjaan = MasterPekerjaan::withTrashed()->whereNotIn('nama', $pekerjaanList)->get();
        foreach ($oldPekerjaan as $old) {
            $upperOld = strtoupper($old->nama);
            $targetNama = $legacyMappings[$upperOld] ?? 'LAINNYA';
            $targetId = $newMap[$targetNama] ?? null;

            if ($targetId) {
                DB::table('data_orangtua')->where('pekerjaan_ayah_id', $old->id)->update(['pekerjaan_ayah_id' => $targetId]);
                DB::table('data_orangtua')->where('pekerjaan_ibu_id', $old->id)->update(['pekerjaan_ibu_id' => $targetId]);
                DB::table('data_orangtua')->where('pekerjaan_wali_id', $old->id)->update(['pekerjaan_wali_id' => $targetId]);
            }

            // Force delete the old record that is not in the new list
            $old->forceDelete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as this is a master data replacement
    }
};
