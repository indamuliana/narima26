<x-layouts.app>
    <x-slot name="title">Dashboard Administrator — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

        <!-- Header Banner Eksekutif -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl text-white shadow-md border border-slate-800/80 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold text-slate-200 backdrop-blur-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Panel Administrator &bull; SPMB SMK Wikrama 1 Garut T.A. 2027/2028</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Selamat Datang, {{ auth()->user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Monitoring terpusat alur pendaftaran calon peserta didik baru, keterisian kuota kompetensi keahlian, status seleksi, dan arus penerimaan kas sekolah.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <a href="{{ route('admin.calon-siswa.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-sm hover:shadow active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Direktori Calon murid &rarr;</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold border border-slate-700 transition-all active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan & Rekapitulasi</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards (KPI Executive Grid) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- 1. Total Pendaftar -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-blue-300 transition-all flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendaftar</span>
                    <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        👥
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($stats['total_pendaftar']) }}</p>
                    <div class="mt-2 text-[11px] text-blue-700 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>T.A. 2027/2028</span>
                    </div>
                </div>
            </div>

            <!-- 2. Menunggu Bayar -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Bayar</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        ⏳
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight">{{ number_format($stats['menunggu_bayar_seleksi']) }}</p>
                    <div class="mt-2 text-[11px] text-amber-800 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>Biaya seleksi awal</span>
                    </div>
                </div>
            </div>

            <!-- 3. Verifikasi & Berkas -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-cyan-300 transition-all flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Verifikasi Berkas</span>
                    <span class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        📑
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl sm:text-3xl font-black text-cyan-700 tracking-tight">{{ number_format($stats['bayar_terverifikasi'] + $stats['sedang_lengkapi_data']) }}</p>
                    <div class="mt-2 text-[11px] text-cyan-800 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                        <span>Lengkapi formulir</span>
                    </div>
                </div>
            </div>

            <!-- 4. Tes Wawancara -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-indigo-300 transition-all flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tes Wawancara</span>
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        🎙️
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl sm:text-3xl font-black text-indigo-700 tracking-tight">{{ number_format($stats['wawancara_selesai']) }}</p>
                    <div class="mt-2 text-[11px] text-indigo-800 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        <span>Siap sidang pleno</span>
                    </div>
                </div>
            </div>

            <!-- 5. Lulus / Diterima -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lulus / Diterima</span>
                    <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        ✅
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl sm:text-3xl font-black text-emerald-700 tracking-tight">{{ number_format($stats['diterima']) }}</p>
                    <div class="mt-2 text-[11px] text-emerald-800 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ $stats['resmi_terdaftar'] }} resmi daftar ulang</span>
                    </div>
                </div>
            </div>

            <!-- 6. Ditolak / Mundur -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-rose-300 transition-all flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ditolak / Mundur</span>
                    <span class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        🚫
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl sm:text-3xl font-black text-rose-700 tracking-tight">{{ number_format($stats['ditolak'] + $stats['mengundurkan_diri']) }}</p>
                    <div class="mt-2 text-[11px] text-rose-800 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span>{{ $stats['mengundurkan_diri'] }} undur diri</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Trend Chart (September 2026 - Juni 2027) -->
        @include('partials.dashboard-timeline-chart')

        <!-- Status Gelombang Pendaftaran -->
        @if ($gelombangStats && $gelombangStats->isNotEmpty())
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Status Periode & Gelombang Pendaftaran</h3>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold">T.A. 2027/2028</span>
                    </div>
                    <span class="text-xs font-medium text-slate-400">Total {{ $gelombangStats->count() }} Gelombang Tersedia</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach ($gelombangStats as $g)
                        <div class="p-4 rounded-2xl border transition-all {{ $g->is_aktif ? 'border-emerald-300 bg-emerald-50/50 shadow-xs' : 'border-slate-200/80 bg-slate-50/50' }} flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $g->nama_gelombang }}</h4>
                                    @if ($g->is_aktif)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            AKTIF
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-200 text-slate-600">
                                            NON-AKTIF
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ \Carbon\Carbon::parse($g->tanggal_mulai)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($g->tanggal_selesai)->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <div class="text-right pl-3 border-l border-slate-200/60">
                                <span class="text-xl font-black text-slate-900 block">{{ $g->calon_siswa_count }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendaftar</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Executive Analytics: Conversion Funnel & Demographics -->
        @include('partials.dashboard-executive-insights')

        <!-- Quota Progress & Financial Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Quota Tracker per Jurusan (8 Cols) -->
            <div class="lg:col-span-8 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">Keterisian Kuota Kompetensi Keahlian</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Target kapasitas 72 murid (2 rombel @ 36 murid) per program keahlian</p>
                        </div>
                        <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline">
                            <span>Analitik Lengkap &rarr;</span>
                        </a>
                    </div>

                    <div class="space-y-3.5 mt-4">
                        @foreach ($jurusanStats as $j)
                            <div class="space-y-2 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-lg font-mono font-black text-xs bg-slate-200 text-slate-800">
                                            {{ $j['kode'] }}
                                        </span>
                                        <span class="font-bold text-slate-900">{{ $j['nama'] }}</span>
                                    </div>
                                    <div class="text-right flex items-center gap-2">
                                        <span class="font-bold text-slate-900">{{ $j['diterima'] }} / {{ $j['kuota'] }} Kursi</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $j['persentase'] >= 90 ? 'bg-rose-100 text-rose-800' : ($j['persentase'] >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ $j['persentase'] }}%
                                        </span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $j['persentase'] >= 90 ? 'bg-rose-600' : ($j['persentase'] >= 60 ? 'bg-amber-500' : 'bg-emerald-600') }}"
                                         style="width: {{ $j['persentase'] }}%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-slate-600 pt-0.5">
                                    <span>Pendaftar Masuk: <strong>{{ $j['pendaftar'] }}</strong> &bull; Resmi Terdaftar: <strong>{{ $j['resmi'] }}</strong></span>
                                    <span class="{{ $j['sisa'] <= 10 ? 'text-rose-700 font-bold' : 'text-slate-600 font-semibold' }}">Sisa Kuota: {{ $j['sisa'] }} kursi</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card (4 Cols) -->
            <div class="lg:col-span-4 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base sm:text-lg font-black text-slate-900">Kas & Keuangan SPMB</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Ringkasan arus kas masuk riil tervalidasi</p>
                    </div>

                    <div class="space-y-3 text-xs mt-4">
                        <div class="p-4 rounded-2xl bg-cyan-50/70 border border-cyan-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-cyan-950 font-bold block">Kas Biaya Seleksi</span>
                                <span class="text-slate-500 text-[11px]">Terverifikasi bendahara</span>
                            </div>
                            <span class="font-black text-base text-cyan-900">Rp {{ number_format($keuangan['kas_seleksi'], 0, ',', '.') }}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-emerald-950 font-bold block">Kas Daftar Ulang</span>
                                <span class="text-slate-500 text-[11px]">Cicilan & lunas masuk</span>
                            </div>
                            <span class="font-black text-base text-emerald-900">Rp {{ number_format($keuangan['kas_daftar_ulang'], 0, ',', '.') }}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-900 text-white flex items-center justify-between shadow-xs">
                            <div>
                                <span class="text-slate-300 font-bold block text-xs">Total Kas Masuk</span>
                                <span class="text-slate-400 text-[11px]">Penerimaan riil kas sekolah</span>
                            </div>
                            <span class="font-black text-lg text-amber-400">Rp {{ number_format($keuangan['total_kas_masuk'], 0, ',', '.') }}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-purple-950 font-bold block">Piutang Tagihan</span>
                                <span class="text-slate-500 text-[11px]">Sisa cicilan murid</span>
                            </div>
                            <span class="font-black text-sm text-purple-900">Rp {{ number_format($keuangan['sisa_piutang'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('bendahara.dashboard') }}"
                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs active:scale-95 cursor-pointer">
                        <span>Buka Manajemen Kas & Keuangan &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Candidates & Activity Log -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Recent Candidates Table (8 Cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">Pendaftar Terbaru</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Calon murid yang baru mendaftar mandiri ke portal SPMB</p>
                        </div>
                        <a href="{{ route('admin.calon-siswa.index') }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline">
                            <span>Lihat Semua Data &rarr;</span>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3.5">calon murid</th>
                                    <th class="px-4 py-3.5">Pilihan Jurusan</th>
                                    <th class="px-4 py-3.5">Gelombang</th>
                                    <th class="px-4 py-3.5 text-center">Status</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentCandidates as $cs)
                                    @php
                                        $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200 shadow-xs">
                                                    {{ strtoupper(substr($cs->nama_lengkap, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900">{{ $cs->nama_lengkap }}</p>
                                                    <p class="text-[11px] text-slate-400 font-mono">{{ $cs->nomor_pendaftaran }} &bull; NISN: {{ $cs->nisn }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-semibold text-slate-800">{{ $cs->jurusan?->nama_jurusan ?? '-' }}</span>
                                            <span class="block text-[11px] text-slate-400">{{ $cs->program?->nama ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-slate-600">
                                            {{ $cs->gelombang?->nama_gelombang ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                                                {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR']) ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' :
                                                   (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                                {{ str_replace('_', ' ', $statusStr) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.calon-siswa.show', $cs) }}"
                                               class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-colors font-bold text-xs shadow-xs">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400">Belum ada data pendaftar baru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                    <a href="{{ route('admin.calon-siswa.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline">
                        Lihat Seluruh Direktori Murid &rarr;
                    </a>
                </div>
            </div>

            <!-- Recent System Activity Logs (4 Cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">Aktivitas Terkini</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Audit log transaksi sistem</p>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.audit-trail.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline">
                                Semua Log &rarr;
                            </a>
                        @endif
                    </div>

                    <div class="p-5 divide-y divide-slate-100 overflow-y-auto max-h-[460px]">
                        @forelse ($recentActivities as $act)
                            <div class="py-3.5 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900">{{ $act->causer?->name ?? 'Sistem' }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $act->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-slate-600 leading-snug">{{ $act->description }}</p>
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono bg-slate-100 text-slate-600 border border-slate-200">
                                    #{{ $act->log_name }}
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs">Belum ada aktivitas tercatat.</div>
                        @endforelse
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50 text-center">
                        <a href="{{ route('admin.audit-trail.index') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 hover:underline">
                            Buka Audit Trail Lengkap &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>
