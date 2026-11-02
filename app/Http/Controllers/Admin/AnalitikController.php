<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterProgram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalitikController extends Controller
{
    /**
     * Tampilkan Dashboard Analitik Komprehensif Calon Murid & Orang Tua.
     */
    public function index(Request $request): View
    {
        $gelombangId = $request->input('gelombang_id');
        $programId = $request->input('program_id');

        $query = CalonSiswa::tanpaDemo();
        if (!empty($gelombangId)) {
            $query->where('calon_siswa.gelombang_id', $gelombangId);
        }
        if (!empty($programId)) {
            $query->where('calon_siswa.program_id', $programId);
        }

        $gelombangList = MasterGelombang::orderBy('periode_mulai')->get();
        $programList = MasterProgram::where('aktif', true)->get();

        $totalMurid = (clone $query)->count();
        $calonSiswaIds = (clone $query)->pluck('calon_siswa.id')->toArray();

        // 1. Analisis Asal SMP (Top 10 Asal Sekolah)
        $sekolahRaw = (clone $query)
            ->leftJoin('master_sekolah_asal', 'calon_siswa.asal_sekolah_id', '=', 'master_sekolah_asal.id')
            ->selectRaw('COALESCE(NULLIF(master_sekolah_asal.nama_sekolah, ""), NULLIF(calon_siswa.asal_sekolah_lainnya, ""), "Belum Diisi") as nama, count(*) as total')
            ->groupBy('nama')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $sekolahLabels = $sekolahRaw->pluck('nama')->toArray();
        $sekolahCounts = $sekolahRaw->pluck('total')->toArray();

        // 2. Analisis Wilayah Domisili (Kabupaten & Kecamatan)
        $kabupatenRaw = (clone $query)
            ->selectRaw('COALESCE(NULLIF(kabupaten_nama, ""), "Belum Terdata") as nama, count(*) as total')
            ->groupBy('nama')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $kabupatenLabels = $kabupatenRaw->pluck('nama')->toArray();
        $kabupatenCounts = $kabupatenRaw->pluck('total')->toArray();

        $kecamatanRaw = (clone $query)
            ->selectRaw('COALESCE(NULLIF(kecamatan_nama, ""), "Belum Terdata") as nama, count(*) as total')
            ->groupBy('nama')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $kecamatanLabels = $kecamatanRaw->pluck('nama')->toArray();
        $kecamatanCounts = $kecamatanRaw->pluck('total')->toArray();

        // 3. Analisis Usia Calon Murid (Dihitung dari tanggal_lahir)
        $currentYear = (int) date('Y');
        $usiaMuridBins = [
            '< 14 Tahun' => 0,
            '14 Tahun' => 0,
            '15 Tahun' => 0,
            '16 Tahun' => 0,
            '> 16 Tahun' => 0,
            'Belum Diisi' => 0,
        ];

        $tglLahirList = (clone $query)->pluck('tanggal_lahir')->toArray();
        foreach ($tglLahirList as $tgl) {
            if (empty($tgl)) {
                $usiaMuridBins['Belum Diisi']++;
                continue;
            }
            try {
                $birthYear = Carbon::parse($tgl)->year;
                $age = $currentYear - $birthYear;
                if ($age < 14) {
                    $usiaMuridBins['< 14 Tahun']++;
                } elseif ($age === 14) {
                    $usiaMuridBins['14 Tahun']++;
                } elseif ($age === 15) {
                    $usiaMuridBins['15 Tahun']++;
                } elseif ($age === 16) {
                    $usiaMuridBins['16 Tahun']++;
                } else {
                    $usiaMuridBins['> 16 Tahun']++;
                }
            } catch (\Throwable) {
                $usiaMuridBins['Belum Diisi']++;
            }
        }

        // 4. Tahun Lulus Murid
        $tahunLulusRaw = (clone $query)
            ->selectRaw('COALESCE(NULLIF(tahun_lulus, ""), "Belum Diisi") as tahun, count(*) as total')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->limit(6)
            ->get();

        $tahunLulusLabels = $tahunLulusRaw->pluck('tahun')->toArray();
        $tahunLulusCounts = $tahunLulusRaw->pluck('total')->toArray();

        // 5. Kesehatan dan Fisik (data_kesehatan)
        $goldarRaw = DB::table('data_kesehatan')
            ->whereIn('calon_siswa_id', $calonSiswaIds)
            ->selectRaw('COALESCE(NULLIF(golongan_darah, ""), "Tidak Tahu") as nama, count(*) as total')
            ->groupBy('nama')
            ->orderByDesc('total')
            ->get();
        $goldarLabels = $goldarRaw->pluck('nama')->toArray();
        $goldarCounts = $goldarRaw->pluck('total')->toArray();

        $butaWarnaRaw = DB::table('data_kesehatan')
            ->whereIn('calon_siswa_id', $calonSiswaIds)
            ->selectRaw('COALESCE(NULLIF(buta_warna, ""), "Belum Tes") as status, count(*) as total')
            ->groupBy('status')
            ->get();
        $butaWarnaLabels = $butaWarnaRaw->pluck('status')->toArray();
        $butaWarnaCounts = $butaWarnaRaw->pluck('total')->toArray();

        // IMT / Kategori Fisik
        $imtBins = [
            'Kurus (< 18.5)' => 0,
            'Normal (18.5 - 24.9)' => 0,
            'Kelebihan BB (25 - 29.9)' => 0,
            'Obesitas (>= 30)' => 0,
            'Belum Lengkap' => 0,
        ];

        $kesehatanRows = DB::table('data_kesehatan')
            ->whereIn('calon_siswa_id', $calonSiswaIds)
            ->select('tinggi_badan', 'berat_badan')
            ->get();

        $totalKesehatanTerisi = $kesehatanRows->count();
        $imtBins['Belum Lengkap'] = max(0, $totalMurid - $totalKesehatanTerisi);

        foreach ($kesehatanRows as $k) {
            $tb = (float) ($k->tinggi_badan ?? 0);
            $bb = (float) ($k->berat_badan ?? 0);
            if ($tb > 50 && $bb > 10) {
                $tbMeter = $tb / 100.0;
                $bmi = $bb / ($tbMeter * $tbMeter);
                if ($bmi < 18.5) {
                    $imtBins['Kurus (< 18.5)']++;
                } elseif ($bmi < 25.0) {
                    $imtBins['Normal (18.5 - 24.9)']++;
                } elseif ($bmi < 30.0) {
                    $imtBins['Kelebihan BB (25 - 29.9)']++;
                } else {
                    $imtBins['Obesitas (>= 30)']++;
                }
            } else {
                $imtBins['Belum Lengkap']++;
            }
        }

        // 6. Rata-rata Nilai Rapor
        $avgRapor = DB::table('nilai_rapor')
            ->whereIn('calon_siswa_id', $calonSiswaIds)
            ->selectRaw('
                ROUND(AVG(NULLIF((COALESCE(mtk_sem1,0)+COALESCE(mtk_sem2,0)+COALESCE(mtk_sem3,0)+COALESCE(mtk_sem4,0)+COALESCE(mtk_sem5,0))/5, 0)), 1) as avg_mtk,
                ROUND(AVG(NULLIF((COALESCE(ind_sem1,0)+COALESCE(ind_sem2,0)+COALESCE(ind_sem3,0)+COALESCE(ind_sem4,0)+COALESCE(ind_sem5,0))/5, 0)), 1) as avg_ind,
                ROUND(AVG(NULLIF((COALESCE(eng_sem1,0)+COALESCE(eng_sem2,0)+COALESCE(eng_sem3,0)+COALESCE(eng_sem4,0)+COALESCE(eng_sem5,0))/5, 0)), 1) as avg_eng,
                ROUND(AVG(NULLIF((COALESCE(pai_sem1,0)+COALESCE(pai_sem2,0)+COALESCE(pai_sem3,0)+COALESCE(pai_sem4,0)+COALESCE(pai_sem5,0))/5, 0)), 1) as avg_pai
            ')->first();

        $raporData = [
            'labels' => ['Matematika', 'B. Indonesia', 'B. Inggris', 'Pendidikan Agama (PAI)'],
            'scores' => [
                (float) ($avgRapor?->avg_mtk ?? 0),
                (float) ($avgRapor?->avg_ind ?? 0),
                (float) ($avgRapor?->avg_eng ?? 0),
                (float) ($avgRapor?->avg_pai ?? 0),
            ],
        ];

        // 7. Profil Orang Tua (Ayah dan Ibu)
        $ortuRows = DB::table('data_orangtua')
            ->leftJoin('master_pekerjaan as p_ayah', 'data_orangtua.pekerjaan_ayah_id', '=', 'p_ayah.id')
            ->leftJoin('master_pekerjaan as p_ibu', 'data_orangtua.pekerjaan_ibu_id', '=', 'p_ibu.id')
            ->whereIn('data_orangtua.calon_siswa_id', $calonSiswaIds)
            ->select(
                'data_orangtua.tahun_lahir_ayah',
                'data_orangtua.tahun_lahir_ibu',
                'data_orangtua.penghasilan_ayah',
                'data_orangtua.penghasilan_ibu',
                'p_ayah.nama as pekerjaan_ayah',
                'p_ibu.nama as pekerjaan_ibu'
            )
            ->get();

        // Usia Ayah & Ibu
        $usiaAyahBins = ['< 40 Tahun' => 0, '40 - 49 Tahun' => 0, '50 - 59 Tahun' => 0, '>= 60 Tahun' => 0, 'Belum Diisi' => 0];
        $usiaIbuBins  = ['< 40 Tahun' => 0, '40 - 49 Tahun' => 0, '50 - 59 Tahun' => 0, '>= 60 Tahun' => 0, 'Belum Diisi' => 0];

        $pekerjaanAyahCounts = [];
        $pekerjaanIbuCounts  = [];
        $penghasilanAyahCounts = [];
        $penghasilanIbuCounts  = [];

        foreach ($ortuRows as $row) {
            // Usia Ayah
            $thnAyah = (int) preg_replace('/\D/', '', (string) $row->tahun_lahir_ayah);
            if ($thnAyah >= 1930 && $thnAyah <= $currentYear) {
                $u = $currentYear - $thnAyah;
                if ($u < 40) $usiaAyahBins['< 40 Tahun']++;
                elseif ($u < 50) $usiaAyahBins['40 - 49 Tahun']++;
                elseif ($u < 60) $usiaAyahBins['50 - 59 Tahun']++;
                else $usiaAyahBins['>= 60 Tahun']++;
            } else {
                $usiaAyahBins['Belum Diisi']++;
            }

            // Usia Ibu
            $thnIbu = (int) preg_replace('/\D/', '', (string) $row->tahun_lahir_ibu);
            if ($thnIbu >= 1930 && $thnIbu <= $currentYear) {
                $u = $currentYear - $thnIbu;
                if ($u < 40) $usiaIbuBins['< 40 Tahun']++;
                elseif ($u < 50) $usiaIbuBins['40 - 49 Tahun']++;
                elseif ($u < 60) $usiaIbuBins['50 - 59 Tahun']++;
                else $usiaIbuBins['>= 60 Tahun']++;
            } else {
                $usiaIbuBins['Belum Diisi']++;
            }

            // Pekerjaan
            $pkAyah = !empty($row->pekerjaan_ayah) ? $row->pekerjaan_ayah : 'Belum Diisi';
            $pekerjaanAyahCounts[$pkAyah] = ($pekerjaanAyahCounts[$pkAyah] ?? 0) + 1;

            $pkIbu = !empty($row->pekerjaan_ibu) ? $row->pekerjaan_ibu : 'Belum Diisi';
            $pekerjaanIbuCounts[$pkIbu] = ($pekerjaanIbuCounts[$pkIbu] ?? 0) + 1;

            // Penghasilan
            $phAyah = !empty($row->penghasilan_ayah) ? $row->penghasilan_ayah : 'Belum Diisi';
            $penghasilanAyahCounts[$phAyah] = ($penghasilanAyahCounts[$phAyah] ?? 0) + 1;

            $phIbu = !empty($row->penghasilan_ibu) ? $row->penghasilan_ibu : 'Belum Diisi';
            $penghasilanIbuCounts[$phIbu] = ($penghasilanIbuCounts[$phIbu] ?? 0) + 1;
        }

        // Tambah selisih jika ortu belum dibuat sama sekali
        $selisihBelumAdaOrtu = max(0, $totalMurid - $ortuRows->count());
        $usiaAyahBins['Belum Diisi'] += $selisihBelumAdaOrtu;
        $usiaIbuBins['Belum Diisi']  += $selisihBelumAdaOrtu;
        if ($selisihBelumAdaOrtu > 0) {
            $pekerjaanAyahCounts['Belum Diisi'] = ($pekerjaanAyahCounts['Belum Diisi'] ?? 0) + $selisihBelumAdaOrtu;
            $pekerjaanIbuCounts['Belum Diisi'] = ($pekerjaanIbuCounts['Belum Diisi'] ?? 0) + $selisihBelumAdaOrtu;
            $penghasilanAyahCounts['Belum Diisi'] = ($penghasilanAyahCounts['Belum Diisi'] ?? 0) + $selisihBelumAdaOrtu;
            $penghasilanIbuCounts['Belum Diisi'] = ($penghasilanIbuCounts['Belum Diisi'] ?? 0) + $selisihBelumAdaOrtu;
        }

        // Sort descending and take top 6 for job & income
        arsort($pekerjaanAyahCounts);
        arsort($pekerjaanIbuCounts);
        arsort($penghasilanAyahCounts);
        arsort($penghasilanIbuCounts);

        return view('admin.analitik.index', [
            'totalMurid' => $totalMurid,
            'gelombangList' => $gelombangList,
            'programList' => $programList,
            'selectedGelombang' => $gelombangId,
            'selectedProgram' => $programId,

            // Charts data
            'sekolahLabels' => $sekolahLabels,
            'sekolahCounts' => $sekolahCounts,

            'kabupatenLabels' => $kabupatenLabels,
            'kabupatenCounts' => $kabupatenCounts,

            'kecamatanLabels' => $kecamatanLabels,
            'kecamatanCounts' => $kecamatanCounts,

            'usiaMuridLabels' => array_keys($usiaMuridBins),
            'usiaMuridCounts' => array_values($usiaMuridBins),

            'tahunLulusLabels' => $tahunLulusLabels,
            'tahunLulusCounts' => $tahunLulusCounts,

            'goldarLabels' => $goldarLabels,
            'goldarCounts' => $goldarCounts,

            'butaWarnaLabels' => $butaWarnaLabels,
            'butaWarnaCounts' => $butaWarnaCounts,

            'imtLabels' => array_keys($imtBins),
            'imtCounts' => array_values($imtBins),

            'raporData' => $raporData,

            'usiaAyahLabels' => array_keys($usiaAyahBins),
            'usiaAyahCounts' => array_values($usiaAyahBins),

            'usiaIbuLabels' => array_keys($usiaIbuBins),
            'usiaIbuCounts' => array_values($usiaIbuBins),

            'pekerjaanAyahLabels' => array_keys(array_slice($pekerjaanAyahCounts, 0, 8)),
            'pekerjaanAyahCounts' => array_values(array_slice($pekerjaanAyahCounts, 0, 8)),

            'pekerjaanIbuLabels' => array_keys(array_slice($pekerjaanIbuCounts, 0, 8)),
            'pekerjaanIbuCounts' => array_values(array_slice($pekerjaanIbuCounts, 0, 8)),

            'penghasilanAyahLabels' => array_keys(array_slice($penghasilanAyahCounts, 0, 8)),
            'penghasilanAyahCounts' => array_values(array_slice($penghasilanAyahCounts, 0, 8)),

            'penghasilanIbuLabels' => array_keys(array_slice($penghasilanIbuCounts, 0, 8)),
            'penghasilanIbuCounts' => array_values(array_slice($penghasilanIbuCounts, 0, 8)),
        ]);
    }
}
