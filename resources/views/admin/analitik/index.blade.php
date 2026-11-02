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
                    Visualisasi demografi, asal sekolah, wilayah domisili, profil kesehatan, nilai akademik, serta profil ayah & ibu calon murid.
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
        <!-- SEKSI 1: DEMOGRAFI & WILAYAH CALON MURID -->
        <!-- ========================================== -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <span class="p-1.5 rounded-lg bg-orange-100 text-nampi-orange text-xs font-black">01</span>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Demografi, Asal Sekolah & Wilayah Domisili</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- 1. Top Asal SMP -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Top 10 Asal Sekolah (SMP / MTs)</h3>
                            <p class="text-[11px] text-slate-400">Peringkat sekolah asal pendaftar terbanyak</p>
                        </div>
                        <span class="text-xs">🏫</span>
                    </div>
                    <div class="relative w-full h-72">
                        <canvas id="chartAsalSmp"></canvas>
                    </div>
                </div>

                <!-- 2. Wilayah Domisili (Kabupaten & Kecamatan) -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Sebaran Wilayah Domisili (Kabupaten/Kota)</h3>
                            <p class="text-[11px] text-slate-400">Berdasarkan alamat domisili tempat tinggal calon murid</p>
                        </div>
                        <span class="text-xs">📍</span>
                    </div>
                    <div class="relative w-full h-72">
                        <canvas id="chartKabupaten"></canvas>
                    </div>
                </div>

                <!-- 3. Top Kecamatan Domisili -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Top Kecamatan Domisili</h3>
                            <p class="text-[11px] text-slate-400">Distribusi kecamatan domisili calon murid</p>
                        </div>
                        <span class="text-xs">🗺️</span>
                    </div>
                    <div class="relative w-full h-72">
                        <canvas id="chartKecamatan"></canvas>
                    </div>
                </div>

                <!-- 4. Usia & Tahun Kelulusan Murid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Usia Calon Murid -->
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">Usia Calon Murid</h3>
                                <p class="text-[10px] text-slate-400">Dari tanggal lahir</p>
                            </div>
                            <span class="text-xs">🎂</span>
                        </div>
                        <div class="relative w-full h-56">
                            <canvas id="chartUsiaMurid"></canvas>
                        </div>
                    </div>

                    <!-- Tahun Lulus SMP -->
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">Tahun Lulus SMP</h3>
                                <p class="text-[10px] text-slate-400">Kelulusan asal sekolah</p>
                            </div>
                            <span class="text-xs">🎓</span>
                        </div>
                        <div class="relative w-full h-56">
                            <canvas id="chartTahunLulus"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 2: KESEHATAN, FISIK & AKADEMIK -->
        <!-- ========================================== -->
        <div class="space-y-4 pt-4">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-black">02</span>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Profil Kesehatan, Fisik & Nilai Rapor</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Golongan Darah & Buta Warna -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Golongan Darah</h3>
                            <p class="text-[11px] text-slate-400">Distribusi tipe darah calon murid</p>
                        </div>
                        <span class="text-xs">🩸</span>
                    </div>
                    <div class="relative w-full h-60">
                        <canvas id="chartGoldar"></canvas>
                    </div>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Status Buta Warna</h3>
                            <p class="text-[11px] text-slate-400">Hasil observasi kesehatan mata</p>
                        </div>
                        <span class="text-xs">👁️</span>
                    </div>
                    <div class="relative w-full h-60">
                        <canvas id="chartButaWarna"></canvas>
                    </div>
                </div>

                <!-- IMT / Postur Fisik -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Kategori Fisik (IMT / BMI)</h3>
                            <p class="text-[11px] text-slate-400">Tinggi & berat badan calon murid</p>
                        </div>
                        <span class="text-xs">⚖️</span>
                    </div>
                    <div class="relative w-full h-60">
                        <canvas id="chartImt"></canvas>
                    </div>
                </div>
            </div>

            <!-- Nilai Rapor -->
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Rata-rata Nilai Rapor (Semester 1 - 5)</h3>
                        <p class="text-[11px] text-slate-400">Komparasi nilai rata-rata 4 mata pelajaran pokok</p>
                    </div>
                    <span class="text-xs">📚</span>
                </div>
                <div class="relative w-full h-64">
                    <canvas id="chartRapor"></canvas>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 3: PROFIL ORANG TUA (AYAH & IBU) -->
        <!-- ========================================== -->
        <div class="space-y-4 pt-4">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-black">03</span>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700">Profil Orang Tua (Ayah & Ibu)</h2>
            </div>

            <!-- Subseksi Ayah -->
            <div class="p-5 bg-slate-50/70 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">👨</span>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Data Ayah Kandung</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Usia Ayah -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                        <h4 class="text-xs font-bold text-slate-700">Usia Ayah (Tahun Lahir)</h4>
                        <div class="relative w-full h-56">
                            <canvas id="chartUsiaAyah"></canvas>
                        </div>
                    </div>

                    <!-- Pekerjaan Ayah -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                        <h4 class="text-xs font-bold text-slate-700">Pekerjaan Ayah</h4>
                        <div class="relative w-full h-56">
                            <canvas id="chartPekerjaanAyah"></canvas>
                        </div>
                    </div>

                    <!-- Penghasilan Ayah -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                        <h4 class="text-xs font-bold text-slate-700">Penghasilan Ayah</h4>
                        <div class="relative w-full h-56">
                            <canvas id="chartPenghasilanAyah"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subseksi Ibu -->
            <div class="p-5 bg-slate-50/70 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">👩</span>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Data Ibu Kandung</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Usia Ibu -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                        <h4 class="text-xs font-bold text-slate-700">Usia Ibu (Tahun Lahir)</h4>
                        <div class="relative w-full h-56">
                            <canvas id="chartUsiaIbu"></canvas>
                        </div>
                    </div>

                    <!-- Pekerjaan Ibu -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                        <h4 class="text-xs font-bold text-slate-700">Pekerjaan Ibu</h4>
                        <div class="relative w-full h-56">
                            <canvas id="chartPekerjaanIbu"></canvas>
                        </div>
                    </div>

                    <!-- Penghasilan Ibu -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                        <h4 class="text-xs font-bold text-slate-700">Penghasilan Ibu</h4>
                        <div class="relative w-full h-56">
                            <canvas id="chartPenghasilanIbu"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ChartLib = window.Chart || Chart;
            if (!ChartLib) return;

            // Palette Warna Modern
            const palette = [
                '#ea580c', '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b',
                '#ec4899', '#06b6d4', '#64748b', '#14b8a6', '#6366f1'
            ];

            const commonBarOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 10 } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            };

            const commonHorizontalBarOptions = {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 10 } },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            };

            const commonDoughnutOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 }, padding: 8 }
                    }
                }
            };

            // 1. Chart Asal SMP
            const elAsalSmp = document.getElementById('chartAsalSmp');
            if (elAsalSmp) {
                new ChartLib(elAsalSmp, {
                    type: 'bar',
                    data: {
                        labels: @json($sekolahLabels),
                        datasets: [{
                            data: @json($sekolahCounts),
                            backgroundColor: '#ea580c',
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 2. Chart Kabupaten
            const elKab = document.getElementById('chartKabupaten');
            if (elKab) {
                new ChartLib(elKab, {
                    type: 'bar',
                    data: {
                        labels: @json($kabupatenLabels),
                        datasets: [{
                            data: @json($kabupatenCounts),
                            backgroundColor: '#3b82f6',
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 3. Chart Kecamatan
            const elKec = document.getElementById('chartKecamatan');
            if (elKec) {
                new ChartLib(elKec, {
                    type: 'bar',
                    data: {
                        labels: @json($kecamatanLabels),
                        datasets: [{
                            data: @json($kecamatanCounts),
                            backgroundColor: '#10b981',
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 4. Chart Usia Murid
            const elUsia = document.getElementById('chartUsiaMurid');
            if (elUsia) {
                new ChartLib(elUsia, {
                    type: 'bar',
                    data: {
                        labels: @json($usiaMuridLabels),
                        datasets: [{
                            data: @json($usiaMuridCounts),
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 5. Chart Tahun Lulus
            const elThnLulus = document.getElementById('chartTahunLulus');
            if (elThnLulus) {
                new ChartLib(elThnLulus, {
                    type: 'doughnut',
                    data: {
                        labels: @json($tahunLulusLabels),
                        datasets: [{
                            data: @json($tahunLulusCounts),
                            backgroundColor: palette
                        }]
                    },
                    options: commonDoughnutOptions
                });
            }

            // 6. Chart Golongan Darah
            const elGoldar = document.getElementById('chartGoldar');
            if (elGoldar) {
                new ChartLib(elGoldar, {
                    type: 'doughnut',
                    data: {
                        labels: @json($goldarLabels),
                        datasets: [{
                            data: @json($goldarCounts),
                            backgroundColor: ['#ef4444', '#f97316', '#3b82f6', '#10b981', '#94a3b8', '#a855f7']
                        }]
                    },
                    options: commonDoughnutOptions
                });
            }

            // 7. Chart Buta Warna
            const elBW = document.getElementById('chartButaWarna');
            if (elBW) {
                new ChartLib(elBW, {
                    type: 'doughnut',
                    data: {
                        labels: @json($butaWarnaLabels),
                        datasets: [{
                            data: @json($butaWarnaCounts),
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#94a3b8']
                        }]
                    },
                    options: commonDoughnutOptions
                });
            }

            // 8. Chart IMT
            const elImt = document.getElementById('chartImt');
            if (elImt) {
                new ChartLib(elImt, {
                    type: 'bar',
                    data: {
                        labels: @json($imtLabels),
                        datasets: [{
                            data: @json($imtCounts),
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#cbd5e1'],
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 9. Chart Rapor
            const elRapor = document.getElementById('chartRapor');
            if (elRapor) {
                new ChartLib(elRapor, {
                    type: 'bar',
                    data: {
                        labels: @json($raporData['labels']),
                        datasets: [{
                            label: 'Rata-rata Nilai',
                            data: @json($raporData['scores']),
                            backgroundColor: ['#f97316', '#3b82f6', '#10b981', '#8b5cf6'],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { font: { size: 11 } },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                ticks: { font: { size: 11, weight: 'bold' } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 10. Chart Usia Ayah
            const elUsiaAyah = document.getElementById('chartUsiaAyah');
            if (elUsiaAyah) {
                new ChartLib(elUsiaAyah, {
                    type: 'bar',
                    data: {
                        labels: @json($usiaAyahLabels),
                        datasets: [{
                            data: @json($usiaAyahCounts),
                            backgroundColor: '#3b82f6',
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 11. Chart Pekerjaan Ayah
            const elPkAyah = document.getElementById('chartPekerjaanAyah');
            if (elPkAyah) {
                new ChartLib(elPkAyah, {
                    type: 'bar',
                    data: {
                        labels: @json($pekerjaanAyahLabels),
                        datasets: [{
                            data: @json($pekerjaanAyahCounts),
                            backgroundColor: '#60a5fa',
                            borderRadius: 6
                        }]
                    },
                    options: commonHorizontalBarOptions
                });
            }

            // 12. Chart Penghasilan Ayah
            const elPhAyah = document.getElementById('chartPenghasilanAyah');
            if (elPhAyah) {
                new ChartLib(elPhAyah, {
                    type: 'doughnut',
                    data: {
                        labels: @json($penghasilanAyahLabels),
                        datasets: [{
                            data: @json($penghasilanAyahCounts),
                            backgroundColor: palette
                        }]
                    },
                    options: commonDoughnutOptions
                });
            }

            // 13. Chart Usia Ibu
            const elUsiaIbu = document.getElementById('chartUsiaIbu');
            if (elUsiaIbu) {
                new ChartLib(elUsiaIbu, {
                    type: 'bar',
                    data: {
                        labels: @json($usiaIbuLabels),
                        datasets: [{
                            data: @json($usiaIbuCounts),
                            backgroundColor: '#ec4899',
                            borderRadius: 6
                        }]
                    },
                    options: commonBarOptions
                });
            }

            // 14. Chart Pekerjaan Ibu
            const elPkIbu = document.getElementById('chartPekerjaanIbu');
            if (elPkIbu) {
                new ChartLib(elPkIbu, {
                    type: 'bar',
                    data: {
                        labels: @json($pekerjaanIbuLabels),
                        datasets: [{
                            data: @json($pekerjaanIbuCounts),
                            backgroundColor: '#f472b6',
                            borderRadius: 6
                        }]
                    },
                    options: commonHorizontalBarOptions
                });
            }

            // 15. Chart Penghasilan Ibu
            const elPhIbu = document.getElementById('chartPenghasilanIbu');
            if (elPhIbu) {
                new ChartLib(elPhIbu, {
                    type: 'doughnut',
                    data: {
                        labels: @json($penghasilanIbuLabels),
                        datasets: [{
                            data: @json($penghasilanIbuCounts),
                            backgroundColor: palette
                        }]
                    },
                    options: commonDoughnutOptions
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
