<x-layouts.app>
    <x-slot name="title">Direktori Calon Siswa — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Direktori Calon Peserta Didik Baru</h1>
                <p class="text-xs text-slate-400 mt-0.5">Tabel data calon siswa terintegrasi dengan filter dinamis dan export dokumen</p>
            </div>
            <div class="flex items-center gap-2" x-data="{ openExport: false }">
                <div class="relative">
                    <button type="button" @click="openExport = !openExport" @click.outside="openExport = false"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Export CSV</span>
                        <svg class="w-3 h-3 text-emerald-200 transition-transform duration-200" :class="{ 'rotate-180': openExport }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openExport" x-cloak class="absolute right-0 mt-2 w-64 rounded-xl bg-white shadow-xl ring-1 ring-black/10 divide-y divide-slate-100 z-50 overflow-hidden" style="display: none;">
                        <div class="p-2">
                            <a href="{{ route('admin.calon-siswa.export.csv', array_merge(request()->query(), ['mode' => 'full'])) }}"
                               class="flex items-start gap-2.5 p-2 rounded-lg hover:bg-emerald-50 text-slate-800 transition-colors group">
                                <span class="p-1.5 rounded-md bg-emerald-100 text-emerald-700 text-xs">📊</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 group-hover:text-emerald-800">Ekspor Master Lengkap</p>
                                    <p class="text-[10px] text-slate-500">65+ kolom: registrasi, ortu, wawancara, EULA, rekap tagihan 1 baris</p>
                                </div>
                            </a>
                            <a href="{{ route('admin.calon-siswa.export.csv', array_merge(request()->query(), ['mode' => 'simple'])) }}"
                               class="flex items-start gap-2.5 p-2 rounded-lg hover:bg-slate-50 text-slate-800 transition-colors group mt-1">
                                <span class="p-1.5 rounded-md bg-slate-100 text-slate-700 text-xs">📄</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 group-hover:text-slate-900">Ekspor Ringkas</p>
                                    <p class="text-[10px] text-slate-500">15 kolom pokok data pendaftaran</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.calon-siswa.export.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar (DataTables Style) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.calon-siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- Search Keyword -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Peserta</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Nama, NISN, No. Pendaftaran..."
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status SPMB</label>
                    <select name="status_spmb" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="SEMUA">Semua Status</option>
                        @foreach ($statusList as $st)
                            <option value="{{ $st->value }}" {{ request('status_spmb') === $st->value ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $st->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jurusan Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jurusan</label>
                    <select name="jurusan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Gelombang Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Gelombang</label>
                    <select name="gelombang_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Gelombang</option>
                        @foreach ($gelombangList as $gel)
                            <option value="{{ $gel->id }}" {{ request('gelombang_id') == $gel->id ? 'selected' : '' }}>
                                {{ $gel->nama_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions / Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if (request()->hasAny(['q', 'status_spmb', 'jurusan_id', 'gelombang_id', 'program_id']))
                        <a href="{{ route('admin.calon-siswa.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Summary Badge -->
        <div class="flex items-center justify-between text-xs text-slate-500 px-1">
            <span>Menampilkan <strong>{{ $calonSiswaList->count() }}</strong> dari total <strong>{{ $totalCount }}</strong> calon siswa</span>
            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <select onchange="window.location.href = this.value" class="px-2 py-1 text-xs rounded-lg border border-slate-200">
                    @foreach ([10, 20, 50, 100] as $count)
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => $count]) }}" {{ request('per_page', 20) == $count ? 'selected' : '' }}>
                            {{ $count }} baris
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-4 py-3.5 text-center">No</th>
                            <th class="px-5 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Kompetensi Keahlian</th>
                            <th class="px-4 py-3.5">Gelombang</th>
                            <th class="px-4 py-3.5 text-center">Biaya Seleksi</th>
                            <th class="px-4 py-3.5 text-center">Status SPMB</th>
                            <th class="px-4 py-3.5 text-center">Keputusan</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($calonSiswaList as $index => $cs)
                            @php
                                $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                                $bayarSeleksi = $cs->pembayaranSeleksi;
                                $keputusan = $cs->keputusanKelulusan;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400">
                                    {{ $calonSiswaList->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900 text-sm">{{ $cs->nama_lengkap }}</p>
                                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        {{ $cs->nomor_pendaftaran }} &bull; NISN: {{ $cs->nisn }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $cs->sekolahAsal?->nama_sekolah ?? $cs->asal_sekolah_lainnya ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-bold text-slate-800 block">{{ $cs->jurusan?->nama_jurusan ?? '-' }}</span>
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains(strtolower($cs->program?->nama_program ?? ''), 'unggul') ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ $cs->program?->nama_program ?? 'Reguler' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ $cs->gelombang?->nama_gelombang }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($bayarSeleksi && $bayarSeleksi->status === 'DIVERIFIKASI')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ✓ Lunas
                                        </span>
                                    @elseif ($bayarSeleksi && $bayarSeleksi->status === 'PENDING')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            ⏳ Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                                        {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR', 'DAFTAR_ULANG_DIVERIFIKASI']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                           (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                        {{ str_replace('_', ' ', $statusStr) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($keputusan)
                                        <span class="font-bold text-xs {{ $keputusan->keputusan === 'DITERIMA' ? 'text-emerald-700' : 'text-rose-600' }}">
                                            {{ $keputusan->keputusan }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-whatsapp-contact-dropdown :calonSiswa="$cs" />
                                        <a href="{{ route('admin.calon-siswa.show', $cs) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                                            <span>Detail</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-slate-400">
                                    Tidak ditemukan calon peserta didik baru yang cocok dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($calonSiswaList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $calonSiswaList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
