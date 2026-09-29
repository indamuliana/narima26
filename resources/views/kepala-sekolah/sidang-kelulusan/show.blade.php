<x-layouts.app>
    <x-slot name="title">Evaluasi Sidang Kelulusan - {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Back & Top Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('kepala-sekolah.sidang-kelulusan.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Daftar Sidang Pleno</span>
            </a>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('kepala-sekolah.calon-siswa.cetak-pdf', $calonSiswa) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Cetak Profil Lengkap (PDF)</span>
                </a>
                @if ($keputusan)
                    <a href="{{ route('kepala-sekolah.sidang-kelulusan.cetak-sk', $calonSiswa) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition shadow-xs">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Unduh SK Kelulusan Resmi (PDF)</span>
                    </a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-xs sm:text-sm font-semibold">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Candidate Identity Header Card -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-4 sm:gap-5">
                @php
                    $pasFoto = $calonSiswa->dokumenPendaftaran?->pas_foto_path;
                    $fotoExists = $pasFoto && file_exists(public_path('storage/' . $pasFoto));
                @endphp
                @if ($fotoExists)
                    <img src="{{ asset('storage/' . $pasFoto) }}" alt="Pas Foto Siswa"
                         class="w-16 h-20 sm:w-20 sm:h-24 object-cover rounded-2xl border-2 border-white shadow-md ring-2 ring-slate-200 shrink-0">
                @else
                    <div class="w-16 h-20 sm:w-20 sm:h-24 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-950 text-white flex flex-col items-center justify-center font-black shadow-md shrink-0">
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
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">{{ $calonSiswa->nama_lengkap }}</h1>
                    <div class="flex flex-wrap items-center gap-2.5 mt-2 text-xs text-slate-600">
                        <span class="font-bold text-slate-800">{{ $calonSiswa->jurusan?->nama_jurusan ?? $calonSiswa->jurusan?->nama ?? '-' }}</span>
                        <span>&bull;</span>
                        <span class="font-bold text-slate-800">{{ $calonSiswa->program?->nama ?? 'Reguler' }} &bull; {{ $calonSiswa->gelombang?->nama ?? 'Gelombang 1' }}</span>
                        <span>&bull;</span>
                        <span>SMP: <strong>{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="flex flex-col md:items-end gap-2 shrink-0">
                @php
                    $statusStr = is_string($calonSiswa->status_spmb) ? $calonSiswa->status_spmb : ($calonSiswa->status_spmb?->value ?? '-');
                    $statusEnum = is_string($calonSiswa->status_spmb) ? \App\Enums\SpmbStatus::tryFrom($calonSiswa->status_spmb) : $calonSiswa->status_spmb;
                @endphp
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-2xl text-xs font-black
                    {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR', 'DAFTAR_ULANG_DIVERIFIKASI']) ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' :
                       (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-50 text-rose-800 border border-rose-300' : 'bg-amber-50 text-amber-800 border border-amber-300') }}">
                    <span class="w-2.5 h-2.5 rounded-full {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR', 'DAFTAR_ULANG_DIVERIFIKASI']) ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                    STATUS: {{ $statusEnum?->label() ?? str_replace('_', ' ', $statusStr) }}
                </span>
                <span class="text-[11px] text-slate-400 font-mono">
                    Registrasi: {{ $calonSiswa->created_at?->format('d/m/Y H:i') ?? '-' }}
                </span>
            </div>
        </div>

        <!-- Main Layout: 7 Cols Left (All Data Tabs), 5 Cols Right (Decision Form & Actions) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" x-data="{ activeTab: 'wawancara' }">

            <!-- LEFT COLUMN (7 COLS): Comprehensive Student Data Tabs -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Navigation Tabs -->
                <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-wrap gap-1.5">
                    <button type="button" @click="activeTab = 'wawancara'"
                            :class="activeTab === 'wawancara' ? 'bg-slate-900 text-white shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>🎙️</span>
                        <span>Wawancara</span>
                    </button>
                    <button type="button" @click="activeTab = 'biodata'"
                            :class="activeTab === 'biodata' ? 'bg-slate-900 text-white shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>👤</span>
                        <span>Biodata & Ortu</span>
                    </button>
                    <button type="button" @click="activeTab = 'akademik'"
                            :class="activeTab === 'akademik' ? 'bg-slate-900 text-white shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>📚</span>
                        <span>Rapor & Prestasi</span>
                    </button>
                    <button type="button" @click="activeTab = 'keuangan'"
                            :class="activeTab === 'keuangan' ? 'bg-slate-900 text-white shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>💳</span>
                        <span>Keuangan Bendahara</span>
                    </button>
                    <button type="button" @click="activeTab = 'seragam'"
                            :class="activeTab === 'seragam' ? 'bg-slate-900 text-white shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>👔</span>
                        <span>Pemesanan Seragam</span>
                    </button>
                    <button type="button" @click="activeTab = 'dokumen'"
                            :class="activeTab === 'dokumen' ? 'bg-slate-900 text-white shadow-2xs font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>📂</span>
                        <span>Berkas Dokumen</span>
                    </button>
                </div>

                <!-- TAB 1: HASIL WAWANCARA (Pewawancara) -->
                <div x-show="activeTab === 'wawancara'" class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Hasil Evaluasi Wawancara</h2>
                                <p class="text-[11px] text-slate-400">Penilaian komprehensif dari Tim Pewawancara untuk Siswa & Orang Tua</p>
                            </div>
                            @if($wawancaraSiswa?->rekomendasi)
                                <span class="px-3 py-1 rounded-xl text-xs font-black
                                    {{ $wawancaraSiswa->rekomendasi === 'TERIMA' ? 'bg-emerald-100 text-emerald-800' :
                                       ($wawancaraSiswa->rekomendasi === 'PERTIMBANGKAN' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    Rekomendasi: {{ $wawancaraSiswa->rekomendasi }}
                                </span>
                            @endif
                        </div>

                        <!-- Wawancara Siswa -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">1. Wawancara Siswa</h3>
                                <span class="text-[11px] text-slate-500">
                                    Pewawancara: <strong>{{ $wawancaraSiswa?->pewawancara?->name ?? $wawancaraSiswa?->nama_petugas ?? '-' }}</strong>
                                    ({{ $wawancaraSiswa?->tanggal_wawancara ? \Carbon\Carbon::parse($wawancaraSiswa->tanggal_wawancara)->format('d/m/Y') : '-' }})
                                </span>
                            </div>

                            @if($wawancaraSiswa)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Membaca Al-Qur'an</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraSiswa->baca_quran ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Kondisi Kesehatan Fisik</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraSiswa->kondisi_kesehatan ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Kerapihan Rambut & Seragam</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraSiswa->kerapihan_rambut ?? '-' }} &bull; Seragam: {{ $wawancaraSiswa->kerapihan_seragam ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Pendengaran & Penglihatan</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraSiswa->status_pendengaran ?? '-' }} &bull; Mata: {{ $wawancaraSiswa->status_penglihatan ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Riwayat Merokok</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraSiswa->merokok ?? 'Tidak' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Buta Warna / Tato / Tindik</span>
                                        <span class="font-bold text-slate-800">Buta Warna: {{ $wawancaraSiswa->buta_warna ?? 'Tidak' }} | Tato: {{ $wawancaraSiswa->tato ?? 'Tidak' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-2 text-xs">
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Alasan Masuk SMK Wikrama</span>
                                        <p class="font-medium text-slate-800 mt-0.5">{{ $wawancaraSiswa->alasan_masuk_wikrama ?: '-' }}</p>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Alasan Memilih Jurusan / Program</span>
                                        <p class="font-medium text-slate-800 mt-0.5">{{ $wawancaraSiswa->alasan_pilih_program_jurusan ?: '-' }}</p>
                                    </div>
                                    <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200">
                                        <span class="text-amber-900 font-bold block text-[11px]">Catatan Rahasia Pewawancara Siswa:</span>
                                        <p class="font-medium text-amber-950 mt-1 italic">{{ $wawancaraSiswa->catatan_pewawancara ?: 'Tidak ada catatan khusus.' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Data wawancara siswa belum tersedia / belum dilaksanakan.</p>
                            @endif
                        </div>

                        <!-- Wawancara Orang Tua -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">2. Wawancara Orang Tua / Wali</h3>
                                <span class="text-[11px] text-slate-500">
                                    Pewawancara: <strong>{{ $wawancaraOrangTua?->pewawancara?->name ?? $wawancaraOrangTua?->nama_petugas ?? '-' }}</strong>
                                </span>
                            </div>

                            @if($wawancaraOrangTua)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Narasumber yang Hadir</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraOrangTua->nama_diwawancarai }} ({{ $wawancaraOrangTua->hubungan_dengan_siswa }})</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Penanggung Jawab Belajar di Rumah</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraOrangTua->penanggung_jawab_belajar ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Tinggal Bersama & Jarak Rumah</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraOrangTua->tinggal_bersama ?? '-' }} &bull; {{ $wawancaraOrangTua->jarak_rumah ?? '-' }} ({{ $wawancaraOrangTua->transportasi ?? '-' }})</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-100">
                                        <span class="text-slate-400 block text-[11px]">Sumber Informasi Wikrama</span>
                                        <span class="font-bold text-slate-800">{{ $wawancaraOrangTua->info_wikrama_dari ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-2 text-xs">
                                    <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200">
                                        <span class="text-amber-900 font-bold block text-[11px]">Kesan & Catatan Rahasia Pewawancara Orang Tua:</span>
                                        <p class="font-medium text-amber-950 mt-1 italic">{{ $wawancaraOrangTua->kesan_pewawancara ?: 'Tidak ada catatan khusus.' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Data wawancara orang tua belum tersedia / belum dilaksanakan.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 2: BIODATA & DATA ORANG TUA (Calon Siswa) -->
                <div x-show="activeTab === 'biodata'" class="space-y-6" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Biodata Pribadi & Kontak</h2>
                            <p class="text-[11px] text-slate-400">Data identitas yang diinput langsung oleh calon siswa pada formulir pendaftaran</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px]">Nomor Induk Kependudukan (NIK)</span>
                                <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $calonSiswa->nik ?? '-' }}</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px]">Tempat, Tanggal Lahir</span>
                                <span class="font-bold text-slate-800 text-sm mt-0.5 block">
                                    {{ $calonSiswa->tempat_lahir ?? '-' }}, {{ $calonSiswa->tanggal_lahir ? \Carbon\Carbon::parse($calonSiswa->tanggal_lahir)->format('d F Y') : '-' }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px]">Agama & Anak Ke-</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">
                                    {{ $calonSiswa->agama ?? 'Islam' }} &bull; Anak ke-{{ $calonSiswa->anak_ke ?? 1 }} dari {{ $calonSiswa->jumlah_saudara ?? 1 }} bersaudara
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px]">Kontak WhatsApp & Email</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">
                                    {{ $calonSiswa->no_hp ?? '-' }} &bull; {{ $calonSiswa->email ?? '-' }}
                                </span>
                            </div>
                            <div class="sm:col-span-2 p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px]">Alamat Domisili Lengkap</span>
                                <span class="font-medium text-slate-800 mt-0.5 block">
                                    {{ $calonSiswa->alamat_lengkap ?? '-' }} 
                                    @if($calonSiswa->rt || $calonSiswa->rw) (RT {{ $calonSiswa->rt ?? '0' }} / RW {{ $calonSiswa->rw ?? '0' }}) @endif, 
                                    Desa {{ $calonSiswa->desa?->nama ?? '-' }}, Kec. {{ $calonSiswa->kecamatan?->nama ?? '-' }}, 
                                    {{ $calonSiswa->kabupaten?->nama ?? '-' }}, Prov. {{ $calonSiswa->provinsi?->nama ?? '-' }} 
                                    @if($calonSiswa->kode_pos) (Kode Pos: {{ $calonSiswa->kode_pos }}) @endif
                                </span>
                            </div>
                        </div>

                        <!-- Data Orang Tua -->
                        <div class="border-t border-slate-100 pt-5 space-y-4">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Data Orang Tua & Wali</h3>
                            @php $ortu = $calonSiswa->dataOrangtua; @endphp
                            @if($ortu)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <!-- Ayah -->
                                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                                        <span class="text-xs font-bold text-slate-900 border-b border-slate-200 pb-1 block">Data Ayah Kandung</span>
                                        <p><span class="text-slate-400">Nama:</span> <strong class="text-slate-800">{{ $ortu->nama_ayah ?: '-' }}</strong></p>
                                        <p><span class="text-slate-400">Pekerjaan:</span> <span class="text-slate-800 font-medium">{{ $ortu->pekerjaanAyah?->nama ?? $ortu->pekerjaan_ayah ?: '-' }}</span></p>
                                        <p><span class="text-slate-400">Penghasilan:</span> <span class="text-slate-800 font-medium">{{ $ortu->penghasilan_ayah ?: '-' }}</span></p>
                                        <p><span class="text-slate-400">Kontak:</span> <span class="text-slate-800 font-medium">{{ $ortu->no_hp_ayah ?: '-' }}</span></p>
                                    </div>
                                    <!-- Ibu -->
                                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                                        <span class="text-xs font-bold text-slate-900 border-b border-slate-200 pb-1 block">Data Ibu Kandung</span>
                                        <p><span class="text-slate-400">Nama:</span> <strong class="text-slate-800">{{ $ortu->nama_ibu ?: '-' }}</strong></p>
                                        <p><span class="text-slate-400">Pekerjaan:</span> <span class="text-slate-800 font-medium">{{ $ortu->pekerjaanIbu?->nama ?? $ortu->pekerjaan_ibu ?: '-' }}</span></p>
                                        <p><span class="text-slate-400">Penghasilan:</span> <span class="text-slate-800 font-medium">{{ $ortu->penghasilan_ibu ?: '-' }}</span></p>
                                        <p><span class="text-slate-400">Kontak:</span> <span class="text-slate-800 font-medium">{{ $ortu->no_hp_ibu ?: '-' }}</span></p>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Data orang tua belum dilengkapi.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 3: AKADEMIK & RAPOR SMP -->
                <div x-show="activeTab === 'akademik'" class="space-y-6" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Nilai Rapor SMP (Semester 1 s.d 5)</h2>
                            <p class="text-[11px] text-slate-400">Rekapitulasi nilai mata pelajaran inti yang diinput calon siswa</p>
                        </div>

                        @php $rapor = $calonSiswa->nilaiRapor; @endphp
                        @if($rapor)
                            <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
                                <table class="w-full text-xs text-center text-slate-700">
                                    <thead class="bg-slate-50 font-bold border-b border-slate-200 text-slate-800">
                                        <tr>
                                            <th class="py-2.5 px-3 text-left">Semester</th>
                                            <th class="py-2.5 px-3">PAI</th>
                                            <th class="py-2.5 px-3">B. Indonesia</th>
                                            <th class="py-2.5 px-3">B. Inggris</th>
                                            <th class="py-2.5 px-3">Matematika</th>
                                            <th class="py-2.5 px-3">IPA</th>
                                            <th class="py-2.5 px-3 bg-amber-50 text-amber-900 font-black">Rata-rata</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-mono">
                                        @for($sem = 1; $sem <= 5; $sem++)
                                            @php
                                                $pai = $rapor->{"sem{$sem}_pai"};
                                                $indo = $rapor->{"sem{$sem}_indo"};
                                                $inggris = $rapor->{"sem{$sem}_inggris"};
                                                $mtk = $rapor->{"sem{$sem}_mtk"};
                                                $ipa = $rapor->{"sem{$sem}_ipa"};
                                                $vals = array_filter([$pai, $indo, $inggris, $mtk, $ipa]);
                                                $avg = count($vals) ? array_sum($vals) / count($vals) : 0;
                                            @endphp
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="py-2 px-3 text-left font-bold font-sans text-slate-800">Semester {{ $sem }}</td>
                                                <td class="py-2 px-3">{{ $pai ?: '-' }}</td>
                                                <td class="py-2 px-3">{{ $indo ?: '-' }}</td>
                                                <td class="py-2 px-3">{{ $inggris ?: '-' }}</td>
                                                <td class="py-2 px-3">{{ $mtk ?: '-' }}</td>
                                                <td class="py-2 px-3">{{ $ipa ?: '-' }}</td>
                                                <td class="py-2 px-3 bg-amber-50/50 font-bold text-amber-950">{{ $avg ? number_format($avg, 1) : '-' }}</td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Data nilai rapor belum diinput.</p>
                        @endif

                        <!-- Prestasi -->
                        <div class="border-t border-slate-100 pt-5 space-y-3">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Prestasi & Penghargaan</h3>
                            @if($calonSiswa->prestasi->isNotEmpty())
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($calonSiswa->prestasi as $pres)
                                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                                            <span class="font-bold text-slate-800 block">{{ $pres->nama_prestasi }}</span>
                                            <span class="text-[11px] text-slate-500 block mt-0.5">Tingkat: {{ $pres->tingkat }} &bull; Juara: {{ $pres->juara }} ({{ $pres->tahun }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Tidak ada catatan prestasi yang diinput.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 4: KEUANGAN (Bendahara) -->
                <div x-show="activeTab === 'keuangan'" class="space-y-6" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Informasi Keuangan & Verifikasi (Bendahara)</h2>
                            <p class="text-[11px] text-slate-400">Status pembayaran seleksi, bukti transfer, dan tagihan daftar ulang</p>
                        </div>

                        <!-- 1. Pembayaran Seleksi -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Biaya Seleksi Masuk (Rp 200.000)</h3>
                                @php $bayarSeleksi = $calonSiswa->pembayaranSeleksi; @endphp
                                @if($bayarSeleksi && ($bayarSeleksi->isDiverifikasi() || $bayarSeleksi->status === 'DIVERIFIKASI'))
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Lunas & Terverifikasi</span>
                                @elseif($bayarSeleksi && $bayarSeleksi->status === 'PENDING')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">⏳ Menunggu Verifikasi Bendahara</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">Belum Ada Pembayaran</span>
                                @endif
                            </div>

                            @if($bayarSeleksi)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div><span class="text-slate-400">Nominal:</span> <strong class="text-slate-800 font-mono">Rp {{ number_format($bayarSeleksi->nominal_dibayar, 0, ',', '.') }}</strong></div>
                                    <div><span class="text-slate-400">Bank Pengirim:</span> <strong class="text-slate-800">{{ $bayarSeleksi->bank_pengirim ?: '-' }}</strong></div>
                                    <div><span class="text-slate-400">Nama Rekening:</span> <span class="text-slate-800 font-medium">{{ $bayarSeleksi->nama_pengirim ?: ($bayarSeleksi->nama_rekening_pengirim ?: '-') }}</span></div>
                                    <div><span class="text-slate-400">Tgl Bayar:</span> <span class="text-slate-800">{{ $bayarSeleksi->tanggal_bayar ? \Carbon\Carbon::parse($bayarSeleksi->tanggal_bayar)->format('d/m/Y') : '-' }}</span></div>
                                </div>

                                @php
                                    $buktiPath = $bayarSeleksi->bukti_transfer_path ?: $bayarSeleksi->bukti_bayar_path;
                                @endphp
                                @if($buktiPath)
                                    <div class="pt-2 flex items-center gap-3">
                                        <a href="{{ asset('storage/' . $buktiPath) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition shadow-2xs">
                                            <span>↗ Buka Bukti Transfer Slip</span>
                                        </a>
                                        <span class="text-[11px] text-slate-400">Diverifikasi oleh: {{ $bayarSeleksi->verifiedBy?->name ?? 'Bendahara' }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- 2. Tagihan Daftar Ulang -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-200/80 pb-2">Tagihan Daftar Ulang & Seragam</h3>
                            @if($calonSiswa->tagihan->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($calonSiswa->tagihan as $t)
                                        <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 text-xs flex items-center justify-between gap-4">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono font-bold text-slate-800">{{ $t->nomor_tagihan }}</span>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ ($t->status === 'LUNAS' || $t->isLunas()) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                        {{ $t->status }}
                                                    </span>
                                                </div>
                                                <span class="text-[11px] text-slate-500 block mt-0.5">
                                                    Jenis: {{ $t->jenis_tagihan }} &bull; Total: <strong class="font-mono text-slate-900">Rp {{ number_format($t->total_netto, 0, ',', '.') }}</strong>
                                                </span>
                                            </div>
                                            <span class="text-[11px] text-slate-400 font-mono">{{ $t->created_at?->format('d/m/Y') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 text-xs text-blue-900">
                                    <p class="font-bold">Tagihan Belum Diterbitkan.</p>
                                    <p class="text-[11px] text-blue-700 mt-0.5">Tagihan Daftar Ulang akan otomatis aktif ketika status kelulusan siswa ditetapkan sebagai <strong>DITERIMA</strong>.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 5: PILIHAN SERAGAM (Calon Siswa) -->
                <div x-show="activeTab === 'seragam'" class="space-y-6" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Data Ukuran & Pemesanan Seragam</h2>
                            <p class="text-[11px] text-slate-400">Pilihan ukuran dan status pemesanan bertahap (Pesan Sekarang / Pesan Nanti)</p>
                        </div>

                        @if($calonSiswa->ukuranSeragam->isNotEmpty())
                            <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
                                <table class="w-full text-xs text-left text-slate-700">
                                    <thead class="bg-slate-50 font-bold border-b border-slate-200 text-slate-800">
                                        <tr>
                                            <th class="py-2.5 px-3">Klaster</th>
                                            <th class="py-2.5 px-3">Jenis Seragam</th>
                                            <th class="py-2.5 px-3 text-center">Ukuran</th>
                                            <th class="py-2.5 px-3 text-center">Status Pemesanan</th>
                                            <th class="py-2.5 px-3 text-center">Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($calonSiswa->ukuranSeragam as $us)
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="py-2.5 px-3">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                        {{ $us->jenisSeragam?->nama_klaster ?? 'Reguler' }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $us->jenisSeragam?->nama_jenis ?? '-' }}</td>
                                                <td class="py-2.5 px-3 text-center font-bold font-mono">{{ $us->ukuran }}</td>
                                                <td class="py-2.5 px-3 text-center">
                                                    @if($us->isPesanSekarang())
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                            ✓ Pesan Sekarang
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                            ⏳ Pesan Nanti
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 px-3 text-center font-mono text-[11px]">
                                                    {{ $us->tagihan?->nomor_tagihan ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Pilihan ukuran seragam belum diinput oleh calon siswa.</p>
                        @endif
                    </div>
                </div>

                <!-- TAB 6: BERKAS & DOKUMEN PERSYARATAN -->
                <div x-show="activeTab === 'dokumen'" class="space-y-6" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Berkas & Dokumen Terunggah</h2>
                            <p class="text-[11px] text-slate-400">Dokumen asli persyaratan seleksi yang dapat langsung dibuka dan diverifikasi</p>
                        </div>

                        @php $doc = $calonSiswa->dokumenPendaftaran; @endphp
                        @if ($doc)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                                <!-- KK -->
                                <div class="p-4 rounded-2xl border {{ $doc->kk_path ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900 text-sm">Kartu Keluarga (KK)</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->kk_path ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-500' }}">
                                                {{ $doc->kk_path ? '✓ Terunggah' : 'Belum Ada' }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 block mt-0.5">Validasi NIK dan hubungan keluarga</span>
                                    </div>
                                    @if ($doc->kk_path)
                                        <a href="{{ asset('storage/' . $doc->kk_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition shadow-2xs w-fit">
                                            <span>↗ Buka Dokumen KK</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Akta Kelahiran -->
                                <div class="p-4 rounded-2xl border {{ $doc->akta_path ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900 text-sm">Akta Kelahiran</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->akta_path ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-500' }}">
                                                {{ $doc->akta_path ? '✓ Terunggah' : 'Belum Ada' }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 block mt-0.5">Validasi tanggal & tempat lahir</span>
                                    </div>
                                    @if ($doc->akta_path)
                                        <a href="{{ asset('storage/' . $doc->akta_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition shadow-2xs w-fit">
                                            <span>↗ Buka Dokumen Akta</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Ijazah / SKL -->
                                <div class="p-4 rounded-2xl border {{ $doc->ijazah_skl_path ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900 text-sm">Ijazah / SKL SMP</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->ijazah_skl_path ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-500' }}">
                                                {{ $doc->ijazah_skl_path ? '✓ Terunggah' : 'Belum Ada' }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 block mt-0.5">Surat Keterangan Lulus / Ijazah</span>
                                    </div>
                                    @if ($doc->ijazah_skl_path)
                                        <a href="{{ asset('storage/' . $doc->ijazah_skl_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition shadow-2xs w-fit">
                                            <span>↗ Buka Dokumen SKL</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Pas Foto -->
                                <div class="p-4 rounded-2xl border {{ $doc->pas_foto_path ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between gap-3">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900 text-sm">Pas Foto Siswa</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->pas_foto_path ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-500' }}">
                                                {{ $doc->pas_foto_path ? '✓ Terunggah' : 'Belum Ada' }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 block mt-0.5">Foto resmi 3x4 berseragam</span>
                                    </div>
                                    @if ($doc->pas_foto_path)
                                        <a href="{{ asset('storage/' . $doc->pas_foto_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition shadow-2xs w-fit">
                                            <span>↗ Buka Pas Foto</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Belum ada berkas dokumen yang diunggah calon siswa.</p>
                        @endif
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (5 COLS): Plenary Decision Card & Quick Actions -->
            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-6">

                <!-- Decision Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Tetapkan / Perbarui Keputusan</h3>
                        @if ($keputusan)
                            @if ($keputusan->isDiterima())
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ LULUS / DITERIMA
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">
                                    ✕ TIDAK DITERIMA
                                </span>
                            @endif
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                ⏳ Menunggu Putusan
                            </span>
                        @endif
                    </div>

                    @if ($keputusan)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2 text-slate-600">
                            <p><strong class="text-slate-800">Ditetapkan Oleh:</strong> {{ $keputusan->ditetapkanOleh?->name ?? 'Kepala Sekolah' }}</p>
                            <p><strong class="text-slate-800">Waktu Penetapan:</strong> {{ $keputusan->ditetapkan_at?->format('d F Y H:i') }}</p>
                            <div class="pt-2 border-t border-slate-200">
                                <strong class="text-slate-800 block mb-0.5">Catatan Sidang:</strong>
                                <p class="text-slate-700 italic bg-white p-2.5 rounded-xl border border-slate-200">{{ $keputusan->alasan_catatan }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Decision Form -->
                    <form method="POST" action="{{ route('kepala-sekolah.sidang-kelulusan.putuskan', $calonSiswa) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Tetapkan Keputusan Hasil Seleksi <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="p-3.5 rounded-2xl border-2 cursor-pointer flex items-center gap-2.5 transition-all {{ ($keputusan?->keputusan ?? 'DITERIMA') === 'DITERIMA' ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-400/20' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" name="keputusan" value="DITERIMA" {{ ($keputusan?->keputusan ?? 'DITERIMA') === 'DITERIMA' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="text-xs font-black text-emerald-900 block">DITERIMA</span>
                                        <span class="text-[10px] text-emerald-700 font-medium">Lulus seleksi</span>
                                    </div>
                                </label>
                                <label class="p-3.5 rounded-2xl border-2 cursor-pointer flex items-center gap-2.5 transition-all {{ ($keputusan?->keputusan ?? '') === 'DITOLAK' ? 'border-rose-500 bg-rose-50/60 ring-2 ring-rose-400/20' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" name="keputusan" value="DITOLAK" {{ ($keputusan?->keputusan ?? '') === 'DITOLAK' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <span class="text-xs font-black text-rose-900 block">DITOLAK</span>
                                        <span class="text-[10px] text-rose-700 font-medium">Tidak lulus</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Notice Auto-Activate Invoice -->
                        <div class="p-3.5 rounded-2xl bg-teal-50/80 border border-teal-200 text-teal-900 text-xs flex items-start gap-2.5">
                            <span class="text-base shrink-0">⚡</span>
                            <div>
                                <strong class="font-bold block">Aktivasi Otomatis Tagihan:</strong>
                                <span class="text-[11px] text-teal-800 leading-relaxed block mt-0.5">
                                    Ketika diputuskan <strong>DITERIMA</strong>, sistem secara otomatis menerbitkan dan mengaktifkan <strong>Tagihan Daftar Ulang (DSP + SPP)</strong> serta <strong>Tagihan Seragam (Pesan Sekarang)</strong> bagi calon siswa.
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pertimbangan / Alasan Sidang</label>
                            <textarea name="alasan_catatan" rows="3"
                                      placeholder="Tuliskan catatan hasil evaluasi sidang pleno kelulusan..."
                                      class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">{{ old('alasan_catatan', $keputusan?->alasan_catatan) }}</textarea>
                        </div>

                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menetapkan keputusan ini?')"
                                class="w-full py-3 px-4 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-wider hover:bg-slate-800 transition-all shadow-md hover:shadow-lg cursor-pointer">
                            Simpan Keputusan Sidang
                        </button>
                    </form>
                </div>

                <!-- Quick Links Box -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Akses Cepat</span>
                    <a href="{{ route('kepala-sekolah.calon-siswa.show', $calonSiswa) }}"
                       class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-800 text-xs font-bold transition">
                        <span>🔍 Buka Tampilan Profil Penuh 360°</span>
                        <span>&rarr;</span>
                    </a>
                    @if ($keputusan)
                        <a href="{{ route('kepala-sekolah.sidang-kelulusan.cetak-sk', $calonSiswa) }}" target="_blank"
                           class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-800 text-xs font-bold transition">
                            <span>📄 Cetak Surat Keputusan (SK) PDF</span>
                            <span>&rarr;</span>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
