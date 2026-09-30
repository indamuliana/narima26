<x-layouts.app>
    <x-slot name="title">Direktori Data Calon Murid - Eksekutif Kepala Sekolah</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Direktori Data Calon Murid</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Akses komprehensif data 360° seluruh pendaftar, hasil wawancara, verifikasi bendahara, dan berkas dokumen.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('kepala-sekolah.sidang-kelulusan.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Buka Sidang Pleno</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Pendaftar</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-1 block">{{ number_format($stats['total']) }}</span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Seluruh akun terdaftar</span>
            </div>
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                <span class="text-emerald-600 text-xs font-bold uppercase tracking-wider block">Lulus / Diterima</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-700 font-mono mt-1 block">{{ number_format($stats['lulus_seleksi']) }}</span>
                <span class="text-[11px] text-emerald-600/80 mt-0.5 block">Dinyatakan lulus seleksi</span>
            </div>
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                <span class="text-blue-600 text-xs font-bold uppercase tracking-wider block">Sudah Wawancara</span>
                <span class="text-2xl sm:text-3xl font-black text-blue-700 font-mono mt-1 block">{{ number_format($stats['sudah_wawancara']) }}</span>
                <span class="text-[11px] text-blue-600/80 mt-0.5 block">Siap disidangkan</span>
            </div>
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                <span class="text-teal-600 text-xs font-bold uppercase tracking-wider block">Daftar Ulang Lunas</span>
                <span class="text-2xl sm:text-3xl font-black text-teal-700 font-mono mt-1 block">{{ number_format($stats['daftar_ulang_lunas']) }}</span>
                <span class="text-[11px] text-teal-600/80 mt-0.5 block">Resmi terdaftar</span>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
            <form method="GET" action="{{ route('kepala-sekolah.calon-siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Cari nama, No. pendaftaran, NISN..."
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kompetensi Keahlian</label>
                    <select name="jurusan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ $jurusanId == $j->id ? 'selected' : '' }}>
                                {{ $j->nama_jurusan ?? $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status SPMB</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $st)
                            <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition cursor-pointer">
                        Filter
                    </button>
                    @if ($search || $status || $jurusanId || $gelombangId)
                        <a href="{{ route('kepala-sekolah.calon-siswa.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition" title="Reset filter">
                            ↺
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Candidate Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Calon Murid</th>
                            <th class="py-3 px-4">Jurusan & Program</th>
                            <th class="py-3 px-4 text-center">Biaya Seleksi</th>
                            <th class="py-3 px-4 text-center">Wawancara</th>
                            <th class="py-3 px-4 text-center">Status SPMB</th>
                            <th class="py-3 px-4 text-center">Keputusan Sidang</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($calonSiswaList as $cs)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $foto = $cs->dokumenPendaftaran?->pas_foto_path;
                                            $hasFoto = $foto && file_exists(public_path('storage/' . $foto));
                                        @endphp
                                        @if ($hasFoto)
                                            <img src="{{ asset('storage/' . $foto) }}" class="w-9 h-11 object-cover rounded-lg border border-slate-200 shadow-2xs shrink-0" alt="Foto">
                                        @else
                                            <div class="w-9 h-11 rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($cs->nama_lengkap, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('kepala-sekolah.calon-siswa.show', $cs) }}" class="font-bold text-slate-900 hover:text-nampi-orange transition block">
                                                {{ $cs->nama_lengkap }}
                                            </a>
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-mono mt-0.5">
                                                <span>{{ $cs->nomor_pendaftaran }}</span>
                                                <span>&bull;</span>
                                                <span>NISN: {{ $cs->nisn }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $cs->jurusan_pilihan_text }}</span>
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains(strtolower($cs->program?->nama ?? $cs->program?->nama_program ?? ''), 'unggul') ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ $cs->program?->nama ?? $cs->program?->nama_program ?? 'Reguler' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">&bull; {{ $cs->gelombang?->nama ?? $cs->gelombang?->nama_gelombang ?? 'Gelombang 1' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $bayarSeleksi = $cs->pembayaranSeleksi;
                                    @endphp
                                    @if ($bayarSeleksi && ($bayarSeleksi->isDiverifikasi() || $bayarSeleksi->status === 'DIVERIFIKASI'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Lunas
                                        </span>
                                    @elseif ($bayarSeleksi && $bayarSeleksi->status === 'PENDING')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            ⏳ Verifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $wSiswaDone = $cs->wawancaraSiswa?->status === 'SELESAI';
                                        $wOrtuDone = $cs->wawancaraOrangTua?->status === 'SELESAI';
                                    @endphp
                                    @if ($wSiswaDone && $wOrtuDone)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Siswa & Ortu Selesai">
                                            ✓ Lengkap
                                        </span>
                                    @elseif ($wSiswaDone || $wOrtuDone)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800" title="Sebagian selesai">
                                            Parsial
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                                        $statusEnum = is_string($cs->status_spmb) ? SpmbStatus::tryFrom($cs->status_spmb) : $cs->status_spmb;
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                        {{ in_array($statusStr, ['DITERIMA', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']) ? 'bg-emerald-100 text-emerald-800' :
                                           (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-100 text-rose-800' :
                                           (in_array($statusStr, ['SUDAH_DIWAWANCARA', 'MENUNGGU_KEPUTUSAN', 'MENUNGGU_DAFTAR_ULANG']) ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700')) }}">
                                        {{ $statusEnum?->label() ?? str_replace('_', ' ', $statusStr) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($cs->keputusanKelulusan)
                                        @if ($cs->keputusanKelulusan->isDiterima())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ✓ LULUS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                                ✕ DITOLAK
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[11px] text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-whatsapp-contact-dropdown :calonSiswa="$cs" />
                                        <a href="{{ route('kepala-sekolah.calon-siswa.show', $cs) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                            <span>Detail 360°</span>
                                        </a>
                                        <a href="{{ route('kepala-sekolah.sidang-kelulusan.show', $cs) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[11px] font-bold transition"
                                           title="Buka Ruang Sidang & Evaluasi">
                                            <span>Sidang</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="font-medium text-xs">Tidak ada data calon murid yang sesuai dengan filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($calonSiswaList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $calonSiswaList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
