<x-layouts.app>
    <x-slot name="title">Rincian Tagihan — {{ $tagihan->nomor_tagihan }}</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Top Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('bendahara.tagihan.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    ← Kembali ke Daftar Tagihan
                </a>
                <h1 class="text-2xl font-black text-slate-800">Tagihan #{{ $tagihan->nomor_tagihan }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Snapshot beku biaya daftar ulang calon siswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bendahara.tagihan.cetak', $tagihan) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                    <span>📄 Cetak PDF Tagihan</span>
                </a>
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

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Identity & Status Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <div class="md:col-span-2 space-y-1">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-nampi-orange font-bold">
                        {{ $tagihan->calonSiswa?->nomor_pendaftaran }}
                    </span>
                    <h2 class="text-lg font-black text-slate-800">{{ $tagihan->calonSiswa?->nama_lengkap }}</h2>
                    <p class="text-xs text-slate-400 font-mono">NISN: {{ $tagihan->calonSiswa?->nisn }}</p>
                    <p class="text-xs text-slate-600 mt-2">
                        Jurusan: <strong>{{ $tagihan->calonSiswa?->jurusan?->nama_jurusan }}</strong> | Program: <strong>{{ $tagihan->program_snapshot }}</strong>
                    </p>
                </div>

                <div>
                    <span class="text-xs text-slate-400 block">Status Pembayaran</span>
                    <div class="mt-1">
                        @if ($tagihan->status === 'LUNAS')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                LUNAS 100%
                            </span>
                        @elseif ($tagihan->status === 'CICILAN')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                CICILAN / SEBAGIAN
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                BELUM LUNAS
                            </span>
                        @endif
                    </div>
                    <span class="text-[11px] text-slate-400 mt-2 block">
                        Diterbitkan: {{ $tagihan->created_at?->format('d F Y H:i') }}
                    </span>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-right">
                    <span class="text-xs text-slate-500 block">Sisa Tagihan:</span>
                    <span class="text-xl font-black text-rose-600 block mt-1">
                        Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-emerald-600 font-semibold block mt-1">
                        Terbayar: Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Snapshot Items Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Rincian Komponen Biaya (Snapshot)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Item biaya di bawah ini terkunci saat tagihan diterbitkan.</p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-500">{{ $tagihan->details->count() }} Komponen</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Komponen Biaya</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-right">Tarif</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-5 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($tagihan->details as $index => $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-600"><code>{{ $item->kode_biaya_snapshot }}</code></td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->nama_biaya_snapshot }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                                        {{ $item->kategori_snapshot }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($item->nominal_snapshot, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">{{ $item->jumlah }}</td>
                                <td class="px-5 py-3 text-right font-bold text-slate-800">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-bold text-xs">
                        <tr>
                            <td colspan="6" class="px-5 py-2.5 text-right uppercase tracking-wider text-slate-600">Total Bruto:</td>
                            <td class="px-5 py-2.5 text-right text-slate-900">Rp {{ number_format($tagihan->total_bruto, 0, ',', '.') }}</td>
                        </tr>
                        @if ($tagihan->total_diskon > 0)
                            <tr class="text-emerald-700 bg-emerald-50/40">
                                <td colspan="6" class="px-5 py-2 text-right uppercase tracking-wider">
                                    Potongan Diskon ({{ $tagihan->diskon?->jenis_diskon ?? 'Khusus' }}):
                                </td>
                                <td class="px-5 py-2 text-right">- Rp {{ number_format($tagihan->total_diskon, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="bg-slate-100 text-slate-900 font-black text-sm">
                            <td colspan="6" class="px-5 py-3 text-right uppercase tracking-wider">Total Netto Kewajiban:</td>
                            <td class="px-5 py-3 text-right text-nampi-orange">Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Diskon Section (Section 24) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Diskon Card / Form -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Diskon & Beasiswa (Section 24)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Potongan biaya daftar ulang tercatat dalam audit trail.</p>
                    </div>
                </div>

                @if ($tagihan->diskon)
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm">{{ $tagihan->diskon->jenis_diskon }}</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-200 text-emerald-800 font-bold uppercase text-[10px]">
                                {{ $tagihan->diskon->metode_diskon }}
                            </span>
                        </div>
                        <p><strong>Potongan:</strong> Rp {{ number_format($tagihan->diskon->nominal_potongan, 0, ',', '.') }} (Nilai: {{ $tagihan->diskon->nilai_diskon }}{{ $tagihan->diskon->metode_diskon === 'persentase' ? '%' : '' }})</p>
                        <p><strong>Alasan:</strong> {{ $tagihan->diskon->alasan }}</p>
                        <p class="text-[11px] text-emerald-700">Diberikan oleh: {{ $tagihan->diskon->diberikanOleh?->name ?? 'Bendahara' }} pada {{ $tagihan->diskon->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                @else
                    <!-- Form Apply Discount -->
                    <form method="POST" action="{{ route('bendahara.diskon.store', $tagihan) }}" class="space-y-3 pt-2">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-600 mb-1">Jenis Diskon / Program:</label>
                                <input type="text" name="jenis_diskon" required placeholder="Contoh: Diskon Prestasi / Hafidz / Saudara Kandung"
                                       class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Metode:</label>
                                <select name="metode_diskon" required class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200">
                                    <option value="nominal">Nominal (Rp)</option>
                                    <option value="persentase">Persentase (%)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Nilai Potongan:</label>
                                <input type="number" name="nilai_diskon" required min="1" placeholder="Misal: 500000 atau 20"
                                       class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Alasan Pemberian:</label>
                                <input type="text" name="alasan" required placeholder="Alasan pemberian diskon..."
                                       class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition-colors shadow-xs cursor-pointer">
                            Terapkan Diskon ke Tagihan
                        </button>
                    </form>
                @endif
            </div>

            <!-- Payment Summary Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-800">Status Pelunasan Tagihan</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Total Netto Tagihan:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Total Telah Diverifikasi:</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Sisa Tagihan Belum Dibayar:</span>
                        <span class="font-black text-rose-600">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Progres Pembayaran:</span>
                        <span class="font-bold text-slate-800">
                            {{ $tagihan->total_netto > 0 ? round(($totalPaid / $tagihan->total_netto) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments History Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Riwayat Pembayaran Daftar Ulang</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar transfer pembayaran cicilan atau pelunasan calon siswa</p>
                </div>
                <a href="{{ route('bendahara.pembayaran-daftar-ulang.index') }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                    Lihat Semua Transaksi →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal Bayar</th>
                            <th class="px-4 py-3">Nominal Dibayar</th>
                            <th class="px-4 py-3">Bank & Pengirim</th>
                            <th class="px-4 py-3">No Referensi</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tagihan->pembayaran as $p)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-medium text-slate-800">
                                    {{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-black text-sm text-slate-900">
                                    Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-800 block">{{ $p->bank_pengirim }}</span>
                                    <span class="text-[11px] text-slate-400">a.n. {{ $p->nama_pengirim }}</span>
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-500">
                                    {{ $p->nomor_referensi ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($p->status === 'DIVERIFIKASI')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">DIVERIFIKASI</span>
                                    @elseif ($p->status === 'PENDING')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">PENDING</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">DITOLAK</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('bendahara.pembayaran-daftar-ulang.show', $p) }}"
                                           class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100">
                                            Review
                                        </a>
                                        @if ($p->status === 'DIVERIFIKASI')
                                            <a href="{{ route('bendahara.pembayaran-daftar-ulang.cetak', $p) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                                                Kwitansi PDF
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                    Belum ada catatan transfer pembayaran untuk tagihan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
