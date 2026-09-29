<x-layouts.app>
    <x-slot name="title">Tagihan & Pembayaran Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'pendidikan' }">
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

            <!-- Invoice Tabs Switcher -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tab Button 1: DSP & SPP -->
                @if ($tagihanDU)
                    <button type="button" @click="activeTab = 'pendidikan'"
                            :class="activeTab === 'pendidikan' ? 'border-nampi-orange bg-orange-50/40 ring-2 ring-nampi-orange/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50'"
                            class="p-5 rounded-2xl border text-left transition cursor-pointer flex items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-nampi-orange"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tagihan #1 (Pendidikan)</span>
                            </div>
                            <h3 class="text-base font-black text-slate-900 mt-1">
                                {{ $tagihanDU->details->where('kategori_snapshot', 'ASRAMA')->isNotEmpty() ? 'DSP, SPP & Biaya Asrama' : 'DSP & SPP Bulan ke-1' }}
                            </h3>
                            <p class="text-xs font-mono font-semibold text-slate-500 mt-0.5">#{{ $tagihanDU->nomor_tagihan }}</p>
                            <p class="text-xs font-semibold text-slate-600 mt-2">
                                Sisa Bayar: <strong class="{{ $remainingDU <= 0 ? 'text-emerald-600' : 'text-nampi-orange' }}">Rp {{ number_format($remainingDU, 0, ',', '.') }}</strong>
                            </p>
                            @if ($tagihanDU->total_diskon > 0)
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        🏷️ Hemat Rp {{ number_format($tagihanDU->total_diskon, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 line-through">Rp {{ number_format($tagihanDU->total_bruto, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if ($tagihanDU->status === 'LUNAS')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
                            @elseif ($tagihanDU->status === 'CICILAN')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">CICILAN</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">BELUM BAYAR</span>
                            @endif
                        </div>
                    </button>
                @endif

                <!-- Tab Button 2: Seragam -->
                @if ($tagihanSRG || (isset($pendingSeragamList) && $pendingSeragamList->isNotEmpty()))
                    <button type="button" @click="activeTab = 'seragam'"
                            :class="activeTab === 'seragam' ? 'border-teal-500 bg-teal-50/40 ring-2 ring-teal-500/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50'"
                            class="p-5 rounded-2xl border text-left transition cursor-pointer flex items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                    {{ isset($tagihanSRGList) && $tagihanSRGList->count() > 1 ? 'Tagihan Seragam (' . $tagihanSRGList->count() . ' Tahap)' : 'Tagihan #2 (Seragam)' }}
                                </span>
                            </div>
                            <h3 class="text-base font-black text-slate-900 mt-1">Paket Seragam & Atribut</h3>
                            @if($tagihanSRG)
                                <p class="text-xs font-mono font-semibold text-slate-500 mt-0.5">#{{ $tagihanSRG->nomor_tagihan }} {{ $tagihanSRG->tahap_seragam > 1 ? '(Tahap ' . $tagihanSRG->tahap_seragam . ')' : '' }}</p>
                                <p class="text-xs font-semibold text-slate-600 mt-2">
                                    Sisa Bayar: <strong class="{{ $remainingSRG <= 0 ? 'text-emerald-600' : 'text-teal-600' }}">Rp {{ number_format($remainingSRG, 0, ',', '.') }}</strong>
                                </p>
                            @else
                                <p class="text-xs text-amber-600 font-semibold mt-1">Status: Ditunda (Pesan Nanti)</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if ($tagihanSRG && $tagihanSRG->status === 'LUNAS')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
                            @elseif ($tagihanSRG && $tagihanSRG->status === 'CICILAN')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">CICILAN</span>
                            @elseif ($tagihanSRG)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">BELUM BAYAR</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">PESAN NANTI</span>
                            @endif
                        </div>
                    </button>
                @endif
            </div>

            <!-- =============================================================== -->
            <!-- TAB 1: BIAYA PENDIDIKAN (DSP & SPP)                             -->
            <!-- =============================================================== -->
            @if ($tagihanDU)
                <div x-show="activeTab === 'pendidikan'" class="space-y-6">
                    <!-- Top Action Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                        <div>
                            <h2 class="text-sm font-black text-slate-800">Tagihan Biaya Pendidikan {{ $tagihanDU->details->where('kategori_snapshot', 'ASRAMA')->isNotEmpty() ? '(DSP, SPP & Asrama)' : '(DSP & SPP Bulan ke-1)' }}</h2>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">Invoice #{{ $tagihanDU->nomor_tagihan }} &bull; Program: {{ $tagihanDU->program_snapshot }} &bull; {{ $tagihanDU->gelombang_snapshot }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('calon-siswa.daftar-ulang.cetak-tagihan', ['tagihan_id' => $tagihanDU->id]) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-xs">
                                <span>📄 Cetak Invoice (PDF)</span>
                            </a>
                            @if ($remainingDU > 0)
                                <a href="{{ route('calon-siswa.daftar-ulang.bayar', ['tagihan_id' => $tagihanDU->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition shadow-xs">
                                    <span>💳 Konfirmasi Transfer</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $tagihanDU->details->where('kategori_snapshot', 'ASRAMA')->isNotEmpty() ? 'Total Biaya Masuk & Asrama' : 'Total DSP & SPP' }}</span>
                            <p class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($tagihanDU->total_bruto, 0, ',', '.') }}</p>
                            <span class="text-[11px] text-slate-400">Tarif asli sebelum diskon</span>
                        </div>

                        <div class="p-5 rounded-2xl {{ $tagihanDU->total_diskon > 0 ? 'bg-emerald-50/70 border-emerald-200 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200/80' }} border shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold {{ $tagihanDU->total_diskon > 0 ? 'text-emerald-700' : 'text-slate-400' }} uppercase tracking-wider">Potongan Diskon</span>
                                @if ($tagihanDU->total_diskon > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-200 text-emerald-900">HEMAT</span>
                                @endif
                            </div>
                            <p class="text-xl font-black {{ $tagihanDU->total_diskon > 0 ? 'text-emerald-700' : 'text-slate-400' }} mt-1">
                                {{ $tagihanDU->total_diskon > 0 ? '- Rp ' . number_format($tagihanDU->total_diskon, 0, ',', '.') : 'Rp 0' }}
                            </p>
                            <span class="text-[11px] {{ $tagihanDU->total_diskon > 0 ? 'text-emerald-800 font-semibold' : 'text-slate-400' }}">
                                @if ($tagihanDU->diskon)
                                    {{ $tagihanDU->diskon->jenis_diskon }}
                                    @if ($tagihanDU->diskon->metode_diskon === 'persentase')
                                        ({{ (float) $tagihanDU->diskon->nilai_diskon }}%)
                                    @endif
                                @else
                                    Tidak ada diskon
                                @endif
                            </span>
                        </div>

                        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Telah Diverifikasi</span>
                            <p class="text-xl font-black text-blue-600 mt-1">Rp {{ number_format($totalPaidDU, 0, ',', '.') }}</p>
                            <span class="text-[11px] text-slate-400">Pembayaran disetujui</span>
                        </div>

                        <div class="p-5 rounded-2xl bg-gradient-to-br {{ $remainingDU <= 0 ? 'from-emerald-500 to-teal-600' : 'from-nampi-orange to-amber-600' }} text-white shadow-xs">
                            <span class="text-xs font-bold uppercase tracking-wider opacity-90">{{ $tagihanDU->details->where('kategori_snapshot', 'ASRAMA')->isNotEmpty() ? 'Sisa Kewajiban Masuk & Asrama' : 'Sisa Kewajiban DSP/SPP' }}</span>
                            <p class="text-2xl font-black mt-1">Rp {{ number_format($remainingDU, 0, ',', '.') }}</p>
                            <span class="text-[11px] opacity-90 inline-block mt-0.5">
                                Status: <strong>{{ $tagihanDU->status === 'LUNAS' ? 'LUNAS 100%' : ($tagihanDU->status === 'CICILAN' ? 'CICILAN AKTIF' : 'BELUM BAYAR') }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Breakdown Table -->
                        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-black text-slate-800">Rincian Komponen Biaya Baku</h3>
                                    <p class="text-[11px] text-slate-400">Snapshot resmi invoice #{{ $tagihanDU->nomor_tagihan }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $tagihanDU->program_snapshot }} &bull; {{ $tagihanDU->gelombang_snapshot }}
                                </span>
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
                                        @foreach ($tagihanDU->details as $item)
                                            <tr>
                                                <td class="px-4 py-3 font-semibold text-slate-800">
                                                    {{ $item->nama_biaya }}
                                                </td>
                                                <td class="px-3 py-3">
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
                                                <td class="px-4 py-3 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-slate-50/70 border-t border-slate-200">
                                        <tr>
                                            <td colspan="2" class="px-4 py-2.5 font-bold text-slate-700 text-right">Total Biaya Awal (Bruto):</td>
                                            <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rp {{ number_format($tagihanDU->total_bruto, 0, ',', '.') }}</td>
                                        </tr>
                                        @if ($tagihanDU->total_diskon > 0)
                                            <tr class="bg-emerald-50/80 border-y border-emerald-200/80 text-emerald-900">
                                                <td colspan="2" class="px-4 py-2.5 font-bold text-emerald-800 text-right">
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <span>🏷️</span>
                                                        <span>Potongan Diskon ({{ $tagihanDU->diskon?->jenis_diskon ?? 'Keringanan Biaya' }}):</span>
                                                    </div>
                                                    @if ($tagihanDU->diskon?->alasan)
                                                        <span class="block text-[10px] text-emerald-700 font-normal italic">{{ $tagihanDU->diskon->alasan }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2.5 font-black text-emerald-700 text-right text-sm">
                                                    - Rp {{ number_format($tagihanDU->total_diskon, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="border-t-2 border-slate-200">
                                            <td colspan="2" class="px-4 py-3 font-black text-slate-900 text-right text-sm">Total Yang Harus Dibayar (Netto):</td>
                                            <td class="px-4 py-3 font-black text-nampi-orange text-right text-base">Rp {{ number_format($tagihanDU->total_netto, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Bank & Payment History -->
                        <div class="lg:col-span-5 space-y-6">
                            <!-- Rekening Bank -->
                            <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-amber-900 space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🏦</span>
                                    <h4 class="text-sm font-bold text-amber-900">Rekening Resmi Pembayaran SPMB</h4>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-amber-200/60 text-xs space-y-1">
                                    <p class="font-bold text-slate-800">Bank BNI </p>
                                    <p class="font-mono text-base font-black text-slate-900">082 0083 086</p>
                                    <p class="text-slate-500">Atas Nama: <strong>SMK WIKRAMA 1 GARUT</strong></p>
                                </div>
                                <p class="text-[11px] text-amber-800 leading-relaxed">
                                    Biaya pendidikan dapat dibayar <strong>lunas sekaligus</strong> atau <strong>dicicil/bertahap</strong>. Cantumkan no pendaftaran <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong> pada berita transfer.
                                </p>
                            </div>

                            <!-- Riwayat Pembayaran DSP & SPP -->
                            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <h4 class="text-sm font-black text-slate-800">Riwayat Pembayaran DSP & SPP</h4>
                                    @if ($remainingDU > 0)
                                        <a href="{{ route('calon-siswa.daftar-ulang.bayar', ['tagihan_id' => $tagihanDU->id]) }}" class="text-xs font-bold text-nampi-orange hover:underline">
                                            + Konfirmasi Bayar
                                        </a>
                                    @endif
                                </div>

                                <div class="divide-y divide-slate-100">
                                    @forelse ($tagihanDU->pembayaran as $p)
                                        <div class="p-4 text-xs space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="font-black text-slate-900 text-sm">
                                                    Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                                </span>
                                                @if ($p->status === 'DIVERIFIKASI')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Valid</span>
                                                @elseif ($p->status === 'PENDING')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">⏳ Menunggu Review</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">✕ Ditolak</span>
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
                                                        Unduh Kwitansi (PDF)
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-6 text-center text-slate-400">
                                            <p class="text-xs font-semibold text-slate-600">Belum ada pembayaran DSP/SPP</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol konfirmasi bayar setelah mentransfer.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- =============================================================== -->
            <!-- TAB 2: PAKET SERAGAM & ATRIBUT (TERPISAH)                       -->
            <!-- =============================================================== -->
            @if ($tagihanSRG || (isset($pendingSeragamList) && $pendingSeragamList->isNotEmpty()))
                <div x-show="activeTab === 'seragam'" class="space-y-6">
                    @if ($tagihanSRG)
                        <!-- Multi-invoice Switcher (Jika ada lebih dari 1 tahap tagihan seragam) -->
                        @if (isset($tagihanSRGList) && $tagihanSRGList->count() > 1)
                            <div class="flex items-center gap-2 p-3.5 bg-teal-50/70 border border-teal-200/80 rounded-2xl text-xs overflow-x-auto shadow-2xs">
                                <span class="font-bold text-teal-900 shrink-0">Tahap Tagihan Seragam:</span>
                                @foreach ($tagihanSRGList as $idx => $tItem)
                                    <a href="{{ route('calon-siswa.daftar-ulang.index', ['tagihan_id' => $tItem->id]) }}"
                                       class="px-3.5 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 {{ $tagihanSRG->id === $tItem->id ? 'bg-teal-600 text-white shadow-2xs' : 'bg-white text-slate-700 border border-teal-200 hover:bg-teal-100' }}">
                                        <span>Tahap {{ $tItem->tahap_seragam ?? ($idx + 1) }}</span>
                                        <span class="font-mono text-[10px] opacity-80">(#{{ $tItem->nomor_tagihan }})</span>
                                        @if ($tItem->status === 'LUNAS')
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Top Action Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                            <div>
                                <h2 class="text-sm font-black text-slate-800">
                                    Tagihan Paket Seragam & Atribut Sekolah {{ $tagihanSRG->tahap_seragam > 1 ? '(Tahap ' . $tagihanSRG->tahap_seragam . ')' : '' }}
                                </h2>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">Invoice #{{ $tagihanSRG->nomor_tagihan }} &bull; Seragam Pilihan Siswa</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('calon-siswa.daftar-ulang.cetak-tagihan', ['tagihan_id' => $tagihanSRG->id]) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-xs">
                                    <span>📄 Cetak Invoice Seragam (PDF)</span>
                                </a>
                                @if ($remainingSRG > 0)
                                    <a href="{{ route('calon-siswa.daftar-ulang.bayar', ['tagihan_id' => $tagihanSRG->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 text-white text-xs font-bold hover:bg-teal-700 transition shadow-xs">
                                        <span>💳 Bayar Tagihan Seragam</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Financial Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $tagihanSRG->total_diskon > 0 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-4">
                            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Biaya Seragam</span>
                                <p class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($tagihanSRG->total_netto, 0, ',', '.') }}</p>
                                <span class="text-[11px] text-slate-400">
                                    @if ($tagihanSRG->total_diskon > 0)
                                        <span class="line-through text-slate-300">Rp {{ number_format($tagihanSRG->total_bruto, 0, ',', '.') }}</span>
                                        <span class="text-emerald-600 font-bold ml-1">(Diskon)</span>
                                    @else
                                        Total paket seragam terpilih
                                    @endif
                                </span>
                            </div>

                            @if ($tagihanSRG->total_diskon > 0)
                                <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 ring-2 ring-emerald-500/20 shadow-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Potongan Diskon</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-200 text-emerald-900">HEMAT</span>
                                    </div>
                                    <p class="text-xl font-black text-emerald-700 mt-1">
                                        - Rp {{ number_format($tagihanSRG->total_diskon, 0, ',', '.') }}
                                    </p>
                                    <span class="text-[11px] text-emerald-800 font-semibold">
                                        @if ($tagihanSRG->diskon)
                                            {{ $tagihanSRG->diskon->jenis_diskon }}
                                            @if ($tagihanSRG->diskon->metode_diskon === 'persentase')
                                                ({{ (float) $tagihanSRG->diskon->nilai_diskon }}%)
                                            @endif
                                        @else
                                            Diskon Seragam
                                        @endif
                                    </span>
                                </div>
                            @endif

                            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Telah Diverifikasi</span>
                                <p class="text-xl font-black text-teal-600 mt-1">Rp {{ number_format($totalPaidSRG, 0, ',', '.') }}</p>
                                <span class="text-[11px] text-slate-400">Pembayaran disetujui bendahara</span>
                            </div>

                            <div class="p-5 rounded-2xl bg-gradient-to-br {{ $remainingSRG <= 0 ? 'from-emerald-500 to-teal-600' : 'from-teal-600 to-cyan-700' }} text-white shadow-xs">
                                <span class="text-xs font-bold uppercase tracking-wider opacity-90">Sisa Kewajiban Seragam</span>
                                <p class="text-2xl font-black mt-1">Rp {{ number_format($remainingSRG, 0, ',', '.') }}</p>
                                <span class="text-[11px] opacity-90 inline-block mt-0.5">
                                    Status: <strong>{{ $tagihanSRG->status === 'LUNAS' ? 'LUNAS 100%' : ($tagihanSRG->status === 'CICILAN' ? 'CICILAN AKTIF' : 'BELUM BAYAR') }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <!-- Breakdown Table -->
                            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-black text-slate-800">Rincian Paket Seragam {{ $tagihanSRG->tahap_seragam > 1 ? '(Tahap ' . $tagihanSRG->tahap_seragam . ')' : '' }}</h3>
                                        <p class="text-[11px] text-slate-400">Komponen seragam yang dipesan pada tahap ini</p>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                        {{ $tagihanSRG->details->count() }} Komponen
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs text-slate-600">
                                        <thead class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                                            <tr>
                                                <th class="px-4 py-3">Nama Seragam</th>
                                                <th class="px-4 py-3 text-right">Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach ($tagihanSRG->details as $item)
                                                <tr>
                                                    <td class="px-4 py-3 font-semibold text-slate-800 flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                                        {{ $item->nama_biaya }}
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-bold text-slate-900">
                                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-slate-50/70 border-t border-slate-200">
                                            @if ($tagihanSRG->total_diskon > 0)
                                                <tr>
                                                    <td class="px-4 py-2.5 font-bold text-slate-700 text-right">Total Biaya Awal (Bruto):</td>
                                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rp {{ number_format($tagihanSRG->total_bruto, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr class="bg-emerald-50/80 border-y border-emerald-200/80 text-emerald-900">
                                                    <td class="px-4 py-2.5 font-bold text-emerald-800 text-right">
                                                        <div class="inline-flex items-center gap-1.5">
                                                          <span>🏷️</span>
                                                          <span>Potongan Diskon ({{ $tagihanSRG->diskon?->jenis_diskon ?? 'Keringanan Biaya' }}):</span>
                                                        </div>
                                                        @if ($tagihanSRG->diskon?->alasan)
                                                          <span class="block text-[10px] text-emerald-700 font-normal italic">{{ $tagihanSRG->diskon->alasan }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2.5 font-black text-emerald-700 text-right text-sm">
                                                        - Rp {{ number_format($tagihanSRG->total_diskon, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr class="border-t {{ $tagihanSRG->total_diskon > 0 ? 'border-t-2' : '' }} border-slate-200">
                                                <td class="px-4 py-3 font-black text-slate-900 text-right text-sm">{{ $tagihanSRG->total_diskon > 0 ? 'Total Yang Harus Dibayar (Netto):' : 'Total Seragam:' }}</td>
                                                <td class="px-4 py-3 font-black text-teal-600 text-right {{ $tagihanSRG->total_diskon > 0 ? 'text-base' : 'text-sm' }}">Rp {{ number_format($tagihanSRG->total_netto, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Bank & Payment History -->
                            <div class="lg:col-span-5 space-y-6">
                                <!-- Rekening Bank -->
                                <div class="p-5 rounded-2xl bg-teal-50/60 border border-teal-200/80 text-teal-900 space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">👔</span>
                                        <h4 class="text-sm font-bold text-teal-900">Pembayaran Khusus Seragam</h4>
                                    </div>
                                    <div class="p-3 bg-white rounded-xl border border-teal-200/60 text-xs space-y-1">
                                        <p class="font-bold text-slate-800">Bank BNI</p>
                                        <p class="font-mono text-base font-black text-slate-900">082 0083 086</p>
                                        <p class="text-slate-500">Atas Nama: <strong>SMK WIKRAMA 1 GARUT</strong></p>
                                    </div>
                                    <p class="text-[11px] text-teal-800 leading-relaxed">
                                        Transfer pembayaran seragam dilakukan terpisah dari DSP/SPP. Tuliskan keterangan: <strong>SERAGAM - {{ $calonSiswa->nomor_pendaftaran }}</strong>.
                                    </p>
                                </div>

                                <!-- Riwayat Pembayaran Seragam -->
                                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                        <h4 class="text-sm font-black text-slate-800">Riwayat Pembayaran Seragam</h4>
                                        @if ($remainingSRG > 0)
                                            <a href="{{ route('calon-siswa.daftar-ulang.bayar', ['tagihan_id' => $tagihanSRG->id]) }}" class="text-xs font-bold text-teal-600 hover:underline">
                                                + Konfirmasi Bayar
                                            </a>
                                        @endif
                                    </div>

                                    <div class="divide-y divide-slate-100">
                                        @forelse ($tagihanSRG->pembayaran as $p)
                                            <div class="p-4 text-xs space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-black text-slate-900 text-sm">
                                                        Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                                    </span>
                                                    @if ($p->status === 'DIVERIFIKASI')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Valid</span>
                                                    @elseif ($p->status === 'PENDING')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">⏳ Menunggu Review</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">✕ Ditolak</span>
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
                                                        <a href="{{ route('calon-siswa.daftar-ulang.cetak-kwitansi', $p) }}" target="_blank" class="font-bold text-teal-600 hover:underline">
                                                            Unduh Kwitansi (PDF)
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-6 text-center text-slate-400">
                                                <p class="text-xs font-semibold text-slate-600">Belum ada pembayaran seragam</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol konfirmasi bayar setelah mentransfer.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- State jika belum ada invoice seragam sama sekali (karena memilih Pesan Nanti untuk semua) -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0">
                                ⏳
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800">Belum Ada Tagihan Seragam Aktif</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Saat pengisian formulir, Anda memilih <strong>Pesan Nanti</strong> untuk seluruh seragam. Anda dapat mengaktifkan pemesanan seragam di bawah ini kapan saja Anda siap.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- =============================================================== -->
                    <!-- KARTU SISA SERAGAM (PESAN NANTI & AKTIVASI MANDIRI)             -->
                    <!-- =============================================================== -->
                    @if (isset($pendingSeragamList) && $pendingSeragamList->isNotEmpty())
                        <div class="p-6 rounded-3xl bg-amber-50/70 border-2 border-amber-300/80 shadow-xs space-y-4"
                             x-data="{
                                 selected: [],
                                 prices: {
                                     @foreach($pendingSeragamList as $ps)
                                         @php
                                             $namaS = $ps->jenisSeragam?->nama_jenis;
                                             $mBiaya = $biayaSeragamList->first(fn($b) => stripos($b->nama_biaya, $namaS) !== false || stripos($namaS, $b->nama_biaya) !== false);
                                             $pNom = $mBiaya ? (float)$mBiaya->nominal : 0;
                                         @endphp
                                         '{{ $ps->id }}': {{ $pNom }},
                                     @endforeach
                                 },
                                 totalSelected() {
                                     return this.selected.reduce((acc, id) => acc + (this.prices[id] || 0), 0);
                                 },
                                 formatRupiah(val) {
                                     return 'Rp ' + Number(val).toLocaleString('id-ID');
                                 },
                                 selectAll() {
                                     this.selected = Object.keys(this.prices);
                                 },
                                 unselectAll() {
                                     this.selected = [];
                                 }
                             }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-200/80 pb-3">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl">⏳</span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-black text-amber-950">Sisa Seragam Sekolah (Pesan Nanti)</h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">
                                                {{ $pendingSeragamList->count() }} Item Belum Dipesan
                                            </span>
                                        </div>
                                        <p class="text-xs text-amber-800 mt-0.5">
                                            Anda dapat mencentang seragam di bawah ini untuk mengaktifkan pemesanan sekarang. Sistem akan otomatis menerbitkan tagihan seragam susulan.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" @click="selectAll()" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-800 hover:bg-amber-200 transition cursor-pointer">
                                        Pilih Semua
                                    </button>
                                    <button type="button" @click="unselectAll()" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-slate-600 border border-amber-200 hover:bg-slate-50 transition cursor-pointer">
                                        Batal Pilih
                                    </button>
                                </div>
                            </div>

                            <form action="{{ route('calon-siswa.daftar-ulang.aktivasi-seragam') }}" method="POST" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($pendingSeragamList as $ps)
                                        @php
                                            $namaS = $ps->jenisSeragam?->nama_jenis;
                                            $mBiaya = $biayaSeragamList->first(fn($b) => stripos($b->nama_biaya, $namaS) !== false || stripos($namaS, $b->nama_biaya) !== false);
                                            $pNom = $mBiaya ? (float)$mBiaya->nominal : 0;
                                        @endphp
                                        <label class="p-3.5 rounded-2xl bg-white border border-amber-200/90 shadow-2xs flex items-center justify-between gap-3 cursor-pointer hover:border-amber-400 transition"
                                               :class="selected.includes('{{ $ps->id }}') ? 'ring-2 ring-amber-500 bg-amber-50/50' : ''">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="ukuran_seragam_ids[]" value="{{ $ps->id }}" x-model="selected" class="w-4 h-4 rounded text-orange-500 focus:ring-orange-400 border-slate-300">
                                                <div>
                                                    <span class="text-xs font-bold text-slate-800 block">{{ $namaS }}</span>
                                                    <span class="text-[11px] text-slate-500">Ukuran: <strong class="text-slate-800 font-mono">{{ $ps->ukuran }}</strong></span>
                                                </div>
                                            </div>
                                            <span class="text-xs font-mono font-bold text-amber-900 shrink-0">
                                                Rp {{ number_format($pNom, 0, ',', '.') }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="pt-3 border-t border-amber-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="text-xs text-amber-900">
                                        <span>Total Seragam Terpilih: </span>
                                        <strong class="text-sm font-black text-slate-900 font-mono" x-text="formatRupiah(totalSelected())">Rp 0</strong>
                                        <span class="text-[11px] text-slate-500" x-text="'(' + selected.length + ' item)'"></span>
                                    </div>

                                    <button type="submit" 
                                            :disabled="selected.length === 0"
                                            :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-600' : 'bg-teal-600 hover:bg-teal-700 text-white shadow-sm cursor-pointer'"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition">
                                        <span>💳 Aktifkan Pemesanan & Terbitkan Tagihan Susulan</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif ($tagihanSRG)
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-xs">
                            <span class="text-lg">✓</span>
                            <span>Seluruh seragam sekolah Anda telah berhasil dipesan dan diterbitkan tagihannya. Tidak ada seragam yang tertunda.</span>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
