<x-layouts.app>
    <x-slot name="title">Laporan Keuangan & Pembukuan Kas SPMB</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header Banner & Action Buttons -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-nampi-green tracking-wider">Pembukuan & Rekapitulasi Kas SPMB</span>
                <h1 class="text-2xl font-black mt-1">Laporan Keuangan Terpadu</h1>
                <p class="text-xs text-slate-400 mt-1">Audit kas harian, piutang calon murid, capaian per jurusan, serta rekonsiliasi beasiswa/diskon.</p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('bendahara.laporan.export.pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700/80 hover:bg-slate-700 text-white text-xs font-bold transition-colors border border-slate-600 shadow-xs">
                    <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Cetak PDF Resmi (Tab Ini)</span>
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

        <!-- 4 Tab Navigation Bar -->
        <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-2 overflow-x-auto">
            <a href="{{ route('bendahara.laporan.index', array_merge(request()->except(['page']), ['tab' => 'piutang_siswa'])) }}"
               class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'piutang_siswa' ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>📋</span>
                <span>1. Rekap Piutang Siswa</span>
            </a>

            <a href="{{ route('bendahara.laporan.index', array_merge(request()->except(['page']), ['tab' => 'buku_kas'])) }}"
               class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'buku_kas' ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>🏦</span>
                <span>2. Jurnal Buku Kas Masuk</span>
            </a>

            <a href="{{ route('bendahara.laporan.index', array_merge(request()->except(['page']), ['tab' => 'matrix_jurusan'])) }}"
               class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'matrix_jurusan' ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>📊</span>
                <span>3. Rekap per Jurusan & Pos Biaya</span>
            </a>

            <a href="{{ route('bendahara.laporan.index', array_merge(request()->except(['page']), ['tab' => 'diskon'])) }}"
               class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'diskon' ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>🎟️</span>
                <span>4. Rekap Diskon & Beasiswa</span>
            </a>
        </div>

        <!-- Dynamic Summary Strip Cards based on Active Tab -->
        @if ($activeTab === 'piutang_siswa')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa Terdata</p>
                    <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['total_siswa'] }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Total catatan calon murid</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Kewajiban</p>
                    <p class="text-2xl font-black text-purple-600 mt-2">Rp {{ number_format($summary['total_kewajiban'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Seleksi + DSP + SPP + Seragam</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kas Terverifikasi</p>
                    <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] font-semibold text-emerald-600">{{ $summary['tingkat_pelunasan'] }}% tingkat pelunasan</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sisa Piutang</p>
                    <p class="text-2xl font-black text-rose-600 mt-2">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-rose-500 font-semibold">Tunggakan kewajiban</div>
                </div>
            </div>
        @elseif ($activeTab === 'buku_kas')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Transaksi</p>
                    <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['total_transaksi'] }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Penerimaan dana tercatat</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Kas Masuk</p>
                    <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($summary['total_kas_masuk'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-emerald-600 font-semibold">Akumulasi seluruh transfer/tunai</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kas Masuk Seleksi</p>
                    <p class="text-2xl font-black text-cyan-600 mt-2">Rp {{ number_format($summary['kas_seleksi'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Biaya tes/administrasi seleksi</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kas Masuk Daftar Ulang</p>
                    <p class="text-2xl font-black text-purple-600 mt-2">Rp {{ number_format($summary['kas_daftar_ulang'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Pendidikan + Seragam</div>
                </div>
            </div>
        @elseif ($activeTab === 'matrix_jurusan')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Target Netto Sekolah</p>
                    <p class="text-2xl font-black text-purple-600 mt-2">Rp {{ number_format($summary['total_target'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Kewajiban seluruh jurusan</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Realisasi Kas Terkumpul</p>
                    <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($summary['total_kas_masuk'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-emerald-600 font-semibold">{{ $summary['rata_capaian'] }}% dari target</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sisa Piutang Sekolah</p>
                    <p class="text-2xl font-black text-rose-600 mt-2">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-rose-500 font-semibold">Tunggakan seluruh jurusan</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rata-rata Capaian</p>
                    <p class="text-2xl font-black text-slate-800 mt-2">{{ $summary['rata_capaian'] }}%</p>
                    <div class="mt-2 text-[11px] text-slate-400">Collection rate PPDB</div>
                </div>
            </div>
        @elseif ($activeTab === 'diskon')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Penerima Beasiswa/Diskon</p>
                    <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['total_penerima'] }} calon murid</p>
                    <div class="mt-2 text-[11px] text-slate-400">Siswa mendapat potongan biaya</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Subsidi Diberikan</p>
                    <p class="text-2xl font-black text-purple-600 mt-2">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-purple-500 font-semibold">Alokasi potongan biaya resmi</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rata-rata Potongan</p>
                    <p class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($summary['rata_diskon'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-[11px] text-slate-400">Rata-rata per siswa penerima</div>
                </div>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                @if ($activeTab === 'buku_kas')
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pos Penerimaan</label>
                        <select name="pos_kategori" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                            <option value="">Semua Pos Biaya</option>
                            <option value="SELEKSI" {{ $posKategori === 'SELEKSI' ? 'selected' : '' }}>Biaya Seleksi</option>
                            <option value="DAFTAR_ULANG" {{ $posKategori === 'DAFTAR_ULANG' ? 'selected' : '' }}>Pendidikan (DSP & SPP)</option>
                            <option value="SERAGAM" {{ $posKategori === 'SERAGAM' ? 'selected' : '' }}>Paket Seragam</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status Verifikasi</label>
                        <select name="status_verifikasi" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                            <option value="DIVERIFIKASI" {{ $statusVerifikasi === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi (Kas Sah)</option>
                            <option value="PENDING" {{ $statusVerifikasi === 'PENDING' ? 'selected' : '' }}>Pending (Menunggu Review)</option>
                            <option value="DITOLAK" {{ $statusVerifikasi === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                            <option value="" {{ $statusVerifikasi === '' ? 'selected' : '' }}>Semua Status</option>
                        </select>
                    </div>
                @else
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

                    @if ($activeTab !== 'matrix_jurusan')
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
                    @endif

                    @if ($activeTab === 'piutang_siswa')
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
                    @elseif ($activeTab === 'diskon')
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jenis Beasiswa / Diskon</label>
                            <select name="jenis_diskon" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                                <option value="">Semua Jenis Diskon</option>
                                @foreach ($masterDiskonList as $md)
                                    <option value="{{ $md->nama_diskon }}" {{ $jenisDiskon === $md->nama_diskon ? 'selected' : '' }}>
                                        {{ $md->nama_diskon }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if ($activeTab !== 'matrix_jurusan')
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pencarian Siswa</label>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Nama / No. Reg / NISN..."
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-700 focus:border-nampi-orange focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>
                    @endif
                @endif

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-colors">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('bendahara.laporan.index', ['tab' => $activeTab]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-colors" title="Reset Filter">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tab 1: Rekap Piutang & Status Siswa Table -->
        @if ($activeTab === 'piutang_siswa')
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Rekapitulasi Piutang & Kewajiban Calon Siswa</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Rincian status pembayaran Seleksi, Tagihan Pendidikan #1, dan Paket Seragam #2 per individu murid.</p>
                    </div>
                    <div class="text-xs text-slate-500 font-medium">Total: <strong class="text-slate-800">{{ $paginatedRows->total() }}</strong> calon murid</div>
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
                                <th class="p-2 text-right border-r border-slate-700 min-w-[85px]">Tagihan</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[85px]">Terbayar</th>
                                <th class="p-2 text-center border-r border-slate-700 min-w-[75px]">Status</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[95px]">Netto</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[95px]">Terbayar</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[95px]">Sisa</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[90px]">Netto</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[90px]">Terbayar</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[90px]">Sisa</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[105px]">Kewajiban</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[105px] text-emerald-400">Terbayar</th>
                                <th class="p-2 text-right border-r border-slate-700 min-w-[105px] text-rose-400">Sisa Piutang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($paginatedRows as $index => $r)
                                @php $s = $r['calon_siswa']; @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center text-slate-500 font-medium border-r border-slate-100">
                                        {{ $paginatedRows->firstItem() + $index }}
                                    </td>
                                    <td class="p-3 border-r border-slate-100">
                                        <div class="font-bold text-slate-800">{{ $s->nama_lengkap }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $s->nomor_pendaftaran }} &bull; NISN: {{ $s->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 border-r border-slate-100">
                                        <div class="font-semibold text-slate-700">{{ $s->jurusan?->nama_jurusan ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $s->gelombang?->nama_gelombang ?? '-' }}</div>
                                    </td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono text-slate-600">Rp {{ number_format($r['tagihan_sel'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono font-semibold text-emerald-600">Rp {{ number_format($r['bayar_sel'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-center border-r border-slate-100">
                                        @if ($r['status_sel'] === 'DIVERIFIKASI')
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">LUNAS</span>
                                        @elseif ($r['status_sel'] === 'PENDING')
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">PENDING</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">BELUM</span>
                                        @endif
                                    </td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono text-slate-600">Rp {{ number_format($r['netto_edu'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono font-semibold text-emerald-600">Rp {{ number_format($r['bayar_edu'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono {{ $r['sisa_edu'] > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">Rp {{ number_format($r['sisa_edu'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono text-slate-600">Rp {{ number_format($r['netto_srg'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono font-semibold text-emerald-600">Rp {{ number_format($r['bayar_srg'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono {{ $r['sisa_srg'] > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">Rp {{ number_format($r['sisa_srg'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono font-bold text-slate-800">Rp {{ number_format($r['total_kewajiban'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono font-bold text-emerald-600">Rp {{ number_format($r['total_terbayar'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right border-r border-slate-100 font-mono font-bold text-rose-600">Rp {{ number_format($r['sisa_piutang'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-center">
                                        @if ($r['status_kelunasan'] === 'LUNAS')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">✓ LUNAS</span>
                                        @elseif ($r['status_kelunasan'] === 'CICILAN')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">⏳ CICILAN</span>
                                        @elseif ($r['status_kelunasan'] === 'SELEKSI_LUNAS')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">SELEKSI OK</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200">BELUM BAYAR</span>
                                        @endif
                                        @if ($r['tagihan_edu_id'])
                                            <div class="mt-1"><a href="{{ route('bendahara.tagihan.show', $r['tagihan_edu_id']) }}" class="text-[10px] font-bold text-nampi-orange hover:underline">Invoice &rarr;</a></div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="15" class="p-8 text-center text-slate-400">Tidak ada data yang sesuai filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($paginatedRows->hasPages())
                    <div class="p-4 border-t border-slate-100">{{ $paginatedRows->links() }}</div>
                @endif
            </div>

        <!-- Tab 2: Jurnal Buku Kas Masuk Table -->
        @elseif ($activeTab === 'buku_kas')
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Jurnal Buku Kas Penerimaan Dana Masuk</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Catatan kronologis penerimaan kas seleksi dan daftar ulang untuk rekonsiliasi rekening koran.</p>
                    </div>
                    <div class="text-xs text-slate-500 font-medium">Total: <strong class="text-slate-800">{{ $paginatedRows->total() }}</strong> transaksi</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold border-b border-slate-800 text-[11px]">
                                <th class="p-3 text-center w-10">No</th>
                                <th class="p-3">Waktu Verifikasi / Bayar</th>
                                <th class="p-3">No. Transaksi / Kwitansi</th>
                                <th class="p-3 min-w-[180px]">Calon Murid</th>
                                <th class="p-3">Jurusan</th>
                                <th class="p-3">Pos Penerimaan</th>
                                <th class="p-3">Metode Bayar</th>
                                <th class="p-3 text-right">Nominal Masuk</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3">Verifikator</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($paginatedRows as $index => $t)
                                @php $s = $t['calon_siswa']; @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center text-slate-400 font-semibold">{{ $paginatedRows->firstItem() + $index }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800">{{ $t['waktu'] ? \Carbon\Carbon::parse($t['waktu'])->translatedFormat('d M Y H:i') : '-' }}</div>
                                        <div class="text-[10px] text-slate-400">Bayar: {{ $t['tanggal_bayar'] ? \Carbon\Carbon::parse($t['tanggal_bayar'])->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="p-3 font-mono font-semibold text-slate-700">{{ $t['nomor_transaksi'] }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800">{{ $s?->nama_lengkap ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $s?->nomor_pendaftaran ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 text-slate-600">{{ $s?->jurusan?->nama_jurusan ?? '-' }}</td>
                                    <td class="p-3 font-semibold text-slate-700">{{ $t['pos'] }}</td>
                                    <td class="p-3 text-slate-500 uppercase">{{ $t['metode_bayar'] }}</td>
                                    <td class="p-3 text-right font-mono font-bold text-emerald-600">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-center">
                                        @if ($t['status'] === 'DIVERIFIKASI')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">DIVERIFIKASI</span>
                                        @elseif ($t['status'] === 'PENDING')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">PENDING</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">DITOLAK</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-600">{{ $t['verifikator'] }}</td>
                                    <td class="p-3 text-center">
                                        @if ($t['source_type'] === 'SELEKSI')
                                            <a href="{{ route('bendahara.pembayaran-seleksi.show', $t['source_id']) }}" class="text-[11px] font-bold text-nampi-orange hover:underline">Detail &rarr;</a>
                                        @else
                                            <a href="{{ route('bendahara.pembayaran-daftar-ulang.show', $t['source_id']) }}" class="text-[11px] font-bold text-nampi-orange hover:underline">Detail &rarr;</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="p-8 text-center text-slate-400">Belum ada transaksi dana masuk yang sesuai filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($paginatedRows->hasPages())
                    <div class="p-4 border-t border-slate-100">{{ $paginatedRows->links() }}</div>
                @endif
            </div>

        <!-- Tab 3: Rekap per Jurusan & Pos Biaya Table -->
        @elseif ($activeTab === 'matrix_jurusan')
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Rekapitulasi Target & Realisasi Finansial per Jurusan</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Matriks pendapatan komparatif seluruh kompetensi keahlian untuk rapat evaluasi PPDB & pimpinan.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold border-b border-slate-800">
                                <th rowspan="2" class="p-3 text-center border-r border-slate-800 w-10">No</th>
                                <th rowspan="2" class="p-3 border-r border-slate-800 min-w-[200px]">Kompetensi Keahlian</th>
                                <th rowspan="2" class="p-3 text-center border-r border-slate-800">Pendaftar</th>
                                <th colspan="2" class="p-2 text-center bg-slate-800 border-r border-slate-700">Seleksi</th>
                                <th colspan="2" class="p-2 text-center bg-slate-800 border-r border-slate-700">Pendidikan (DSP/SPP)</th>
                                <th colspan="2" class="p-2 text-center bg-slate-800 border-r border-slate-700">Seragam</th>
                                <th rowspan="2" class="p-3 text-right border-r border-slate-800">Diskon</th>
                                <th rowspan="2" class="p-3 text-right border-r border-slate-800">Target Netto</th>
                                <th rowspan="2" class="p-3 text-right border-r border-slate-800 text-emerald-400">Kas Masuk</th>
                                <th rowspan="2" class="p-3 text-right border-r border-slate-800 text-rose-400">Sisa Piutang</th>
                                <th rowspan="2" class="p-3 text-center min-w-[120px]">% Capaian</th>
                            </tr>
                            <tr class="bg-slate-800 text-slate-300 font-semibold border-b border-slate-700 text-[11px]">
                                <th class="p-2 text-right border-r border-slate-700">Target</th>
                                <th class="p-2 text-right border-r border-slate-700">Kas</th>
                                <th class="p-2 text-right border-r border-slate-700">Target</th>
                                <th class="p-2 text-right border-r border-slate-700">Kas</th>
                                <th class="p-2 text-right border-r border-slate-700">Target</th>
                                <th class="p-2 text-right border-r border-slate-700">Kas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($matrixRows as $index => $row)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center text-slate-400 font-semibold border-r border-slate-100">{{ $index + 1 }}</td>
                                    <td class="p-3 border-r border-slate-100">
                                        <div class="font-bold text-slate-800">{{ $row['jurusan']->nama_jurusan }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $row['jurusan']->kode }}</div>
                                    </td>
                                    <td class="p-3 text-center font-bold text-slate-700 border-r border-slate-100">{{ $row['pendaftar_count'] }}</td>
                                    <td class="p-2 text-right font-mono border-r border-slate-100 text-slate-600">Rp {{ number_format($row['target_seleksi'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right font-mono border-r border-slate-100 font-semibold text-emerald-600">Rp {{ number_format($row['kas_seleksi'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right font-mono border-r border-slate-100 text-slate-600">Rp {{ number_format($row['target_edu'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right font-mono border-r border-slate-100 font-semibold text-emerald-600">Rp {{ number_format($row['kas_edu'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right font-mono border-r border-slate-100 text-slate-600">Rp {{ number_format($row['target_srg'], 0, ',', '.') }}</td>
                                    <td class="p-2 text-right font-mono border-r border-slate-100 font-semibold text-emerald-600">Rp {{ number_format($row['kas_srg'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono text-purple-600 border-r border-slate-100">Rp {{ number_format($row['diskon'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono font-bold text-slate-800 border-r border-slate-100">Rp {{ number_format($row['total_target'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono font-bold text-emerald-600 border-r border-slate-100">Rp {{ number_format($row['total_kas'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono font-bold text-rose-600 border-r border-slate-100">Rp {{ number_format($row['sisa_piutang'], 0, ',', '.') }}</td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $row['persentase']) }}%"></div>
                                            </div>
                                            <span class="font-bold text-slate-700 min-w-[35px] text-right">{{ $row['persentase'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t-2 border-slate-200">
                                <td colspan="2" class="p-3 text-center">TOTAL KESELURUHAN SEKOLAH</td>
                                <td class="p-3 text-center">{{ $matrixRows->sum('pendaftar_count') }}</td>
                                <td class="p-2 text-right font-mono">Rp {{ number_format($matrixRows->sum('target_seleksi'), 0, ',', '.') }}</td>
                                <td class="p-2 text-right font-mono text-emerald-600">Rp {{ number_format($matrixRows->sum('kas_seleksi'), 0, ',', '.') }}</td>
                                <td class="p-2 text-right font-mono">Rp {{ number_format($matrixRows->sum('target_edu'), 0, ',', '.') }}</td>
                                <td class="p-2 text-right font-mono text-emerald-600">Rp {{ number_format($matrixRows->sum('kas_edu'), 0, ',', '.') }}</td>
                                <td class="p-2 text-right font-mono">Rp {{ number_format($matrixRows->sum('target_srg'), 0, ',', '.') }}</td>
                                <td class="p-2 text-right font-mono text-emerald-600">Rp {{ number_format($matrixRows->sum('kas_srg'), 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-mono text-purple-600">Rp {{ number_format($matrixRows->sum('diskon'), 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-mono">Rp {{ number_format($matrixRows->sum('total_target'), 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-mono text-emerald-600">Rp {{ number_format($matrixRows->sum('total_kas'), 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-mono text-rose-600">Rp {{ number_format($matrixRows->sum('sisa_piutang'), 0, ',', '.') }}</td>
                                <td class="p-3 text-center text-emerald-700 font-black">{{ $summary['rata_capaian'] }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        <!-- Tab 4: Rekap Diskon & Beasiswa Table -->
        @elseif ($activeTab === 'diskon')
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Rekapitulasi Pemberian Diskon & Beasiswa</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Audit transparansi pemotongan biaya pendidikan bagi calon murid berprestasi, yatim, dan afirmasi.</p>
                    </div>
                    <div class="text-xs text-slate-500 font-medium">Total: <strong class="text-slate-800">{{ $paginatedRows->total() }}</strong> penerima</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold border-b border-slate-800 text-[11px]">
                                <th class="p-3 text-center w-10">No</th>
                                <th class="p-3">Tanggal Diberikan</th>
                                <th class="p-3 min-w-[180px]">Calon Murid</th>
                                <th class="p-3">Jurusan & Gelombang</th>
                                <th class="p-3">Jenis Beasiswa / Diskon</th>
                                <th class="p-3 text-center">Skema</th>
                                <th class="p-3 text-right">Nominal Potongan</th>
                                <th class="p-3 min-w-[200px]">Alasan / Keterangan</th>
                                <th class="p-3">Diberikan Oleh</th>
                                <th class="p-3">Disetujui Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($paginatedRows as $index => $d)
                                @php $s = $d->calonSiswa; @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center text-slate-400 font-semibold">{{ $paginatedRows->firstItem() + $index }}</td>
                                    <td class="p-3 text-slate-600">{{ $d->diberikan_at ? \Carbon\Carbon::parse($d->diberikan_at)->format('d/m/Y') : ($d->created_at ? \Carbon\Carbon::parse($d->created_at)->format('d/m/Y') : '-') }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800">{{ $s?->nama_lengkap ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $s?->nomor_pendaftaran ?? '-' }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-semibold text-slate-700">{{ $s?->jurusan?->nama_jurusan ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $s?->gelombang?->nama_gelombang ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 font-semibold text-purple-700">{{ $d->jenis_diskon ?? 'Diskon Tagihan' }}</td>
                                    <td class="p-3 text-center uppercase text-[10px] font-bold text-slate-500">{{ $d->metode_diskon ?? 'Nominal' }}</td>
                                    <td class="p-3 text-right font-mono font-bold text-emerald-600">Rp {{ number_format($d->nominal_potongan, 0, ',', '.') }}</td>
                                    <td class="p-3 text-slate-600">{{ $d->alasan ?? ($d->keterangan ?? '-') }}</td>
                                    <td class="p-3 text-slate-600">{{ $d->diberikanOleh?->name ?? '-' }}</td>
                                    <td class="p-3 text-slate-600">{{ $d->disetujuiOleh?->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="p-8 text-center text-slate-400">Belum ada catatan beasiswa atau diskon yang sesuai parameter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($paginatedRows->hasPages())
                    <div class="p-4 border-t border-slate-100">{{ $paginatedRows->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>
