<x-layouts.app>
    <x-slot name="title">Hub Wawancara: {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('pewawancara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Top Navigation & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('pewawancara.antrian') }}" class="hover:text-nampi-orange transition-colors">Antrian Wawancara</a>
                    <span>/</span>
                    <span class="text-slate-600">Hub Wawancara</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1">Pusat Penilaian Wawancara</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pilih instrumen wawancara calon siswa atau orang tua/wali untuk mengisi dan memperbarui penilaian.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('pewawancara.antrian') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Antrian</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Perhatian">{{ session('error') }}</x-alert>
        @endif

        <!-- Banner Profil Calon Siswa -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-lg shadow-sm shrink-0">
                    {{ strtoupper(substr($calonSiswa->nama_lengkap, 0, 2)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-black text-slate-900">{{ $calonSiswa->nama_lengkap }}</h2>
                        @if($calonSiswa->nama_panggilan)
                            <span class="text-xs text-slate-400 font-medium">({{ $calonSiswa->nama_panggilan }})</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                        <span class="font-mono font-bold text-slate-700">{{ $calonSiswa->nomor_pendaftaran }}</span>
                        <span>&bull;</span>
                        <span>NISN: <strong class="font-mono text-slate-700">{{ $calonSiswa->nisn }}</strong></span>
                        <span>&bull;</span>
                        <span>Asal Sekolah: <strong class="text-slate-700">{{ $calonSiswa->asalSekolah?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</strong></span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                            {{ $calonSiswa->programBelajar?->nama ?? 'Reguler' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                            {{ $calonSiswa->jurusan?->nama ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:items-end gap-1 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                <span class="text-xs font-bold text-slate-400">Status SPMB:</span>
                <span class="px-3 py-1 inline-flex text-xs font-black rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $calonSiswa->status_spmb instanceof \BackedEnum ? $calonSiswa->status_spmb->label() : $calonSiswa->status_spmb }}
                </span>
            </div>
        </div>

        <!-- Dua Instrumen Wawancara -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card 1: Wawancara Siswa -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-5 {{ $calonSiswa->wawancaraSiswa?->status === 'SELESAI' ? 'ring-2 ring-emerald-500/20' : '' }}">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold">
                                🎓
                            </span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Wawancara Calon Siswa</h3>
                                <p class="text-[11px] text-slate-400">Instrumen individu siswa</p>
                            </div>
                        </div>

                        @if($calonSiswa->wawancaraSiswa)
                            @if($calonSiswa->wawancaraSiswa->status === 'SELESAI')
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ SELESAI
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    ✍️ DRAFT
                                </span>
                            @endif
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                BELUM DIMULAI
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Instrumen penilaian wawancara langsung untuk calon murid. Meliputi observasi fisik (rambut, pakaian, pendengaran, penglihatan), hafalan Qur'an, minat bakat, dan motivasi jurusan.
                    </p>

                    @if($calonSiswa->wawancaraSiswa?->pewawancara)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Pewawancara:</span>
                                <span class="font-bold text-slate-700">{{ $calonSiswa->wawancaraSiswa->pewawancara->name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Rekomendasi:</span>
                                <span class="font-black {{ $calonSiswa->wawancaraSiswa->rekomendasi === 'TERIMA' ? 'text-emerald-600' : ($calonSiswa->wawancaraSiswa->rekomendasi === 'PERTIMBANGKAN' ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $calonSiswa->wawancaraSiswa->rekomendasi ?? '-' }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <a href="{{ route('pewawancara.wawancara.form-siswa', $calonSiswa) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-orange-500 hover:bg-orange-600 transition-colors shadow-xs">
                        <span>{{ $calonSiswa->wawancaraSiswa ? ($calonSiswa->wawancaraSiswa->status === 'SELESAI' ? 'Buka / Edit Penilaian' : 'Lanjutkan Draft Wawancara') : 'Mulai Wawancara Siswa' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Card 2: Wawancara Orang Tua -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-5 {{ $calonSiswa->wawancaraOrangTua?->status === 'SELESAI' ? 'ring-2 ring-emerald-500/20' : '' }}">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-700 flex items-center justify-center font-bold">
                                👨‍👩‍👧
                            </span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Wawancara Orang Tua / Wali</h3>
                                <p class="text-[11px] text-slate-400">Instrumen pendampingan orang tua</p>
                            </div>
                        </div>

                        @if($calonSiswa->wawancaraOrangTua)
                            @if($calonSiswa->wawancaraOrangTua->status === 'SELESAI')
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ SELESAI
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    ✍️ DRAFT
                                </span>
                            @endif
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                BELUM DIMULAI
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Instrumen wawancara untuk orang tua atau wali murid. Meliputi kesiapan pembiayaan pendidikan, komitmen tata tertib sekolah, dan riwayat bimbingan keluarga di rumah.
                    </p>

                    @if($calonSiswa->wawancaraOrangTua?->pewawancara)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Pewawancara:</span>
                                <span class="font-bold text-slate-700">{{ $calonSiswa->wawancaraOrangTua->pewawancara->name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Rekomendasi:</span>
                                <span class="font-black {{ $calonSiswa->wawancaraOrangTua->rekomendasi === 'TERIMA' ? 'text-emerald-600' : ($calonSiswa->wawancaraOrangTua->rekomendasi === 'PERTIMBANGKAN' ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $calonSiswa->wawancaraOrangTua->rekomendasi ?? '-' }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <a href="{{ route('pewawancara.wawancara.form-orang-tua', $calonSiswa) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-cyan-700 hover:bg-cyan-800 transition-colors shadow-xs">
                        <span>{{ $calonSiswa->wawancaraOrangTua ? ($calonSiswa->wawancaraOrangTua->status === 'SELESAI' ? 'Buka / Edit Penilaian' : 'Lanjutkan Draft Wawancara') : 'Mulai Wawancara Orang Tua' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

        </div>

        @if($calonSiswa->wawancaraSiswa?->status === 'SELESAI' && $calonSiswa->wawancaraOrangTua?->status === 'SELESAI')
            <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white flex items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-7 h-7 text-emerald-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <div class="font-bold text-sm">Seluruh Instrumen Wawancara Telah Selesai!</div>
                        <p class="text-xs text-emerald-100 mt-0.5">Penilaian wawancara calon siswa dan orang tua telah tuntas dan siap dibawa ke Sidang Pleno Kelulusan.</p>
                    </div>
                </div>
                <a href="{{ route('pewawancara.wawancara.show', $calonSiswa) }}" class="px-4 py-2 bg-white text-emerald-800 hover:bg-emerald-50 rounded-xl text-xs font-bold transition shadow-xs whitespace-nowrap">
                    Lihat Hasil Gabungan
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
