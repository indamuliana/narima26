<x-layouts.app>
    <x-slot name="title">Verifikasi Pembayaran Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Pembayaran Daftar Ulang</h1>
                <p class="text-xs text-slate-500 mt-1">Verifikasi berkas bukti transfer dan konfirmasi pembayaran daftar ulang calon siswa.</p>
            </div>
            <a href="{{ route('bendahara.tagihan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                <span>Daftar Tagihan Siswa &rarr;</span>
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Review</span>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending']) }}</p>
                <span class="text-[11px] text-slate-400">Bukti transfer baru</span>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Diverifikasi Valid</span>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['diverifikasi']) }}</p>
                <span class="text-[11px] text-slate-400">Transaksi diterima</span>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dana Masuk</span>
                <p class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($stats['total_dana_masuk'], 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-400">Total pembayaran valid</span>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bukti Ditolak</span>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['ditolak']) }}</p>
                <span class="text-[11px] text-slate-400">Tidak valid/buram</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.pembayaran-daftar-ulang.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-7">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari no referensi, pengirim, siswa, no tagihan..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-3">
                    <select name="status" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="">Semua Status Pembayaran</option>
                        <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="DIVERIFIKASI" {{ $status === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi (Valid)</option>
                        <option value="DITOLAK" {{ $status === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if ($search || $status)
                        <a href="{{ route('bendahara.pembayaran-daftar-ulang.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Tanggal / No Ref</th>
                            <th class="px-4 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Tagihan Terkait</th>
                            <th class="px-4 py-3.5 text-right">Nominal Bayar</th>
                            <th class="px-4 py-3.5">Bank & Pengirim</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pembayaranList as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 text-xs block">
                                        {{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}
                                    </span>
                                    <span class="text-[11px] font-mono text-slate-400">
                                        Ref: {{ $p->nomor_referensi ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-800">{{ $p->calonSiswa?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $p->calonSiswa?->nomor_pendaftaran }} • {{ $p->calonSiswa?->jurusan?->kode_jurusan }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <a href="{{ route('bendahara.tagihan.show', $p->tagihan_id) }}" class="font-mono text-xs font-bold text-nampi-orange hover:underline block">
                                        {{ $p->tagihan?->nomor_tagihan }}
                                    </a>
                                    <span class="text-[11px] text-slate-400">
                                        Status: {{ $p->tagihan?->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-slate-900 block">
                                        Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-semibold text-slate-800 block">{{ $p->bank_pengirim }}</span>
                                    <span class="text-[11px] text-slate-400">a.n {{ $p->nama_pengirim }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($p->status === 'DIVERIFIKASI')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Valid
                                        </span>
                                    @elseif ($p->status === 'PENDING')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('bendahara.pembayaran-daftar-ulang.show', $p) }}"
                                           class="px-3 py-1.5 rounded-lg {{ $p->status === 'PENDING' ? 'bg-nampi-orange text-white hover:bg-orange-600' : 'border border-slate-200 text-slate-700 hover:bg-slate-100' }} text-xs font-semibold transition-colors">
                                            {{ $p->status === 'PENDING' ? 'Verifikasi' : 'Detail' }}
                                        </a>
                                        @if ($p->status === 'DIVERIFIKASI')
                                            <a href="{{ route('bendahara.pembayaran-daftar-ulang.cetak-kwitansi', $p) }}" target="_blank"
                                               class="px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors" title="Cetak Kwitansi">
                                                Kwitansi
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada transaksi pembayaran daftar ulang</p>
                                    <p class="text-xs text-slate-400 mt-1">Calon siswa yang telah mengunggah bukti transfer daftar ulang akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pembayaranList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $pembayaranList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
