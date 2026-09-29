<?php

namespace Database\Seeders;

use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
use Illuminate\Database\Seeder;

class WilayahIndonesiaSeeder extends Seeder
{
    /**
     * Run the database seeds for Indonesian Provinces, Jawa Barat regencies, and Garut districts.
     */
    public function run(): void
    {
        // 1. 38 Indonesian Provinces
        $provinces = [
            ['kode' => '11', 'nama' => 'Aceh'],
            ['kode' => '12', 'nama' => 'Sumatera Utara'],
            ['kode' => '13', 'nama' => 'Sumatera Barat'],
            ['kode' => '14', 'nama' => 'Riau'],
            ['kode' => '15', 'nama' => 'Jambi'],
            ['kode' => '16', 'nama' => 'Sumatera Selatan'],
            ['kode' => '17', 'nama' => 'Bengkulu'],
            ['kode' => '18', 'nama' => 'Lampung'],
            ['kode' => '19', 'nama' => 'Kepulauan Bangka Belitung'],
            ['kode' => '21', 'nama' => 'Kepulauan Riau'],
            ['kode' => '31', 'nama' => 'DKI Jakarta'],
            ['kode' => '32', 'nama' => 'Jawa Barat'],
            ['kode' => '33', 'nama' => 'Jawa Tengah'],
            ['kode' => '34', 'nama' => 'DI Yogyakarta'],
            ['kode' => '35', 'nama' => 'Jawa Timur'],
            ['kode' => '36', 'nama' => 'Banten'],
            ['kode' => '51', 'nama' => 'Bali'],
            ['kode' => '52', 'nama' => 'Nusa Tenggara Barat'],
            ['kode' => '53', 'nama' => 'Nusa Tenggara Timur'],
            ['kode' => '61', 'nama' => 'Kalimantan Barat'],
            ['kode' => '62', 'nama' => 'Kalimantan Tengah'],
            ['kode' => '63', 'nama' => 'Kalimantan Selatan'],
            ['kode' => '64', 'nama' => 'Kalimantan Timur'],
            ['kode' => '65', 'nama' => 'Kalimantan Utara'],
            ['kode' => '71', 'nama' => 'Sulawesi Utara'],
            ['kode' => '72', 'nama' => 'Sulawesi Tengah'],
            ['kode' => '73', 'nama' => 'Sulawesi Selatan'],
            ['kode' => '74', 'nama' => 'Sulawesi Tenggara'],
            ['kode' => '75', 'nama' => 'Gorontalo'],
            ['kode' => '76', 'nama' => 'Sulawesi Barat'],
            ['kode' => '81', 'nama' => 'Maluku'],
            ['kode' => '82', 'nama' => 'Maluku Utara'],
            ['kode' => '91', 'nama' => 'Papua Barat'],
            ['kode' => '92', 'nama' => 'Papua'],
            ['kode' => '93', 'nama' => 'Papua Barat Daya'],
            ['kode' => '94', 'nama' => 'Papua Selatan'],
            ['kode' => '95', 'nama' => 'Papua Tengah'],
            ['kode' => '96', 'nama' => 'Papua Pegunungan'],
        ];

        foreach ($provinces as $p) {
            MasterProvinsi::updateOrCreate(
                ['kode' => $p['kode']],
                ['nama' => $p['nama']]
            );
        }

        // 2. Jawa Barat Regencies & Cities (27 Kab/Kota)
        $provJabar = MasterProvinsi::where('kode', '32')->first();

        if ($provJabar) {
            $jabarRegencies = [
                ['kode' => '3201', 'nama' => 'Kabupaten Bogor'],
                ['kode' => '3202', 'nama' => 'Kabupaten Sukabumi'],
                ['kode' => '3203', 'nama' => 'Kabupaten Cianjur'],
                ['kode' => '3204', 'nama' => 'Kabupaten Bandung'],
                ['kode' => '3205', 'nama' => 'Kabupaten Garut'],
                ['kode' => '3206', 'nama' => 'Kabupaten Tasikmalaya'],
                ['kode' => '3207', 'nama' => 'Kabupaten Ciamis'],
                ['kode' => '3208', 'nama' => 'Kabupaten Kuningan'],
                ['kode' => '3209', 'nama' => 'Kabupaten Cirebon'],
                ['kode' => '3210', 'nama' => 'Kabupaten Majalengka'],
                ['kode' => '3211', 'nama' => 'Kabupaten Sumedang'],
                ['kode' => '3212', 'nama' => 'Kabupaten Indramayu'],
                ['kode' => '3213', 'nama' => 'Kabupaten Subang'],
                ['kode' => '3214', 'nama' => 'Kabupaten Purwakarta'],
                ['kode' => '3215', 'nama' => 'Kabupaten Karawang'],
                ['kode' => '3216', 'nama' => 'Kabupaten Bekasi'],
                ['kode' => '3217', 'nama' => 'Kabupaten Bandung Barat'],
                ['kode' => '3218', 'nama' => 'Kabupaten Pangandaran'],
                ['kode' => '3271', 'nama' => 'Kota Bogor'],
                ['kode' => '3272', 'nama' => 'Kota Sukabumi'],
                ['kode' => '3273', 'nama' => 'Kota Bandung'],
                ['kode' => '3274', 'nama' => 'Kota Cirebon'],
                ['kode' => '3275', 'nama' => 'Kota Bekasi'],
                ['kode' => '3276', 'nama' => 'Kota Depok'],
                ['kode' => '3277', 'nama' => 'Kota Cimahi'],
                ['kode' => '3278', 'nama' => 'Kota Tasikmalaya'],
                ['kode' => '3279', 'nama' => 'Kota Banjar'],
            ];

            foreach ($jabarRegencies as $r) {
                $existing = MasterKabupaten::where('provinsi_id', $provJabar->id)
                    ->where(function ($q) use ($r) {
                        $q->where('kode', $r['kode'])
                          ->orWhere('nama', $r['nama']);
                    })->first();

                if ($existing) {
                    $existing->update([
                        'kode' => $r['kode'],
                        'nama' => $r['nama'],
                    ]);
                } else {
                    MasterKabupaten::create([
                        'provinsi_id' => $provJabar->id,
                        'kode' => $r['kode'],
                        'nama' => $r['nama'],
                    ]);
                }
            }
        }

        // 3. Kabupaten Garut Districts (42 Kecamatan dengan kode BPS resmi)
        $kabGarut = MasterKabupaten::where('kode', '3205')->first();

        if ($kabGarut) {
            $garutDistricts = [
                ['kode' => '3205010', 'nama' => 'Cisewu'],
                ['kode' => '3205011', 'nama' => 'Caringin'],
                ['kode' => '3205020', 'nama' => 'Talegong'],
                ['kode' => '3205030', 'nama' => 'Bungbulang'],
                ['kode' => '3205031', 'nama' => 'Mekarmukti'],
                ['kode' => '3205040', 'nama' => 'Pamulihan'],
                ['kode' => '3205050', 'nama' => 'Pakenjeng'],
                ['kode' => '3205060', 'nama' => 'Cikelet'],
                ['kode' => '3205070', 'nama' => 'Pameungpeuk'],
                ['kode' => '3205080', 'nama' => 'Cibalong'],
                ['kode' => '3205090', 'nama' => 'Cisompet'],
                ['kode' => '3205100', 'nama' => 'Peundeuy'],
                ['kode' => '3205110', 'nama' => 'Singajaya'],
                ['kode' => '3205111', 'nama' => 'Cihurip'],
                ['kode' => '3205120', 'nama' => 'Cikajang'],
                ['kode' => '3205130', 'nama' => 'Banjarwangi'],
                ['kode' => '3205140', 'nama' => 'Cilawu'],
                ['kode' => '3205150', 'nama' => 'Bayongbong'],
                ['kode' => '3205151', 'nama' => 'Cigedug'],
                ['kode' => '3205160', 'nama' => 'Cisurupan'],
                ['kode' => '3205161', 'nama' => 'Sukaresmi'],
                ['kode' => '3205170', 'nama' => 'Samarang'],
                ['kode' => '3205171', 'nama' => 'Pasirwangi'],
                ['kode' => '3205181', 'nama' => 'Tarogong Kidul'],
                ['kode' => '3205182', 'nama' => 'Tarogong Kaler'],
                ['kode' => '3205190', 'nama' => 'Garut Kota'],
                ['kode' => '3205200', 'nama' => 'Karangpawitan'],
                ['kode' => '3205210', 'nama' => 'Wanaraja'],
                ['kode' => '3205211', 'nama' => 'Sucinaraja'],
                ['kode' => '3205212', 'nama' => 'Pangatikan'],
                ['kode' => '3205220', 'nama' => 'Sukawening'],
                ['kode' => '3205221', 'nama' => 'Karangtengah'],
                ['kode' => '3205230', 'nama' => 'Banyuresmi'],
                ['kode' => '3205240', 'nama' => 'Leles'],
                ['kode' => '3205250', 'nama' => 'Leuwigoong'],
                ['kode' => '3205260', 'nama' => 'Cibatu'],
                ['kode' => '3205261', 'nama' => 'Kersamanah'],
                ['kode' => '3205270', 'nama' => 'Cibiuk'],
                ['kode' => '3205280', 'nama' => 'Kadungora'],
                ['kode' => '3205290', 'nama' => 'Blubur Limbangan'],
                ['kode' => '3205300', 'nama' => 'Selaawi'],
                ['kode' => '3205310', 'nama' => 'Malangbong'],
            ];

            foreach ($garutDistricts as $d) {
                $existing = MasterKecamatan::where('kabupaten_id', $kabGarut->id)
                    ->where(function ($q) use ($d) {
                        $q->where('kode', $d['kode'])
                          ->orWhere('nama', $d['nama']);
                    })->first();

                if ($existing) {
                    $existing->update([
                        'kode' => $d['kode'],
                        'nama' => $d['nama'],
                    ]);
                } else {
                    MasterKecamatan::create([
                        'kabupaten_id' => $kabGarut->id,
                        'kode' => $d['kode'],
                        'nama' => $d['nama'],
                    ]);
                }
            }

            // 4. Sample Desa untuk Tarogong Kidul, Tarogong Kaler, & Garut Kota
            $kecTarkid = MasterKecamatan::where('kabupaten_id', $kabGarut->id)->where('kode', '3205181')->first();
            if ($kecTarkid) {
                // Remove non-existent Patallassang from Garut if present
                MasterDesa::where('kecamatan_id', $kecTarkid->id)->where('nama', 'Patallassang')->delete();

                $tarkidVillages = [
                    ['kode' => '3205181001', 'nama' => 'Kersamenak', 'kode_pos' => '44151'],
                    ['kode' => '3205181002', 'nama' => 'Cibunar', 'kode_pos' => '44151'],
                    ['kode' => '3205181003', 'nama' => 'Sukabakti', 'kode_pos' => '44151'],
                    ['kode' => '3205181004', 'nama' => 'Sukakarya', 'kode_pos' => '44151'],
                    ['kode' => '3205181005', 'nama' => 'Sukajaya', 'kode_pos' => '44151'],
                    ['kode' => '3205181006', 'nama' => 'Jayawaras', 'kode_pos' => '44151'],
                    ['kode' => '3205181007', 'nama' => 'Haurpanggung', 'kode_pos' => '44151'],
                    ['kode' => '3205181008', 'nama' => 'Jayaraga', 'kode_pos' => '44151'],
                    ['kode' => '3205181009', 'nama' => 'Pataruman', 'kode_pos' => '44151'],
                    ['kode' => '3205181010', 'nama' => 'Sukagalih', 'kode_pos' => '44151'],
                    ['kode' => '3205181011', 'nama' => 'Mekargalih', 'kode_pos' => '44151'],
                    ['kode' => '3205181012', 'nama' => 'Tarogong', 'kode_pos' => '44151'],
                ];
                foreach ($tarkidVillages as $v) {
                    $existing = MasterDesa::where('kecamatan_id', $kecTarkid->id)
                        ->where(function ($q) use ($v) {
                            $q->where('kode', $v['kode'])
                              ->orWhere('nama', $v['nama']);
                        })->first();

                    if ($existing) {
                        $existing->update(['kode' => $v['kode'], 'nama' => $v['nama'], 'kode_pos' => $v['kode_pos']]);
                    } else {
                        MasterDesa::create([
                            'kecamatan_id' => $kecTarkid->id,
                            'kode' => $v['kode'],
                            'nama' => $v['nama'],
                            'kode_pos' => $v['kode_pos'],
                        ]);
                    }
                }
            }

            $kecTarkal = MasterKecamatan::where('kabupaten_id', $kabGarut->id)->where('kode', '3205182')->first();
            if ($kecTarkal) {
                $tarkalVillages = [
                    ['kode' => '3205182001', 'nama' => 'Sirnajaya', 'kode_pos' => '44151'],
                    ['kode' => '3205182002', 'nama' => 'Rancabango', 'kode_pos' => '44151'],
                    ['kode' => '3205182003', 'nama' => 'Cimurah', 'kode_pos' => '44151'],
                    ['kode' => '3205182004', 'nama' => 'Jati', 'kode_pos' => '44151'],
                    ['kode' => '3205182005', 'nama' => 'Tanjung Kamuning', 'kode_pos' => '44151'],
                    ['kode' => '3205182006', 'nama' => 'Sukawangi', 'kode_pos' => '44151'],
                    ['kode' => '3205182007', 'nama' => 'Sukajadi', 'kode_pos' => '44151'],
                    ['kode' => '3205182008', 'nama' => 'Mekarwangi', 'kode_pos' => '44151'],
                    ['kode' => '3205182009', 'nama' => 'Panembong', 'kode_pos' => '44151'],
                    ['kode' => '3205182010', 'nama' => 'Sukaratu', 'kode_pos' => '44151'],
                    ['kode' => '3205182011', 'nama' => 'Mekarjaya', 'kode_pos' => '44151'],
                    ['kode' => '3205182012', 'nama' => 'Pananjung', 'kode_pos' => '44151'],
                    ['kode' => '3205182013', 'nama' => 'Pasawahan', 'kode_pos' => '44151'],
                ];
                foreach ($tarkalVillages as $v) {
                    $existing = MasterDesa::where('kecamatan_id', $kecTarkal->id)
                        ->where(function ($q) use ($v) {
                            $q->where('kode', $v['kode'])
                              ->orWhere('nama', $v['nama']);
                        })->first();

                    if ($existing) {
                        $existing->update(['kode' => $v['kode'], 'nama' => $v['nama'], 'kode_pos' => $v['kode_pos']]);
                    } else {
                        MasterDesa::create([
                            'kecamatan_id' => $kecTarkal->id,
                            'kode' => $v['kode'],
                            'nama' => $v['nama'],
                            'kode_pos' => $v['kode_pos'],
                        ]);
                    }
                }
            }

            $kecGarkot = MasterKecamatan::where('kabupaten_id', $kabGarut->id)->where('kode', '3205190')->first();
            if ($kecGarkot) {
                $garkotVillages = [
                    ['kode' => '3205190001', 'nama' => 'Margawati', 'kode_pos' => '44111'],
                    ['kode' => '3205190002', 'nama' => 'Sukanegla', 'kode_pos' => '44111'],
                    ['kode' => '3205190003', 'nama' => 'Cimuncang', 'kode_pos' => '44111'],
                    ['kode' => '3205190004', 'nama' => 'Kotawetan', 'kode_pos' => '44111'],
                    ['kode' => '3205190005', 'nama' => 'Kota Kulon', 'kode_pos' => '44111'],
                    ['kode' => '3205190006', 'nama' => 'Muara Sanding', 'kode_pos' => '44111'],
                    ['kode' => '3205190007', 'nama' => 'Paminggir', 'kode_pos' => '44111'],
                    ['kode' => '3205190008', 'nama' => 'Regol', 'kode_pos' => '44111'],
                    ['kode' => '3205190009', 'nama' => 'Ciwalen', 'kode_pos' => '44111'],
                    ['kode' => '3205190010', 'nama' => 'Pakuwon', 'kode_pos' => '44111'],
                    ['kode' => '3205190011', 'nama' => 'Sukamantri', 'kode_pos' => '44111'],
                ];
                foreach ($garkotVillages as $v) {
                    $existing = MasterDesa::where('kecamatan_id', $kecGarkot->id)
                        ->where(function ($q) use ($v) {
                            $q->where('kode', $v['kode'])
                              ->orWhere('nama', $v['nama'])
                              ->orWhere('nama', str_replace(' ', '', $v['nama']));
                        })->first();

                    if ($existing) {
                        $existing->update(['kode' => $v['kode'], 'nama' => $v['nama'], 'kode_pos' => $v['kode_pos']]);
                    } else {
                        MasterDesa::create([
                            'kecamatan_id' => $kecGarkot->id,
                            'kode' => $v['kode'],
                            'nama' => $v['nama'],
                            'kode_pos' => $v['kode_pos'],
                        ]);
                    }
                }
            }
        }
    }
}
