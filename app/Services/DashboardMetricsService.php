<?php

namespace App\Services;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Define the official academic year SPMB months: Sep 2026 - Jun 2027 (10 months).
     */
    public const TIMELINE_MONTHS = [
        ['year' => 2026, 'month' => 9,  'ym' => '2026-09', 'label' => 'Sep 2026', 'short' => 'Sep', 'days' => 30],
        ['year' => 2026, 'month' => 10, 'ym' => '2026-10', 'label' => 'Okt 2026', 'short' => 'Okt', 'days' => 31],
        ['year' => 2026, 'month' => 11, 'ym' => '2026-11', 'label' => 'Nov 2026', 'short' => 'Nov', 'days' => 30],
        ['year' => 2026, 'month' => 12, 'ym' => '2026-12', 'label' => 'Des 2026', 'short' => 'Des', 'days' => 31],
        ['year' => 2027, 'month' => 1,  'ym' => '2027-01', 'label' => 'Jan 2027', 'short' => 'Jan', 'days' => 31],
        ['year' => 2027, 'month' => 2,  'ym' => '2027-02', 'label' => 'Feb 2027', 'short' => 'Feb', 'days' => 28],
        ['year' => 2027, 'month' => 3,  'ym' => '2027-03', 'label' => 'Mar 2027', 'short' => 'Mar', 'days' => 31],
        ['year' => 2027, 'month' => 4,  'ym' => '2027-04', 'label' => 'Apr 2027', 'short' => 'Apr', 'days' => 30],
        ['year' => 2027, 'month' => 5,  'ym' => '2027-05', 'label' => 'Mei 2027', 'short' => 'Mei', 'days' => 31],
        ['year' => 2027, 'month' => 6,  'ym' => '2027-06', 'label' => 'Jun 2027', 'short' => 'Jun', 'days' => 30],
    ];

    /**
     * Get registration weekly & monthly timeline metrics for September 2026 to June 2027.
     */
    public function getTimelinePendaftar(): array
    {
        $startDate = Carbon::create(2026, 9, 1, 0, 0, 0);
        $endDate   = Carbon::create(2027, 6, 30, 23, 59, 59);

        // Build 49 weekly intervals
        $weeks = [];
        $weekIndexMap = [];
        $index = 0;
        foreach (self::TIMELINE_MONTHS as $m) {
            $numWeeks = (int) ceil($m['days'] / 7);
            for ($w = 1; $w <= $numWeeks; $w++) {
                $startDay = ($w - 1) * 7 + 1;
                $endDay   = min($w * 7, $m['days']);
                $key      = "{$m['ym']}-w{$w}";

                $weeks[] = [
                    'key'   => $key,
                    'label' => "M{$w} {$m['short']}",
                    'range' => "{$startDay} - {$endDay} {$m['short']} {$m['year']}",
                ];
                $weekIndexMap[$key] = $index++;
            }
        }

        $acceptedStatuses = [
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ];

        $candidates = CalonSiswa::whereBetween('created_at', [$startDate, $endDate])
            ->select(['id', 'status_spmb', 'created_at'])
            ->get();

        $registeredByMonth = [];
        $acceptedByMonth   = [];
        $registeredByWeek  = [];
        $acceptedByWeek    = [];

        foreach ($candidates as $cs) {
            $date = $cs->created_at;
            $ym   = $date->format('Y-m');
            $d    = (int) $date->format('j');
            $daysInMonth = (int) $date->daysInMonth;
            $w    = min((int) ceil($d / 7), (int) ceil($daysInMonth / 7));
            $weekKey = "{$ym}-w{$w}";

            $registeredByMonth[$ym]     = ($registeredByMonth[$ym] ?? 0) + 1;
            $registeredByWeek[$weekKey] = ($registeredByWeek[$weekKey] ?? 0) + 1;

            $statusVal = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '');
            if (in_array($statusVal, $acceptedStatuses, true)) {
                $acceptedByMonth[$ym]   = ($acceptedByMonth[$ym] ?? 0) + 1;
                $acceptedByWeek[$weekKey] = ($acceptedByWeek[$weekKey] ?? 0) + 1;
            }
        }

        // Build Weekly Timeline dataset
        $weeklyLabels     = [];
        $weeklyRanges     = [];
        $weeklyPendaftar  = [];
        $weeklyDiterima   = [];
        $weeklyKumulatif  = [];
        $weeklyRunningSum = 0;
        $weeklyPeakValue  = 0;
        $weeklyPeakLabel  = '-';

        foreach ($weeks as $wItem) {
            $key    = $wItem['key'];
            $countP = (int) ($registeredByWeek[$key] ?? 0);
            $countD = (int) ($acceptedByWeek[$key] ?? 0);

            $weeklyRunningSum += $countP;

            $weeklyLabels[]    = $wItem['label'];
            $weeklyRanges[]    = $wItem['range'];
            $weeklyPendaftar[] = $countP;
            $weeklyDiterima[]  = $countD;
            $weeklyKumulatif[] = $weeklyRunningSum;

            if ($countP > $weeklyPeakValue) {
                $weeklyPeakValue = $countP;
                $weeklyPeakLabel = "{$wItem['label']} ({$countP})";
            }
        }

        $weeklyTotal   = array_sum($weeklyPendaftar);
        $weeklyAverage = count($weeklyPendaftar) > 0 ? round($weeklyTotal / count($weeklyPendaftar), 1) : 0;

        // Build Monthly Timeline dataset
        $monthlyLabels     = [];
        $monthlyPendaftar  = [];
        $monthlyDiterima   = [];
        $monthlyKumulatif  = [];
        $monthlyRunningSum = 0;
        $monthlyPeakValue  = 0;
        $monthlyPeakLabel  = '-';

        foreach (self::TIMELINE_MONTHS as $m) {
            $ym     = $m['ym'];
            $countP = (int) ($registeredByMonth[$ym] ?? 0);
            $countD = (int) ($acceptedByMonth[$ym] ?? 0);

            $monthlyRunningSum += $countP;

            $monthlyLabels[]    = $m['label'];
            $monthlyPendaftar[] = $countP;
            $monthlyDiterima[]  = $countD;
            $monthlyKumulatif[] = $monthlyRunningSum;

            if ($countP > $monthlyPeakValue) {
                $monthlyPeakValue = $countP;
                $monthlyPeakLabel = "{$m['label']} ({$countP})";
            }
        }

        $monthlyTotal   = array_sum($monthlyPendaftar);
        $monthlyAverage = count($monthlyPendaftar) > 0 ? round($monthlyTotal / count($monthlyPendaftar), 1) : 0;

        // Static Milestones for Vertical Marker Lines
        $milestones = [
            [
                'title'         => 'Pembukaan',
                'date_label'    => '1 Okt 2026',
                'color'         => '#10b981', // emerald
                'weekly_index'  => $weekIndexMap['2026-10-w1'] ?? 5,
                'monthly_index' => 1,
            ],
            [
                'title'         => 'Gelombang 1',
                'date_label'    => '31 Des 2026',
                'color'         => '#3b82f6', // blue
                'weekly_index'  => $weekIndexMap['2026-12-w5'] ?? 19,
                'monthly_index' => 3,
            ],
            [
                'title'         => 'Gelombang 2',
                'date_label'    => '28 Feb 2027',
                'color'         => '#8b5cf6', // purple
                'weekly_index'  => $weekIndexMap['2027-02-w4'] ?? 28,
                'monthly_index' => 5,
            ],
            [
                'title'         => 'Gelombang 3',
                'date_label'    => '30 Jun 2027',
                'color'         => '#ea580c', // orange
                'weekly_index'  => $weekIndexMap['2027-06-w5'] ?? 48,
                'monthly_index' => 9,
            ],
        ];

        return [
            'mingguan' => [
                'labels'         => $weeklyLabels,
                'ranges'         => $weeklyRanges,
                'pendaftar'      => $weeklyPendaftar,
                'diterima'       => $weeklyDiterima,
                'kumulatif'      => $weeklyKumulatif,
                'total_periode'  => $weeklyTotal,
                'total_diterima' => array_sum($weeklyDiterima),
                'puncak_label'   => $weeklyPeakLabel !== '-' ? $weeklyPeakLabel : 'Belum Ada',
                'peak_value'     => $weeklyPeakValue,
                'rata_rata'      => $weeklyAverage,
            ],
            'bulanan' => [
                'labels'          => $monthlyLabels,
                'pendaftar'       => $monthlyPendaftar,
                'diterima'        => $monthlyDiterima,
                'kumulatif'       => $monthlyKumulatif,
                'total_periode'   => $monthlyTotal,
                'total_diterima'  => array_sum($monthlyDiterima),
                'bulan_tertinggi' => $monthlyPeakLabel !== '-' ? $monthlyPeakLabel : 'Belum Ada',
                'peak_value'      => $monthlyPeakValue,
                'rata_rata'       => $monthlyAverage,
            ],
            'milestones' => $milestones,

            // Root backwards-compatibility aliases (defaulting to weekly):
            'labels'          => $weeklyLabels,
            'ranges'          => $weeklyRanges,
            'pendaftar'       => $weeklyPendaftar,
            'diterima'        => $weeklyDiterima,
            'kumulatif'       => $weeklyKumulatif,
            'total_periode'   => $weeklyTotal,
            'total_diterima'  => array_sum($weeklyDiterima),
            'bulan_tertinggi' => $weeklyPeakLabel !== '-' ? $weeklyPeakLabel : 'Belum Ada',
            'peak_value'      => $weeklyPeakValue,
            'rata_rata'       => $weeklyAverage,
        ];
    }

    /**
     * Funnel Konversi Alur Pendaftaran (6 Tahapan Utama).
     */
    public function getFunnelKonversi(): array
    {
        $total = CalonSiswa::count();
        if ($total === 0) {
            return [
                'total' => 0,
                'steps' => [],
            ];
        }

        // 1. Akun Terdaftar
        $step1 = $total;

        // 2. Pembayaran Seleksi Terverifikasi (status beyond REGISTRASI / MENUNGGU_PEMBAYARAN_SELEKSI)
        $step2 = CalonSiswa::whereNotIn('status_spmb', [
            SpmbStatus::REGISTRASI->value,
            SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI->value,
        ])->count();

        // 3. Berkas & Rapor Lengkap
        $step3 = CalonSiswa::whereIn('status_spmb', [
            SpmbStatus::DATA_LENGKAP->value,
            SpmbStatus::MENUNGGU_WAWANCARA->value,
            SpmbStatus::SUDAH_DIWAWANCARA->value,
            SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ])->count();

        // 4. Tes Wawancara Selesai
        $step4 = CalonSiswa::whereIn('status_spmb', [
            SpmbStatus::SUDAH_DIWAWANCARA->value,
            SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ])->count();

        // 5. Lulus / Diterima
        $step5 = CalonSiswa::whereIn('status_spmb', [
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ])->count();

        // 6. Resmi Terdaftar (Daftar Ulang Lunas)
        $step6 = CalonSiswa::where('status_spmb', SpmbStatus::RESMI_TERDAFTAR->value)->count();

        $calcPct = fn($val) => $total > 0 ? round(($val / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'steps' => [
                [
                    'name'        => '1. Registrasi Akun',
                    'count'       => $step1,
                    'pct'         => 100.0,
                    'color'       => 'from-blue-500 to-indigo-600',
                    'badge_class' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'icon'        => '📝',
                ],
                [
                    'name'        => '2. Bayar Seleksi Valid',
                    'count'       => $step2,
                    'pct'         => $calcPct($step2),
                    'color'       => 'from-cyan-500 to-teal-600',
                    'badge_class' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                    'icon'        => '💳',
                ],
                [
                    'name'        => '3. Berkas & Rapor Lengkap',
                    'count'       => $step3,
                    'pct'         => $calcPct($step3),
                    'color'       => 'from-amber-500 to-orange-600',
                    'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'icon'        => '📑',
                ],
                [
                    'name'        => '4. Wawancara Selesai',
                    'count'       => $step4,
                    'pct'         => $calcPct($step4),
                    'color'       => 'from-purple-500 to-violet-600',
                    'badge_class' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'icon'        => '🎙️',
                ],
                [
                    'name'        => '5. Dinyatakan Diterima',
                    'count'       => $step5,
                    'pct'         => $calcPct($step5),
                    'color'       => 'from-emerald-500 to-teal-600',
                    'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'icon'        => '🎓',
                ],
                [
                    'name'        => '6. Resmi Terdaftar (DU)',
                    'count'       => $step6,
                    'pct'         => $calcPct($step6),
                    'color'       => 'from-emerald-600 to-green-700',
                    'badge_class' => 'bg-green-50 text-green-700 border-green-200',
                    'icon'        => '🏆',
                ],
            ],
        ];
    }

    /**
     * Demographic and Program choices analysis.
     */
    public function getDemografiData(): array
    {
        $total = CalonSiswa::count();

        // Gender breakdown
        $laki = CalonSiswa::where('jenis_kelamin', 'L')->count();
        $perempuan = CalonSiswa::where('jenis_kelamin', 'P')->count();
        $belumIsiGender = max(0, $total - ($laki + $perempuan));

        // Program breakdown (Reguler vs Unggulan)
        $programs = MasterProgram::aktif()->get()->map(function ($p) use ($total) {
            $count = CalonSiswa::where('program_id', $p->id)->count();
            $pct   = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return [
                'id'    => $p->id,
                'nama'  => $p->nama,
                'count' => $count,
                'pct'   => $pct,
            ];
        });

        // Referensi / Sumber Informasi
        $referensiRaw = CalonSiswa::whereNotNull('referensi_jenis')
            ->select('referensi_jenis', DB::raw('count(*) as count'))
            ->groupBy('referensi_jenis')
            ->orderByDesc('count')
            ->get();

        $referensiMap = [
            'GURU_WIKRAMA_GARUT'     => 'Guru / Karyawan Wikrama',
            'SISWA_WIKRAMA_GARUT'    => 'Siswa / Alumni Wikrama',
            'SOSIAL_MEDIA'           => 'Media Sosial (IG, TikTok, FB)',
            'GURU_BK_SMP'            => 'Guru BK SMP / MTs',
            'BROSUR_PAMFLET'         => 'Brosur / Spanduk / Baliho',
            'KUNJUNGAN_SOSIALISASI'  => 'Sosialisasi Tim SPMB ke SMP',
            'TEMAN_KELUARGA'         => 'Rekomendasi Keluarga / Teman',
            'WEBSITE_INTERNET'       => 'Website Resmi / Google Search',
        ];

        $referensiStats = $referensiRaw->map(function ($r) use ($referensiMap, $total) {
            $label = $referensiMap[$r->referensi_jenis] ?? str_replace('_', ' ', $r->referensi_jenis);
            $pct   = $total > 0 ? round(($r->count / $total) * 100, 1) : 0;
            return [
                'kode'  => $r->referensi_jenis,
                'label' => $label,
                'count' => $r->count,
                'pct'   => $pct,
            ];
        });

        return [
            'gender' => [
                'laki'            => $laki,
                'laki_pct'        => $total > 0 ? round(($laki / $total) * 100, 1) : 0,
                'perempuan'       => $perempuan,
                'perempuan_pct'   => $total > 0 ? round(($perempuan / $total) * 100, 1) : 0,
                'belum_isi'       => $belumIsiGender,
            ],
            'programs'        => $programs,
            'program_gender'  => $this->getProgramGenderStats(),
            'referensi_stats' => $referensiStats,
        ];
    }

    /**
     * Total Pendaftar Program Unggulan & Reguler (All, Laki-laki, Perempuan).
     */
    public function getProgramGenderStats(): array
    {
        $raw = CalonSiswa::selectRaw("
            COALESCE(SUM(CASE WHEN master_program.kode = 'UGG' OR master_program.nama LIKE '%Unggulan%' THEN 1 ELSE 0 END), 0) as unggulan_all,
            COALESCE(SUM(CASE WHEN (master_program.kode = 'UGG' OR master_program.nama LIKE '%Unggulan%') AND calon_siswa.jenis_kelamin = 'L' THEN 1 ELSE 0 END), 0) as unggulan_laki,
            COALESCE(SUM(CASE WHEN (master_program.kode = 'UGG' OR master_program.nama LIKE '%Unggulan%') AND calon_siswa.jenis_kelamin = 'P' THEN 1 ELSE 0 END), 0) as unggulan_perempuan,
            COALESCE(SUM(CASE WHEN master_program.kode = 'REG' OR master_program.nama LIKE '%Reguler%' THEN 1 ELSE 0 END), 0) as reguler_all,
            COALESCE(SUM(CASE WHEN (master_program.kode = 'REG' OR master_program.nama LIKE '%Reguler%') AND calon_siswa.jenis_kelamin = 'L' THEN 1 ELSE 0 END), 0) as reguler_laki,
            COALESCE(SUM(CASE WHEN (master_program.kode = 'REG' OR master_program.nama LIKE '%Reguler%') AND calon_siswa.jenis_kelamin = 'P' THEN 1 ELSE 0 END), 0) as reguler_perempuan
        ")
        ->leftJoin('master_program', 'calon_siswa.program_id', '=', 'master_program.id')
        ->first();

        $uAll  = (int) ($raw->unggulan_all ?? 0);
        $uLaki = (int) ($raw->unggulan_laki ?? 0);
        $uPer  = (int) ($raw->unggulan_perempuan ?? 0);

        $rAll  = (int) ($raw->reguler_all ?? 0);
        $rLaki = (int) ($raw->reguler_laki ?? 0);
        $rPer  = (int) ($raw->reguler_perempuan ?? 0);

        return [
            'unggulan_all'           => $uAll,
            'unggulan_laki'          => $uLaki,
            'unggulan_perempuan'     => $uPer,
            'reguler_all'            => $rAll,
            'reguler_laki'           => $rLaki,
            'reguler_perempuan'      => $rPer,
            'unggulan_laki_pct'      => $uAll > 0 ? round(($uLaki / $uAll) * 100, 1) : 0,
            'unggulan_perempuan_pct' => $uAll > 0 ? round(($uPer / $uAll) * 100, 1) : 0,
            'reguler_laki_pct'       => $rAll > 0 ? round(($rLaki / $rAll) * 100, 1) : 0,
            'reguler_perempuan_pct'  => $rAll > 0 ? round(($rPer / $rAll) * 100, 1) : 0,
        ];
    }

    /**
     * Top Origin Junior High Schools (SMP / MTs Feeder Schools).
     * Mendukung sekolah dari master_sekolah_asal maupun input mandiri (asal_sekolah_lainnya).
     */
    public function getTopAsalSekolah(int $limit = 5): array
    {
        $total = CalonSiswa::count();

        $nameExpr = "TRIM(COALESCE(master_sekolah_asal.nama_sekolah, calon_siswa.asal_sekolah_lainnya))";
        $cityExpr = "MAX(COALESCE(master_sekolah_asal.kokab, calon_siswa.kabupaten_nama, 'Garut'))";

        $topSekolah = CalonSiswa::query()
            ->leftJoin('master_sekolah_asal', 'calon_siswa.asal_sekolah_id', '=', 'master_sekolah_asal.id')
            ->where(function ($q) {
                $q->whereNotNull('calon_siswa.asal_sekolah_id')
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('calon_siswa.asal_sekolah_lainnya')
                          ->where('calon_siswa.asal_sekolah_lainnya', '!=', '');
                  });
            })
            ->selectRaw("{$nameExpr} as nama, {$cityExpr} as kota, count(*) as total_siswa")
            ->groupBy('nama')
            ->orderByDesc('total_siswa')
            ->limit($limit)
            ->get()
            ->map(function ($row) use ($total) {
                $nama = $row->nama ?: 'Sekolah Tidak Terdaftar';
                $kota = $row->kota ?: 'Garut';
                $pct  = $total > 0 ? round(($row->total_siswa / $total) * 100, 1) : 0;

                return [
                    'nama'        => $nama,
                    'kota'        => $kota,
                    'total_siswa' => (int) $row->total_siswa,
                    'persentase'  => $pct,
                ];
            })
            ->toArray();

        // Count others / unregistered
        $registeredCount = array_sum(array_column($topSekolah, 'total_siswa'));
        $othersCount     = max(0, $total - $registeredCount);

        return [
            'top'          => $topSekolah,
            'total_siswa'  => $total,
            'others_count' => $othersCount,
        ];
    }
}
