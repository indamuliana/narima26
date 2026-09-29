<x-layouts.app>
    <x-slot name="title">Dashboard Pewawancara</x-slot>

    <x-slot name="sidebar">
        @include('pewawancara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Session Flash Notification -->
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Welcome Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-nampi-cyan tracking-wider">Panel Penguji & Wawancara</span>
                <h1 class="text-2xl font-black mt-1">Selamat Datang, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Kelola penilaian wawancara calon siswa & orang tua berdasarkan rubrik indikator resmi.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pewawancara.antrian') }}" class="px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                    Buka Antrian Wawancara
                </a>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Antrian Menunggu</p>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        ⏳
                    </span>
                </div>
                <p class="text-2xl font-black text-amber-600 mt-2">{{ $stats['antrian_menunggu'] ?? 0 }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Siap diuji & evaluasi</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sedang Proses / Draft</p>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        ✍️
                    </span>
                </div>
                <p class="text-2xl font-black text-blue-600 mt-2">{{ $stats['sedang_proses'] ?? 0 }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Draft penilaian tersimpan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Selesai Wawancara</p>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        ✅
                    </span>
                </div>
                <p class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['selesai'] ?? 0 }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Menunggu keputusan kelulusan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kriteria Rubrik</p>
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        📋
                    </span>
                </div>
                <p class="text-2xl font-black text-indigo-600 mt-2">{{ $stats['kriteria_aktif'] ?? 9 }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Indikator penilaian aktif</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Queue (Antrian Terkini) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Antrian Calon Siswa Siap Diuji</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Calon siswa yang telah menyelesaikan data & kesepahaman</p>
                    </div>
                    <a href="{{ route('pewawancara.antrian') }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentAntrian as $cs)
                        <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-600 text-xs">
                                    {{ strtoupper(substr($cs->nama_lengkap, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-slate-800">{{ $cs->nama_lengkap }}</p>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                            {{ $cs->jurusan?->kode_jurusan ?? '-' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $cs->nomor_pendaftaran }} • {{ $cs->sekolahAsal?->nama_sekolah ?? $cs->sekolah_asal_text ?? '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('pewawancara.wawancara.hub', $cs) }}"
                               class="px-3 py-1.5 rounded-lg bg-orange-500 text-white text-xs font-semibold hover:bg-orange-600 transition-colors shrink-0 shadow-xs">
                                Mulai Uji
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            Belum ada calon siswa dalam antrian siap uji.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Completed Interviews (Riwayat Terkini) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Riwayat Wawancara Selesai</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Penilaian terakhir yang telah selesai disimpan</p>
                    </div>
                    <a href="{{ route('pewawancara.riwayat') }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentRiwayat as $cs)
                        <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-slate-800">{{ $cs->nama_lengkap }}</p>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        SELESAI
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5 font-mono">
                                    {{ $cs->nomor_pendaftaran }} • Tgl: {{ $cs->wawancaraSiswa?->tanggal_wawancara?->format('d/m/Y') ?? $cs->wawancaraOrangTua?->tanggal_wawancara?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>
                            <a href="{{ route('pewawancara.wawancara.show', $cs) }}"
                               class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors shrink-0">
                                Detail Hasil
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            Belum ada sesi wawancara yang telah diselesaikan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
