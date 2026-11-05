<x-layouts.app>
    <x-slot name="title">Tagihan & Pembayaran Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Tagihan & Daftar Ulang</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Informasi rincian biaya pendidikan resmi dan paket seragam sekolah. Pembayaran seragam dipisahkan dari DSP & SPP.
                </p>
            </div>
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

        @if (! $tagihanDU && ! $tagihanSRG)
            <!-- Invoice Not Ready State -->
            <div class="p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xs text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-3xl mx-auto">
                    ⏳
                </div>
                <div class="max-w-md mx-auto space-y-1">
                    <h2 class="text-lg font-black text-slate-800">Tagihan Belum Diterbitkan</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Tagihan daftar ulang resmi dan seragam akan diterbitkan oleh panitia/bendahara sekolah setelah Anda dinyatakan berhak mengikuti tahap pendaftaran ulang.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('calon-siswa.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @else
            {{-- Banner Keterangan Diskon / Beasiswa Diterima --}}
            @php
                $diskonDiterima = collect([$tagihanDU, $tagihanSRG])->filter(fn($t) => $t && $t->total_diskon > 0);
            @endphp

            @if ($diskonDiterima->isNotEmpty())
                <div class="p-5 sm:p-6 rounded-3xl bg-emerald-50/90 border-2 border-emerald-300/80 shadow-xs relative overflow-hidden">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-200 text-emerald-700 flex items-center justify-center text-2xl shrink-0 shadow-xs">
                                🏷️
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-700 text-white shadow-xs">
                                        Penerima Diskon / Beasiswa
                                    </span>
                                    <span class="text-xs text-emerald-800 font-bold">Keputusan Resmi Panitia SPMB & Kepala Sekolah</span>
                                </div>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900">
                                    Selamat! Anda Memperoleh Keringanan Biaya Pendidikan
                                </h2>
                                <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
                                    Sekolah telah menyetujui pemberian potongan biaya pendidikan untuk akun Anda. Total nominal diskon telah otomatis memotong nilai kewajiban tagihan Anda.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white px-5 py-4 rounded-2xl border border-emerald-200 shadow-xs flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center shrink-0 gap-1 min-w-[200px]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Potongan Diskon</span>
                            <span class="text-2xl sm:text-3xl font-black text-emerald-600">
                                - Rp {{ number_format($diskonDiterima->sum('total_diskon'), 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] font-bold text-emerald-800">
                                {{ $diskonDiterima->pluck('diskon.jenis_diskon')->filter()->unique()->join(', ') }}
                            </span>
                        </div>
                    </div>

                    <!-- Breakdown per tagihan penerima diskon -->
                    <div class="mt-4 pt-4 border-t border-emerald-200/70 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @foreach ($diskonDiterima as $td)
                            <div class="flex items-center justify-between bg-white px-4 py-3 rounded-2xl border border-emerald-200/80 shadow-xs">
                                <div class="min-w-0 pr-3">
                                    <span class="font-bold text-slate-900 block truncate text-xs">
                                        {{ $td->jenis_tagihan === \App\Models\Tagihan::JENIS_DAFTAR_ULANG ? 'Tagihan Biaya Pendidikan' : 'Tagihan Paket Seragam' }}
                                    </span>
                                    <span class="text-[11px] text-slate-600 block truncate mt-0.5">
                                        Program: <strong class="text-emerald-800">{{ $td->diskon?->jenis_diskon ?? 'Diskon Khusus' }}</strong>
                                        @if ($td->diskon?->metode_diskon === 'persentase')
                                            ({{ (float)$td->diskon->nilai_diskon }}%)
                                        @endif
                                    </span>
                                    @if ($td->diskon?->alasan)
                                        <span class="text-[10px] text-emerald-700 italic block truncate mt-0.5" title="{{ $td->diskon->alasan }}">
                                            "{{ $td->diskon->alasan }}"
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-black text-emerald-600 text-sm block">
                                        - Rp {{ number_format($td->total_diskon, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 line-through">
                                        Dari Rp {{ number_format($td->total_bruto, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- =============================================================== -->
            <!-- TABEL RINCIAN BIAYA KONSOLIDASI (TAGIHAN 1 & TAGIHAN 2)         -->
            <!-- =============================================================== -->
            @php
                $seragamDetails = $tagihanSRGList->isNotEmpty() ? $tagihanSRGList->flatMap->details : ($tagihanSRG ? $tagihanSRG->details : collect());
                $totalBrutoSRG = $tagihanSRGList->isNotEmpty() ? $tagihanSRGList->sum('total_bruto') : ($tagihanSRG?->total_bruto ?? 0);
                $totalDiskonSRG = $tagihanSRGList->isNotEmpty() ? $tagihanSRGList->sum('total_diskon') : ($tagihanSRG?->total_diskon ?? 0);
                $totalNettoSRG = $tagihanSRGList->isNotEmpty() ? $tagihanSRGList->sum('total_netto') : ($tagihanSRG?->total_netto ?? 0);

                $totalBrutoDU = $tagihanDU?->total_bruto ?? 0;
                $totalDiskonDU = $tagihanDU?->total_diskon ?? 0;
                $totalNettoDU = $tagihanDU?->total_netto ?? 0;

                $grandTotal = $totalNettoDU + $totalNettoSRG;
            @endphp

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900">Rincian Seluruh Tagihan Daftar Ulang</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kompilasi resmi Tagihan 1 (Biaya Pendidikan) dan Tagihan 2 (Paket Seragam & Atribut).
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $calonSiswa->program?->nama_program ?? 'Reguler' }} &bull; {{ $calonSiswa->gelombang?->nama_gelombang ?? 'Gelombang 1' }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-100/70 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3">Komponen Biaya</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-5 py-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {{-- SECTION 1: TAGIHAN 1 (PENDIDIKAN) --}}
                            <tr class="bg-orange-50/60 border-t border-b border-orange-200/80">
                                <td colspan="4" class="px-4 py-2.5 font-black text-slate-800 text-xs sm:text-sm uppercase tracking-wide">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-nampi-orange"></span>
                                            <span>Rincian Biaya Pendidikan (Tagihan 1)</span>
                                        </div>
                                        @if ($tagihanDU)
                                            <span class="font-mono text-[11px] text-slate-500 font-semibold normal-case">#{{ $tagihanDU->nomor_tagihan }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if ($tagihanDU && $tagihanDU->details->isNotEmpty())
                                @foreach ($tagihanDU->details as $idx => $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                        <td class="px-4 py-2.5 font-semibold text-slate-800">
                                            {{ $item->nama_biaya }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @php
                                                $kategoriNama = strtoupper($item->kategori_snapshot ?? $item->kategori);
                                                $badgeClass = match($kategoriNama) {
                                                    'DSP' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'SPP' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'ASRAMA' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'SERAGAM' => 'bg-teal-50 text-teal-700 border-teal-200',
                                                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded border text-[10px] font-bold {{ $badgeClass }}">
                                                {{ $item->kategori_snapshot ?? $item->kategori }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-2.5 text-right font-mono font-bold text-slate-900">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-slate-400 italic">
                                        Belum ada rincian komponen biaya pendidikan yang diterbitkan.
                                    </td>
                                </tr>
                            @endif

                            {{-- SUB TOTAL TAGIHAN 1 --}}
                            <tr class="bg-slate-50/80 font-bold border-t border-slate-200">
                                <td colspan="3" class="px-4 py-2.5 text-right text-slate-700 text-xs">
                                    Sub total (tagihan 1):
                                </td>
                                <td class="px-5 py-2.5 text-right font-mono font-bold text-slate-900 text-xs">
                                    Rp {{ number_format($totalBrutoDU, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if ($totalDiskonDU > 0)
                                <tr class="bg-emerald-50/70 text-emerald-900 border-t border-emerald-100 text-xs">
                                    <td colspan="3" class="px-4 py-2 text-right">
                                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-800 text-[11px]">
                                            <span>🏷️</span>
                                            <span>Potongan Diskon / Beasiswa:</span>
                                            <span class="font-normal italic">({{ $tagihanDU->diskon?->jenis_diskon ?? 'Keringanan Biaya' }})</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-right font-mono font-bold text-emerald-700 text-xs">
                                        - Rp {{ number_format($totalDiskonDU, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="bg-slate-50 font-black border-t border-slate-200 text-xs">
                                    <td colspan="3" class="px-4 py-2 text-right text-slate-800">
                                        Sub total Netto (tagihan 1):
                                    </td>
                                    <td class="px-5 py-2 text-right font-mono font-bold text-nampi-orange text-xs">
                                        Rp {{ number_format($totalNettoDU, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif

                            {{-- SECTION 2: TAGIHAN 2 (SERAGAM & ATRIBUT) --}}
                            <tr class="bg-teal-50/60 border-t-2 border-b border-teal-200/80">
                                <td colspan="4" class="px-4 py-2.5 font-black text-slate-800 text-xs sm:text-sm uppercase tracking-wide">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                            <span>Rincian Biaya Seragam & Atribut (Tagihan 2)</span>
                                        </div>
                                        @if ($tagihanSRG)
                                            <span class="font-mono text-[11px] text-slate-500 font-semibold normal-case">#{{ $tagihanSRG->nomor_tagihan }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if ($seragamDetails->isNotEmpty())
                                @foreach ($seragamDetails as $idx => $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                        <td class="px-4 py-2.5 font-semibold text-slate-800">
                                            {{ $item->nama_biaya }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="px-2 py-0.5 rounded border text-[10px] font-bold bg-teal-50 text-teal-700 border-teal-200">
                                                SERAGAM
                                            </span>
                                        </td>
                                        <td class="px-5 py-2.5 text-right font-mono font-bold text-slate-900">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-slate-500 italic bg-amber-50/30 text-xs">
                                        Belum ada paket seragam yang dipesan (Calon siswa memilih opsi "Pesan Nanti").
                                    </td>
                                </tr>
                            @endif

                            {{-- SUB TOTAL TAGIHAN 2 --}}
                            <tr class="bg-slate-50/80 font-bold border-t border-slate-200">
                                <td colspan="3" class="px-4 py-2.5 text-right text-slate-700 text-xs">
                                    Sub Total (tagihan 2):
                                </td>
                                <td class="px-5 py-2.5 text-right font-mono font-bold text-slate-900 text-xs">
                                    Rp {{ number_format($totalBrutoSRG, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if ($totalDiskonSRG > 0)
                                <tr class="bg-emerald-50/70 text-emerald-900 border-t border-emerald-100 text-xs">
                                    <td colspan="3" class="px-4 py-2 text-right">
                                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-800 text-[11px]">
                                            <span>🏷️</span>
                                            <span>Potongan Diskon Seragam:</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-right font-mono font-bold text-emerald-700 text-xs">
                                        - Rp {{ number_format($totalDiskonSRG, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="bg-slate-50 font-black border-t border-slate-200 text-xs">
                                    <td colspan="3" class="px-4 py-2 text-right text-slate-800">
                                        Sub Total Netto (tagihan 2):
                                    </td>
                                    <td class="px-5 py-2 text-right font-mono font-bold text-teal-600 text-xs">
                                        Rp {{ number_format($totalNettoSRG, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>

                        {{-- FOOTER / TOTAL BIAYA --}}
                        <tfoot class="border-t-2 border-slate-300">
                            <tr class="bg-slate-100/90">
                                <td colspan="3" class="px-5 py-3.5 text-right font-black text-slate-900 text-xs sm:text-sm">
                                    Total Biaya ( Sub total (tagihan 1)+Sub Total (tagihan 2) ) :
                                </td>
                                <td class="px-5 py-3.5 text-right font-black text-slate-900 text-sm sm:text-base font-mono">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- FOOTNOTE CATATAN --}}
                <div class="px-5 py-4 bg-slate-50/90 border-t border-slate-200 text-xs text-slate-600 space-y-1">
                    <p class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Catatan:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-600 pl-0.5">
                        <li>Pembayaran DSP (Dana Sumbangan Pendidikan) dapat diangsur di tahun pertama</li>
                        <li>Dana yang telah dibayarkan tidak dapat dikembalikan (non-refundable) apabila calon murid mengundurkan diri (kecuali tidak lulus dari SMP/MTs/Sederajat).</li>
                    </ul>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- MENU PEMBAYARAN & KONFIRMASI TRANSFER (TANPA TAB)               -->
            <!-- =============================================================== -->
            <div class="pt-2">
                <h2 class="text-lg font-black text-slate-800">Pembayaran & Konfirmasi Transfer</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Informasi nomor rekening resmi SPMB, status kewajiban kedua tagihan, dan riwayat konfirmasi pembayaran Anda.
                </p>
            </div>

            <!-- Rekening Resmi Pembayaran SPMB (Tampil 1 kali di atas) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-amber-50/70 border border-amber-200/90 text-amber-950 shadow-xs space-y-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🏦</span>
                    <div>
                        <h3 class="text-sm font-black text-amber-950">Rekening Resmi Pembayaran SPMB SMK Wikrama 1 Garut</h3>
                        <p class="text-xs text-amber-800">Gunakan rekening resmi di bawah ini untuk seluruh pembayaran Biaya Pendidikan maupun Paket Seragam:</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="p-4 bg-white rounded-2xl border border-amber-200/80 shadow-2xs">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Bank Tujuan</span>
                        <p class="text-base font-black text-slate-900 mt-0.5">Bank BNI</p>
                        <p class="text-xs text-slate-600 mt-1">Atas Nama: <strong class="text-slate-900">SMK WIKRAMA 1 GARUT</strong></p>
                    </div>

                    <div class="p-4 bg-white rounded-2xl border border-amber-200/80 shadow-2xs flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Rekening Resmi</span>
                            <p class="font-mono text-xl sm:text-2xl font-black text-slate-900 tracking-wider mt-0.5">082 0083 086</p>
                        </div>
                        <p class="text-[11px] text-amber-800 mt-1">
                            Berita transfer: <strong class="text-slate-900">{{ $calonSiswa->nomor_pendaftaran }} - {{ $calonSiswa->nama_lengkap }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2 Kartu Status & Aksi Pembayaran Berdampingan (Side-by-Side Grid) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- KARTU 1: TAGIHAN BIAYA PENDIDIKAN (DSP & SPP) -->
                @if ($tagihanDU)
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col justify-between space-y-5">
                        <div class="space-y-4">
                            <!-- Card Header -->
                            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-nampi-orange"></span>
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tagihan #1 (Pendidikan)</span>
                                    </div>
                                    <h3 class="text-base font-black text-slate-900 mt-1">
                                        {{ $tagihanDU->details->where('kategori_snapshot', 'ASRAMA')->isNotEmpty() ? 'DSP, SPP & Biaya Asrama' : 'DSP & SPP Bulan ke-1' }}
                                    </h3>
                                    <p class="text-xs font-mono text-slate-400 mt-0.5">#{{ $tagihanDU->nomor_tagihan }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if ($tagihanDU->status === 'LUNAS')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
                                    @elseif ($tagihanDU->status === 'CICILAN')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">CICILAN</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">BELUM BAYAR</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Financial Numbers -->
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="text-slate-400 block text-[11px]">Total Kewajiban</span>
                                    <span class="font-bold text-slate-800 text-sm font-mono block mt-0.5">Rp {{ number_format($tagihanDU->total_netto, 0, ',', '.') }}</span>
                                    @if ($tagihanDU->total_diskon > 0)
                                        <span class="text-[10px] text-emerald-600 block">Diskon: -Rp {{ number_format($tagihanDU->total_diskon, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="text-slate-400 block text-[11px]">Telah Diverifikasi</span>
                                    <span class="font-bold text-blue-600 text-sm font-mono block mt-0.5">Rp {{ number_format($totalPaidDU, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl {{ $remainingDU <= 0 ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-orange-50/70 text-orange-950 border border-orange-200/80' }} flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider">Sisa Kewajiban Pendidikan</span>
                                <span class="text-base sm:text-lg font-black font-mono {{ $remainingDU <= 0 ? 'text-emerald-700' : 'text-nampi-orange' }}">
                                    Rp {{ number_format($remainingDU, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-2">
                            <a href="{{ route('calon-siswa.daftar-ulang.cetak-tagihan', ['tagihan_id' => $tagihanDU->id]) }}" target="_blank"
                               class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                                <span>📄 Invoice (PDF)</span>
                            </a>
                            @if ($remainingDU > 0)
                                <a href="{{ route('calon-siswa.daftar-ulang.bayar', ['tagihan_id' => $tagihanDU->id]) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition shadow-xs">
                                    <span>💳 Konfirmasi Bayar</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- KARTU 2: TAGIHAN PAKET SERAGAM & ATRIBUT -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col justify-between space-y-5">
                    @if ($tagihanSRG)
                        <div class="space-y-4">
                            <!-- Card Header -->
                            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                            {{ isset($tagihanSRGList) && $tagihanSRGList->count() > 1 ? 'Tagihan Seragam (' . $tagihanSRGList->count() . ' Tahap)' : 'Tagihan #2 (Seragam)' }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-black text-slate-900 mt-1">Paket Seragam & Atribut</h3>
                                    <p class="text-xs font-mono text-slate-400 mt-0.5">#{{ $tagihanSRG->nomor_tagihan }} {{ $tagihanSRG->tahap_seragam > 1 ? '(Tahap ' . $tagihanSRG->tahap_seragam . ')' : '' }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if ($tagihanSRG->status === 'LUNAS')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
                                    @elseif ($tagihanSRG->status === 'CICILAN')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">CICILAN</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">BELUM BAYAR</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Financial Numbers -->
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="text-slate-400 block text-[11px]">Total Kewajiban</span>
                                    <span class="font-bold text-slate-800 text-sm font-mono block mt-0.5">Rp {{ number_format($tagihanSRG->total_netto, 0, ',', '.') }}</span>
                                    @if ($tagihanSRG->total_diskon > 0)
                                        <span class="text-[10px] text-emerald-600 block">Diskon: -Rp {{ number_format($tagihanSRG->total_diskon, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="text-slate-400 block text-[11px]">Telah Diverifikasi</span>
                                    <span class="font-bold text-teal-600 text-sm font-mono block mt-0.5">Rp {{ number_format($totalPaidSRG, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl {{ $remainingSRG <= 0 ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-teal-50/70 text-teal-950 border border-teal-200/80' }} flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider">Sisa Kewajiban Seragam</span>
                                <span class="text-base sm:text-lg font-black font-mono {{ $remainingSRG <= 0 ? 'text-emerald-700' : 'text-teal-600' }}">
                                    Rp {{ number_format($remainingSRG, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-2">
                            <a href="{{ route('calon-siswa.daftar-ulang.cetak-tagihan', ['tagihan_id' => $tagihanSRG->id]) }}" target="_blank"
                               class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                                <span>📄 Invoice Seragam</span>
                            </a>
                            @if ($remainingSRG > 0)
                                <a href="{{ route('calon-siswa.daftar-ulang.bayar', ['tagihan_id' => $tagihanSRG->id]) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-teal-600 text-white text-xs font-bold hover:bg-teal-700 transition shadow-xs">
                                    <span>💳 Bayar Seragam</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- State jika belum ada invoice seragam (Pesan Nanti) -->
                        <div class="space-y-4 my-auto">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                                    ⏳
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800">Tagihan Seragam Belum Aktif</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">PESAN NANTI</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Anda memilih opsi <strong>Pesan Nanti</strong> pada formulir seragam. Anda dapat mengaktifkan pesanan seragam pada formulir aktivasi di bawah kapan saja.
                            </p>
                        </div>
                    @endif
                </div>
            </div>


            <!-- Riwayat Seluruh Pembayaran Terpadu (Single Unified History) -->
            @php
                $semuaPembayaran = $calonSiswa->tagihan
                    ->flatMap(fn($t) => $t->pembayaran->map(function ($p) use ($t) {
                        $p->tagihan_ref = $t;
                        return $p;
                    }))
                    ->sortByDesc(fn($p) => $p->tanggal_bayar ?? $p->created_at)
                    ->values();
            @endphp

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-black text-slate-800">Riwayat Seluruh Pembayaran Daftar Ulang</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh transaksi transfer Biaya Pendidikan maupun Paket Seragam yang telah Anda konfirmasikan.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $semuaPembayaran->count() }} Transaksi
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($semuaPembayaran as $p)
                        <div class="p-5 text-xs space-y-3 hover:bg-slate-50/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @php
                                        $isDU = ($p->tagihan_ref?->jenis_tagihan ?? $p->tagihan?->jenis_tagihan) === \App\Models\Tagihan::JENIS_DAFTAR_ULANG;
                                    @endphp
                                    @if ($isDU)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-orange-100 text-orange-900 border border-orange-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-nampi-orange"></span>
                                            <span>Biaya Pendidikan (#{{ $p->tagihan_ref?->nomor_tagihan ?? $p->tagihan?->nomor_tagihan }})</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-100 text-teal-900 border border-teal-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                            <span>Paket Seragam (#{{ $p->tagihan_ref?->nomor_tagihan ?? $p->tagihan?->nomor_tagihan }})</span>
                                        </span>
                                    @endif

                                    <span class="font-black text-slate-900 text-base font-mono">
                                        Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div>
                                    @if ($p->status === 'DIVERIFIKASI')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Valid / Terverifikasi</span>
                                    @elseif ($p->status === 'PENDING')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">⏳ Menunggu Review</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">✕ Ditolak</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-[11px] text-slate-500 space-y-1">
                                <p>
                                    Tanggal Transfer: <strong class="text-slate-700">{{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}</strong> &bull;
                                    Pengirim: <strong class="text-slate-700">{{ $p->nama_pengirim }}</strong> ({{ $p->bank_pengirim }})
                                    @if ($p->nomor_referensi)
                                        &bull; <span class="font-mono">Ref: {{ $p->nomor_referensi }}</span>
                                    @endif
                                </p>
                                @if ($p->catatan_bendahara)
                                    <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 italic">
                                        Catatan Bendahara: {{ $p->catatan_bendahara }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 pt-1 text-xs">
                                <a href="{{ asset('storage/' . $p->bukti_transfer_path) }}" target="_blank" class="font-semibold text-slate-600 hover:text-slate-900 underline flex items-center gap-1">
                                    <span>🔍 Lihat Bukti Transfer</span>
                                </a>
                                @if ($p->status === 'DIVERIFIKASI')
                                    <span class="text-slate-300">&bull;</span>
                                    <a href="{{ route('calon-siswa.daftar-ulang.cetak-kwitansi', $p) }}" target="_blank" class="font-bold text-nampi-orange hover:underline flex items-center gap-1">
                                        <span>🧾 Unduh Kwitansi (PDF)</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 space-y-1">
                            <p class="text-sm font-semibold text-slate-600">Belum ada riwayat pembayaran</p>
                            <p class="text-xs text-slate-400">Silakan lakukan transfer ke rekening resmi di atas lalu klik tombol konfirmasi pembayaran.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @endif
    </div>
</x-layouts.app>
