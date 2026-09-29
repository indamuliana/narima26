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
        ['year' => 2026, 'month' => 9,  'ym' => '2026-09', 'label' => 'Sep 2026'],
        ['year' => 2026, 'month' => 10, 'ym' => '2026-10', 'label' => 'Okt 2026'],
        ['year' => 2026, 'month' => 11, 'ym' => '2026-11', 'label' => 'Nov 2026'],
        ['year' => 2026, 'month' => 12, 'ym' => '2026-12', 'label' => 'Des 2026'],
        ['year' => 2027, 'month' => 1,  'ym' => '2027-01', 'label' => 'Jan 2027'],
        ['year' => 2027, 'month' => 2,  'ym' => '2027-02', 'label' => 'Feb 2027'],
        ['year' => 2027, 'month' => 3,  'ym' => '2027-03', 'label' => 'Mar 2027'],
        ['year' => 2027, 'month' => 4,  'ym' => '2027-04', 'label' => 'Apr 2027'],
        ['year' => 2027, 'month' => 5,  'ym' => '2027-05', 'label' => 'Mei 2027'],
        ['year' => 2027, 'month' => 6,  'ym' => '2027-06', 'label' => 'Jun 2027'],
    ];

    /**
     * Get registration monthly timeline metrics for September 2026 to June 2027.
     */
    public function getTimelinePendaftar(): array
    {
        $startDate = Carbon::create(2026, 9, 1, 0, 0, 0);
        $endDate   = Carbon::create(2027, 6, 30, 23, 59, 59);

        $isSqlite = DB::getDriverName() === 'sqlite';
        $formatExpr = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        // Fetch monthly registrations grouped by YYYY-MM
        $registeredByMonth = CalonSiswa::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$formatExpr} as ym, count(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        // Fetch accepted/verified students grouped by YYYY-MM (using updated_at or created_at)
        $acceptedStatuses = [
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ];

        $acceptedByMonth = CalonSiswa::whereIn('status_spmb', $acceptedStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$formatExpr} as ym, count(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        $labels     = [];
        $pendaftar  = [];
        $diterima   = [];
        $kumulatif  = [];
        $runningSum = 0;

        $peakValue = 0;
        $peakMonth = '-';

        $currentYm = now()->format('Y-m');
        $bulanIniCount = 0;

        foreach (self::TIMELINE_MONTHS as $m) {
            $ym = $m['ym'];
            $countPendaftar = (int) ($registeredByMonth[$ym] ?? 0);
            $countDiterima  = (int) ($acceptedByMonth[$ym] ?? 0);

            $runningSum += $countPendaftar;

            $labels[]    = $m['label'];
            $pendaftar[] = $countPendaftar;
            $diterima[]  = $countDiterima;
            $kumulatif[] = $runningSum;

            if ($countPendaftar > $peakValue) {
                $peakValue = $countPendaftar;
                $peakMonth = $m['label'];
            }

            if ($ym === $currentYm) {
                $bulanIniCount = $countPendaftar;
            }
        }

        $totalPendaftar = array_sum($pendaftar);
        $averageMonthly = count($pendaftar) > 0 ? round($totalPendaftar / count($pendaftar), 1) : 0;

        return [
            'labels'          => $labels,
            'pendaftar'       => $pendaftar,
            'diterima'        => $diterima,
            'kumulatif'       => $kumulatif,
            'total_periode'   => $totalPendaftar,
            'total_diterima'  => array_sum($diterima),
            'bulan_tertinggi' => $peakMonth !== '-' ? "{$peakMonth} ({$peakValue})" : 'Belum Ada',
            'peak_value'      => $peakValue,
            'rata_rata'       => $averageMonthly,
            'bulan_ini'       => $bulanIniCount,
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
            'referensi_stats' => $referensiStats,
        ];
    }

    /**
     * Top Origin Junior High Schools (SMP / MTs Feeder Schools).
     */
    public function getTopAsalSekolah(int $limit = 5): array
    {
        $total = CalonSiswa::count();

        $topSekolah = CalonSiswa::whereNotNull('asal_sekolah_id')
            ->select('asal_sekolah_id', DB::raw('count(*) as total_siswa'))
            ->with('asalSekolah')
            ->groupBy('asal_sekolah_id')
            ->orderByDesc('total_siswa')
            ->limit($limit)
            ->get()
            ->map(function ($row) use ($total) {
                $nama = $row->asalSekolah?->nama_sekolah ?? 'Sekolah Tidak Terdaftar';
                $kota = $row->asalSekolah?->kabupaten_kota ?? 'Garut';
                $pct  = $total > 0 ? round(($row->total_siswa / $total) * 100, 1) : 0;

                return [
                    'nama'        => $nama,
                    'kota'        => $kota,
                    'total_siswa' => $row->total_siswa,
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
