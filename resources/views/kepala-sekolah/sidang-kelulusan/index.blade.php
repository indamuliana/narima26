<x-layouts.app>
    <x-slot name="title">Sidang Pleno Kelulusan SPMB</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Sidang Pleno Kelulusan SPMB</h1>
                <p class="text-xs text-slate-500 mt-1">Penetapan keputusan hasil seleksi penerimaan murid baru oleh Kepala Sekolah dan Komite Seleksi.</p>
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

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Sidang Pleno</span>
                        <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['menunggu_sidang']) }} Calon Siswa</p>
                    </div>
                    <span class="text-2xl">⏳</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Telah Dinyatakan Diterima</span>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['total_diterima']) }} Siswa</p>
                    </div>
                    <span class="text-2xl">🎓</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tidak Diterima / Ditolak</span>
                        <p class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['total_ditolak']) }} Siswa</p>
                    </div>
                    <span class="text-2xl">🚫</span>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('kepala-sekolah.sidang-kelulusan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari nama siswa, NISN, atau no pendaftaran..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-3">
                    <select name="jurusan_id" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="">Semua Pilihan Jurusan</option>
                        @foreach ($jurusanList as $jur)
                            <option value="{{ $jur['id'] }}" {{ $jurusanId == $jur['id'] ? 'selected' : '' }}>
                                {{ $jur['nama'] }} ({{ $jur['diterima'] }}/{{ $jur['kuota'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status_filter" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="SEMUA" {{ $statusFilter === 'SEMUA' ? 'selected' : '' }}>Semua Tahap</option>
                        <option value="MENUNGGU_SIDANG" {{ $statusFilter === 'MENUNGGU_SIDANG' ? 'selected' : '' }}>Menunggu Sidang</option>
                        <option value="DITERIMA" {{ $statusFilter === 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                        <option value="DITOLAK" {{ $statusFilter === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if ($search || $jurusanId || $statusFilter !== 'SEMUA')
                        <a href="{{ route('kepala-sekolah.sidang-kelulusan.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table of Candidates -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Kompetensi Keahlian</th>
                            <th class="px-4 py-3.5">Evaluasi Wawancara</th>
                            <th class="px-4 py-3.5 text-center">Status Keputusan</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($calonSiswaList as $siswa)
                            @php
                                $wawancara = $siswa->wawancaraTerakhir;
                                $keputusan = $siswa->keputusanKelulusan;
                                $statusVal = is_string($siswa->status_spmb) ? $siswa->status_spmb : $siswa->status_spmb->value;
                                $isDecided = in_array($statusVal, ['DITERIMA', 'DITOLAK', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $siswa->nama_lengkap }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $siswa->nomor_pendaftaran }} &bull; NISN: {{ $siswa->nisn }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-bold text-slate-800 block">{{ $siswa->jurusan?->nama_jurusan ?? '-' }}</span>
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains(strtolower($siswa->program?->nama_program ?? ''), 'unggul') ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ $siswa->program?->nama_program ?? 'Reguler' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">&bull; {{ $siswa->gelombang?->nama_gelombang ?? 'Gelombang 1' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($wawancara)
                                        @php
                                            $avgScore = $wawancara->details->count() > 0 ? round($wawancara->details->avg('skor')) : null;
                                        @endphp
                                        <div class="space-y-0.5">
                                            <span class="inline-flex items-center gap-1.5 font-bold text-xs {{ $avgScore >= 75 ? 'text-emerald-700' : ($avgScore >= 60 ? 'text-amber-700' : 'text-rose-700') }}">
                                                Skor Rata-rata: {{ $avgScore ?? '-' }}/100
                                            </span>
                                            <span class="text-[11px] text-slate-400 block">
                                                Penguji: {{ $wawancara->pewawancara?->name ?? 'Pewawancara' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum ada nilai wawancara</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if (in_array($statusVal, ['DITERIMA', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            DITERIMA
                                        </span>
                                    @elseif ($statusVal === 'DITOLAK')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            DITOLAK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu Sidang
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-whatsapp-contact-dropdown :calonSiswa="$siswa" />
                                        <a href="{{ route('kepala-sekolah.sidang-kelulusan.show', $siswa) }}"
                                           class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors">
                                            {{ $isDecided ? 'Tinjau Keputusan' : 'Evaluasi Sidang' }}
                                        </a>
                                        @if ($isDecided)
                                            <a href="{{ route('kepala-sekolah.sidang-kelulusan.cetak-sk', $siswa) }}" target="_blank"
                                               class="px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors" title="Unduh SK Kelulusan">
                                                Cetak SK
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada kandidat untuk disidangkan</p>
                                    <p class="text-xs text-slate-400 mt-1">Calon siswa yang telah selesai tes wawancara akan tampil di sini untuk penetapan keputusan.</p>
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
