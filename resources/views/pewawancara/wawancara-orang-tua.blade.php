<x-layouts.app>
    <x-slot name="title">Formulir Wawancara Orang Tua: {{ $calonSiswa->nama_lengkap }}</x-slot>

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
                    <a href="{{ route('pewawancara.wawancara.hub', $calonSiswa) }}" class="hover:text-nampi-orange transition-colors">Hub Wawancara</a>
                    <span>/</span>
                    <span class="text-slate-600">Formulir Wawancara Orang Tua</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1">Formulir Wawancara Orang Tua / Wali</h1>
                <p class="text-xs text-slate-500 mt-0.5">Instrumen evaluasi tatap muka dengan orang tua/wali murid mengenai kesiapan komitmen, pembiayaan, dan pembinaan di rumah.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('pewawancara.wawancara.hub', $calonSiswa) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Hub Wawancara</span>
                </a>
            </div>
        </div>

        <!-- Flash Message Alerts -->
        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Perhatian">{{ session('error') }}</x-alert>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="font-bold mb-1">Terdapat kesalahan pada input formulir:</div>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Banner Profil Calon Siswa -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-600 text-white flex items-center justify-center font-black text-lg shadow-sm shrink-0">
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
                        <span>Asal SMP: <strong class="text-slate-700">{{ $calonSiswa->asalSekolah?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</strong></span>
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
                <span class="text-xs font-bold text-slate-400">Status Wawancara Orang Tua:</span>
                @if($wawancara->status === 'SELESAI')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        SELESAI DINILAI
                    </span>
                    @if($wawancara->tanggal_wawancara)
                        <span class="text-[11px] text-slate-400 font-mono">Tgl: {{ $wawancara->tanggal_wawancara->format('d/m/Y') }}</span>
                    @endif
                @elseif($wawancara->status === 'DRAFT')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300">
                        ✍️ DRAFT TERSIMPAN
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        ⏳ BELUM DIISI
                    </span>
                @endif
            </div>
        </div>

        <!-- Panel Data Pengisian Calon Siswa (Pembanding) -->
        @include('pewawancara.partials.panel-data-pembanding', ['defaultTab' => 'ortu'])

        <!-- FORMULIR WAWANCARA ORANG TUA -->
        <form action="{{ route('pewawancara.wawancara.save-orang-tua', $calonSiswa) }}" method="POST" class="space-y-6">
            @csrf

            <!-- ==================================================== -->
            <!-- BAGIAN A: IDENTITAS NARASUMBER                      -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-600"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">A. Identitas Narasumber Orang Tua / Wali</h3>
                        <p class="text-xs text-slate-500">Data orang tua atau wali yang hadir langsung mendampingi calon siswa pada sesi wawancara.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Nama Calon Siswa</label>
                        <input type="text" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm bg-slate-50 text-slate-600 font-medium outline-none cursor-not-allowed" value="{{ $calonSiswa->nama_lengkap }}" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Nama Yang Diwawancarai <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_diwawancarai" value="{{ old('nama_diwawancarai', $wawancara->nama_diwawancarai ?? $calonSiswa->dataOrangtua?->nama_ayah ?? $calonSiswa->dataOrangtua?->nama_ibu) }}" placeholder="Nama Bapak / Ibu / Wali yang hadir" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Hubungan Dengan Siswa <span class="text-red-500">*</span></label>
                        <select name="hubungan_dengan_siswa" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                            <option value="">-- Pilih Hubungan --</option>
                            @foreach(['Ayah', 'Ibu', 'Wali', 'Saudara', 'Kerabat'] as $rel)
                                <option value="{{ $rel }}" {{ old('hubungan_dengan_siswa', $wawancara->hubungan_dengan_siswa) == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN B: LINGKUNGAN & KEBIASAAN KELUARGA           -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">B. Lingkungan Belajar & Kebiasaan Siswa di Rumah</h3>
                        <p class="text-xs text-slate-500">Dukungan lingkungan keluarga, jarak tempat tinggal, dan kemandirian siswa.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Selama Sekolah Tinggal Bersama <span class="text-red-500">*</span></label>
                        <input type="text" name="tinggal_bersama" value="{{ old('tinggal_bersama', $wawancara->tinggal_bersama) }}" placeholder="Contoh: Orang Tua / Kakek / Paman" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Penanggung Jawab Belajar <span class="text-red-500">*</span></label>
                        <input type="text" name="penanggung_jawab_belajar" value="{{ old('penanggung_jawab_belajar', $wawancara->penanggung_jawab_belajar) }}" placeholder="Contoh: Ayah & Ibu / Paman" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Jarak dari Rumah ke Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" name="jarak_rumah" value="{{ old('jarak_rumah', $wawancara->jarak_rumah) }}" placeholder="Contoh: 3 km / 15 menit" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Transportasi yang Digunakan <span class="text-red-500">*</span></label>
                        <input type="text" name="transportasi" value="{{ old('transportasi', $wawancara->transportasi) }}" placeholder="Contoh: Sepeda motor, angkutan umum, diantar orang tua" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Mendapat Informasi Wikrama Dari <span class="text-red-500">*</span></label>
                        <input type="text" name="info_wikrama_dari" value="{{ old('info_wikrama_dari', $wawancara->info_wikrama_dari) }}" placeholder="Contoh: Media sosial, rekomendasi keluarga" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Kebiasaan Merapihkan Tempat Tidur <span class="text-red-500">*</span></label>
                        <select name="kebiasaan_tempat_tidur" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                            <option value="">-- Pilih Kebiasaan --</option>
                            <option value="Selalu" {{ old('kebiasaan_tempat_tidur', $wawancara->kebiasaan_tempat_tidur) == 'Selalu' ? 'selected' : '' }}>Selalu Mandiri</option>
                            <option value="Kadang-kadang" {{ old('kebiasaan_tempat_tidur', $wawancara->kebiasaan_tempat_tidur) == 'Kadang-kadang' ? 'selected' : '' }}>Kadang-kadang / Harus Diingatkan</option>
                            <option value="Tidak Pernah" {{ old('kebiasaan_tempat_tidur', $wawancara->kebiasaan_tempat_tidur) == 'Tidak Pernah' ? 'selected' : '' }}>Tidak Pernah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Hobi Siswa</label>
                        <input type="text" name="hobi" value="{{ old('hobi', $wawancara->hobi) }}" placeholder="Contoh: Menggambar, futsal, coding"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Cita-Cita Siswa</label>
                        <input type="text" name="cita_cita" value="{{ old('cita_cita', $wawancara->cita_cita) }}" placeholder="Contoh: Software Engineer, Desainer Grafis, Pengusaha"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN C: KEAGAMAAN & ALASAN MEMILIH                 -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">C. Keagamaan & Alasan Memilih Sekolah</h3>
                        <p class="text-xs text-slate-500">Perspektif orang tua mengenai pembiasaan ibadah anak dan alasan memilih SMK Wikrama.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Lancar Membaca Al-Qur'an? <span class="text-red-500">*</span></label>
                        <select name="baca_quran" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                            <option value="">-- Pilih --</option>
                            <option value="Ya" {{ old('baca_quran', $wawancara->baca_quran) == 'Ya' ? 'selected' : '' }}>Ya (Lancar)</option>
                            <option value="Kurang" {{ old('baca_quran', $wawancara->baca_quran) == 'Kurang' ? 'selected' : '' }}>Kurang (Masih Terbata)</option>
                            <option value="Belum" {{ old('baca_quran', $wawancara->baca_quran) == 'Belum' ? 'selected' : '' }}>Belum Bisa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Memiliki Hafalan Al-Qur'an (Juz / Surat)</label>
                        <input type="text" name="hafalan_quran" value="{{ old('hafalan_quran', $wawancara->hafalan_quran) }}" placeholder="Contoh: Juz 30 atau sebutkan surat"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Kesanggupan Infaq Rutin Bulanan</label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                                Rp
                            </span>
                            <input type="text" 
                                   id="infaq_rutin_bulanan" 
                                   name="infaq_rutin_bulanan" 
                                   value="{{ old('infaq_rutin_bulanan', $wawancara->formatted_infaq_rutin_bulanan ?? ($wawancara->infaq_rutin_bulanan ? number_format($wawancara->infaq_rutin_bulanan, 0, ',', '.') : '')) }}" 
                                   placeholder="0" 
                                   autocomplete="off"
                                   class="w-full rounded-xl border border-slate-300 pl-11 pr-3.5 py-2 text-sm font-semibold text-slate-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Nominal per bulan (otomatis format delimiter per 3 digit).</p>
                    </div>

                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Alasan Memilih SMK Wikrama 1 Garut <span class="text-red-500">*</span></label>
                        <textarea name="alasan_masuk_wikrama" rows="2" placeholder="Harapan dan alasan orang tua mendaftarkan anak ke SMK Wikrama 1 Garut..." required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('alasan_masuk_wikrama', $wawancara->alasan_masuk_wikrama) }}</textarea>
                    </div>

                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Alasan Memilih Program & Jurusan <span class="text-red-500">*</span></label>
                        <textarea name="alasan_pilih_program_jurusan" rows="2" placeholder="Dukungan orang tua terhadap program dan jurusan yang dipilih anak..." required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('alasan_pilih_program_jurusan', $wawancara->alasan_pilih_program_jurusan) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Minat Program (Menurut Ortu)</label>
                        <input type="text" name="minat_program" value="{{ old('minat_program', $wawancara->minat_program ?? $calonSiswa->programBelajar?->nama) }}" placeholder="Program yang disepakati orang tua"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Minat Jurusan (Menurut Ortu)</label>
                        <input type="text" name="minat_jurusan" value="{{ old('minat_jurusan', $wawancara->minat_jurusan ?? $calonSiswa->jurusan?->nama) }}" placeholder="Kompetensi keahlian yang disepakati"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN D: KESEHATAN SISWA                           -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">D. Riwayat Kesehatan Siswa (Informasi Orang Tua)</h3>
                        <p class="text-xs text-slate-500">Konfirmasi orang tua mengenai kebiasaan merokok, alergi, dan riwayat pengobatan anak.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Apakah Siswa Pernah / Aktif Merokok? <span class="text-red-500">*</span></label>
                        <select name="merokok" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                            <option value="">-- Pilih Status --</option>
                            <option value="Tidak" {{ old('merokok', $wawancara->merokok ?? 'Tidak') == 'Tidak' ? 'selected' : '' }}>Tidak Pernah Merokok</option>
                            <option value="Pernah" {{ old('merokok', $wawancara->merokok) == 'Pernah' ? 'selected' : '' }}>Pernah (Sudah Berhenti)</option>
                            <option value="Ya" {{ old('merokok', $wawancara->merokok) == 'Ya' ? 'selected' : '' }}>Ya (Aktif Merokok/Vape)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Memiliki Alergi Tertentu <span class="text-red-500">*</span></label>
                        <input type="text" name="alergi" value="{{ old('alergi', $wawancara->alergi ?? '-') }}" placeholder="Contoh: Dingin, obat antibiotik tertentu (isi '-' jika tidak ada)" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Penyakit yang Pernah / Sedang Diderita</label>
                        <textarea name="penyakit_diderita" rows="2" placeholder="Riwayat operasi, patah tulang, atau penyakit menahun (kosongkan jika tidak ada)..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('penyakit_diderita', $wawancara->penyakit_diderita) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN E: CATATAN ORANG TUA & PEWAWANCARA            -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">E. Catatan Orang Tua & Pewawancara</h3>
                            <p class="text-xs text-slate-500">Pesan orang tua serta evaluasi pewawancara terhadap dukungan keluarga.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1">
                        <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>RAHASIA / INTERNAL</span>
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Hal yang Perlu Diperhatikan dari Siswa (Menurut Orang Tua)</label>
                        <textarea name="hal_perhatian_ortu" rows="2" placeholder="Catatan orang tua mengenai sifat khusus anak atau bimbingan yang dibutuhkan..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('hal_perhatian_ortu', $wawancara->hal_perhatian_ortu) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Kesan Pewawancara Terhadap Orang Tua / Wali (Rahasia)</label>
                        <textarea name="kesan_pewawancara" rows="2" placeholder="Sikap kooperatif, keterbukaan, dan komitmen orang tua..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('kesan_pewawancara', $wawancara->kesan_pewawancara) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Tambahan Pewawancara (Rahasia)</label>
                        <textarea name="catatan_tambahan" rows="2" placeholder="Catatan khusus kesiapan biaya daftar ulang atau kendala keluarga..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('catatan_tambahan', $wawancara->catatan_tambahan) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sticky / Fixed Action Bottom Bar -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('pewawancara.wawancara.hub', $calonSiswa) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 order-2 sm:order-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Batal / Kembali ke Hub</span>
                </a>

                <div class="flex items-center gap-3 w-full sm:w-auto order-1 sm:order-2">
                    <button type="submit" name="action" value="draft"
                        class="w-1/2 sm:w-auto px-5 py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 shadow-xs transition text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Simpan Draft</span>
                    </button>

                    <button type="submit" name="action" value="selesai"
                        onclick="return confirm('Apakah Anda yakin ingin menyelesaikan penilaian wawancara orang tua ini? Data akan disimpan secara permanen.')"
                        class="w-1/2 sm:w-auto px-6 py-2.5 rounded-xl font-bold text-white bg-cyan-700 hover:bg-cyan-800 shadow-sm transition text-xs flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Selesai & Simpan Permanen</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const infaqInput = document.getElementById('infaq_rutin_bulanan');
            if (infaqInput) {
                function formatRupiah(val) {
                    if (!val) return '';
                    let clean = val.toString().replace(/\D/g, '');
                    if (!clean) return '';
                    return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                infaqInput.addEventListener('input', function () {
                    let cursorPosition = this.selectionStart;
                    let originalLength = this.value.length;
                    let formatted = formatRupiah(this.value);
                    this.value = formatted;
                    let newLength = formatted.length;
                    let diff = newLength - originalLength;
                    cursorPosition = cursorPosition + diff;
                    if (cursorPosition >= 0) {
                        this.setSelectionRange(cursorPosition, cursorPosition);
                    }
                });

                if (infaqInput.value) {
                    infaqInput.value = formatRupiah(infaqInput.value);
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
