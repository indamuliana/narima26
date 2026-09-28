<x-layouts.app>
    <x-slot name="title">Dashboard Bendahara</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
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

        <!-- Welcome Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-nampi-green tracking-wider">Panel Keuangan & Kas SPMB</span>
                <h1 class="text-2xl font-black mt-1">Selamat Datang, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Kelola kas seleksi, verifikasi transfer daftar ulang, snapshot biaya, dan validasi potongan diskon.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bendahara.tagihan.create') }}" class="px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                    + Terbitkan Tagihan
                </a>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kas Masuk Seleksi</p>
                    <span class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold text-xs">
                        💵
                    </span>
                </div>
                <p class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($stats['seleksi_masuk'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] text-slate-400">
                    {{ $stats['seleksi_pending'] }} transfer pending
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kas Masuk Daftar Ulang</p>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        🏦
                    </span>
                </div>
                <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($stats['daftar_ulang_masuk'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] text-slate-400">
                    {{ $stats['daftar_ulang_pending'] }} transfer pending
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tagihan Terbit</p>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                        📑
                    </span>
                </div>
                <p class="text-2xl font-black text-purple-600 mt-2">{{ $stats['total_tagihan'] }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Snapshot tagihan aktif</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Verifikasi Tertunda</p>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                        ⏳
                    </span>
                </div>
                <p class="text-2xl font-black text-amber-500 mt-2">{{ $stats['seleksi_pending'] + $stats['daftar_ulang_pending'] }}</p>
                <div class="mt-2 text-[11px] text-amber-600 font-semibold">Perlu tindakan review</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Seleksi Payments -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Pembayaran Seleksi Terbaru</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Transaksi biaya pendaftaran awal</p>
                    </div>
                    <a href="{{ route('bendahara.pembayaran-seleksi.index') }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentSeleksi as $p)
                        <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-slate-800">{{ $p->calonSiswa?->nama_lengkap }}</p>
                                    @if ($p->status === 'DIVERIFIKASI')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">DIVERIFIKASI</span>
                                    @elseif ($p->status === 'PENDING')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">PENDING</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">DITOLAK</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5 font-mono">
                                    Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }} • {{ $p->calonSiswa?->nomor_pendaftaran }}
                                </p>
                            </div>
                            <a href="{{ route('bendahara.pembayaran-seleksi.show', $p) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                Review
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            Belum ada transaksi pembayaran seleksi.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Daftar Ulang Payments -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Pembayaran Daftar Ulang Terbaru</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Transfer cicilan atau pelunasan tagihan</p>
                    </div>
                    <a href="{{ route('bendahara.pembayaran-daftar-ulang.index') }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentDaftarUlang as $pdu)
                        <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-slate-800">{{ $pdu->calonSiswa?->nama_lengkap }}</p>
                                    @if ($pdu->status === 'DIVERIFIKASI')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">DIVERIFIKASI</span>
                                    @elseif ($pdu->status === 'PENDING')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">PENDING</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">DITOLAK</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5 font-mono">
                                    Rp {{ number_format($pdu->nominal_dibayar, 0, ',', '.') }} • {{ $pdu->tagihan?->nomor_tagihan }}
                                </p>
                            </div>
                            <a href="{{ route('bendahara.pembayaran-daftar-ulang.show', $pdu) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                Review
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            Belum ada transaksi pembayaran daftar ulang.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
