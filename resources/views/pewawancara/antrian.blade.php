<x-layouts.app>
    <x-slot name="title">Antrian Wawancara</x-slot>

    <x-slot name="sidebar">
        @include('pewawancara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Antrian Calon Siswa</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar calon siswa yang siap diwawancara dan penilaian rubrik seleksi.</p>
            </div>
            <a href="{{ route('pewawancara.instrumen') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                <span>📋 Lihat Rubrik & Indikator</span>
            </a>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('pewawancara.antrian') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari nama, nomor pendaftaran, NISN, atau sekolah..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-3">
                    <select name="jurusan_id" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ $jurusanId == $j->id ? 'selected' : '' }}>
                                {{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="">Semua Status</option>
                        <option value="BELUM" {{ $statusWawancara === 'BELUM' ? 'selected' : '' }}>Belum Diuji</option>
                        <option value="PROSES" {{ $statusWawancara === 'PROSES' ? 'selected' : '' }}>Sedang Proses (Draft)</option>
                        <option value="SELESAI" {{ $statusWawancara === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if ($search || $jurusanId || $statusWawancara)
                        <a href="{{ route('pewawancara.antrian') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Jurusan / Program</th>
                            <th class="px-4 py-3.5">Asal Sekolah</th>
                            <th class="px-4 py-3.5">Status Wawancara</th>
                            <th class="px-4 py-3.5">Pewawancara</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($calonSiswaList as $cs)
                            @php
                                $w = $cs->wawancara->sortByDesc('id')->first();
                                $wStatus = $w ? $w->status : 'BELUM';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200/60 text-nampi-orange flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($cs->nama_lengkap, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $cs->nama_lengkap }}</p>
                                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $cs->nomor_pendaftaran }} • NISN: {{ $cs->nisn }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-cyan-50 text-cyan-800 border border-cyan-200 mb-1">
                                        1. {{ $cs->jurusan?->nama_jurusan ?? '-' }}
                                    </span>
                                    @if($cs->jurusan2)
                                        <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-50 text-slate-600 border border-slate-200 block mb-1">
                                            2. {{ $cs->jurusan2?->nama_jurusan ?? '-' }}
                                        </span>
                                    @endif
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $cs->programBelajar?->nama_program ?? '-' }}</p>
                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Beasiswa: {{ $cs->tag_beasiswa ?? 'Normal' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Jalur: {{ $cs->tag_jalur ?? 'Normal' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-slate-700 text-xs">{{ $cs->sekolah_asal_text }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($wStatus === 'SELESAI')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Selesai Diuji
                                        </span>
                                    @elseif ($wStatus === 'PROSES')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Draft / Proses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu Uji
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if ($w && $w->pewawancara)
                                        <p class="text-xs font-medium text-slate-700">{{ $w->pewawancara->name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $w->tanggal_wawancara?->format('d M Y') ?? '-' }}</p>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-whatsapp-contact-dropdown :calonSiswa="$cs" />

                                        @if ($wStatus === 'SELESAI')
                                            <a href="{{ route('pewawancara.wawancara.show', $cs) }}"
                                               class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors">
                                                Lihat Detail
                                            </a>
                                            <a href="{{ route('pewawancara.wawancara.hub', $cs) }}"
                                               class="px-2.5 py-1.5 rounded-lg border border-blue-200 text-blue-700 text-xs font-semibold hover:bg-blue-50 transition-colors">
                                                Edit
                                            </a>
                                        @elseif ($wStatus === 'PROSES')
                                            <a href="{{ route('pewawancara.wawancara.hub', $cs) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs">
                                                Lanjutkan Draft
                                            </a>
                                        @else
                                            <a href="{{ route('pewawancara.wawancara.hub', $cs) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                                                Mulai Wawancara →
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    <div class="max-w-sm mx-auto">
                                        <p class="text-base font-bold text-slate-600">Tidak ada calon siswa ditemukan</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter status antrian.</p>
                                    </div>
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
