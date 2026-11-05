<x-layouts.app>
    <x-slot name="title">Laporan Keuangan Terpadu SPMB</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header Banner & Action Buttons -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-nampi-green tracking-wider">Pembukuan & Rekapitulasi Kas SPMB</span>
                <h1 class="text-2xl font-black mt-1">Laporan Keuangan Terpadu</h1>
                <p class="text-xs text-slate-400 mt-1">Rekapitulasi lengkap kewajiban, realisasi pembayaran seleksi, daftar ulang, seragam, dan piutang calon murid.</p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('bendahara.laporan.export.pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700/80 hover:bg-slate-700 text-white text-xs font-bold transition-colors border border-slate-600 shadow-xs">
                    <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Cetak PDF Resmi</span>
                </a>
                <a href="{{ route('bendahara.laporan.export.xls', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Ekspor Excel (.xls)</span>
                </a>
            </div>
        </div>

        <!-- Summary Strip Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa Terdata</p>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">👥</span>
                </div>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['total_siswa'] }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Total catatan calon murid</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Kewajiban</p>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">📑</span>
                </div>
                <p class="text-2xl font-black text-purple-600 mt-2">Rp {{ number_format($summary['total_kewajiban'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] text-slate-400">Seleksi + DU Pendidikan + Seragam</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kas Masuk Terverifikasi</p>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">💰</span>
                </div>
                <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] font-semibold text-emerald-600">
                    {{ $summary['tingkat_pelunasan'] }}% tingkat pelunasan
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sisa Piutang</p>
                    <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">⏳</span>
                </div>
                <p class="text-2xl font-black text-rose-600 mt-2">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</p>
                <div class="mt-2 text-[11px] text-rose-500 font-semibold">Tunggakan & sisa cicilan</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Gelombang</label>
                    <select name="gelombang_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                        <option value="">Semua Gelombang</option>
                        @foreach ($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ (string)$gelombangId === (string)$g->id ? 'selected' : '' }}>
                                {{ $g->nama_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jurusan</label>
                    <select name="jurusan_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ (string)$jurusanId === (string)$j->id ? 'selected' : '' }}>
                                {{ $j->nama_jurusan }} ({{ $j->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status Kelunasan</label>
                    <select name="status_kelunasan" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                        <option value="">Semua Status</option>
                        <option value="LUNAS" {{ $statusKelunasan === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                        <option value="CICILAN" {{ $statusKelunasan === 'CICILAN' ? 'selected' : '' }}>Cicilan / Angsuran</option>
                        <option value="BELUM_BAYAR" {{ $statusKelunasan === 'BELUM_BAYAR' ? 'selected' : '' }}>Belum Bayar DU</option>
                        <option value="SELEKSI_LUNAS" {{ $statusKelunasan === 'SELEKSI_LUNAS' ? 'selected' : '' }}>Seleksi OK (Belum DU)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pencarian Siswa</label>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Nama / No. Reg / NISN..."
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-colors">
                        Terapkan Filter
                    </button>
                    @if ($gelombangId || $jurusanId || $statusKelunasan || $search)
                        <a href="{{ route('bendahara.laporan.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-colors" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Comprehensive Data Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rincian Finansial Terpadu per Calon Siswa</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Menampilkan status lengkap transaksi biaya seleksi, paket pendidikan, seragam, dan sisa piutang.</p>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <strong class="text-slate-800">{{ $paginatedRows->total() }}</strong> calon murid
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white font-bold border-b border-slate-800">
                            <th rowspan="2" class="p-3 text-center border-r border-slate-800 w-10">No</th>
                            <th rowspan="2" class="p-3 border-r border-slate-800 min-w-[200px]">Calon Murid</th>
                            <th rowspan="2" class="p-3 border-r border-slate-800 min-w-[150px]">Jurusan & Gelombang</th>
                            <th colspan="3" class="p-2 text-center bg-slate-800 border-r border-slate-700">Biaya Seleksi</th>
                            <th colspan="3" class="p-2 text-center bg-slate-800 border-r border-slate-700">Tagihan #1 (Pendidikan)</th>
                            <th colspan="3" class="p-2 text-center bg-slate-800 border-r border-slate-700">Tagihan #2 (Seragam)</th>
                            <th colspan="3" class="p-2 text-center bg-slate-800 border-r border-slate-700">Rekapitulasi Total</th>
                            <th rowspan="2" class="p-3 text-center min-w-[120px]">Status & Aksi</th>
                        </tr>
                        <tr class="bg-slate-800 text-slate-300 font-semibold border-b border-slate-700 text-[11px]">
                            <!-- Seleksi -->
                            <th class="p-2 text-right border-r border-slate-700 min-w-[85px]">Tagihan</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[85px]">Terbayar</th>
                            <th class="p-2 text-center border-r border-slate-700 min-w-[75px]">Status</th>
                            <!-- Pendidikan -->
                            <th class="p-2 text-right border-r border-slate-700 min-w-[95px]">Netto</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[95px]">Terbayar</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[95px]">Sisa</th>
                            <!-- Seragam -->
                            <th class="p-2 text-right border-r border-slate-700 min-w-[90px]">Netto</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[90px]">Terbayar</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[90px]">Sisa</th>
                            <!-- Total -->
                            <th class="p-2 text-right border-r border-slate-700 min-w-[105px]">Kewajiban</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[105px] text-emerald-400">Terbayar</th>
                            <th class="p-2 text-right border-r border-slate-700 min-w-[105px] text-rose-400">Sisa Piutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($paginatedRows as $index => $r)
                            @php
                                $s = $r['calon_siswa'];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-3 text-center text-slate-500 font-medium border-r border-slate-100">
                                    {{ $paginatedRows->firstItem() + $index }}
                                </td>
                                <td class="p-3 border-r border-slate-100">
                                    <div class="font-bold text-slate-800">{{ $s->nama_lengkap }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        {{ $s->nomor_pendaftaran }} &bull; NISN: {{ $s->nisn ?? '-' }}
                                    </div>
                                </td>
                                <td class="p-3 border-r border-slate-100">
                                    <div class="font-semibold text-slate-700">{{ $s->jurusan?->nama_jurusan ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $s->gelombang?->nama_gelombang ?? '-' }} &bull; {{ $s->program?->nama_program ?? 'Reguler' }}
                                    </div>
                                </td>

                                <!-- Biaya Seleksi -->
                                <td class="p-2 text-right border-r border-slate-100 font-mono text-slate-600">
                                    Rp {{ number_format($r['tagihan_sel'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono font-semibold text-emerald-600">
                                    Rp {{ number_format($r['bayar_sel'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-center border-r border-slate-100">
                                    @if ($r['status_sel'] === 'DIVERIFIKASI')
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">LUNAS</span>
                                    @elseif ($r['status_sel'] === 'PENDING')
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">PENDING</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">BELUM</span>
                                    @endif
                                </td>

                                <!-- Tagihan Pendidikan -->
                                <td class="p-2 text-right border-r border-slate-100 font-mono text-slate-600">
                                    Rp {{ number_format($r['netto_edu'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono font-semibold text-emerald-600">
                                    Rp {{ number_format($r['bayar_edu'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono {{ $r['sisa_edu'] > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                    Rp {{ number_format($r['sisa_edu'], 0, ',', '.') }}
                                </td>

                                <!-- Tagihan Seragam -->
                                <td class="p-2 text-right border-r border-slate-100 font-mono text-slate-600">
                                    Rp {{ number_format($r['netto_srg'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono font-semibold text-emerald-600">
                                    Rp {{ number_format($r['bayar_srg'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono {{ $r['sisa_srg'] > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                    Rp {{ number_format($r['sisa_srg'], 0, ',', '.') }}
                                </td>

                                <!-- Rekap Total -->
                                <td class="p-2 text-right border-r border-slate-100 font-mono font-bold text-slate-800">
                                    Rp {{ number_format($r['total_kewajiban'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono font-bold text-emerald-600">
                                    Rp {{ number_format($r['total_terbayar'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right border-r border-slate-100 font-mono font-bold text-rose-600">
                                    Rp {{ number_format($r['sisa_piutang'], 0, ',', '.') }}
                                </td>

                                <!-- Status & Aksi -->
                                <td class="p-3 text-center">
                                    @if ($r['status_kelunasan'] === 'LUNAS')
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ✓ LUNAS
                                        </span>
                                    @elseif ($r['status_kelunasan'] === 'CICILAN')
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                            ⏳ CICILAN
                                        </span>
                                    @elseif ($r['status_kelunasan'] === 'SELEKSI_LUNAS')
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                            SELEKSI OK
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200">
                                            BELUM BAYAR
                                        </span>
                                    @endif

                                    @if ($r['tagihan_edu_id'])
                                        <div class="mt-1.5">
                                            <a href="{{ route('bendahara.tagihan.show', $r['tagihan_edu_id']) }}"
                                               class="text-[10px] font-bold text-nampi-orange hover:underline">
                                                Lihat Invoice →
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="p-8 text-center text-slate-400">
                                    Tidak ada data catatan keuangan yang sesuai dengan parameter filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($paginatedRows->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $paginatedRows->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
