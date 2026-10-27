<x-layouts.app>
    <x-slot name="title">Dashboard — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

        <!-- Header Dashboard Minimalis -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dashboard SPMB</h1>
                <p class="text-xs font-semibold text-slate-600 mt-1 flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold border border-slate-200/80">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tahun SPMB {{ $pengaturan->tahun_ajaran }}
                    </span>
                    <span class="text-slate-300 hidden sm:inline">&bull;</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-bold border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $gelombangAktif->nama ?? 'Gelombang 1' }}
                    </span>
                    <span class="text-slate-300 hidden sm:inline">&bull;</span>
                    <span class="text-slate-500 text-[11px] font-medium">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y H:i') }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.calon-siswa.index') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-nampi-orange hover:bg-orange-600 text-white text-xs font-bold transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Direktori Calon Murid</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-xs">
                    <span>Laporan</span>
                </a>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 1: PROGRAM & GENDER METRICS          -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <!-- Kelompok 1: Ringkasan Total, Unggulan & Reguler (5 Cols) -->
            <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-400">
                    Kelompok 1: Total Pendaftar & Program
                </div>
                <div class="grid grid-cols-3 gap-3 pt-1">
                    <!-- Total Pendaftar -->
                    <div class="p-3.5 rounded-xl bg-slate-900 text-white flex flex-col justify-between">
                        <span class="text-[11px] font-bold text-slate-300">Total Pendaftar</span>
                        <div class="mt-2">
                            <span class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ number_format($baris1['total_pendaftar']) }}</span>
                            <span class="text-[10px] text-slate-400 block font-semibold">Semua Murid</span>
                        </div>
                    </div>

                    <!-- Total Unggulan -->
                    <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 flex flex-col justify-between">
                        <span class="text-[11px] font-bold text-amber-900">Total Unggulan</span>
                        <div class="mt-2">
                            <span class="text-2xl sm:text-3xl font-black tracking-tight text-amber-700">{{ number_format($baris1['total_unggulan']) }}</span>
                            <span class="text-[10px] text-amber-600 block font-semibold">Peminat</span>
                        </div>
                    </div>

                    <!-- Total Reguler -->
                    <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex flex-col justify-between">
                        <span class="text-[11px] font-bold text-blue-900">Total Reguler</span>
                        <div class="mt-2">
                            <span class="text-2xl sm:text-3xl font-black tracking-tight text-blue-700">{{ number_format($baris1['total_reguler']) }}</span>
                            <span class="text-[10px] text-blue-600 block font-semibold">Peminat</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kelompok 2: Detail Program & Gender (7 Cols) -->
            <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-400">
                    Kelompok 2: Detail Gender per Program
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Detail Unggulan -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2.5">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <span class="font-bold text-xs text-amber-900">Program Unggulan</span>
                            <span class="font-black text-sm text-slate-900">{{ number_format($baris1['total_unggulan']) }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 rounded-lg bg-white border border-slate-200/60">
                                <span class="text-[11px] text-slate-500 font-medium block">Laki-laki</span>
                                <span class="font-black text-slate-800 text-base">{{ number_format($baris1['unggulan_laki']) }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-white border border-slate-200/60">
                                <span class="text-[11px] text-slate-500 font-medium block">Perempuan</span>
                                <span class="font-black text-slate-800 text-base">{{ number_format($baris1['unggulan_perempuan']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Reguler -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2.5">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <span class="font-bold text-xs text-blue-900">Program Reguler</span>
                            <span class="font-black text-sm text-slate-900">{{ number_format($baris1['total_reguler']) }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 rounded-lg bg-white border border-slate-200/60">
                                <span class="text-[11px] text-slate-500 font-medium block">Laki-laki</span>
                                <span class="font-black text-slate-800 text-base">{{ number_format($baris1['reguler_laki']) }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-white border border-slate-200/60">
                                <span class="text-[11px] text-slate-500 font-medium block">Perempuan</span>
                                <span class="font-black text-slate-800 text-base">{{ number_format($baris1['reguler_perempuan']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 2: JUMLAH SISWA BERDASARKAN STATUS   -->
        <!-- ========================================== -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">
                    Status SPMB Calon Murid
                </span>
                <span class="text-[11px] text-slate-400 font-medium">8 Alur Utama SPMB</span>
            </div>

            <!-- 1 Baris Horizontal 8 Kolom (Tiap sub card menempati 1 kolom) -->
            <div class="overflow-x-auto pb-1 -mx-1 px-1">
                <div class="gap-2 min-w-[760px] lg:min-w-0" style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr));">
                    @php
                        $colorStyles = [
                            'amber'   => 'bg-amber-50/80 border-amber-200/90 text-amber-900 hover:border-amber-400 hover:bg-amber-100/50',
                            'orange'  => 'bg-orange-50/80 border-orange-200/90 text-orange-900 hover:border-orange-400 hover:bg-orange-100/50',
                            'cyan'    => 'bg-cyan-50/80 border-cyan-200/90 text-cyan-900 hover:border-cyan-400 hover:bg-cyan-100/50',
                            'indigo'  => 'bg-indigo-50/80 border-indigo-200/90 text-indigo-900 hover:border-indigo-400 hover:bg-indigo-100/50',
                            'emerald' => 'bg-emerald-50/80 border-emerald-200/90 text-emerald-900 hover:border-emerald-400 hover:bg-emerald-100/50',
                            'teal'    => 'bg-teal-50/80 border-teal-200/90 text-teal-900 hover:border-teal-400 hover:bg-teal-100/50',
                            'rose'    => 'bg-rose-50/80 border-rose-200/90 text-rose-900 hover:border-rose-400 hover:bg-rose-100/50',
                            'slate'   => 'bg-slate-100/90 border-slate-300/80 text-slate-800 hover:border-slate-400 hover:bg-slate-200/50',
                        ];
                    @endphp
                    @foreach ($statusCounts as $key => $st)
                        @php
                            $cardStyle = $colorStyles[$st['color']] ?? 'bg-slate-50 border-slate-200 text-slate-800';
                        @endphp
                        <a href="{{ route('admin.calon-siswa.index', ['status' => $st['status_key']]) }}"
                           class="p-2 sm:p-2.5 rounded-xl border {{ $cardStyle }} transition-all flex flex-col justify-between group shadow-2xs">
                            <span class="text-lg sm:text-xl font-black leading-none tracking-tight">
                                {{ number_format($st['count']) }}
                            </span>
                            <span class="text-[10px] sm:text-[11px] font-bold mt-2 leading-tight line-clamp-2">
                                {{ $st['label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 3: SEBARAN DATA BERDASARKAN JURUSAN  -->
        <!-- ========================================== -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">
                    Sebaran Pendaftar per Jurusan
                </span>
                <span class="text-[11px] text-slate-400 font-medium">{{ $jurusanStats->count() }} Pilihan Keahlian</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                @foreach ($jurusanStats as $j)
                    <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="px-2 py-0.5 rounded-md font-mono font-black text-[11px] bg-slate-200 text-slate-800 inline-block mb-1">
                                {{ $j->kode }}
                            </span>
                            <p class="font-bold text-xs text-slate-900 truncate" title="{{ $j->nama }}">
                                {{ $j->nama }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-2xl font-black text-slate-900 block leading-none">
                                {{ number_format($j->calon_siswa_count) }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">Murid</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 4: GRAFIK TIMELINE PENDAFTAR         -->
        <!-- ========================================== -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-black text-slate-900 tracking-tight" id="timelineTitle">
                        Timeline Pendaftar (All Time Mingguan)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5" id="timelineSub">
                        Tren volume pendaftaran per minggu berjalan sepanjang siklus SPMB (Sep 2026 – Jun 2027)
                    </p>
                </div>

                <!-- 4 Pilihan Filter Kategori Timeline: All Time Harian, All Time Mingguan (Default), All Time Bulanan, Gelombang -->
                <div class="flex items-center overflow-x-auto pb-1 max-w-full">
                    <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200/80 text-xs font-bold shrink-0">
                        <button type="button" onclick="switchTimelineMode('all_time_harian')" id="btn_all_time_harian"
                                class="px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all cursor-pointer">
                            All Time Harian
                        </button>
                        <button type="button" onclick="switchTimelineMode('all_time_mingguan')" id="btn_all_time_mingguan"
                                class="px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-2xs transition-all cursor-pointer">
                            All Time Mingguan
                        </button>
                        <button type="button" onclick="switchTimelineMode('all_time_bulanan')" id="btn_all_time_bulanan"
                                class="px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all cursor-pointer">
                            All Time Bulanan
                        </button>
                        <button type="button" onclick="switchTimelineMode('gelombang')" id="btn_gelombang"
                                class="px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all cursor-pointer">
                            Gelombang
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4 KARTU KPI TIMELINE PENDAFTAR -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <!-- KPI 1: Total Pendaftar Periode -->
                <div class="p-3.5 rounded-xl bg-slate-900 text-white flex flex-col justify-between shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-300 block" id="kpiTotalLabel">Total All Time</span>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-2xl font-black tracking-tight" id="kpiTotalValue">{{ number_format($timeline['all_time_mingguan']['total']) }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Calon Murid</span>
                    </div>
                </div>

                <!-- KPI 2: Puncak Pendaftaran (Peak) -->
                <div class="p-3.5 rounded-xl bg-orange-50/80 border border-orange-200/90 flex flex-col justify-between shadow-2xs">
                    <span class="text-[11px] font-bold text-orange-900 block">Puncak Pendaftaran</span>
                    <div class="mt-2">
                        <span class="text-base sm:text-lg font-black text-orange-700 block truncate" id="kpiPeakValue">
                            {{ $timeline['all_time_mingguan']['peak']['label'] }}
                        </span>
                        <span class="text-[10px] text-orange-600 block font-semibold mt-0.5">Volume Tertinggi</span>
                    </div>
                </div>

                <!-- KPI 3: Laju / Rata-rata Pendaftaran -->
                <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200/90 flex flex-col justify-between shadow-2xs">
                    <span class="text-[11px] font-bold text-blue-900 block" id="kpiAvgLabel">Laju Mingguan</span>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-2xl font-black text-blue-700" id="kpiAvgValue">{{ $timeline['all_time_mingguan']['avg'] }}</span>
                        <span class="text-[10px] text-blue-600 font-semibold" id="kpiAvgUnit">/ Minggu</span>
                    </div>
                </div>

                <!-- KPI 4: Gelombang Aktif & Sisa Waktu -->
                <div class="p-3.5 rounded-xl bg-emerald-50/80 border border-emerald-200/90 flex flex-col justify-between shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-900 block">Periode Berjalan</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base font-black text-emerald-800">{{ $timeline['kpi_gelombang']['nama'] }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-200/70 text-emerald-900">
                                {{ $timeline['kpi_gelombang']['status_text'] }}
                            </span>
                        </div>
                        <span class="text-[10px] text-emerald-600 block font-semibold mt-0.5 truncate">
                            Batas Akhir: {{ $timeline['kpi_gelombang']['target_tgl'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative w-full h-72 sm:h-80">
                <canvas id="timelineChart"></canvas>
            </div>

            <!-- Strip Legend Tanggal Pembukaan & Gelombang 1, 2, 3 -->
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Jadwal Gelombang:
                </span>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @foreach ($timeline['legend'] as $leg)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11px] font-medium {{ $leg['bg_badge'] }}">
                            <span class="w-2 h-2 rounded-full {{ $leg['dot_color'] }}"></span>
                            <span class="font-bold">{{ $leg['title'] }}:</span>
                            <span>{{ $leg['date_range'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 5: GELOMBANG & KAS KEUANGAN SPMB     -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Kolom Kiri: Status Periode & Gelombang Pendaftaran (6 Cols) -->
            <div class="lg:col-span-6 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3.5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900 tracking-tight">Status Periode & Gelombang</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Jadwal dan status gelombang pendaftaran aktif</p>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                            {{ $gelombangList->count() }} Gelombang
                        </span>
                    </div>

                    <div class="space-y-2.5 pt-2">
                        @foreach ($gelombangList as $g)
                            @php
                                $now = \Carbon\Carbon::now();
                                $isAktif = $g->aktif || ($g->periode_mulai && $g->periode_selesai && $now->between($g->periode_mulai, $g->periode_selesai));
                                $isPast = $g->periode_selesai && $now->gt($g->periode_selesai);
                            @endphp
                            <div class="p-3 rounded-xl border {{ $isAktif ? 'bg-orange-50/40 border-orange-200' : 'bg-slate-50/70 border-slate-200/70' }} flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900">{{ $g->nama }}</span>
                                        @if ($isAktif)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @elseif ($isPast)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">
                                                Akan Datang
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 font-medium">
                                        {{ $g->periode_mulai ? $g->periode_mulai->translatedFormat('d M Y') : '-' }} s/d {{ $g->periode_selesai ? $g->periode_selesai->translatedFormat('d M Y') : '-' }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-lg sm:text-xl font-black text-slate-900 block leading-none">
                                        {{ number_format($g->calon_siswa_count) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold">Pendaftar</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Kas & Keuangan SPMB (6 Cols) -->
            <div class="lg:col-span-6 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3.5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900 tracking-tight">Kas & Keuangan SPMB</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Penerimaan kas seleksi, daftar ulang & sisa piutang</p>
                        </div>
                        <a href="{{ route('admin.keuangan.index') }}" class="text-xs font-bold text-orange-600 hover:underline">
                            Manajemen Kas &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <!-- Kas Biaya Seleksi -->
                        <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
                            <span class="text-[11px] font-bold text-emerald-900 block">Kas Biaya Seleksi</span>
                            <span class="text-base sm:text-lg font-black text-emerald-700 block mt-1 tracking-tight">
                                Rp {{ number_format($keuangan['kas_seleksi'], 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-emerald-600 block mt-0.5 font-medium">Terverifikasi</span>
                        </div>

                        <!-- Kas Daftar Ulang -->
                        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80">
                            <span class="text-[11px] font-bold text-blue-900 block">Kas Daftar Ulang</span>
                            <span class="text-base sm:text-lg font-black text-blue-700 block mt-1 tracking-tight">
                                Rp {{ number_format($keuangan['kas_daftar_ulang'], 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-blue-600 block mt-0.5 font-medium">Terverifikasi</span>
                        </div>

                        <!-- Total Kas Masuk -->
                        <div class="p-3.5 rounded-xl bg-slate-900 text-white border border-slate-800">
                            <span class="text-[11px] font-bold text-slate-300 block">Total Kas Masuk</span>
                            <span class="text-base sm:text-lg font-black text-white block mt-1 tracking-tight">
                                Rp {{ number_format($keuangan['total_kas_masuk'], 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5 font-medium">Seleksi + Daftar Ulang</span>
                        </div>

                        <!-- Sisa Piutang Tagihan -->
                        <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/80">
                            <span class="text-[11px] font-bold text-amber-900 block">Piutang Tagihan (Cicilan)</span>
                            <span class="text-base sm:text-lg font-black text-amber-700 block mt-1 tracking-tight">
                                Rp {{ number_format($keuangan['sisa_piutang'], 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-amber-600 block mt-0.5 font-medium">Sisa Belum Lunas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 6: REFERENSI & TOP SEKOLAH ASAL      -->
        <!-- ========================================== -->
        <div x-data="{ showModalReferensi: false, showModalSekolah: false }">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                <!-- Saluran Referensi & Promosi Teratas (6 Cols) -->
                <div class="lg:col-span-6 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3.5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <div>
                                <h2 class="text-sm sm:text-base font-black text-slate-900 tracking-tight">Saluran Referensi & Promosi Teratas</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Asal informasi calon murid mengetahui sekolah</p>
                            </div>
                            <button type="button" @click="showModalReferensi = true"
                                    class="text-xs font-bold text-orange-600 hover:underline cursor-pointer">
                                Seluruh Data &rarr;
                            </button>
                        </div>

                        <div class="space-y-3 pt-2">
                            @forelse ($referensiStats->take(5) as $ref)
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="font-bold text-slate-800">{{ $ref['label'] }}</span>
                                        <span class="font-mono font-bold text-slate-600">{{ number_format($ref['count']) }} Murid ({{ $ref['pct'] }}%)</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-orange-500 h-2 rounded-full transition-all" style="width: {{ min(100, max(2, $ref['pct'])) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada data referensi pendaftar.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <button type="button" @click="showModalReferensi = true"
                                class="w-full py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-700 transition-colors cursor-pointer text-center">
                            Buka Modal Seluruh Saluran Referensi ({{ $referensiStats->count() }} Data)
                        </button>
                    </div>
                </div>

                <!-- Top Sekolah Asal (Feeder Schools) (6 Cols) -->
                <div class="lg:col-span-6 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3.5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <div>
                                <h2 class="text-sm sm:text-base font-black text-slate-900 tracking-tight">Top Sekolah Asal (Feeder Schools)</h2>
                                <p class="text-xs text-slate-500 mt-0.5">SMP / MTs penyumbang pendaftar terbanyak</p>
                            </div>
                            <button type="button" @click="showModalSekolah = true"
                                    class="text-xs font-bold text-orange-600 hover:underline cursor-pointer">
                                Seluruh Data &rarr;
                            </button>
                        </div>

                        <div class="space-y-2.5 pt-2">
                            @forelse ($topSekolah['top'] as $idx => $sch)
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-200/70 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold font-mono text-[11px] flex items-center justify-center shrink-0">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 truncate">{{ $sch['nama'] }}</p>
                                            <p class="text-[11px] text-slate-400 font-medium">{{ $sch['kota'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-black text-slate-900 text-sm block leading-none">{{ number_format($sch['total_siswa']) }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $sch['persentase'] }}%</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada data sekolah asal pendaftar.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <button type="button" @click="showModalSekolah = true"
                                class="w-full py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-700 transition-colors cursor-pointer text-center">
                            Buka Modal Seluruh Sekolah Asal ({{ count($allSekolah['top'] ?? []) }} Sekolah)
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL 1: SELURUH SALURAN REFERENSI -->
            <template x-teleport="body">
                <div x-show="showModalReferensi"
                     x-cloak
                     @keydown.escape.window="showModalReferensi = false"
                     class="fixed inset-0 z-[100] overflow-y-auto"
                     aria-labelledby="modal-referensi-title"
                     role="dialog"
                     aria-modal="true"
                     style="display: none;">
                    
                    <!-- Backdrop -->
                    <div x-show="showModalReferensi"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                         @click="showModalReferensi = false"></div>

                    <!-- Centering Wrapper -->
                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
                        <!-- Modal Content Box -->
                        <div x-show="showModalReferensi"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                             @click.stop
                             class="relative z-[101] w-full max-w-2xl bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-slate-200 my-8">
                            
                            <!-- Header Modal -->
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                                <div>
                                    <h3 id="modal-referensi-title" class="text-base font-black text-slate-900">Seluruh Saluran Referensi & Promosi</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Daftar lengkap sumber informasi pendaftaran calon murid</p>
                                </div>
                                <button @click="showModalReferensi = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-200/60 transition-colors cursor-pointer" title="Tutup Modal (Esc)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Body Modal Table -->
                            <div class="p-6 max-h-[60vh] overflow-y-auto">
                                <table class="w-full text-left text-xs text-slate-700">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-12 bg-slate-50">No</th>
                                            <th class="px-4 py-3 bg-slate-50">Saluran Informasi</th>
                                            <th class="px-4 py-3 text-right bg-slate-50">Jumlah Murid</th>
                                            <th class="px-4 py-3 text-right bg-slate-50">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($referensiStats as $idx => $r)
                                            <tr class="hover:bg-slate-50/70 transition-colors">
                                                <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                                <td class="px-4 py-3 font-bold text-slate-900">{{ $r['label'] }}</td>
                                                <td class="px-4 py-3 text-right font-black text-slate-800">{{ number_format($r['count']) }}</td>
                                                <td class="px-4 py-3 text-right font-bold text-orange-600">{{ $r['pct'] }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="p-6 text-center text-slate-400 text-xs">Belum ada data saluran referensi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Modal -->
                            <div class="px-6 py-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">
                                    Total: <strong class="text-slate-800">{{ $referensiStats->count() }}</strong> saluran terdata
                                </span>
                                <button @click="showModalReferensi = false" type="button"
                                        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-all cursor-pointer">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- MODAL 2: SELURUH SEKOLAH ASAL -->
            <template x-teleport="body">
                <div x-show="showModalSekolah"
                     x-cloak
                     @keydown.escape.window="showModalSekolah = false"
                     class="fixed inset-0 z-[100] overflow-y-auto"
                     aria-labelledby="modal-sekolah-title"
                     role="dialog"
                     aria-modal="true"
                     style="display: none;">
                    
                    <!-- Backdrop -->
                    <div x-show="showModalSekolah"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                         @click="showModalSekolah = false"></div>

                    <!-- Centering Wrapper -->
                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
                        <!-- Modal Content Box -->
                        <div x-show="showModalSekolah"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                             @click.stop
                             class="relative z-[101] w-full max-w-2xl bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-slate-200 my-8">
                            
                            <!-- Header Modal -->
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                                <div>
                                    <h3 id="modal-sekolah-title" class="text-base font-black text-slate-900">Daftar Seluruh Sekolah Asal (Feeder Schools)</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Daftar lengkap SMP/MTs asal seluruh calon murid baru</p>
                                </div>
                                <button @click="showModalSekolah = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-200/60 transition-colors cursor-pointer" title="Tutup Modal (Esc)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Body Modal Table -->
                            <div class="p-6 max-h-[60vh] overflow-y-auto">
                                <table class="w-full text-left text-xs text-slate-700">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-12 bg-slate-50">No</th>
                                            <th class="px-4 py-3 bg-slate-50">Nama Sekolah</th>
                                            <th class="px-4 py-3 bg-slate-50">Kab/Kota</th>
                                            <th class="px-4 py-3 text-right bg-slate-50">Jumlah Murid</th>
                                            <th class="px-4 py-3 text-right bg-slate-50">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($allSekolah['top'] ?? [] as $idx => $sch)
                                            <tr class="hover:bg-slate-50/70 transition-colors">
                                                <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                                <td class="px-4 py-3 font-bold text-slate-900">{{ $sch['nama'] }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $sch['kota'] }}</td>
                                                <td class="px-4 py-3 text-right font-black text-slate-800">{{ number_format($sch['total_siswa']) }}</td>
                                                <td class="px-4 py-3 text-right font-bold text-orange-600">{{ $sch['persentase'] }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="p-6 text-center text-slate-400 text-xs">Belum ada data sekolah asal.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Modal -->
                            <div class="px-6 py-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">
                                    Total: <strong class="text-slate-800">{{ count($allSekolah['top'] ?? []) }}</strong> sekolah
                                    @if(($allSekolah['others_count'] ?? 0) > 0)
                                        <span class="text-slate-400">({{ $allSekolah['others_count'] }} murid belum terdata sekolahnya)</span>
                                    @endif
                                </span>
                                <button @click="showModalSekolah = false" type="button"
                                        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-all cursor-pointer">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- ========================================== -->
        <!-- BARIS 7: PENDAFTAR TERBARU & AUDIT LOG     -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Pendaftar Terbaru (6 Cols) -->
            <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900">Pendaftar Terbaru</h2>
                            <p class="text-xs text-slate-500 mt-0.5">10 calon murid yang baru masuk ke sistem</p>
                        </div>
                        <a href="{{ route('admin.calon-siswa.index') }}"
                           class="text-xs font-bold text-nampi-orange hover:underline">
                            Semua Calon Murid &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50/80 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3">Calon Murid</th>
                                    <th class="px-4 py-3">Jurusan</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentCandidates as $cs)
                                    @php
                                        $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3">
                                            <p class="font-bold text-slate-900">{{ $cs->nama_lengkap }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono">{{ $cs->nomor_pendaftaran }} &bull; {{ $cs->created_at?->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">
                                            {{ $cs->jurusan?->nama ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                                {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR']) ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' :
                                                   (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                                {{ str_replace('_', ' ', $statusStr) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ route('admin.calon-siswa.show', $cs) }}"
                                               class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-6 text-center text-slate-400 text-xs">Belum ada calon murid terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Aktivitas Terkini / Audit Log (6 Cols) -->
            <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900">Aktivitas Terkini</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Audit log transaksi dan perubahan data sistem</p>
                        </div>
                        <a href="{{ route('admin.audit-trail.index') }}" class="text-xs font-bold text-nampi-orange hover:underline">
                            Log Lengkap &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 pt-1">
                        @forelse ($recentActivities as $act)
                            <div class="py-2.5 flex items-start justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 leading-snug line-clamp-2">
                                        {{ $act->description }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 font-medium">
                                        <span class="text-slate-600 font-semibold">{{ $act->causer?->name ?? 'Sistem' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $act->created_at?->diffForHumans() }}</span>
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 shrink-0">
                                    {{ $act->log_name ?: 'system' }}
                                </span>
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-400 text-xs">
                                Belum ada catatan aktivitas sistem.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('timelineChart');
            if (!ctx) return;

            const timelineData = @json($timeline);
            let currentMode = 'all_time_mingguan';
            let chartInstance = null;

            const modeConfigs = {
                'all_time_harian': {
                    title: 'Timeline Pendaftar (All Time Harian)',
                    subtitle: 'Volume pendaftaran harian sepanjang siklus pembukaan s/d akhir gelombang (Sep 2026 – Jun 2027)',
                    labelTotal: 'Total All Time',
                    labelAvg: 'Laju Harian',
                },
                'all_time_mingguan': {
                    title: 'Timeline Pendaftar (All Time Mingguan)',
                    subtitle: 'Tren pendaftaran per minggu berjalan sepanjang siklus SPMB (Sep 2026 – Jun 2027)',
                    labelTotal: 'Total All Time',
                    labelAvg: 'Laju Mingguan',
                },
                'all_time_bulanan': {
                    title: 'Timeline Pendaftar (All Time Bulanan)',
                    subtitle: 'Distribusi volume pendaftaran per bulan penuh sepanjang siklus SPMB (Sep 2026 – Jun 2027)',
                    labelTotal: 'Total All Time',
                    labelAvg: 'Laju Bulanan',
                },
                'gelombang': {
                    title: 'Timeline Pendaftar (Per Gelombang)',
                    subtitle: 'Komparasi perolehan calon murid pada Gelombang 1, 2, dan 3',
                    labelTotal: 'Total Semua Gelombang',
                    labelAvg: 'Rata-rata / Gelombang',
                }
            };

            // Custom Plugin: Garis Penanda Vertikal & Keterangan Milestone
            const milestoneMarkerPlugin = {
                id: 'milestoneMarkerPlugin',
                afterDraw(chart) {
                    const milestones = chart.config.options?.plugins?.milestones;
                    if (!milestones || !milestones.length) return;
                    if (!chart.chartArea || !chart.scales || !chart.scales.x) return;

                    const { ctx: c, chartArea: { top, bottom, left, right }, scales: { x } } = chart;

                    milestones.forEach(m => {
                        if (m.index === undefined || m.index === null) return;
                        const xPos = x.getPixelForValue(m.index);
                        if (xPos === undefined || isNaN(xPos)) return;

                        c.save();

                        // 1. Garis vertikal putus-putus
                        c.beginPath();
                        c.setLineDash([4, 4]);
                        c.lineWidth = 1.5;
                        c.strokeStyle = m.color || '#ea580c';
                        c.moveTo(xPos, top);
                        c.lineTo(xPos, bottom);
                        c.stroke();

                        // 2. Badge label keterangan
                        c.setLineDash([]);
                        c.font = 'bold 10px "Plus Jakarta Sans", sans-serif';
                        const text = m.label;
                        const textMetrics = c.measureText(text);
                        const badgeW = textMetrics.width + 12;
                        const badgeH = 18;
                        const badgeY = top + 6;
                        let badgeX = xPos - (badgeW / 2);

                        if (badgeX < left + 2) badgeX = left + 2;
                        if (badgeX + badgeW > right - 2) badgeX = right - badgeW - 2;

                        c.fillStyle = m.color || '#ea580c';
                        c.beginPath();
                        if (c.roundRect) {
                            c.roundRect(badgeX, badgeY, badgeW, badgeH, 4);
                        } else {
                            c.rect(badgeX, badgeY, badgeW, badgeH);
                        }
                        c.fill();

                        c.fillStyle = '#ffffff';
                        c.textAlign = 'center';
                        c.textBaseline = 'middle';
                        c.fillText(text, badgeX + (badgeW / 2), badgeY + (badgeH / 2));

                        c.restore();
                    });
                }
            };

            function buildChart(mode) {
                if (chartInstance) {
                    chartInstance.destroy();
                }

                const dataset = timelineData[mode];
                const canvasCtx = ctx.getContext('2d');
                const gradient = canvasCtx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(234, 88, 12, 0.25)');
                gradient.addColorStop(1, 'rgba(234, 88, 12, 0.01)');

                const isLine = dataset.type === 'line';
                const isGelombang = mode === 'gelombang';

                let bgColors = '#ea580c';
                if (isLine) {
                    bgColors = gradient;
                } else if (isGelombang && dataset.bar_colors) {
                    bgColors = dataset.bar_colors;
                }

                chartInstance = new Chart(ctx, {
                    type: isLine ? 'line' : 'bar',
                    data: {
                        labels: dataset.labels,
                        datasets: [{
                            label: 'Pendaftar Baru',
                            data: dataset.counts,
                            borderColor: isGelombang ? dataset.bar_colors : '#ea580c',
                            backgroundColor: bgColors,
                            fill: isLine,
                            tension: 0.3,
                            borderWidth: 2,
                            borderRadius: isLine ? 0 : 6,
                            pointBackgroundColor: '#ea580c',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 1.5,
                            pointRadius: isLine ? 3 : 0,
                            pointHoverRadius: isLine ? 5 : 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: { display: false },
                            milestones: dataset.milestones || [],
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    title: function (items) {
                                        if (!items.length) return '';
                                        const idx = items[0].dataIndex;
                                        if (dataset.ranges && dataset.ranges[idx]) {
                                            return dataset.labels[idx] + ' (' + dataset.ranges[idx] + ')';
                                        }
                                        return dataset.labels[idx];
                                    },
                                    label: function (c) {
                                        return ' Pendaftar: ' + c.parsed.y + ' Siswa';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: mode === '30_hari' ? 12 : 10,
                                    font: { family: 'Plus Jakarta Sans', size: 11 },
                                    color: '#94a3b8'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                border: { dash: [4, 4] },
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    stepSize: 1,
                                    font: { family: 'Plus Jakarta Sans', size: 11 },
                                    color: '#94a3b8'
                                }
                            }
                        }
                    },
                    plugins: [milestoneMarkerPlugin]
                });
            }

            window.switchTimelineMode = function (mode) {
                currentMode = mode;
                const modes = ['all_time_harian', 'all_time_mingguan', 'all_time_bulanan', 'gelombang'];

                // Update tab buttons
                modes.forEach(m => {
                    const btn = document.getElementById('btn_' + m);
                    if (!btn) return;
                    if (m === mode) {
                        btn.className = 'px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-2xs transition-all cursor-pointer';
                    } else {
                        btn.className = 'px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all cursor-pointer';
                    }
                });

                // Update Header & 4 Kartu KPI Timeline
                const cfg = modeConfigs[mode] || modeConfigs['all_time_mingguan'];
                const dataset = timelineData[mode];

                document.getElementById('timelineTitle').innerText = cfg.title;
                document.getElementById('timelineSub').innerText = cfg.subtitle;
                document.getElementById('kpiTotalLabel').innerText = cfg.labelTotal;
                document.getElementById('kpiTotalValue').innerText = dataset.total.toLocaleString('id-ID');
                document.getElementById('kpiPeakValue').innerText = dataset.peak ? dataset.peak.label : '-';
                document.getElementById('kpiAvgLabel').innerText = cfg.labelAvg;
                document.getElementById('kpiAvgValue').innerText = dataset.avg;
                document.getElementById('kpiAvgUnit').innerText = '/ ' + (dataset.unit || 'Periode');

                buildChart(mode);
            };

            // Inisialisasi awal pada All Time Mingguan (Default)
            buildChart('all_time_mingguan');
        });
    </script>
    @endpush
</x-layouts.app>
