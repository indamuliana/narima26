<x-layouts.app>
    <x-slot name="title">Wawancara & Tes Potensi Akademik</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Notification Messages -->
        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Perhatian">{{ session('error') }}</x-alert>
        @endif

        <!-- Main Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-nampi-orange uppercase tracking-wider">Tahap 5 SPMB &bull; Wawancara & TPA</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-200">
                            {{ $calonSiswa->program?->nama ?? 'Reguler' }} - {{ $calonSiswa->jurusan?->nama ?? '-' }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">
                        Jadwal & Tahapan Wawancara
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        SMK Wikrama 1 Garut &bull; Informasi lengkap jadwal seleksi wawancara dan Tes Potensi Akademik (TPA).
                    </p>
                </div>

                {{-- Status Badge Header --}}
                <div>
                    @if(!$isPrerequisiteMet)
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-black bg-slate-100 text-slate-700 border border-slate-300 shadow-2xs">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Belum Memenuhi Syarat</span>
                        </span>
                    @elseif($isFinished)
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Wawancara Selesai</span>
                        </span>
                    @elseif($isScheduled)
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Sudah Dijadwalkan</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs animate-pulse">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Menunggu Penjadwalan</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- STATUS BANNER UTAMA SESUAI KONDISI --}}
        @if(!$isPrerequisiteMet)
            {{-- KONDISI 1: Belum Lengkap Data atau Belum Setuju Kesepahaman --}}
            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 shadow-xs space-y-4">
                <div class="flex items-start gap-3">
                    <div class="p-2.5 rounded-2xl bg-amber-100 text-amber-700 shrink-0 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h3 class="text-base font-bold text-amber-900">Tahap Wawancara Belum Dibuka</h3>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Wawancara baru dapat dijadwalkan dan dilaksanakan setelah seluruh berkas/data pendaftaran dinyatakan lengkap serta surat lembar kesepahaman (EULA) telah disetujui bersama orang tua/wali.
                        </p>
                    </div>
                </div>

                {{-- Checklist Prasyarat --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                    {{-- Item 1: Lengkapi Data --}}
                    <div class="p-4 rounded-2xl {{ $isDataComplete ? 'bg-white border border-emerald-300' : 'bg-white border border-amber-300' }} flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $isDataComplete ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                                {{ $isDataComplete ? '✓' : '1' }}
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">1. Kelengkapan Formulir & Berkas</h4>
                                <p class="text-[11px] {{ $isDataComplete ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">
                                    {{ $isDataComplete ? '✓ Data telah lengkap 100%' : 'Biodata/berkas belum lengkap' }}
                                </p>
                            </div>
                        </div>
                        @if(!$isDataComplete)
                            <a href="{{ route('calon-siswa.lengkapi-data.index') }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-nampi-orange text-white hover:bg-orange-600 transition shadow-xs whitespace-nowrap">
                                <span>Lengkapi &rarr;</span>
                            </a>
                        @endif
                    </div>

                    {{-- Item 2: Lembar Kesepahaman --}}
                    <div class="p-4 rounded-2xl {{ $isKesepahamanAgreed ? 'bg-white border border-emerald-300' : 'bg-white border border-amber-300' }} flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $isKesepahamanAgreed ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                                {{ $isKesepahamanAgreed ? '✓' : '2' }}
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">2. Lembar Kesepahaman (EULA)</h4>
                                <p class="text-[11px] {{ $isKesepahamanAgreed ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">
                                    {{ $isKesepahamanAgreed ? '✓ Telah disetujui' : 'Belum disetujui orang tua & murid' }}
                                </p>
                            </div>
                        </div>
                        @if(!$isKesepahamanAgreed)
                            <a href="{{ route('calon-siswa.kesepahaman.index') }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-600 text-white hover:bg-purple-700 transition shadow-xs whitespace-nowrap">
                                <span>Buka &rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        @elseif($isScheduled)
            {{-- KONDISI 2: Sudah Dijadwalkan oleh Admin --}}
            <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-emerald-50 via-teal-50 to-white border border-emerald-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-emerald-100 pb-5">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 mb-1">
                                Sesi Terjadwal
                            </span>
                            <h2 class="text-lg font-black text-slate-900">
                                Sudah Dijadwalkan
                            </h2>
                            <p class="text-xs text-slate-600">
                                Panitia SPMB telah menetapkan jadwal dan guru pewawancara untuk Anda.
                            </p>
                        </div>
                    </div>

                    {{-- WhatsApp Pewawancara CTA --}}
                    <div>
                        @if($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs font-black bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-600/25 transition transform active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Hubungi Via Whatsapp</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Detail Penjadwalan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Guru Pewawancara --}}
                    <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Guru Pewawancara</span>
                        <div class="flex items-center gap-2 pt-1">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($pewawancara?->name ?? 'G', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">{{ $pewawancara?->name ?? 'Pewawancara SPMB' }}</h3>
                                <p class="text-[11px] text-slate-500">{{ $pewawancara?->email ?? 'Panitia Seleksi Wikrama' }}</p>
                            </div>
                        </div>

                        @if(!$waUrl)
                            <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600">
                                ℹ️ Nomor kontak guru pewawancara belum terdaftar di sistem. Silakan hubungi Panitia SPMB Sekolah jika membutuhkan informasi jadwal.
                            </div>
                        @endif
                    </div>

                    {{-- Tanggal Wawancara --}}
                    <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-2xs space-y-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Pelaksanaan</span>
                        <div class="flex items-center gap-2 pt-1">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">
                                    {{ $tanggalWawancaraFormatted ?? 'Sesuai Jadwal Ditentukan' }}
                                </h3>
                                <p class="text-[11px] text-slate-500">
                                    Lokasi: Kampus SMK Wikrama 1 Garut
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- KONDISI 3: Data Lengkap & Kesepahaman Sudah Diisi, Menunggu Penjadwalan oleh Admin --}}
            <div class="p-6 sm:p-8 rounded-3xl bg-amber-50/70 border border-amber-300 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                            <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200 mb-1">
                                Antrean Seleksi
                            </span>
                            <h2 class="text-lg font-black text-slate-900">
                                Menunggu Penjadwalan
                            </h2>
                            <p class="text-xs text-slate-600">
                                Seluruh berkas pendaftaran dan lembar kesepahaman telah lengkap diterima oleh sistem.
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                            Sedang Diproses Admin SPMB
                        </span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white/90 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                    <p>
                        <strong>Informasi:</strong> Saat ini Panitia SPMB SMK Wikrama 1 Garut sedang mengalokasikan guru pewawancara dan waktu jadwal wawancara untuk Ananda. Harap pantau halaman ini atau WhatsApp secara berkala. Segera setelah dijadwalkan, detail guru pewawancara dan tanggal ujian akan otomatis tampil di sini.
                    </p>
                </div>
            </div>
        @endif

        {{-- TAHAPAN WAWANCARA --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-5">
            <div>
                <span class="text-xs font-bold text-nampi-orange uppercase tracking-wider">Alur Pelaksanaan</span>
                <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                    Tahapan Wawancara & Seleksi
                </h3>
                <p class="text-xs text-slate-500">
                    Berikut 3 tahapan yang akan dilalui oleh calon murid dan orang tua saat pelaksanaan di SMK Wikrama 1 Garut:
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Tahap 1: TPA --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-300 hover:bg-blue-50/40 transition flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                                1
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                Pemetaan Bakat
                            </span>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">
                            Test Potensi Akademik (TPA)
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Bukan seleksi kelulusan, data siswa akan digunakan oleh sekolah untuk menyusun kurikulum sesuai minat dan bakat murid.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-200/80">
                        <a href="{{ route('tpa.login') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                            <span>Buka Portal Ujian TPA (/tpa-siswa)</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Tahap 2: Wawancara Orang Tua --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-purple-300 hover:bg-purple-50/40 transition flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-7 h-7 rounded-full bg-purple-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                                2
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">
                                Sesi Orang Tua
                            </span>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">
                            Wawancara Orang Tua
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dialog dan konfirmasi komitmen pendampingan belajar, penyesuaian pembiayaan, serta kesepahaman tata tertib bersama orang tua/wali murid di sekolah.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-200/80">
                        <span class="text-[11px] text-purple-700 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Didampingi orang tua / wali
                        </span>
                    </div>
                </div>

                {{-- Tahap 3: Wawancara Siswa --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/40 transition flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                                3
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                                Sesi Murid
                            </span>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">
                            Wawancara Siswa
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Sesi wawancara langsung calon murid bersama guru pewawancara untuk menggali cita-cita siswa, motivasi belajar, kedisiplinan, serta kesiapan pemilihan jurusan.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-200/80">
                        <span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Calon murid berdialog langsung
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO BERKAS YANG HARUS DIBAWA & PORTAL TPA --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Berkas yang harus dibawa --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-nampi-orange flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900">Berkas yang Harus Dibawa</h4>
                        <p class="text-[11px] text-slate-500">Persyaratan dokumen saat hadir wawancara ke sekolah</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    {{-- Wajib --}}
                    <div class="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-purple-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                Lembar Kesepahaman (Wajib Dibawa)
                            </span>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-purple-200 text-purple-800">
                                WAJIB
                            </span>
                        </div>
                        <p class="text-[11px] text-purple-800 leading-relaxed">
                            Cetak / printout Surat Pernyataan Kesepahaman Bersama yang telah disetujui secara online di portal SPMB.
                        </p>
                        @if($isKesepahamanAgreed)
                            <div class="pt-1">
                                <a href="{{ route('calon-siswa.kesepahaman.cetak') }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-600 text-white hover:bg-purple-700 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Unduh Surat Kesepahaman (PDF)</span>
                                </a>
                            </div>
                        @else
                            <p class="text-[11px] text-amber-700 font-medium">
                                * Silakan setujui terlebih dahulu di menu Kesepahaman SPMB.
                            </p>
                        @endif
                    </div>

                    {{-- Opsional --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Berkas Pendukung Lainnya (Opsional)
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                TIDAK WAJIB
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Berkas pendukung lainnya (seperti fotokopi rapor, sertifikat piagam prestasi, atau dokumen pelengkap lainnya) <strong>boleh dibawa tapi tidak wajib</strong>.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Info Halaman TPA --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-900">Portal Ujian TPA Calon Siswa</h4>
                            <p class="text-[11px] text-slate-500">Tes daring berbasis komputer / gadget</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 text-xs text-blue-900 space-y-2">
                        <p class="leading-relaxed">
                            Tes Potensi Akademik (TPA) dilakukan secara digital melalui tautan khusus di portal sekolah:
                        </p>
                        <div class="p-2.5 rounded-xl bg-white border border-blue-200 font-mono text-[11px] text-blue-800 break-all select-all">
                            {{ url('/tpa-siswa') }}
                        </div>
                        <p class="text-[11px] text-blue-700">
                            Masuk menggunakan <strong>Nomor Pendaftaran</strong> dan password yang sama dengan akun portal ini.
                        </p>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('tpa.login') }}" target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-xs transition">
                        <span>Buka Halaman TPA (/tpa-siswa)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
