<x-layouts.app>
    <x-slot name="title">Tagihan & Pembayaran Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('calon-siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Pembayaran Seleksi</span>
        </a>
        <a href="{{ route('calon-siswa.lengkapi-data.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Lengkapi Data & Berkas</span>
        </a>
        <a href="{{ route('calon-siswa.kesepahaman.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Kesepahaman SPMB</span>
        </a>
        <a href="{{ route('calon-siswa.dokumen.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Dokumen & Cetak PDF</span>
        </a>
        <a href="{{ route('calon-siswa.daftar-ulang.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Tagihan Daftar Ulang</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Tagihan & Daftar Ulang</h1>
                <p class="text-xs text-slate-500 mt-1">Informasi rincian biaya pendidikan resmi dan riwayat pembayaran cicilan / lunas calon siswa.</p>
            </div>
            @if ($tagihan)
                <div class="flex items-center gap-2">
                    <a href="{{ route('calon-siswa.daftar-ulang.cetak-tagihan') }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs">
                        <span>📄 Unduh Invoice Tagihan (PDF)</span>
                    </a>
                    @if ($remainingBalance > 0)
                        <a href="{{ route('calon-siswa.daftar-ulang.bayar') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                            <span>💳 Bayar / Konfirmasi Transfer</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

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

        @if (session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('info') }}</span>
            </div>
        @endif

        @if (! $tagihan)
            <!-- Invoice Not Ready State -->
            <div class="p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xs text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-3xl mx-auto">
                    ⏳
                </div>
                <div class="max-w-md mx-auto space-y-1">
                    <h2 class="text-lg font-black text-slate-800">Tagihan Belum Diterbitkan</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Tagihan daftar ulang resmi akan diterbitkan oleh panitia/bendahara sekolah setelah Anda dinyatakan berhak mengikuti tahap pendaftaran ulang.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('calon-siswa.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @else
            <!-- Invoice Financial Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan Bruto</span>
                    <p class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($tagihan->total_bruto, 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-400">Total standar tarif</span>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Potongan Diskon</span>
                    <p class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($tagihan->total_diskon, 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-400">{{ $tagihan->diskon ? $tagihan->diskon->jenis_diskon : 'Tidak ada diskon' }}</span>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Telah Diverifikasi</span>
                    <p class="text-xl font-black text-blue-600 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-400">Tercatat oleh bendahara</span>
                </div>

                <div class="p-5 rounded-2xl bg-gradient-to-br {{ $remainingBalance <= 0 ? 'from-emerald-500 to-teal-600' : 'from-nampi-orange to-amber-600' }} text-white shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider opacity-90">Sisa Kewajiban Bayar</span>
                    <p class="text-2xl font-black mt-1">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</p>
                    <span class="text-[11px] opacity-90 inline-block mt-0.5">
                        Status: <strong>{{ $tagihan->status === 'LUNAS' ? 'LUNAS 100%' : ($tagihan->status === 'CICILAN' ? 'CICILAN AKTIF' : 'BELUM BAYAR') }}</strong>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Breakdown Table (Snapshot) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-black text-slate-800">Rincian Komponen Biaya Baku</h2>
                            <p class="text-[11px] text-slate-400">Snapshot resmi invoice #{{ $tagihan->nomor_tagihan }}</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-500">{{ $tagihan->program_snapshot }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                                <tr>
                                    <th class="px-4 py-3">Komponen Biaya</th>
                                    <th class="px-3 py-3">Kategori</th>
                                    <th class="px-4 py-3 text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($tagihan->details as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-slate-800">
                                            {{ $item->nama_biaya }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-medium text-slate-600">
                                                {{ $item->kategori }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-slate-900">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50/70 border-t border-slate-200">
                                <tr>
                                    <td colspan="2" class="px-4 py-2.5 font-bold text-slate-700 text-right">Total Bruto:</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rp {{ number_format($tagihan->total_bruto, 0, ',', '.') }}</td>
                                </tr>
                                @if ($tagihan->total_diskon > 0)
                                    <tr>
                                        <td colspan="2" class="px-4 py-2 font-bold text-emerald-700 text-right">Potongan Diskon:</td>
                                        <td class="px-4 py-2 font-bold text-emerald-700 text-right">- Rp {{ number_format($tagihan->total_diskon, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr class="border-t border-slate-200">
                                    <td colspan="2" class="px-4 py-3 font-black text-slate-900 text-right text-sm">Total Bersih (Netto):</td>
                                    <td class="px-4 py-3 font-black text-nampi-orange text-right text-sm">Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment History & Submission CTA -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Bank Account Info Card -->
                    <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-amber-900 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🏦</span>
                            <h3 class="text-sm font-bold text-amber-900">Rekening Resmi Pembayaran SPMB</h3>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-amber-200/60 text-xs space-y-1">
                            <p class="font-bold text-slate-800">Bank Syariah Indonesia (BSI)</p>
                            <p class="font-mono text-base font-black text-slate-900">7145 8899 01</p>
                            <p class="text-slate-500">Atas Nama: <strong>SMK WIKRAMA 1 GARUT</strong></p>
                        </div>
                        <p class="text-[11px] text-amber-800 leading-relaxed">
                            Dapat dibayar secara <strong>lunas sekaligus</strong> atau <strong>dicicil/bertahap</strong> sesuai kesepakatan. Pastikan mencantumkan nomor pendaftaran pada berita transfer.
                        </p>
                    </div>

                    <!-- Payment Submissions History -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-black text-slate-800">Riwayat Pembayaran Anda</h2>
                            @if ($remainingBalance > 0)
                                <a href="{{ route('calon-siswa.daftar-ulang.bayar') }}" class="text-xs font-bold text-nampi-orange hover:underline">
                                    + Konfirmasi Bayar
                                </a>
                            @endif
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($tagihan->pembayaran as $p)
                                <div class="p-4 text-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-black text-slate-900 text-sm">
                                            Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                        </span>
                                        @if ($p->status === 'DIVERIFIKASI')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ✓ Valid
                                            </span>
                                        @elseif ($p->status === 'PENDING')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                ⏳ Menunggu Review
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                ✕ Ditolak
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-slate-500 space-y-0.5">
                                        <p>Tanggal: {{ $p->tanggal_bayar?->format('d/m/Y') }} &bull; Pengirim: {{ $p->nama_pengirim }} ({{ $p->bank_pengirim }})</p>
                                        @if ($p->nomor_referensi)
                                            <p class="font-mono">Ref: {{ $p->nomor_referensi }}</p>
                                        @endif
                                        @if ($p->catatan_bendahara)
                                            <p class="p-2 rounded bg-slate-50 border border-slate-200 text-slate-700 mt-1 italic">
                                                Catatan Bendahara: {{ $p->catatan_bendahara }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3 pt-1 text-[11px]">
                                        <a href="{{ asset('storage/' . $p->bukti_transfer_path) }}" target="_blank" class="font-semibold text-slate-600 hover:text-slate-900 underline">
                                            Lihat Bukti Transfer
                                        </a>
                                        @if ($p->status === 'DIVERIFIKASI')
                                            <span class="text-slate-300">&bull;</span>
                                            <a href="{{ route('calon-siswa.daftar-ulang.cetak-kwitansi', $p) }}" target="_blank" class="font-bold text-nampi-orange hover:underline">
                                                Unduh Kwitansi Resmi (PDF)
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400">
                                    <p class="text-xs font-semibold text-slate-600">Belum ada pembayaran yang diunggah</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol konfirmasi transfer jika Anda telah melakukan transfer dana.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
