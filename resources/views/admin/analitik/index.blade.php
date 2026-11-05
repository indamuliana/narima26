<x-layouts.app>
    <x-slot name="title">Analitik Calon Murid & Orang Tua — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @if(auth()->user()?->isKepalaSekolah())
            @include('kepala-sekolah.partials.sidebar')
        @else
            @include('admin.partials.sidebar')
        @endif
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Filter Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                    <span>📊</span>
                    <span>Analitik Calon Murid & Orang Tua</span>
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Ringkasan data demografi, asal sekolah, wilayah domisili, profil kesehatan, nilai akademik, serta profil ayah & ibu calon murid.
                </p>
            </div>

            <!-- Summary KPI Pill -->
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-nampi-orange flex items-center justify-center font-black text-lg">
                        👥
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Calon Murid</p>
                        <p class="text-lg font-black text-slate-800 leading-tight">{{ number_format($totalMurid, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Dropdown Bar -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
            <form method="GET" action="{{ route('admin.analitik.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Filter Data:</span>
                </div>

                <!-- Dropdown Gelombang -->
                <div class="min-w-[180px]">
                    <select name="gelombang_id" onchange="this.form.submit()"
                            class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-nampi-orange/20">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ (string)$selectedGelombang === (string)$g->id ? 'selected' : '' }}>
                                {{ $g->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Program -->
                <div class="min-w-[180px]">
                    <select name="program_id" onchange="this.form.submit()"
                            class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-nampi-orange/20">
                        <option value="">Semua Program / Jalur</option>
                        @foreach($programList as $p)
                            <option value="{{ $p->id }}" {{ (string)$selectedProgram === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Opsi Tampilkan Belum Diisi -->
                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold px-3 py-2 rounded-xl border transition-colors {{ $includeEmpty ? 'bg-orange-50 border-orange-300 text-nampi-orange' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    <input type="checkbox" name="include_empty" value="1" {{ $includeEmpty ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-nampi-orange focus:ring-nampi-orange">
                    <span>Sertakan Data yang Belum Diisi</span>
                </label>

                @if($selectedGelombang || $selectedProgram || $includeEmpty)
                    <a href="{{ route('admin.analitik.index') }}"
                       class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 1: DEMOGRAFI & WILAYAH CALON MURID  -->
        <!-- ========================================== -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <span class="p-1.5 rounded-lg bg-orange-100 text-nampi-orange text-xs font-black">01</span>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Demografi, Asal Sekolah & Wilayah Domisili</h2>
            </div>

            <!-- Top Sekolah & Wilayah (3 Kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- 1. Top Asal SMP -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Top 15 Asal Sekolah (SMP / MTs)',
                    'subtitle' => 'Peringkat sekolah asal pendaftar terbanyak',
                    'icon' => '🏫',
                    'labels' => $sekolahLabels,
                    'counts' => $sekolahCounts,
                    'colLabel' => 'Asal Sekolah',
                    'barColor' => 'bg-orange-500'
                ])

                <!-- 2. Kabupaten/Kota -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Sebaran Wilayah (Kabupaten/Kota)',
                    'subtitle' => 'Berdasarkan domisili tempat tinggal calon murid',
                    'icon' => '📍',
                    'labels' => $kabupatenLabels,
                    'counts' => $kabupatenCounts,
                    'colLabel' => 'Kabupaten / Kota',
                    'barColor' => 'bg-blue-500'
                ])

                <!-- 3. Kecamatan -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Top 15 Kecamatan Domisili',
                    'subtitle' => 'Distribusi kecamatan domisili calon murid',
                    'icon' => '🗺️',
                    'labels' => $kecamatanLabels,
                    'counts' => $kecamatanCounts,
                    'colLabel' => 'Kecamatan',
                    'barColor' => 'bg-emerald-500'
                ])
            </div>

            <!-- Usia & Tahun Lulus (2 Kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- 4. Usia Calon Murid -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Usia Calon Murid',
                    'subtitle' => 'Berdasarkan perhitungan tanggal lahir',
                    'icon' => '🎂',
                    'labels' => $usiaMuridLabels,
                    'counts' => $usiaMuridCounts,
                    'colLabel' => 'Kelompok Usia',
                    'barColor' => 'bg-amber-500'
                ])

                <!-- 5. Tahun Kelulusan SMP -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Tahun Kelulusan SMP',
                    'subtitle' => 'Tahun kelulusan dari sekolah asal',
                    'icon' => '🎓',
                    'labels' => $tahunLulusLabels,
                    'counts' => $tahunLulusCounts,
                    'colLabel' => 'Tahun Lulus',
                    'barColor' => 'bg-indigo-500'
                ])
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 2: KESEHATAN, FISIK & AKADEMIK      -->
        <!-- ========================================== -->
        <div class="space-y-4 pt-4">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-black">02</span>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Profil Kesehatan, Fisik & Nilai Rapor</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- 6. Golongan Darah -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Golongan Darah',
                    'subtitle' => 'Distribusi tipe darah calon murid',
                    'icon' => '🩸',
                    'labels' => $goldarLabels,
                    'counts' => $goldarCounts,
                    'colLabel' => 'Tipe Golongan Darah',
                    'barColor' => 'bg-rose-500'
                ])

                <!-- 7. Buta Warna -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Status Buta Warna',
                    'subtitle' => 'Hasil observasi kesehatan mata',
                    'icon' => '👁️',
                    'labels' => $butaWarnaLabels,
                    'counts' => $butaWarnaCounts,
                    'colLabel' => 'Status Penglihatan',
                    'barColor' => 'bg-cyan-500'
                ])

                <!-- 8. Kategori Fisik (IMT / BMI) -->
                @include('admin.analitik.partials.data-table-card', [
                    'title' => 'Kategori Fisik (IMT / BMI)',
                    'subtitle' => 'Indeks massa tubuh (tinggi & berat)',
                    'icon' => '⚖️',
                    'labels' => $imtLabels,
                    'counts' => $imtCounts,
                    'colLabel' => 'Kategori Postur Fisik',
                    'barColor' => 'bg-teal-500'
                ])
            </div>

            <!-- 9. Nilai Rapor -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col justify-between">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h3 class="text-xs sm:text-sm font-black text-slate-800 tracking-tight">Rata-rata Nilai Rapor (Semester 1 - 5)</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Komparasi nilai rata-rata 4 mata pelajaran pokok calon murid</p>
                    </div>
                    <span class="text-base sm:text-lg p-2 rounded-xl bg-slate-100 text-slate-700 shrink-0">📚</span>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="py-2.5 px-3.5 w-10 text-center">#</th>
                                <th class="py-2.5 px-3">Mata Pelajaran Pokok</th>
                                <th class="py-2.5 px-3 text-right">Rata-rata Nilai</th>
                                <th class="py-2.5 px-3.5 w-40 sm:w-60 text-right">Indikator Nilai (Skala 100)</th>
                                <th class="py-2.5 px-3.5 text-center w-32">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $raporLabels = $raporData['labels'] ?? [];
                                $raporScores = $raporData['scores'] ?? [];
                            @endphp
                            @forelse($raporLabels as $i => $mapel)
                                @php
                                    $score = (float)($raporScores[$i] ?? 0);
                                    $badgePredikat = match(true) {
                                        $score >= 85 => ['Sangat Baik', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                        $score >= 75 => ['Baik', 'bg-blue-50 text-blue-700 border-blue-200'],
                                        $score >= 65 => ['Cukup', 'bg-amber-50 text-amber-700 border-amber-200'],
                                        $score > 0   => ['Kurang', 'bg-rose-50 text-rose-700 border-rose-200'],
                                        default      => ['Belum Ada', 'bg-slate-100 text-slate-600 border-slate-200']
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-[11px]">{{ $i + 1 }}</td>
                                    <td class="py-3 px-3 font-semibold text-slate-800">
                                        {{ $mapel }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-black text-slate-900 text-sm">
                                        {{ $score > 0 ? number_format($score, 1, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-3.5 text-right">
                                        <div class="flex items-center justify-end gap-2.5">
                                            <div class="w-24 sm:w-40 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $score) }}%"></div>
                                            </div>
                                            <span class="font-mono text-[11px] font-bold text-slate-600 w-11 text-right">{{ $score }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgePredikat[1] }}">
                                            {{ $badgePredikat[0] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 italic text-xs">
                                        Belum ada data nilai rapor
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 3: PROFIL ORANG TUA (AYAH & IBU)     -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-black">03</span>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Profil Orang Tua (Ayah & Ibu)</h2>
            </div>

            <!-- Subseksi Ayah -->
            <div class="p-5 bg-slate-50/70 rounded-3xl border border-slate-200 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">👨</span>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Data Ayah Kandung</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- 10. Usia Ayah -->
                    @include('admin.analitik.partials.data-table-card', [
                        'title' => 'Usia Ayah',
                        'subtitle' => 'Berdasarkan tahun lahir ayah',
                        'icon' => '📅',
                        'labels' => $usiaAyahLabels,
                        'counts' => $usiaAyahCounts,
                        'colLabel' => 'Rentang Usia',
                        'barColor' => 'bg-blue-500'
                    ])

                    <!-- 11. Pekerjaan Ayah -->
                    @include('admin.analitik.partials.data-table-card', [
                        'title' => 'Pekerjaan Ayah (Top 15)',
                        'subtitle' => 'Profesi / mata pencaharian ayah',
                        'icon' => '💼',
                        'labels' => $pekerjaanAyahLabels,
                        'counts' => $pekerjaanAyahCounts,
                        'colLabel' => 'Jenis Pekerjaan',
                        'barColor' => 'bg-indigo-500'
                    ])

                    <!-- 12. Penghasilan Ayah -->
                    @include('admin.analitik.partials.data-table-card', [
                        'title' => 'Penghasilan Ayah',
                        'subtitle' => 'Kategori rentang penghasilan ayah',
                        'icon' => '💰',
                        'labels' => $penghasilanAyahLabels,
                        'counts' => $penghasilanAyahCounts,
                        'colLabel' => 'Rentang Penghasilan',
                        'barColor' => 'bg-violet-500'
                    ])
                </div>
            </div>

            <!-- Subseksi Ibu -->
            <div class="p-5 bg-slate-50/70 rounded-3xl border border-slate-200 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">👩</span>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Data Ibu Kandung</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- 13. Usia Ibu -->
                    @include('admin.analitik.partials.data-table-card', [
                        'title' => 'Usia Ibu',
                        'subtitle' => 'Berdasarkan tahun lahir ibu',
                        'icon' => '📅',
                        'labels' => $usiaIbuLabels,
                        'counts' => $usiaIbuCounts,
                        'colLabel' => 'Rentang Usia',
                        'barColor' => 'bg-pink-500'
                    ])

                    <!-- 14. Pekerjaan Ibu -->
                    @include('admin.analitik.partials.data-table-card', [
                        'title' => 'Pekerjaan Ibu (Top 15)',
                        'subtitle' => 'Profesi / mata pencaharian ibu',
                        'icon' => '💼',
                        'labels' => $pekerjaanIbuLabels,
                        'counts' => $pekerjaanIbuCounts,
                        'colLabel' => 'Jenis Pekerjaan',
                        'barColor' => 'bg-purple-500'
                    ])

                    <!-- 15. Penghasilan Ibu -->
                    @include('admin.analitik.partials.data-table-card', [
                        'title' => 'Penghasilan Ibu',
                        'subtitle' => 'Kategori rentang penghasilan ibu',
                        'icon' => '💰',
                        'labels' => $penghasilanIbuLabels,
                        'counts' => $penghasilanIbuCounts,
                        'colLabel' => 'Rentang Penghasilan',
                        'barColor' => 'bg-fuchsia-500'
                    ])
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
