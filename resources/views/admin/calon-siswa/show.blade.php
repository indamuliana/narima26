<x-layouts.app>
    <x-slot name="title">Profil Lengkap Calon Siswa — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @if(auth()->user()->isKepalaSekolah())
            @include('kepala-sekolah.partials.sidebar')
        @else
            @include('admin.partials.sidebar')
        @endif
    </x-slot>

    <div class="space-y-6 print:space-y-4">
        <!-- Top Action Bar (Hidden on Print) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
            @if(auth()->user()->isKepalaSekolah())
                <a href="{{ route('kepala-sekolah.calon-siswa.index') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Direktori Calon Siswa</span>
                </a>
            @else
                <a href="{{ route('admin.calon-siswa.index') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Direktori Calon Siswa</span>
                </a>
            @endif
            <div class="flex flex-wrap items-center gap-2.5">
                @if(auth()->user()->isKepalaSekolah())
                    <a href="{{ route('kepala-sekolah.sidang-kelulusan.show', $calonSiswa) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Ruang Sidang Pleno</span>
                    </a>
                @endif
                <a href="{{ auth()->user()->isKepalaSekolah() ? route('kepala-sekolah.calon-siswa.cetak-pdf', $calonSiswa) : route('admin.calon-siswa.cetak-pdf', $calonSiswa) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-700 text-white text-xs font-bold hover:from-red-700 hover:to-rose-800 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Cetak Profil Lengkap (PDF)</span>
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Print Halaman</span>
                </button>
                @if ($calonSiswa->keputusanKelulusan)
                    <a href="{{ route('kepala-sekolah.sidang-kelulusan.cetak-sk', $calonSiswa) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-2xs">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Unduh SK Kelulusan</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Primary Candidate Identity Banner -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6 print:border-none print:shadow-none print:p-0">
            <div class="flex items-start sm:items-center gap-5">
                @php
                    $pasFoto = $calonSiswa->dokumenPendaftaran?->pas_foto_path;
                    $fotoExists = $pasFoto && file_exists(public_path('storage/' . $pasFoto));
                @endphp
                @if ($fotoExists)
                    <img src="{{ asset('storage/' . $pasFoto) }}" alt="Pas Foto Siswa"
                         class="w-20 h-24 object-cover rounded-2xl border-2 border-white shadow-md ring-2 ring-slate-200 shrink-0">
                @else
                    <div class="w-20 h-24 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-950 text-white flex flex-col items-center justify-center font-black shadow-md shrink-0">
                        <span class="text-2xl">{{ strtoupper(substr($calonSiswa->nama_lengkap, 0, 2)) }}</span>
                        <span class="text-[9px] font-mono text-slate-400 mt-1">NO FOTO</span>
                    </div>
                @endif
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $calonSiswa->nomor_pendaftaran }}
                        </span>
                        <span class="px-2 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700">
                            NISN: {{ $calonSiswa->nisn }}
                        </span>
                        <span class="px-2 py-0.5 rounded-lg text-xs font-bold {{ $calonSiswa->jenis_kelamin === 'L' || $calonSiswa->jenis_kelamin === 'Laki-laki' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                            {{ $calonSiswa->jenis_kelamin === 'L' || $calonSiswa->jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 mt-1.5">{{ $calonSiswa->nama_lengkap }}</h1>
                    @if($calonSiswa->nama_panggilan)
                        <p class="text-xs text-slate-400 font-medium">Nama Panggilan: <span class="text-slate-600 font-semibold">{{ $calonSiswa->nama_panggilan }}</span></p>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-600">
                        <span class="font-bold text-slate-800">{{ $calonSiswa->program?->nama ?? $calonSiswa->program?->nama_program ?? '-' }}</span>
                        <span>&bull;</span>
                        <span class="font-bold text-slate-800">{{ $calonSiswa->jurusan?->nama ?? $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>{{ $calonSiswa->gelombang?->nama ?? $calonSiswa->gelombang?->nama_gelombang ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>SMP: <strong>{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Status Indicator & Quick Contact -->
            <div class="flex flex-col md:items-end gap-2.5 shrink-0">
                @php
                    $statusStr = is_string($calonSiswa->status_spmb) ? $calonSiswa->status_spmb : ($calonSiswa->status_spmb?->value ?? '-');
                    $hpSiswaRaw = preg_replace('/[^0-9]/', '', $calonSiswa->no_hp_siswa ?? '');
                    $hpSiswaWa = $hpSiswaRaw ? preg_replace('/^0/', '62', $hpSiswaRaw) : null;
                    $hpAyahRaw = preg_replace('/[^0-9]/', '', $calonSiswa->no_hp_ayah ?: ($calonSiswa->dataOrangtua?->no_hp_ayah ?? ''));
                    $hpAyahWa = $hpAyahRaw ? preg_replace('/^0/', '62', $hpAyahRaw) : null;
                    $hpIbuRaw = preg_replace('/[^0-9]/', '', $calonSiswa->no_hp_ibu ?: ($calonSiswa->dataOrangtua?->no_hp_ibu ?? ''));
                    $hpIbuWa = $hpIbuRaw ? preg_replace('/^0/', '62', $hpIbuRaw) : null;
                @endphp
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-black
                        {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR', 'DAFTAR_ULANG_DIVERIFIKASI']) ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' :
                           (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-50 text-rose-800 border border-rose-300' : 'bg-amber-50 text-amber-800 border border-amber-300') }}">
                        <span class="w-2.5 h-2.5 rounded-full {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR', 'DAFTAR_ULANG_DIVERIFIKASI']) ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                        STATUS: {{ str_replace('_', ' ', $statusStr) }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 print:hidden">
                    @if($hpSiswaWa)
                        <a href="https://wa.me/{{ $hpSiswaWa }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-xs transition-colors"
                           title="Kirim pesan WhatsApp ke Calon Siswa ({{ $calonSiswa->no_hp_siswa }})">
                            <span>💬 Chat Siswa</span>
                        </a>
                    @endif
                    @if($hpAyahWa)
                        <a href="https://wa.me/{{ $hpAyahWa }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-bold shadow-xs transition-colors"
                           title="Kirim pesan WhatsApp ke Ayah ({{ $calonSiswa->no_hp_ayah ?: $calonSiswa->dataOrangtua?->no_hp_ayah }})">
                            <span>👨 Chat Ayah</span>
                        </a>
                    @endif
                    @if($hpIbuWa)
                        <a href="https://wa.me/{{ $hpIbuWa }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-bold shadow-xs transition-colors"
                           title="Kirim pesan WhatsApp ke Ibu ({{ $calonSiswa->no_hp_ibu ?: $calonSiswa->dataOrangtua?->no_hp_ibu }})">
                            <span>👩 Chat Ibu</span>
                        </a>
                    @endif
                </div>
                <span class="text-[11px] text-slate-400 font-mono">
                    Registrasi: {{ $calonSiswa->created_at ? $calonSiswa->created_at->translatedFormat('d M Y H:i') : '-' }}
                </span>
            </div>
        </div>

        <!-- Sticky Quick Navigation Tab Bar (Hidden on Print) -->
        <div class="sticky top-2 z-30 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-slate-200/90 shadow-sm print:hidden">
            <div class="flex items-center gap-1.5 overflow-x-auto text-[11px] font-bold text-slate-600 no-scrollbar">
                <span class="text-slate-400 text-xs shrink-0 mr-1 font-semibold">Lompat ke:</span>
                <a href="#sec-registrasi" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">1. Registrasi</a>
                <a href="#sec-biodata" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">2. Biodata</a>
                <a href="#sec-kesehatan" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition-colors shrink-0 font-black">3. Kesehatan & Fisik</a>
                <a href="#sec-orangtua" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">4. Orang Tua</a>
                <a href="#sec-rapor" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">5. Nilai Rapor</a>
                <a href="#sec-seragam" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">6. Seragam</a>
                <a href="#sec-eula" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">7. EULA</a>
                <a href="#sec-berkas" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">8. Berkas</a>
                <a href="#sec-seleksi" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">9. Biaya Seleksi</a>
                <a href="#sec-wawancara" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">10. Wawancara</a>
                <a href="#sec-keuangan" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">11. Keuangan</a>
                <a href="#sec-kelulusan" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">12. Kelulusan</a>
                <a href="#sec-riwayat" class="px-2.5 py-1 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition-colors shrink-0">13. Riwayat</a>
            </div>
        </div>

        <!-- 2 Columns Grid for Comprehensive 360-degree Data -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN (7 COLS): Registrasi, Biodata, Orang Tua, Nilai Rapor, Seragam, Dokumen -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. DATA REGISTRASI & AKUN -->
                <div id="sec-registrasi" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-sm">1</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Identitas Registrasi & Pilihan</h2>
                                <p class="text-[11px] text-slate-400">Data pendaftaran awal dan referensi promotor siswa</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600">
                            Reg ID: #{{ $calonSiswa->id }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Nomor Registrasi / Pendaftaran</span>
                            <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $calonSiswa->nomor_pendaftaran }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Nomor Induk Siswa Nasional (NISN)</span>
                            <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $calonSiswa->nisn }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Kompetensi Keahlian (Jurusan)</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->jurusan?->nama ?? $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</span>
                            <span class="text-[11px] text-slate-400">Kode: {{ $calonSiswa->jurusan?->kode ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Program Belajar</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->program?->nama ?? $calonSiswa->program?->nama_program ?? '-' }}</span>
                            <span class="text-[11px] text-slate-400">Kode: {{ $calonSiswa->program?->kode ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Gelombang Pendaftaran</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->gelombang?->nama ?? $calonSiswa->gelombang?->nama_gelombang ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Kelengkapan Data</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $calonSiswa->status_data ?? 'LENGKAP' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Akun Pengguna (Login Siswa)</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">
                                {{ $calonSiswa->user?->email ?? $calonSiswa->email ?? '-' }}
                                @if($calonSiswa->user?->is_active)
                                    <span class="text-[10px] text-emerald-600 font-bold ml-1">● Aktif</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Username Login</span>
                            <span class="font-mono font-bold text-slate-700 mt-0.5 block">
                                {{ $calonSiswa->user?->username ?? '-' }}
                            </span>
                        </div>
                        @if ($calonSiswa->referensi_jenis)
                            <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                                <span class="text-slate-400 block font-medium">Referensi / Promotor</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase mr-1">
                                        {{ str_replace('_', ' ', $calonSiswa->referensi_jenis) }}
                                    </span>
                                    {{ $calonSiswa->referensi_nama }}
                                    @if ($calonSiswa->referensi_rayon) &bull; Rayon: <strong>{{ $calonSiswa->referensi_rayon }}</strong> @endif
                                    @if ($calonSiswa->referensi_nomor_seleksi) &bull; No: <strong>{{ $calonSiswa->referensi_nomor_seleksi }}</strong> @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. BIODATA PRIBADI -->
                <div id="sec-biodata" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">2</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Biodata Pribadi Calon Siswa</h2>
                            <p class="text-[11px] text-slate-400">Identitas kependudukan, kontak aktif, dan alamat domisili</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Tempat, Tanggal Lahir</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">
                                {{ $calonSiswa->tempat_lahir ?? '-' }}, {{ $calonSiswa->tanggal_lahir ? $calonSiswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Agama</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->agama ?? 'Islam' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Susunan Keluarga</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">
                                Anak ke- <strong class="text-blue-700 font-mono">{{ $calonSiswa->anak_ke ?? '-' }}</strong> dari <strong class="text-blue-700 font-mono">{{ $calonSiswa->jumlah_saudara ?? '-' }}</strong> bersaudara
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Tahun Lulus SMP/MTs</span>
                            <span class="font-mono font-bold text-slate-800 mt-0.5 block">
                                {{ $calonSiswa->tahun_lulus ? 'Tahun ' . $calonSiswa->tahun_lulus : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">NIK (No. KTP Calon Siswa)</span>
                            <span class="font-mono font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->nik ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Nomor Kartu Keluarga (KK)</span>
                            <span class="font-mono font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->no_kk ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">No. HP / WhatsApp Siswa</span>
                            <span class="font-mono font-bold text-slate-800 mt-0.5 block">
                                @if($calonSiswa->no_hp_siswa)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $calonSiswa->no_hp_siswa)) }}" target="_blank" class="text-emerald-600 hover:underline">
                                        📱 {{ $calonSiswa->no_hp_siswa }}
                                    </a>
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Alamat Email</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->email ?? $calonSiswa->user?->email ?? '-' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 block font-medium">Alamat Domisili Lengkap</span>
                                @if($calonSiswa->is_luar_negeri)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200">
                                        🌐 LUAR NEGERI
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        🇮🇩 DALAM NEGERI
                                    </span>
                                @endif
                            </div>
                            <div class="mt-1 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium leading-relaxed">
                                @if($calonSiswa->is_luar_negeri)
                                    <div>{{ $calonSiswa->alamat_lengkap ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        @if($calonSiswa->desa_luar_negeri) {{ $calonSiswa->desa_luar_negeri }} &bull; @endif
                                        @if($calonSiswa->kecamatan_luar_negeri) Distrik: <strong>{{ $calonSiswa->kecamatan_luar_negeri }}</strong> &bull; @endif
                                        Kota: <strong>{{ $calonSiswa->kabupaten_luar_negeri ?? '-' }}</strong> &bull;
                                        Prov/State: <strong>{{ $calonSiswa->provinsi_luar_negeri ?? '-' }}</strong> &bull;
                                        Negara: <strong>{{ $calonSiswa->negara ?? '-' }}</strong>
                                        @if($calonSiswa->kode_pos) &bull; Kode Pos: <strong>{{ $calonSiswa->kode_pos }}</strong> @endif
                                    </div>
                                @else
                                    @php
                                        $desaText = $calonSiswa->nama_desa ?: ($calonSiswa->desa?->nama ?? $calonSiswa->desa_nama);
                                        $kecText = $calonSiswa->nama_kecamatan ?: ($calonSiswa->kecamatan?->nama ?? $calonSiswa->kecamatan_nama);
                                        $kabText = $calonSiswa->nama_kabupaten ?: ($calonSiswa->kabupaten?->nama ?? $calonSiswa->kabupaten_nama);
                                        $provText = $calonSiswa->nama_provinsi ?: ($calonSiswa->provinsi?->nama ?? $calonSiswa->provinsi_nama);
                                    @endphp
                                    {{ $calonSiswa->alamat_lengkap ?? '-' }}
                                    @if ($calonSiswa->rt || $calonSiswa->rw)
                                        (RT {{ $calonSiswa->rt ?? '0' }} / RW {{ $calonSiswa->rw ?? '0' }})
                                    @endif
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        @if($desaText) Desa/Kel. <strong>{{ $desaText }}</strong> &bull; @endif
                                        @if($kecText) Kec. <strong>{{ $kecText }}</strong> &bull; @endif
                                        @if($kabText) Kab/Kota <strong>{{ $kabText }}</strong> &bull; @endif
                                        @if($provText) Prov. <strong>{{ $provText }}</strong> @endif
                                        @if($calonSiswa->kode_pos) &bull; Kode Pos: <strong>{{ $calonSiswa->kode_pos }}</strong> @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 block font-medium">Asal Sekolah SMP / MTs</span>
                            <div class="mt-1 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block text-sm">
                                        {{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        @if($calonSiswa->sekolahAsal)
                                            NPSN: {{ $calonSiswa->sekolahAsal->npsn ?? '-' }} &bull; Wilayah: {{ $calonSiswa->sekolahAsal->kabupaten ?? 'Garut' }}
                                        @else
                                            Sekolah Tidak Terdaftar di Master (Pilihan Mandiri / Luar Daerah)
                                        @endif
                                        @if($calonSiswa->tahun_lulus)
                                            &bull; Tahun Kelulusan: <strong>{{ $calonSiswa->tahun_lulus }}</strong>
                                        @endif
                                    </span>
                                </div>
                                @if($calonSiswa->sekolahAsal)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ SMP Terdata
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        ✎ Input Mandiri
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. DATA KESEHATAN & FISIK -->
                <div id="sec-kesehatan" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm">3</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Data Kesehatan & Fisik Siswa</h2>
                                <p class="text-[11px] text-slate-400">Pemeriksaan fisik antropometri, riwayat medis, dan kondisi penglihatan</p>
                            </div>
                        </div>
                        @php
                            $kes = $calonSiswa->dataKesehatan;
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $kes ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $kes ? '✓ Terdata' : 'Belum Diisi' }}
                        </span>
                    </div>

                    @if ($kes)
                        @php
                            $tb = (float) ($kes->tinggi_badan ?? 0);
                            $bb = (float) ($kes->berat_badan ?? 0);
                            $bmi = ($tb > 0 && $bb > 0) ? round($bb / (($tb / 100) ** 2), 1) : null;
                            $bmiLabel = '-';
                            $bmiBadge = 'bg-slate-100 text-slate-600 border-slate-200';
                            if ($bmi) {
                                if ($bmi < 18.5) {
                                    $bmiLabel = 'Kurus / Berat Kurang';
                                    $bmiBadge = 'bg-amber-50 text-amber-800 border-amber-200';
                                } elseif ($bmi <= 22.9) {
                                    $bmiLabel = 'Normal / Ideal';
                                    $bmiBadge = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                } elseif ($bmi <= 24.9) {
                                    $bmiLabel = 'Kelebihan Berat Badan';
                                    $bmiBadge = 'bg-orange-50 text-orange-800 border-orange-200';
                                } else {
                                    $bmiLabel = 'Obesitas';
                                    $bmiBadge = 'bg-rose-50 text-rose-800 border-rose-200';
                                }
                            }
                        @endphp

                        <!-- 3 Kartu Metrik Fisik -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-2xl bg-teal-50/60 border border-teal-100 text-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-600 block">Tinggi Badan</span>
                                <span class="text-xl font-black text-teal-900 mt-0.5 block font-mono">
                                    {{ $tb > 0 ? $tb . ' cm' : '-' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cyan-50/60 border border-cyan-100 text-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 block">Berat Badan</span>
                                <span class="text-xl font-black text-cyan-900 mt-0.5 block font-mono">
                                    {{ $bb > 0 ? $bb . ' kg' : '-' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Indeks Massa Tubuh</span>
                                <span class="text-xl font-black text-slate-900 mt-0.5 block font-mono">
                                    {{ $bmi ?? '-' }}
                                </span>
                                @if($bmi)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[9px] font-bold border {{ $bmiBadge }}">
                                        {{ $bmiLabel }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Data Klinis & Riwayat -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 block text-[11px] font-medium">Golongan Darah</span>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">🩸</span>
                                    <span class="font-bold text-slate-900 text-sm">{{ $kes->golongan_darah ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 block text-[11px] font-medium">Status Buta Warna</span>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <span class="w-6 h-6 rounded-lg {{ str_contains(strtolower($kes->buta_warna ?? ''), 'tidak') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center font-bold text-xs">👁</span>
                                    <span class="font-bold {{ str_contains(strtolower($kes->buta_warna ?? ''), 'tidak') ? 'text-emerald-800' : 'text-amber-800' }} text-xs">
                                        {{ $kes->buta_warna ?: '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 block text-[11px] font-medium">Kesehatan Mata</span>
                                <span class="font-bold text-slate-800 mt-1 block capitalize">
                                    {{ $kes->kesehatan_mata ?: 'Normal' }}
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 block text-[11px] font-medium">Jenis Alergi yang Diderita</span>
                                <span class="font-bold text-slate-800 mt-1 block">
                                    {{ $kes->jenis_alergi ?: 'Tidak ada alergi tercatat' }}
                                </span>
                            </div>
                            <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                <div>
                                    <span class="text-slate-400 block text-[11px] font-medium">Penyakit Berat yang Pernah Diderita:</span>
                                    <span class="font-bold text-slate-800 mt-0.5 block">
                                        {{ $kes->penyakit_pernah_diderita ?: 'Tidak ada' }}
                                        @if($kes->penyakit_pernah_diderita_lainnya)
                                            <span class="font-normal text-slate-600">({{ $kes->penyakit_pernah_diderita_lainnya }})</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-slate-400 block text-[11px] font-medium">Penyakit Berat yang Sedang Diderita:</span>
                                    <span class="font-bold text-slate-800 mt-0.5 block">
                                        {{ $kes->penyakit_sedang_diderita ?: 'Tidak ada' }}
                                        @if($kes->penyakit_sedang_diderita_lainnya)
                                            <span class="font-normal text-slate-600">({{ $kes->penyakit_sedang_diderita_lainnya }})</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 text-center text-xs text-amber-800">
                            <p class="font-bold">Calon siswa belum melengkapi data kesehatan.</p>
                            <p class="text-[11px] text-amber-600 mt-0.5">Data antropometri dan riwayat medis akan muncul setelah calon siswa mengisi formulir kesehatan.</p>
                        </div>
                    @endif
                </div>

                <!-- 4. DATA ORANG TUA / WALI -->
                <div id="sec-orangtua" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">4</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Data Orang Tua & Wali</h2>
                            <p class="text-[11px] text-slate-400">Identitas ayah, ibu kandung, wali murid dan penghasilan keluarga</p>
                        </div>
                    </div>

                    @php $ortu = $calonSiswa->dataOrangtua; @endphp
                    @if ($ortu)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <!-- Data Ayah -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                                    <span class="font-black text-slate-800">DATA AYAH KANDUNG</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                                        {{ $ortu->status_ayah ?? 'Hidup' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Nama Lengkap Ayah:</span>
                                    <span class="font-bold text-slate-800 text-sm">{{ $ortu->nama_ayah ?? '-' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-[11px]">
                                    <div>
                                        <span class="text-slate-400 block">NIK:</span>
                                        <span class="font-mono font-semibold text-slate-700">{{ $ortu->nik_ayah ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Tahun Lahir:</span>
                                        <span class="font-semibold text-slate-700">{{ $ortu->tahun_lahir_ayah ?? '-' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Pendidikan & Pekerjaan:</span>
                                    <span class="font-medium text-slate-700">
                                        {{ $ortu->pendidikan_ayah ?? '-' }} &bull; {{ $ortu->pekerjaanAyah?->nama ?? $ortu->pekerjaan_ayah ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Penghasilan Ayah:</span>
                                    <span class="font-bold text-slate-800">{{ $ortu->penghasilan_ayah ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">No. Telepon / WhatsApp:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $ortu->no_hp_ayah ?? '-' }}</span>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                                    <span class="font-black text-slate-800">DATA IBU KANDUNG</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                                        {{ $ortu->status_ibu ?? 'Hidup' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Nama Lengkap Ibu:</span>
                                    <span class="font-bold text-slate-800 text-sm">{{ $ortu->nama_ibu ?? '-' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-[11px]">
                                    <div>
                                        <span class="text-slate-400 block">NIK:</span>
                                        <span class="font-mono font-semibold text-slate-700">{{ $ortu->nik_ibu ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Tahun Lahir:</span>
                                        <span class="font-semibold text-slate-700">{{ $ortu->tahun_lahir_ibu ?? '-' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Pendidikan & Pekerjaan:</span>
                                    <span class="font-medium text-slate-700">
                                        {{ $ortu->pendidikan_ibu ?? '-' }} &bull; {{ $ortu->pekerjaanIbu?->nama ?? $ortu->pekerjaan_ibu ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Penghasilan Ibu:</span>
                                    <span class="font-bold text-slate-800">{{ $ortu->penghasilan_ibu ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">No. Telepon / WhatsApp:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $ortu->no_hp_ibu ?? '-' }}</span>
                                </div>
                            </div>

                            @if (!empty($ortu->nama_wali))
                                <div class="md:col-span-2 p-4 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-1.5">
                                    <span class="font-bold text-amber-900 block text-xs">DATA WALI MURID</span>
                                    <p class="text-xs text-slate-700">
                                        Nama: <strong>{{ $ortu->nama_wali }}</strong> (Hubungan: {{ $ortu->hubungan_wali ?? '-' }}) &bull; 
                                        Pekerjaan: {{ $ortu->pekerjaanWali?->nama ?? $ortu->pekerjaan_wali ?? '-' }} &bull; 
                                        Penghasilan: {{ $ortu->penghasilan_wali ?? '-' }} &bull; 
                                        HP: {{ $ortu->no_hp_wali ?? '-' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Data orang tua belum dilengkapi oleh calon siswa.</p>
                    @endif
                </div>

                <!-- 5. NILAI RAPOR SISWA -->
                <div id="sec-rapor" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">5</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Nilai Rapor SMP (Semester 1–5)</h2>
                                <p class="text-[11px] text-slate-400">Rekapitulasi 4 mata pelajaran pokok penentu kelulusan</p>
                            </div>
                        </div>
                        @if($calonSiswa->dataAkademik?->nilai_rata_rata)
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block font-semibold">RATA-RATA TOTAL</span>
                                <span class="text-base font-black text-emerald-600 font-mono">{{ number_format($calonSiswa->dataAkademik->nilai_rata_rata, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    @php $rapor = $calonSiswa->nilaiRapor; @endphp
                    @if ($rapor)
                        @php
                            $matrix = $rapor->matrix;
                            $mapels = [
                                'ind' => 'Bahasa Indonesia',
                                'eng' => 'Bahasa Inggris',
                                'mtk' => 'Matematika',
                                'pai' => 'Pendidikan Agama Islam (PAI)',
                            ];
                        @endphp
                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="p-3">Mata Pelajaran</th>
                                        <th class="p-3 text-center">Sem 1</th>
                                        <th class="p-3 text-center">Sem 2</th>
                                        <th class="p-3 text-center">Sem 3</th>
                                        <th class="p-3 text-center">Sem 4</th>
                                        <th class="p-3 text-center">Sem 5</th>
                                        <th class="p-3 text-center bg-slate-100">Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono">
                                    @php
                                        $semTotals = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                                        $semCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                                        $grandSum = 0;
                                        $grandCount = 0;
                                    @endphp
                                    @foreach($mapels as $k => $label)
                                        @php
                                            $rowSum = 0;
                                            $rowCount = 0;
                                            for($s = 1; $s <= 5; $s++) {
                                                $v = (float)($matrix[$k][$s] ?? 0);
                                                if($v > 0) {
                                                    $rowSum += $v;
                                                    $rowCount++;
                                                    $semTotals[$s] += $v;
                                                    $semCounts[$s]++;
                                                }
                                            }
                                            $rowAvg = $rowCount > 0 ? $rowSum / $rowCount : 0;
                                            $grandSum += $rowSum;
                                            $grandCount += $rowCount;
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="p-3 font-sans font-bold text-slate-800">{{ $label }}</td>
                                            @for($s = 1; $s <= 5; $s++)
                                                <td class="p-3 text-center text-slate-700 font-semibold">
                                                    {{ number_format((float)($matrix[$k][$s] ?? 0), 1) }}
                                                </td>
                                            @endfor
                                            <td class="p-3 text-center font-bold bg-slate-50 text-slate-900">
                                                {{ number_format($rowAvg, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-slate-50 font-bold border-t-2 border-slate-200">
                                        <td class="p-3 font-sans text-slate-700">Rata-rata Semester</td>
                                        @for($s = 1; $s <= 5; $s++)
                                            @php $sAvg = $semCounts[$s] > 0 ? $semTotals[$s] / $semCounts[$s] : 0; @endphp
                                            <td class="p-3 text-center text-slate-900">{{ number_format($sAvg, 2) }}</td>
                                        @endfor
                                        <td class="p-3 text-center font-black text-emerald-600 bg-emerald-50">
                                            {{ number_format($grandCount > 0 ? $grandSum / $grandCount : 0, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Nilai rapor belum diisi di sistem.</p>
                    @endif
                </div>

                <!-- 6. UKURAN SERAGAM (KLASTER 1, 2, 3) -->
                <div id="sec-seragam" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm">6</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Data Ukuran Seragam & Atribut</h2>
                                <p class="text-[11px] text-slate-400">Rincian pemesanan seragam berdasarkan Klaster 1, Klaster 2, dan Klaster 3</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                            {{ $calonSiswa->ukuranSeragam->count() }} Item Terdata
                        </span>
                    </div>

                    @if ($calonSiswa->ukuranSeragam->isNotEmpty())
                        @php
                            $semuaUkuran = $calonSiswa->ukuranSeragam->loadMissing('seragam');
                            $klaster1 = $semuaUkuran->filter(function($s) {
                                $kl = strtoupper($s->seragam?->klaster ?? '');
                                $nm = strtolower($s->seragam?->nama ?? $s->seragam?->nama_jenis ?? '');
                                return $kl === 'MPLS' || str_contains($nm, 'almamater') || str_contains($nm, 'olahraga') || str_contains($nm, 'olah raga') || str_contains($nm, 'atribut');
                            });
                            $klaster2 = $semuaUkuran->filter(function($s) use ($klaster1) {
                                if ($klaster1->contains('id', $s->id)) return false;
                                $kl = strtoupper($s->seragam?->klaster ?? '');
                                $nm = strtolower($s->seragam?->nama ?? $s->seragam?->nama_jenis ?? '');
                                return $kl === 'KBM' || str_contains($nm, 'celana') || str_contains($nm, 'rok') || str_contains($nm, 'muslim');
                            });
                            $klaster3 = $semuaUkuran->filter(function($s) use ($klaster1, $klaster2) {
                                return !$klaster1->contains('id', $s->id) && !$klaster2->contains('id', $s->id);
                            });
                        @endphp

                        <div class="space-y-4">
                            <!-- Klaster 1 -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Klaster 1 (Prioritas SPMB)
                                    </span>
                                    <span class="text-[10px] font-medium text-slate-400">Jas Almamater, Olahraga, Atribut</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 text-xs">
                                    @forelse($klaster1 as $srg)
                                        <div class="p-3 rounded-2xl bg-emerald-50/40 border border-emerald-200/70 flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800 block text-xs">
                                                    {{ $srg->seragam?->nama ?? $srg->seragam?->nama_seragam ?? 'Seragam' }}
                                                </span>
                                                <span class="text-[10px] text-emerald-700 font-semibold block mt-0.5">
                                                    ✓ Pesan Sekarang &bull; {{ $srg->jumlah ?? 1 }} stel
                                                </span>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-white border border-emerald-200 text-slate-900 shadow-2xs">
                                                {{ $srg->ukuran ?: '-' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-slate-400 text-xs italic col-span-3">Item Klaster 1 belum dipilih.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Klaster 2 -->
                            <div class="pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        Klaster 2 (Seragam Khusus)
                                    </span>
                                    <span class="text-[10px] font-medium text-slate-400">Celana/Rok Hijau, Seragam Muslim</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 text-xs">
                                    @forelse($klaster2 as $srg)
                                        <div class="p-3 rounded-2xl bg-blue-50/40 border border-blue-200/70 flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800 block text-xs">
                                                    {{ $srg->seragam?->nama ?? $srg->seragam?->nama_seragam ?? 'Seragam' }}
                                                </span>
                                                <span class="text-[10px] text-blue-700 font-semibold block mt-0.5">
                                                    ✓ Pesan Sekarang &bull; {{ $srg->jumlah ?? 1 }} stel
                                                </span>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-white border border-blue-200 text-slate-900 shadow-2xs">
                                                {{ $srg->ukuran ?: '-' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-slate-400 text-xs italic col-span-3">Item Klaster 2 belum dipilih.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Klaster 3 -->
                            <div class="pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Klaster 3 (Pilihan Fleksibel)
                                    </span>
                                    <span class="text-[10px] font-medium text-slate-400">Pramuka, Kemeja Putih, Sepatu Pantovel</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 text-xs">
                                    @forelse($klaster3 as $srg)
                                        @php
                                            $stPesan = $srg->status_pemesanan ?: ($srg->beli_di_sekolah ? 'PESAN_SEKARANG' : 'PESAN_NANTI');
                                            $cardBg = $stPesan === 'PESAN_SEKARANG' ? 'bg-emerald-50/50 border-emerald-200' :
                                                      ($stPesan === 'TIDAK_PESAN' ? 'bg-slate-100/60 border-slate-200 opacity-75' : 'bg-amber-50/50 border-amber-200');
                                        @endphp
                                        <div class="p-3 rounded-2xl border {{ $cardBg }} flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800 block text-xs">
                                                    {{ $srg->seragam?->nama ?? $srg->seragam?->nama_seragam ?? 'Seragam' }}
                                                </span>
                                                <span class="text-[10px] mt-0.5 block">
                                                    @if($stPesan === 'PESAN_SEKARANG')
                                                        <span class="text-emerald-700 font-bold">✓ Pesan Sekarang</span>
                                                    @elseif($stPesan === 'TIDAK_PESAN')
                                                        <span class="text-slate-500 font-semibold">✕ Tidak Pesan (Beli Sendiri)</span>
                                                    @else
                                                        <span class="text-amber-700 font-semibold">⏳ Pesan Nanti</span>
                                                    @endif
                                                    @if($stPesan !== 'TIDAK_PESAN')
                                                        &bull; {{ $srg->jumlah ?? 1 }} {{ str_contains(strtolower($srg->seragam?->nama ?? ''), 'pantovel') ? 'pasang' : 'stel' }}
                                                    @endif
                                                </span>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-white border border-slate-200 text-slate-900 shadow-2xs">
                                                {{ $srg->ukuran ?: '-' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-slate-400 text-xs italic col-span-3">Item Klaster 3 belum dipilih.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Pilihan ukuran seragam belum diinput.</p>
                    @endif
                </div>

                <!-- 7. KESEPAHAMAN / PAKTA INTEGRITAS (EULA) -->
                <div id="sec-eula" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">7</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Kesepahaman & Pakta Integritas (EULA)</h2>
                                <p class="text-[11px] text-slate-400">Tanda tangan elektronik persetujuan komitmen dan tata tertib sekolah</p>
                            </div>
                        </div>
                        @php $eula = $calonSiswa->kesepahaman->first(); @endphp
                        <span class="px-3 py-1 rounded-xl text-[11px] font-black {{ $eula && $eula->setuju ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                            {{ $eula && $eula->setuju ? '✓ DISETUJUI' : 'BELUM DISETUJUI' }}
                        </span>
                    </div>

                    @if ($eula)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-600">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Waktu Persetujuan:</span>
                                    <span class="font-bold text-slate-800 mt-0.5 block">
                                        {{ $eula->agreed_at ? $eula->agreed_at->translatedFormat('d F Y H:i:s') : '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Program & Versi Dokumen:</span>
                                    <span class="font-mono font-bold text-slate-800 mt-0.5 block">
                                        {{ $eula->program_snapshot ?? ($calonSiswa->program?->nama ?? 'REGULER') }} &bull; {{ $eula->versi_dokumen ?? 'v2.0' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Alamat IP:</span>
                                    <span class="font-mono text-slate-700 mt-0.5 block">{{ $eula->ip_address ?? '127.0.0.1' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Checklist Butir Poin:</span>
                                    <span class="font-bold text-emerald-700 mt-0.5 block">
                                        {{ count($eula->poin_disetujui ?? []) > 0 ? count($eula->poin_disetujui) . ' butir klausul disetujui lengkap' : 'Seluruh klausul disetujui' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-slate-200 text-slate-700 text-[11px] italic leading-relaxed">
                                "{{ $eula->isi_dokumen_atau_referensi_dokumen ?? 'Calon siswa dan orang tua telah menyetujui seluruh ketentuan SPMB, pembiayaan, serta norma integritas SMK Wikrama 1 Garut.' }}"
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Calon siswa belum menyetujui kesepahaman EULA.</p>
                    @endif
                </div>

                <!-- 8. DOKUMEN & BERKAS PERSYARATAN -->
                <div id="sec-berkas" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">8</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Berkas & Dokumen Terunggah</h2>
                                <p class="text-[11px] text-slate-400">Dokumen asli seleksi (KK & Pas Foto Wajib, Akta & Ijazah Opsional)</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">Maks. 10 MB / berkas</span>
                    </div>

                    @php $doc = $calonSiswa->dokumenPendaftaran; @endphp
                    @if ($doc)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                            <!-- KK -->
                            <div class="p-4 rounded-2xl border {{ $doc->kk_path ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 text-sm">Kartu Keluarga (KK)</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-100 text-rose-700 border border-rose-200">WAJIB</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->kk_path ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                            {{ $doc->kk_path ? '✓ Terunggah' : 'Belum Ada' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">Wajib diverifikasi kesesuaian nomor KK dan NIK</span>
                                </div>
                                @if ($doc->kk_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->kk_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition-colors shadow-2xs">
                                            <span>↗ Buka Dokumen</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Akta Kelahiran -->
                            <div class="p-4 rounded-2xl border {{ $doc->akta_path ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 text-sm">Akta Kelahiran</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">OPSIONAL</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->akta_path ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                            {{ $doc->akta_path ? '✓ Terunggah' : 'Belum Ada' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">Opsional / validasi tempat & tanggal lahir</span>
                                </div>
                                @if ($doc->akta_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->akta_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition-colors shadow-2xs">
                                            <span>↗ Buka Dokumen</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Ijazah / SKL -->
                            <div class="p-4 rounded-2xl border {{ $doc->ijazah_skl_path ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 text-sm">Ijazah / SKL SMP</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">OPSIONAL</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->ijazah_skl_path ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                            {{ $doc->ijazah_skl_path ? '✓ Terunggah' : 'Belum Ada' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">Opsional / Surat Keterangan Lulus / Ijazah</span>
                                </div>
                                @if ($doc->ijazah_skl_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->ijazah_skl_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition-colors shadow-2xs">
                                            <span>↗ Buka Dokumen</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Pas Foto -->
                            <div class="p-4 rounded-2xl border {{ $doc->pas_foto_path ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 text-sm">Pas Foto Calon Siswa</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-100 text-rose-700 border border-rose-200">WAJIB</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->pas_foto_path ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                            {{ $doc->pas_foto_path ? '✓ Terunggah' : 'Belum Ada' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">Wajib untuk kartu peserta & arsip resmi</span>
                                </div>
                                @if ($doc->pas_foto_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->pas_foto_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition-colors shadow-2xs">
                                            <span>↗ Buka Foto</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum ada berkas persyaratan yang diunggah.</p>
                    @endif
                </div>

            </div>

            <!-- RIGHT COLUMN (5 COLS): Biaya Seleksi, Wawancara, Keuangan & Tagihan Daftar Ulang, Keputusan, Riwayat -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 9. DATA PEMBAYARAN BIAYA SELEKSI -->
                <div id="sec-seleksi" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">9</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Biaya Seleksi Pendaftaran</h2>
                                <p class="text-[11px] text-slate-400">Verifikasi pembayaran awal formulir seleksi</p>
                            </div>
                        </div>
                        @php $bayarSeleksi = $calonSiswa->pembayaranSeleksi; @endphp
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black
                            {{ $bayarSeleksi?->status === 'DIVERIFIKASI' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' :
                               ($bayarSeleksi?->status === 'DITOLAK' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                            {{ $bayarSeleksi?->status ?? 'BELUM' }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 font-medium">Nominal Tagihan Seleksi:</span>
                            <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($bayarSeleksi?->nominal_tagihan ?? 200000, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 font-medium">Nominal Telah Dibayar:</span>
                            <span class="font-black text-emerald-600 font-mono text-sm">Rp {{ number_format($bayarSeleksi?->nominal_dibayar ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                            <span class="text-slate-400 font-medium">Tanggal Pembayaran:</span>
                            <span class="font-semibold text-slate-700">{{ $bayarSeleksi?->tanggal_bayar ? $bayarSeleksi->tanggal_bayar->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 font-medium">Bank & Atas Nama Pengirim:</span>
                            <span class="font-semibold text-slate-700">{{ $bayarSeleksi?->bank_pengirim ?? '-' }} a.n. {{ $bayarSeleksi?->nama_pengirim ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 font-medium">Diverifikasi Oleh:</span>
                            <span class="font-semibold text-slate-700">{{ $bayarSeleksi?->verifiedBy?->name ?? 'Bendahara Sekolah' }}</span>
                        </div>
                        @if ($bayarSeleksi?->bukti_transfer_path)
                            <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-slate-400 font-medium">Berkas Bukti Transfer:</span>
                                <a href="{{ asset('storage/' . $bayarSeleksi->bukti_transfer_path) }}" target="_blank"
                                   class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    ↗ Lihat Bukti Transfer
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 10. HASIL EVALUASI WAWANCARA (SISWA & ORTU) -->
                <div id="sec-wawancara" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">10</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Hasil Wawancara Seleksi</h2>
                            <p class="text-[11px] text-slate-400">Data hasil observasi siswa dan komitmen orang tua</p>
                        </div>
                    </div>

                    <!-- Wawancara Siswa -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-2.5">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <div>
                                <span class="font-black text-slate-800 text-xs">EVALUASI WAWANCARA SISWA</span>
                                @if($calonSiswa->wawancaraSiswa?->tanggal_wawancara)
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        {{ $calonSiswa->wawancaraSiswa->tanggal_wawancara->translatedFormat('d M Y') }} &bull; Oleh: {{ $calonSiswa->wawancaraSiswa->pewawancara?->name ?? $calonSiswa->wawancaraSiswa->nama_petugas }}
                                    </span>
                                @endif
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $calonSiswa->wawancaraSiswa?->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $calonSiswa->wawancaraSiswa?->status ?? 'BELUM' }}
                            </span>
                        </div>

                        @if ($calonSiswa->wawancaraSiswa)
                            @php $ws = $calonSiswa->wawancaraSiswa; @endphp
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Rekomendasi Pewawancara:</span>
                                    <span class="font-black text-sm {{ $ws->rekomendasi === 'TERIMA' ? 'text-emerald-600' : ($ws->rekomendasi === 'PERTIMBANGKAN' ? 'text-amber-600' : 'text-rose-600') }}">
                                        {{ $ws->rekomendasi ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Kondisi Kesehatan:</span>
                                    <span class="font-bold text-slate-800">{{ $ws->kondisi_kesehatan ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Baca & Hafalan Qur'an:</span>
                                    <span class="font-semibold text-slate-700">{{ $ws->baca_quran ?? '-' }} @if($ws->hafalan_quran) ({{ $ws->hafalan_quran }}) @endif</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Observasi Kedisiplinan:</span>
                                    <span class="font-semibold text-slate-700">Rambut: {{ $ws->kerapihan_rambut ?? '-' }} &bull; Seragam: {{ $ws->kerapihan_seragam ?? '-' }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-slate-400 block text-[10px]">Alasan Masuk & Jurusan:</span>
                                    <span class="font-medium text-slate-700">{{ $ws->alasan_masuk_wikrama ?: '-' }}</span>
                                </div>
                                @if ($ws->catatan_pewawancara)
                                    <div class="col-span-2 p-2.5 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-[11px]">
                                        <strong>Catatan Pewawancara:</strong> {{ $ws->catatan_pewawancara }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Data wawancara siswa belum diinput oleh pewawancara.</p>
                        @endif
                    </div>

                    <!-- Wawancara Orang Tua -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-2.5">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <div>
                                <span class="font-black text-slate-800 text-xs">EVALUASI WAWANCARA ORANG TUA</span>
                                @if($calonSiswa->wawancaraOrangTua?->tanggal_wawancara)
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        {{ $calonSiswa->wawancaraOrangTua->tanggal_wawancara->translatedFormat('d M Y') }} &bull; Oleh: {{ $calonSiswa->wawancaraOrangTua->pewawancara?->name ?? $calonSiswa->wawancaraOrangTua->nama_petugas }}
                                    </span>
                                @endif
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $calonSiswa->wawancaraOrangTua?->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $calonSiswa->wawancaraOrangTua?->status ?? 'BELUM' }}
                            </span>
                        </div>

                        @if ($calonSiswa->wawancaraOrangTua)
                            @php $wo = $calonSiswa->wawancaraOrangTua; @endphp
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Narasumber yang Hadir:</span>
                                    <span class="font-bold text-slate-800">{{ $wo->nama_diwawancarai }} ({{ $wo->hubungan_dengan_siswa }})</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Penanggung Jawab Belajar:</span>
                                    <span class="font-bold text-slate-800">{{ $wo->penanggung_jawab_belajar ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Info Wikrama dari:</span>
                                    <span class="font-semibold text-slate-700">{{ $wo->info_wikrama_dari ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Tinggal Bersama:</span>
                                    <span class="font-semibold text-slate-700">{{ $wo->tinggal_bersama ?? '-' }}</span>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <span class="text-slate-400 block text-[10px]">Kesanggupan Infaq Bulanan:</span>
                                    <span class="font-bold text-emerald-700 font-mono">{{ $wo->infaq_rutin_bulanan !== null ? 'Rp ' . number_format($wo->infaq_rutin_bulanan, 0, ',', '.') : '-' }}</span>
                                </div>
                                @if ($wo->hal_perhatian_ortu)
                                    <div class="col-span-2">
                                        <span class="text-slate-400 block text-[10px]">Perhatian Khusus Orang Tua:</span>
                                        <span class="font-medium text-slate-700">{{ $wo->hal_perhatian_ortu }}</span>
                                    </div>
                                @endif
                                @if ($wo->kesan_pewawancara)
                                    <div class="col-span-2 p-2.5 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-[11px]">
                                        <strong>Kesan / Catatan Tambahan:</strong> {{ $wo->kesan_pewawancara }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Data wawancara orang tua belum diinput oleh pewawancara.</p>
                        @endif
                    </div>
                </div>

                <!-- 11. KEUANGAN & DAFTAR ULANG (WALAU BELUM DITERBITKAN) -->
                <div id="sec-keuangan" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold text-sm">11</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Keuangan Daftar Ulang</h2>
                                <p class="text-[11px] text-slate-400">Rincian invoice resmi atau estimasi biaya baku program</p>
                            </div>
                        </div>
                        @php
                            $tagihanDU = $calonSiswa->tagihan->firstWhere('jenis_tagihan', 'DAFTAR_ULANG');
                            $tagihanSRG = $calonSiswa->tagihan->firstWhere('jenis_tagihan', 'SERAGAM');
                        @endphp
                        @if ($tagihanDU || $tagihanSRG)
                            <span class="px-2.5 py-1 rounded-xl text-xs font-black {{ ($tagihanDU?->status === 'LUNAS') ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-cyan-100 text-cyan-800 border border-cyan-300' }}">
                                {{ $tagihanDU?->status ?? 'TAGIHAN TERBIT' }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                ⏳ ESTIMASI BAKU
                            </span>
                        @endif
                    </div>

                    @if ($tagihanDU || $tagihanSRG)
                        <!-- SUDAH DITERBITKAN -->
                        <div class="space-y-3">
                            @if ($tagihanDU)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                    <div class="flex justify-between items-center border-b border-slate-200 pb-1.5">
                                        <span class="font-bold text-slate-800">Biaya Pendidikan (DSP, SPP, Asrama)</span>
                                        <span class="font-mono font-bold text-slate-600">{{ $tagihanDU->nomor_tagihan }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-600">
                                        <span>Total Bruto:</span>
                                        <span class="font-mono font-semibold">Rp {{ number_format($tagihanDU->total_bruto, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($tagihanDU->total_diskon > 0)
                                        <div class="flex justify-between items-center text-rose-600 font-semibold">
                                            <span>Potongan Diskon:</span>
                                            <span class="font-mono">- Rp {{ number_format($tagihanDU->total_diskon, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center text-slate-900 font-black text-sm pt-1 border-t border-slate-200">
                                        <span>Total Netto Wajib:</span>
                                        <span class="font-mono text-cyan-700">Rp {{ number_format($tagihanDU->total_netto, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            @if ($tagihanSRG)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                    <div class="flex justify-between items-center border-b border-slate-200 pb-1.5">
                                        <span class="font-bold text-slate-800">Paket Seragam & Atribut</span>
                                        <span class="font-mono font-bold text-slate-600">{{ $tagihanSRG->nomor_tagihan }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-600">
                                        <span>Total Bruto Seragam:</span>
                                        <span class="font-mono font-semibold">Rp {{ number_format($tagihanSRG->total_bruto, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($tagihanSRG->total_diskon > 0)
                                        <div class="flex justify-between items-center text-rose-600 font-semibold">
                                            <span>Diskon Seragam:</span>
                                            <span class="font-mono">- Rp {{ number_format($tagihanSRG->total_diskon, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center text-slate-900 font-black text-sm pt-1 border-t border-slate-200">
                                        <span>Total Netto Seragam:</span>
                                        <span class="font-mono text-cyan-700">Rp {{ number_format($tagihanSRG->total_netto, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            @php
                                $totalNettoAll = ($tagihanDU?->total_netto ?? 0) + ($tagihanSRG?->total_netto ?? 0);
                                $totalBayarVerified = (float) $calonSiswa->pembayaranDaftarUlang->where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
                                $sisaKewajiban = max(0, $totalNettoAll - $totalBayarVerified);
                            @endphp

                            <div class="p-4 rounded-2xl bg-cyan-50/50 border border-cyan-200 space-y-2 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-bold">Total Kewajiban Biaya:</span>
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($totalNettoAll, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-bold">Telah Dibayar (Diverifikasi):</span>
                                    <span class="font-mono font-bold text-emerald-600">Rp {{ number_format($totalBayarVerified, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-cyan-200 font-black text-sm">
                                    <span class="text-slate-800">Sisa Piutang:</span>
                                    <span class="font-mono {{ $sisaKewajiban == 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        Rp {{ number_format($sisaKewajiban, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- BELUM DITERBITKAN (ESTIMASI BAKU) -->
                        <div class="space-y-3">
                            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                <div class="font-bold flex items-center gap-1.5 mb-1">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Estimasi Biaya Baku (Belum Diterbitkan)</span>
                                </div>
                                <p class="text-[11px] leading-relaxed">
                                    Invoice resmi belum di-generate oleh Bendahara. Tabel di bawah merupakan estimasi biaya standar untuk Program <strong>{{ $calonSiswa->program?->nama ?? '-' }}</strong> dan <strong>{{ $calonSiswa->gelombang?->nama ?? '-' }}</strong>:
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                @php $estimasiTotal = 0; @endphp
                                @foreach($estimasiBiayaDaftarUlang as $item)
                                    @php $estimasiTotal += $item->nominal; @endphp
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">{{ $item->nama_biaya }} ({{ $item->kategori }}):</span>
                                        <span class="font-mono font-semibold text-slate-800">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach

                                @if(isset($estimasiBiayaSeragam) && $estimasiBiayaSeragam->isNotEmpty())
                                    @php $totSeragam = $estimasiBiayaSeragam->sum('nominal'); $estimasiTotal += $totSeragam; @endphp
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Estimasi Paket Seragam Wajib ({{ $estimasiBiayaSeragam->count() }} Komponen):</span>
                                        <span class="font-mono font-semibold text-slate-800">Rp {{ number_format($totSeragam, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center pt-2 border-t border-slate-200 font-black text-sm text-slate-900">
                                    <span>Total Estimasi Biaya Baku:</span>
                                    <span class="font-mono text-cyan-700">Rp {{ number_format($estimasiTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 12. KEPUTUSAN SIDANG PLENO KELULUSAN -->
                <div id="sec-kelulusan" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3 scroll-mt-24">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">12</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Keputusan Sidang Kelulusan</h2>
                                <p class="text-[11px] text-slate-400">Keputusan resmi hasil pleno pimpinan sekolah</p>
                            </div>
                        </div>
                        @php $keputusan = $calonSiswa->keputusanKelulusan; @endphp
                        @if ($keputusan)
                            <span class="px-3 py-1 rounded-xl text-xs font-black {{ $keputusan->keputusan === 'DITERIMA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                                {{ $keputusan->keputusan }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-500">
                                BELUM PLENO
                            </span>
                        @endif
                    </div>

                    @if ($keputusan)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Ditetapkan Oleh:</span>
                                <span class="font-bold text-slate-800">{{ $keputusan->ditetapkanOleh?->name ?? 'Kepala Sekolah' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Waktu Penetapan:</span>
                                <span class="font-semibold text-slate-700">{{ $keputusan->ditetapkan_at?->translatedFormat('d F Y H:i') }}</span>
                            </div>
                            @if ($keputusan->alasan_catatan)
                                <div class="mt-2 p-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-[11px]">
                                    <strong>Catatan Keputusan:</strong> {{ $keputusan->alasan_catatan }}
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Sidang pleno penetapan kelulusan belum dilaksanakan untuk calon siswa ini.</p>
                    @endif
                </div>

                <!-- 13. AUDIT TRAIL / RIWAYAT STATUS -->
                <div id="sec-riwayat" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3 print:hidden scroll-mt-24">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">13</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Riwayat Status SPMB (Audit Trail)</h2>
                            <p class="text-[11px] text-slate-400">Log kronologis perubahan status calon siswa</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        @forelse ($calonSiswa->riwayatStatus->sortByDesc('id') as $riwayat)
                            <div class="text-xs border-l-2 border-orange-500 pl-3 py-1 space-y-0.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ str_replace('_', ' ', $riwayat->status_baru) }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $riwayat->created_at?->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                <p class="text-slate-500 text-[11px]">{{ $riwayat->alasan ?: ($riwayat->catatan ?: 'Perubahan otomatis sistem') }}</p>
                                <span class="text-[10px] text-slate-400 block font-mono">Oleh: {{ $riwayat->changedBy?->name ?? 'Sistem' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada riwayat transisi status.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
