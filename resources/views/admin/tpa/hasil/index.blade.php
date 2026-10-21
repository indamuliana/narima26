<x-layouts.app>
    <x-slot name="title">Hasil & Rekapitulasi Nilai TPA — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Hasil & Rekapitulasi Nilai TPA</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pemetaan potensi akademik calon siswa (Literasi & Numerasi) sebagai acuan penyelenggaraan pembelajaran</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Export Excel -->
                <a href="{{ route('admin.tpa.hasil.export.xls', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1.8 14-1.8-3.2-1.8 3.2H10l2.7-4.5L10.2 7h2.2l1.6 3 1.6-3h2.2l-2.5 4.5 2.7 4.5h-2.2z"/>
                    </svg>
                    <span>Export Excel (.xls)</span>
                </a>

                <!-- Export PDF -->
                <a href="{{ route('admin.tpa.hasil.export.pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Berita Acara (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Calon Siswa</p>
                    <p class="text-xl font-black text-slate-800">{{ $totalPeserta }}</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tuntas Mengerjakan</p>
                    <p class="text-xl font-black text-emerald-700">{{ $totalSelesai }}</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sedang Berlangsung</p>
                    <p class="text-xl font-black text-amber-700">{{ $totalSedang }}</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Didiskualifikasi</p>
                    <p class="text-xl font-black text-rose-700">{{ $totalDiskualifikasi }}</p>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <form action="{{ route('admin.tpa.hasil.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Nama, No. Daftar, NISN..."
                               class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Gelombang</label>
                    <select name="gelombang_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                        <option value="SEMUA">Semua Gelombang</option>
                        @foreach ($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jurusan</label>
                    <select name="jurusan_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                        <option value="SEMUA">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Pengerjaan</label>
                    <select name="status_ujian" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                        <option value="">Semua Status</option>
                        <option value="SELESAI" {{ request('status_ujian') === 'SELESAI' ? 'selected' : '' }}>Tuntas Selesai</option>
                        <option value="PROSES" {{ request('status_ujian') === 'PROSES' ? 'selected' : '' }}>Sedang Mengerjakan</option>
                        <option value="BELUM_MULAI" {{ request('status_ujian') === 'BELUM_MULAI' ? 'selected' : '' }}>Belum Mulai</option>
                        <option value="DIDISKUALIFIKASI" {{ request('status_ujian') === 'DIDISKUALIFIKASI' ? 'selected' : '' }}>Didiskualifikasi</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Tabel Rekap Nilai -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Calon Siswa</th>
                            <th class="py-3.5 px-4">Jurusan</th>
                            <th class="py-3.5 px-4 text-center">Skor Literasi</th>
                            <th class="py-3.5 px-4 text-center">Skor Numerasi</th>
                            <th class="py-3.5 px-4 text-center">Total Skor</th>
                            <th class="py-3.5 px-4 w-28 text-center">Status Ujian</th>
                            <th class="py-3.5 px-4 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse ($calonSiswaList as $index => $cs)
                            @php
                                $sesi = $cs->tpaSesi;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                    {{ $calonSiswaList->firstItem() + $index }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $cs->nama_lengkap }}</div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                        <span>No: {{ $cs->nomor_pendaftaran }}</span>
                                        <span>&bull;</span>
                                        <span>NISN: {{ $cs->nisn ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $cs->jurusan?->nama_jurusan ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $cs->gelombang?->nama_gelombang ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($sesi && in_array($sesi->status, ['transisi', 'babak_2', 'selesai', 'didiskualifikasi']))
                                        <div class="font-black text-slate-900 text-sm">{{ number_format($sesi->skor_literasi, 0) }}</div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">{{ $sesi->benar_literasi }} Benar &bull; {{ $sesi->salah_literasi }} Salah</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($sesi && in_array($sesi->status, ['selesai', 'didiskualifikasi']))
                                        <div class="font-black text-slate-900 text-sm">{{ number_format($sesi->skor_numerasi, 0) }}</div>
                                        <div class="text-[10px] text-indigo-600 font-semibold">{{ $sesi->benar_numerasi }} Benar &bull; {{ $sesi->salah_numerasi }} Salah</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($sesi && in_array($sesi->status, ['selesai', 'didiskualifikasi']))
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl bg-orange-50 border border-orange-200 text-orange-700 font-black text-sm">
                                            {{ number_format($sesi->total_skor, 0) }} Poin
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Belum Tuntas</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if (! $sesi || $sesi->status === 'belum_mulai')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Belum Mulai</span>
                                    @elseif ($sesi->status === 'babak_1')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">Babak 1</span>
                                    @elseif ($sesi->status === 'transisi')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700">Jeda Babak</span>
                                    @elseif ($sesi->status === 'babak_2')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700">Babak 2</span>
                                    @elseif ($sesi->status === 'selesai')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700">Tuntas</span>
                                    @elseif ($sesi->status === 'didiskualifikasi')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700" title="Pelanggaran {{ $sesi->jumlah_pelanggaran }}x">Diskualifikasi</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.tpa.hasil.show', $cs) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
                                       title="Lihat Lembar Jawaban Siswa">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada data pengerjaan TPA yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($calonSiswaList->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $calonSiswaList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
