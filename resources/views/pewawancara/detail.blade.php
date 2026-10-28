<x-layouts.app>
    <x-slot name="title">Hasil Wawancara — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Nav -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('pewawancara.riwayat') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    ← Kembali ke Riwayat
                </a>
                <h1 class="text-2xl font-black text-slate-800">Hasil Penilaian Wawancara</h1>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan evaluasi seleksi calon siswa dan orang tua secara menyeluruh.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pewawancara.wawancara.cetak-pdf', $calonSiswa) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-bold text-white transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Cetak PDF Hasil Wawancara</span>
                </a>
                <a href="{{ route('pewawancara.wawancara.hub', $calonSiswa) }}"
                   class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                    ✏️ Edit Penilaian
                </a>
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

        <!-- Candidate & Interview Overview Banner -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($calonSiswa->dokumenPendaftaran?->pas_foto_path && file_exists(public_path('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path)))
                        <img src="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path) }}"
                             alt="Pas Foto"
                             class="w-16 h-20 object-cover rounded-xl border-2 border-white/30 shadow-md shrink-0">
                    @else
                        <div class="w-16 h-20 rounded-xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-slate-400 shrink-0">
                            <span class="text-2xl">👤</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-cyan-300 font-bold">
                            {{ $calonSiswa->nomor_pendaftaran }}
                        </span>
                        <h2 class="text-xl font-black text-white mt-0.5">{{ $calonSiswa->nama_lengkap }}</h2>
                        <p class="text-xs text-slate-300 mt-0.5 font-mono">NISN: {{ $calonSiswa->nisn }} &bull; Asal: {{ $calonSiswa->sekolah_asal_text }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-400/30">
                                Pil 1: {{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}
                            </span>
                            @if($calonSiswa->jurusan2)
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-500/20 text-slate-300 border border-slate-400/30">
                                Pil 2: {{ $calonSiswa->jurusan2?->nama_jurusan ?? '-' }}
                            </span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-500/20 text-purple-300 border border-purple-400/30">
                                Program: {{ $calonSiswa->programBelajar?->nama_program ?? '-' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                                Beasiswa: {{ $calonSiswa->tag_beasiswa ?? 'Normal' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-pink-500/20 text-pink-300 border border-pink-400/30">
                                Jalur: {{ $calonSiswa->tag_jalur ?? 'Normal' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Status SPMB: {{ $calonSiswa->status_spmb instanceof \BackedEnum ? $calonSiswa->status_spmb->label() : $calonSiswa->status_spmb }}
                    </span>
                </div>
            </div>

            <!-- Scoring Breakdown Table -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                    <!-- ========================================== -->
                    <!-- KOLOM 1: DATA LENGKAP WAWANCARA SISWA       -->
                    <!-- ========================================== -->
                    <div class="border border-slate-200/80 rounded-2xl p-5 bg-white shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                                <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">
                                    Wawancara Calon Siswa
                                </h3>
                            </div>
                            @if($wawancaraSiswa?->status === 'SELESAI')
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                                    ✓ Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">
                                    Draft / Belum Selesai
                                </span>
                            @endif
                        </div>

                        @if($wawancaraSiswa)
                            <!-- Pewawancara & Tanggal -->
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Pewawancara</span>
                                    <span class="font-bold text-slate-800">{{ $wawancaraSiswa->pewawancara?->name ?? ($wawancaraSiswa->nama_petugas ?? '-') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Wawancara</span>
                                    <span class="font-bold text-slate-800">{{ $wawancaraSiswa->tanggal_wawancara ? $wawancaraSiswa->tanggal_wawancara->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                            </div>

                            <!-- A. Motivasi, Minat & Cita-Cita -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50/60 px-2.5 py-1 rounded-md mb-2">
                                    A. Motivasi, Minat & Cita-Cita
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Cita-Cita Siswa:</dt>
                                        <dd class="col-span-2 font-black text-indigo-900 bg-indigo-50/30 px-2 py-0.5 rounded border border-indigo-100">
                                            {{ $wawancaraSiswa->cita_cita ?: '-' }}
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Alasan Masuk Wikrama:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->alasan_masuk_wikrama ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Alasan Pilih Program:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->alasan_pilih_program ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Alasan Pilih Jurusan:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->alasan_pilih_jurusan ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Aktivitas Rutin Luar Sekolah:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->aktivitas_rutin ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- B. Keagamaan & Pembiasaan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50/60 px-2.5 py-1 rounded-md mb-2">
                                    B. Keagamaan & Pembiasaan
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Baca Al-Qur'an:</dt>
                                        <dd class="col-span-2 font-bold text-slate-800">{{ $wawancaraSiswa->baca_quran ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Hafalan Qur'an:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->hafalan_quran ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Tinggal Bersama:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->tinggal_bersama ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Penanggung Jawab Belajar:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->penanggung_jawab_belajar ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Info Wikrama dari:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->info_wikrama_dari ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Kesan Siswa Aktif:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->kesan_siswa_aktif ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- C. Kesehatan & Kebiasaan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-rose-700 bg-rose-50/60 px-2.5 py-1 rounded-md mb-2">
                                    C. Riwayat Kesehatan & Kebiasaan
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Status Merokok / Vape:</dt>
                                        <dd class="col-span-2 font-bold {{ $wawancaraSiswa->merokok === 'Ya' ? 'text-rose-600' : 'text-slate-800' }}">
                                            {{ $wawancaraSiswa->merokok ?: '-' }}
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Kondisi Kesehatan:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->kondisi_kesehatan ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Penyakit Menahun:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->penyakit_menahun ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Disabilitas Fisik:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->disabilitas ?: 'Tidak Ada' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Riwayat Alergi:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraSiswa->alergi ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- D. Observasi Fisik & Kedisiplinan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50/60 px-2.5 py-1 rounded-md mb-2">
                                    D. Observasi Fisik & Kedisiplinan
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Kerapihan Rambut:</dt>
                                        <dd class="col-span-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold text-white {{ $wawancaraSiswa->kerapihan_rambut == 'HIJAU' ? 'bg-emerald-500' : ($wawancaraSiswa->kerapihan_rambut == 'OREN' ? 'bg-amber-500' : 'bg-red-500') }}">
                                                {{ $wawancaraSiswa->kerapihan_rambut }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Kerapihan Seragam:</dt>
                                        <dd class="col-span-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold text-white {{ $wawancaraSiswa->kerapihan_seragam == 'HIJAU' ? 'bg-emerald-500' : ($wawancaraSiswa->kerapihan_seragam == 'OREN' ? 'bg-amber-500' : 'bg-red-500') }}">
                                                {{ $wawancaraSiswa->kerapihan_seragam }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Pendengaran:</dt>
                                        <dd class="col-span-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold text-white {{ $wawancaraSiswa->status_pendengaran == 'HIJAU' ? 'bg-emerald-500' : ($wawancaraSiswa->status_pendengaran == 'OREN' ? 'bg-amber-500' : 'bg-red-500') }}">
                                                {{ $wawancaraSiswa->status_pendengaran }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Penglihatan:</dt>
                                        <dd class="col-span-2 text-slate-800">
                                            <strong>{{ $wawancaraSiswa->status_penglihatan }}</strong>
                                            @if($wawancaraSiswa->mata_kiri || $wawancaraSiswa->mata_kanan)
                                                (Kiri: {{ $wawancaraSiswa->mata_kiri ?? '0' }}, Kanan: {{ $wawancaraSiswa->mata_kanan ?? '0' }})
                                            @endif
                                            @if($wawancaraSiswa->status_penglihatan_lainnya)
                                                &bull; {{ $wawancaraSiswa->status_penglihatan_lainnya }}
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- E. Evaluasi & Rekomendasi Pewawancara -->
                            <div class="pt-2 border-t border-slate-100 space-y-3">
                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Rekomendasi Pewawancara Siswa:</span>
                                    <div class="p-3 rounded-xl border {{ $wawancaraSiswa->rekomendasi == 'TERIMA' ? 'bg-emerald-50 border-emerald-200' : ($wawancaraSiswa->rekomendasi == 'PERTIMBANGKAN' ? 'bg-amber-50 border-amber-200' : 'bg-rose-50 border-rose-200') }}">
                                        <span class="text-base font-black {{ $wawancaraSiswa->rekomendasi == 'TERIMA' ? 'text-emerald-700' : ($wawancaraSiswa->rekomendasi == 'PERTIMBANGKAN' ? 'text-amber-700' : 'text-rose-700') }}">
                                            @if($wawancaraSiswa->rekomendasi == 'TERIMA')
                                                ✓ DIREKOMENDASIKAN DITERIMA
                                            @elseif($wawancaraSiswa->rekomendasi == 'PERTIMBANGKAN')
                                                ⚠ DIPERTIMBANGKAN
                                            @else
                                                ✕ TIDAK DIREKOMENDASIKAN
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Hal Perhatian Khusus:</span>
                                    <p class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                        {{ $wawancaraSiswa->hal_perhatian_khusus ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Kesan Pewawancara:</span>
                                    <p class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                        {{ $wawancaraSiswa->kesan_pewawancara ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Catatan Rahasia Pewawancara:</span>
                                    <p class="text-xs text-slate-800 bg-yellow-50 p-2.5 rounded-xl border border-yellow-200 font-mono">
                                        {{ $wawancaraSiswa->catatan_pewawancara ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-400 text-xs italic">
                                Belum ada data wawancara siswa.
                            </div>
                        @endif
                    </div>

                    <!-- ========================================== -->
                    <!-- KOLOM 2: DATA LENGKAP WAWANCARA ORANG TUA   -->
                    <!-- ========================================== -->
                    <div class="border border-slate-200/80 rounded-2xl p-5 bg-white shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                                <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">
                                    Wawancara Orang Tua / Wali
                                </h3>
                            </div>
                            @if($wawancaraOrangTua?->status === 'SELESAI')
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                                    ✓ Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">
                                    Draft / Belum Selesai
                                </span>
                            @endif
                        </div>

                        @if($wawancaraOrangTua)
                            <!-- Pewawancara & Tanggal -->
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Pewawancara</span>
                                    <span class="font-bold text-slate-800">{{ $wawancaraOrangTua->pewawancara?->name ?? ($wawancaraOrangTua->nama_petugas ?? '-') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Wawancara</span>
                                    <span class="font-bold text-slate-800">{{ $wawancaraOrangTua->tanggal_wawancara ? $wawancaraOrangTua->tanggal_wawancara->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                            </div>

                            <!-- A. Identitas Narasumber & Lingkungan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50/60 px-2.5 py-1 rounded-md mb-2">
                                    A. Identitas Narasumber & Lingkungan
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Narasumber Hadir:</dt>
                                        <dd class="col-span-2 font-bold text-slate-800">
                                            {{ $wawancaraOrangTua->nama_diwawancarai }} ({{ $wawancaraOrangTua->hubungan_dengan_siswa }})
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Tinggal Bersama:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->tinggal_bersama ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Jarak Rumah ke Sekolah:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->jarak_rumah ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Transportasi ke Sekolah:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->transportasi ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Penanggung Jawab Belajar:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->penanggung_jawab_belajar ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Info Wikrama dari:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->info_wikrama_dari ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- B. Kebiasaan & Karakter Anak -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md mb-2">
                                    B. Kebiasaan & Karakter Anak
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Rapihkan Tempat Tidur:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->kebiasaan_tempat_tidur ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Hobi Siswa:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->hobi ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Pernah Merokok:</dt>
                                        <dd class="col-span-2 font-bold {{ $wawancaraOrangTua->merokok === 'Ya' ? 'text-rose-600' : 'text-slate-800' }}">
                                            {{ $wawancaraOrangTua->merokok ?: '-' }}
                                        </dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Penyakit Diderita:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->penyakit_diderita ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Alergi Makanan/Obat:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->alergi ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- C. Keagamaan & Dukungan Pilihan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50/60 px-2.5 py-1 rounded-md mb-2">
                                    C. Keagamaan & Dukungan Pilihan
                                </h4>
                                <dl class="space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Baca Al-Qur'an:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->baca_quran ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Hafalan Qur'an:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->hafalan_quran ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Minat Program:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->minat_program ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Minat Jurusan:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->minat_jurusan ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Alasan Masuk Wikrama:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->alasan_masuk_wikrama ?: '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <dt class="text-slate-500">Alasan Pilih Program/Jur:</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $wawancaraOrangTua->alasan_pilih_program_jurusan ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- D. Komitmen Finansial & Catatan -->
                            <div class="pt-2 border-t border-slate-100 space-y-3">
                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Kesanggupan Infaq Rutin Bulanan:</span>
                                    <div class="p-3 rounded-xl border bg-emerald-50 border-emerald-200">
                                        <span class="text-lg font-black text-emerald-800">
                                            {{ $wawancaraOrangTua->infaq_rutin_bulanan !== null ? 'Rp ' . number_format($wawancaraOrangTua->infaq_rutin_bulanan, 0, ',', '.') . ' / bulan' : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Perhatian Khusus dari Orang Tua:</span>
                                    <p class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                        {{ $wawancaraOrangTua->hal_perhatian_ortu ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Kesan Pewawancara (Rahasia):</span>
                                    <p class="text-xs text-slate-700 bg-yellow-50 p-2.5 rounded-xl border border-yellow-200">
                                        {{ $wawancaraOrangTua->kesan_pewawancara ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500 block text-xs font-bold mb-1">Catatan Tambahan Pewawancara:</span>
                                    <p class="text-xs text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-200 font-mono">
                                        {{ $wawancaraOrangTua->catatan_tambahan ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-400 text-xs italic">
                                Belum ada data wawancara orang tua.
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
