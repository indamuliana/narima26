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
                <p class="text-xs text-slate-400 mt-1">Pusat kendali arus kas masuk, verifikasi transfer daftar ulang, piutang siswa, dan pelaporan keuangan terpadu.</p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('bendahara.laporan.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors border border-white/20 shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan Keuangan &rarr;</span>
                </a>
                <a href="{{ route('bendahara.tagihan.create') }}" class="px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                    + Terbitkan Tagihan
                </a>
            </div>
        </div>

        <!-- 4 Hero KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Kas Masuk -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Kas Diterima</p>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        💰
                    </span>
                </div>
                <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($stats['total_kas_masuk'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] text-slate-400">
                    Seleksi: Rp {{ number_format($stats['seleksi_masuk'], 0, ',', '.') }} &bull; DU: Rp {{ number_format($stats['daftar_ulang_masuk'], 0, ',', '.') }}
                </div>
            </div>

            <!-- 2. Target Tagihan Netto -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Target Tagihan Netto</p>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                        📑
                    </span>
                </div>
                <p class="text-2xl font-black text-purple-600 mt-2">Rp {{ number_format($stats['target_tagihan_netto'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] text-slate-400">
                    {{ $stats['total_tagihan'] }} tagihan resmi terbit
                </div>
            </div>

            <!-- 3. Sisa Piutang -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sisa Piutang DU</p>
                    <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">
                        ⏳
                    </span>
                </div>
                <p class="text-2xl font-black text-rose-600 mt-2">Rp {{ number_format($stats['sisa_piutang'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] font-semibold text-rose-500">
                    {{ 100 - $stats['collection_rate'] }}% belum tertagih
                </div>
            </div>

            <!-- 4. Antrean Verifikasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Antrean Verifikasi</p>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                        ⚡
                    </span>
                </div>
                <p class="text-2xl font-black text-amber-500 mt-2">{{ $stats['antrean_verifikasi'] }}</p>
                <div class="mt-2 text-[11px] text-amber-600 font-semibold">
                    {{ $stats['seleksi_pending'] }} seleksi &bull; {{ $stats['daftar_ulang_pending'] }} daftar ulang
                </div>
            </div>
        </div>

        <!-- 3 Financial Insight Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 1. Realisasi Target & Collection Rate -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Capaian Realisasi (DU)</h2>
                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                            {{ $stats['collection_rate'] }}%
                        </span>
                    </div>

                    <!-- Progress bar -->
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden mb-4">
                        <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, $stats['collection_rate']) }}%"></div>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Kas Masuk Daftar Ulang</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($stats['daftar_ulang_masuk'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Target Netto Tagihan</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($stats['target_tagihan_netto'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-slate-500">Sisa Piutang Belum Bayar</span>
                            <span class="font-bold text-rose-600">Rp {{ number_format($stats['sisa_piutang'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('bendahara.tagihan.index') }}" class="text-xs font-bold text-nampi-orange hover:underline flex items-center justify-between">
                        <span>Kelola Daftar Tagihan</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 2. Arus Kas Cepat & Pos Biaya -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Arus Kas Masuk Cepat</h2>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="p-2.5 bg-slate-50 rounded-xl text-center border border-slate-100">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Hari Ini</div>
                            <div class="text-xs font-black text-slate-800 mt-1">Rp {{ number_format($arusKas['hari_ini'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-xl text-center border border-slate-100">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Minggu Ini</div>
                            <div class="text-xs font-black text-slate-800 mt-1">Rp {{ number_format($arusKas['minggu_ini'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-2.5 bg-emerald-50 rounded-xl text-center border border-emerald-100">
                            <div class="text-[10px] text-emerald-600 font-semibold uppercase">Bulan Ini</div>
                            <div class="text-xs font-black text-emerald-700 mt-1">Rp {{ number_format($arusKas['bulan_ini'], 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-slate-100">
                            <span class="text-slate-500">1. Pendaftaran Seleksi</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($posBiaya['seleksi'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-100">
                            <span class="text-slate-500">2. DU Biaya Pendidikan</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($posBiaya['pendidikan'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500">3. DU Paket Seragam</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($posBiaya['seragam'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('bendahara.laporan.index') }}" class="text-xs font-bold text-nampi-orange hover:underline flex items-center justify-between">
                        <span>Lihat Buku Kas & Rekap</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 3. Status Kelunasan Siswa Daftar Ulang -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Status Kelunasan Siswa</h2>
                        <span class="text-xs font-medium text-slate-400">{{ $statusKelunasan['total_siswa'] }} calon murid</span>
                    </div>

                    <div class="space-y-3 mt-4 text-xs">
                        <!-- Lunas -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-emerald-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lunas Sempurna
                                </span>
                                <span class="font-mono font-bold text-slate-800">{{ $statusKelunasan['lunas'] }} siswa</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $statusKelunasan['total_siswa'] > 0 ? ($statusKelunasan['lunas'] / $statusKelunasan['total_siswa']) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Cicilan -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-amber-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Cicilan / Berjalan
                                </span>
                                <span class="font-mono font-bold text-slate-800">{{ $statusKelunasan['cicilan'] }} siswa</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $statusKelunasan['total_siswa'] > 0 ? ($statusKelunasan['cicilan'] / $statusKelunasan['total_siswa']) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Belum Bayar -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-rose-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Belum Bayar Sama Sekali
                                </span>
                                <span class="font-mono font-bold text-slate-800">{{ $statusKelunasan['belum_bayar'] }} siswa</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $statusKelunasan['total_siswa'] > 0 ? ($statusKelunasan['belum_bayar'] / $statusKelunasan['total_siswa']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('bendahara.pembayaran-daftar-ulang.index') }}" class="text-xs font-bold text-nampi-orange hover:underline flex items-center justify-between">
                        <span>Verifikasi Pembayaran DU</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabel Capaian Keuangan per Jurusan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Capaian Realisasi Keuangan per Kompetensi Keahlian</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Ringkasan target dan penerimaan kas daftar ulang berdasarkan program keahlian.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-600 font-bold border-b border-slate-100 uppercase tracking-wider text-[11px]">
                            <th class="p-3 text-center w-12">No</th>
                            <th class="p-3">Kompetensi Keahlian</th>
                            <th class="p-3 text-right">Target Tagihan Netto</th>
                            <th class="p-3 text-right">Kas Masuk Terverifikasi</th>
                            <th class="p-3 text-right">Sisa Piutang</th>
                            <th class="p-3 text-center w-40">% Realisasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($jurusanStats as $index => $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-3 text-center text-slate-400 font-semibold">{{ $index + 1 }}</td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-800">{{ $row['nama'] }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $row['kode'] }}</div>
                                </td>
                                <td class="p-3 text-right font-mono font-semibold text-slate-700">
                                    Rp {{ number_format($row['target'], 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right font-mono font-bold text-emerald-600">
                                    Rp {{ number_format($row['terbayar'], 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right font-mono {{ $row['piutang'] > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                    Rp {{ number_format($row['piutang'], 0, ',', '.') }}
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $row['persentase']) }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-700 min-w-[35px] text-right">
                                            {{ $row['persentase'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistik Program Unggulan vs Reguler & Gender -->
        @include('partials.dashboard-program-stats')

        <!-- Transaksi Terbaru Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Seleksi Payments -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Pembayaran Seleksi Terbaru</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Transaksi biaya pendaftaran awal</p>
                    </div>
                    <a href="{{ route('bendahara.pembayaran-seleksi.index') }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                        Lihat Semua &rarr;
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
                                    Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }} &bull; {{ $p->calonSiswa?->nomor_pendaftaran }}
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
                        Lihat Semua &rarr;
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
                                    Rp {{ number_format($pdu->nominal_dibayar, 0, ',', '.') }} &bull; {{ $pdu->tagihan?->nomor_tagihan }}
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
