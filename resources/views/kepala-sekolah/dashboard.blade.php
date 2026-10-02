<x-layouts.app>
    <x-slot name="title">Dashboard Kepala Sekolah</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Welcome Executive Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-amber-400 tracking-wider">Executive Management SPMB 2027/2028</span>
                <h1 class="text-2xl font-black mt-1">Selamat Datang, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Monitoring komprehensif penerimaan murid baru, penetapan sidang pleno kelulusan, dan tata kelola kuota rombel.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kepala-sekolah.diskon.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-700 border border-slate-700 transition-colors shadow-xs">
                    🏷️ Kelola Diskon
                </a>
                <a href="{{ route('kepala-sekolah.sidang-kelulusan.index') }}" class="px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                    ⚖️ Buka Sidang Pleno
                </a>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pendaftar</p>
                    <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        📋
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total_pendaftar']) }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Semua pendaftar akun</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Sidang</p>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        ⏳
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-amber-600">{{ number_format($stats['menunggu_sidang']) }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Siap dievaluasi kepsek</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Dinyatakan Diterima</p>
                    <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        🎓
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['diterima']) }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Lulus seleksi SPMB</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Mengundurkan Diri</p>
                    <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        🚪
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-slate-600">{{ number_format($stats['mengundurkan_diri']) }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Penarikan berkas resmi</div>
                </div>
            </div>

            <a href="{{ route('kepala-sekolah.diskon.index') }}" class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-nampi-orange/50 transition-colors flex flex-col justify-between h-full cursor-pointer">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Diskon Disetujui</p>
                    <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shadow-xs">
                        🏷️
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-black text-purple-600">{{ number_format($stats['total_diskon']) }} Siswa</p>
                    <div class="mt-2 text-[11px] font-semibold text-emerald-600">Rp {{ number_format($stats['nominal_diskon'], 0, ',', '.') }}</div>
                </div>
            </a>
        </div>

        <!-- Statistik Program Unggulan vs Reguler & Gender -->
        @include('partials.dashboard-program-stats')

        <!-- Timeline Trend Chart (September 2026 - Juni 2027) -->
        @include('partials.dashboard-timeline-chart')

        <!-- Executive Analytics: Conversion Funnel, Demographics & Top Feeder Schools -->
        @include('partials.dashboard-executive-insights')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left: Department Quota Progress -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-base sm:text-lg font-black text-slate-900">Keterisian Kuota Kompetensi Keahlian</h2>
                    <span class="text-xs text-slate-400">Standar 72 Siswa (2 Rombel)</span>
                </div>

                <div class="space-y-4">
                    @forelse ($jurusanStats as $j)
                        @php
                            $target = 72;
                            $percent = min(100, round(($j->diterima_count / $target) * 100));
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800">{{ $j->nama_jurusan }} ({{ $j->kode_jurusan }})</span>
                                <span class="text-slate-500 font-mono">
                                    <strong class="text-slate-900">{{ $j->diterima_count }}</strong> / {{ $target }} diterima ({{ $percent }}%)
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-nampi-orange h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Total Peminat: {{ $j->total_count }} siswa</span>
                                <span>Sisa Kuota: {{ max(0, $target - $j->diterima_count) }} kursi</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada data jurusan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Right: Candidate Queue Awaiting Plenary Review -->
            <div class="lg:col-span-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">Antrian Menunggu Keputusan Sidang</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Kandidat yang telah selesai tes wawancara</p>
                        </div>
                        <a href="{{ route('kepala-sekolah.sidang-kelulusan.index', ['status_filter' => 'MENUNGGU_SIDANG']) }}"
                           class="text-xs font-semibold text-nampi-orange hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($antrianSidang as $kandidat)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors text-xs">
                            <div class="space-y-0.5">
                                <p class="font-bold text-slate-900">{{ $kandidat->nama_lengkap }}</p>
                                <p class="text-slate-400 font-mono text-[11px]">
                                    {{ $kandidat->nomor_pendaftaran }} &bull; {{ $kandidat->jurusan?->nama_jurusan }}
                                </p>
                                <p class="text-[11px] text-slate-500">
                                    Pewawancara: <strong>{{ $kandidat->wawancaraTerakhir?->pewawancara?->name ?? '-' }}</strong>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kepala-sekolah.sidang-kelulusan.show', $kandidat) }}"
                                   class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 transition-colors text-xs">
                                    Evaluasi & Keputusan &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Tidak ada calon siswa dalam antrian sidang saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
