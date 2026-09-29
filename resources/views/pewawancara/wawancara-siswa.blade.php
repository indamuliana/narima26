<x-layouts.app>
    <x-slot name="title">Formulir Wawancara Siswa: {{ $calonSiswa->nama_lengkap }}</x-slot>

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
                    <span class="text-slate-600">Formulir Wawancara Siswa</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1">Formulir Wawancara Calon Siswa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Instrumen evaluasi tatap muka langsung untuk menggali kepribadian, akhlak, motivasi, dan kesiapan fisik calon murid.</p>
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
                        <span>SMP Asal: <strong class="text-slate-700">{{ $calonSiswa->asalSekolah?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</strong></span>
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
                <span class="text-xs font-bold text-slate-400">Status Wawancara Siswa:</span>
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
        @include('pewawancara.partials.panel-data-pembanding', ['defaultTab' => 'biodata'])

        <!-- FORMULIR WAWANCARA SISWA -->
        <form action="{{ route('pewawancara.wawancara.save-siswa', $calonSiswa) }}" method="POST" class="space-y-6">
            @csrf

            <!-- ==================================================== -->
            <!-- BAGIAN A: INFORMASI DASAR & LINGKUNGAN BELAJAR        -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">A. Informasi Dasar & Lingkungan Belajar</h3>
                        <p class="text-xs text-slate-500">Kondisi tempat tinggal dan penanggung jawab siswa selama bersekolah di SMK Wikrama.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap Siswa</label>
                        <input type="text" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm bg-slate-50 text-slate-600 font-medium outline-none cursor-not-allowed" value="{{ $calonSiswa->nama_lengkap }}" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Nama Panggilan</label>
                        <input type="text" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm bg-slate-50 text-slate-600 font-medium outline-none cursor-not-allowed" value="{{ $calonSiswa->nama_panggilan ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Selama Sekolah Tinggal Bersama <span class="text-red-500">*</span></label>
                        <input type="text" name="tinggal_bersama" value="{{ old('tinggal_bersama', $wawancara->tinggal_bersama) }}" placeholder="Contoh: Orang Tua / Kakek / Paman / Asrama" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Penanggung Jawab Selama Belajar <span class="text-red-500">*</span></label>
                        <input type="text" name="penanggung_jawab_belajar" value="{{ old('penanggung_jawab_belajar', $wawancara->penanggung_jawab_belajar) }}" placeholder="Contoh: Ayah / Ibu / Wali" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Mendapat Informasi Wikrama Dari <span class="text-red-500">*</span></label>
                        <input type="text" name="info_wikrama_dari" value="{{ old('info_wikrama_dari', $wawancara->info_wikrama_dari) }}" placeholder="Contoh: Guru BK SMP, Alumni, Media Sosial, Saudara, Brosur" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Bagaimana Kesan Terhadap Siswa Aktif Wikrama Saat Ini <span class="text-red-500">*</span></label>
                        <textarea name="kesan_siswa_aktif" rows="2" placeholder="Tuliskan kesan siswa mengenai kedisiplinan, keramahan, atau sikap siswa SMK Wikrama..." required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('kesan_siswa_aktif', $wawancara->kesan_siswa_aktif) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN B: KEAGAMAAN & ALASAN MEMILIH                 -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">B. Keagamaan & Alasan Memilih</h3>
                        <p class="text-xs text-slate-500">Pemahaman bacaan Al-Qur'an, hafalan, dan motivasi memilih SMK Wikrama 1 Garut.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Lancar Membaca Al-Qur'an? <span class="text-red-500">*</span></label>
                        <select name="baca_quran" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                            <option value="">-- Pilih Tingkat Kemampuan --</option>
                            <option value="Ya" {{ old('baca_quran', $wawancara->baca_quran) == 'Ya' ? 'selected' : '' }}>Ya (Lancar & Bertajwid)</option>
                            <option value="Kurang" {{ old('baca_quran', $wawancara->baca_quran) == 'Kurang' ? 'selected' : '' }}>Kurang (Bata-bata / Masih Belajar)</option>
                            <option value="Belum" {{ old('baca_quran', $wawancara->baca_quran) == 'Belum' ? 'selected' : '' }}>Belum Bisa Membaca</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Jumlah Hafalan Yang Dimiliki (Juz / Surat)</label>
                        <input type="text" name="hafalan_quran" value="{{ old('hafalan_quran', $wawancara->hafalan_quran) }}" placeholder="Contoh: Juz 30 (An-Naba s.d An-Nas) atau sebutkan surat"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Masuk Ke SMK Wikrama 1 Garut Karena <span class="text-red-500">*</span></label>
                        <textarea name="alasan_masuk_wikrama" rows="2" placeholder="Uraikan motivasi dasar calon siswa memilih bersekolah di Wikrama..." required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('alasan_masuk_wikrama', $wawancara->alasan_masuk_wikrama) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Alasan Memilih Program {{ $calonSiswa->programBelajar?->nama ?? 'Belajar' }} <span class="text-red-500">*</span></label>
                        <textarea name="alasan_pilih_program" rows="2" placeholder="Mengapa tertarik dengan program ini..." required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('alasan_pilih_program', $wawancara->alasan_pilih_program) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Alasan Memilih Jurusan {{ $calonSiswa->jurusan?->nama ?? 'Kompetensi Keahlian' }} <span class="text-red-500">*</span></label>
                        <textarea name="alasan_pilih_jurusan" rows="2" placeholder="Mengapa meminati bidang keahlian ini dan apa cita-cita ke depan..." required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('alasan_pilih_jurusan', $wawancara->alasan_pilih_jurusan) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN C: KESEHATAN & KEBIASAAN                      -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">C. Riwayat Kesehatan & Kebiasaan</h3>
                        <p class="text-xs text-slate-500">Kebugaran fisik, kebiasaan sehari-hari, dan catatan riwayat penyakit.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Kesibukan / Aktivitas Rutin di Luar Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" name="aktivitas_rutin" value="{{ old('aktivitas_rutin', $wawancara->aktivitas_rutin) }}" placeholder="Contoh: Mengaji sore, futsal, membantu orang tua berdagang" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Apakah Siswa Seorang Perokok? <span class="text-red-500">*</span></label>
                        <select name="merokok" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                            <option value="">-- Pilih Status --</option>
                            <option value="Tidak" {{ old('merokok', $wawancara->merokok ?? 'Tidak') == 'Tidak' ? 'selected' : '' }}>Tidak Pernah Merokok</option>
                            <option value="Pernah" {{ old('merokok', $wawancara->merokok) == 'Pernah' ? 'selected' : '' }}>Pernah Mencoba (Sudah Berhenti)</option>
                            <option value="Ya" {{ old('merokok', $wawancara->merokok) == 'Ya' ? 'selected' : '' }}>Ya (Aktif Merokok/Vape)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Penyakit Menahun yang Diderita</label>
                        <input type="text" name="penyakit_menahun" value="{{ old('penyakit_menahun', $wawancara->penyakit_menahun) }}" placeholder="Contoh: Asma, Sinusitis (kosongkan jika tidak ada)"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Kondisi Kesehatan Saat Ini <span class="text-red-500">*</span></label>
                        <input type="text" name="kondisi_kesehatan" value="{{ old('kondisi_kesehatan', $wawancara->kondisi_kesehatan ?? 'Sehat wal afiat') }}" placeholder="Contoh: Sehat / Pemulihan" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Disabilitas yang Dimiliki <span class="text-red-500">*</span></label>
                        <input type="text" name="disabilitas" value="{{ old('disabilitas', $wawancara->disabilitas ?? '-') }}" placeholder="Isi '-' jika tidak ada" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>

                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Alergi yang Dimiliki <span class="text-red-500">*</span></label>
                        <input type="text" name="alergi" value="{{ old('alergi', $wawancara->alergi ?? '-') }}" placeholder="Contoh: Udang, Dingin, Debu (isi '-' jika tidak ada)" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN D: OBSERVASI FISIK (INDIKATOR RAMBU)          -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6"
                 x-data="{
                     rambut: '{{ old('kerapihan_rambut', $wawancara->kerapihan_rambut ?? 'HIJAU') }}',
                     seragam: '{{ old('kerapihan_seragam', $wawancara->kerapihan_seragam ?? 'HIJAU') }}',
                     pendengaran: '{{ old('status_pendengaran', $wawancara->status_pendengaran ?? 'HIJAU') }}',
                     penglihatan: '{{ old('status_penglihatan', $wawancara->status_penglihatan ?? 'NORMAL') }}'
                 }">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">D. Observasi Fisik (Indikator Rambu)</h3>
                        <p class="text-xs text-slate-500">Indikator rambu: Hijau (Sesuai Standar), Oren (Perhatian/Pembinaan), Merah (Khusus/Bermasalah).</p>
                    </div>
                </div>

                <!-- Indikator Rambu 3 Kolom -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- 1. Kerapihan Rambut -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <label class="block text-xs font-bold text-slate-800 uppercase">1. Kerapihan Rambut</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="rambut === 'HIJAU' ? 'bg-emerald-50/80 border-emerald-400 ring-2 ring-emerald-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="kerapihan_rambut" value="HIJAU" x-model="rambut" class="text-emerald-600 focus:ring-emerald-500">
                                <div class="text-xs">
                                    <span class="font-extrabold text-emerald-800">🟢 HIJAU</span>
                                    <p class="text-[11px] text-slate-500">Rapi, sopan, warna alami</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="rambut === 'OREN' ? 'bg-amber-50/80 border-amber-400 ring-2 ring-amber-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="kerapihan_rambut" value="OREN" x-model="rambut" class="text-amber-500 focus:ring-amber-400">
                                <div class="text-xs">
                                    <span class="font-extrabold text-amber-800">🟡 OREN</span>
                                    <p class="text-[11px] text-slate-500">Agak panjang / perlu dipotong</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="rambut === 'MERAH' ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="kerapihan_rambut" value="MERAH" x-model="rambut" class="text-rose-600 focus:ring-rose-500">
                                <div class="text-xs">
                                    <span class="font-extrabold text-rose-800">🔴 MERAH</span>
                                    <p class="text-[11px] text-slate-500">Dicat / model ekstrem / gondrong</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Kerapihan Seragam -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <label class="block text-xs font-bold text-slate-800 uppercase">2. Kerapihan Pakaian / Seragam</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="seragam === 'HIJAU' ? 'bg-emerald-50/80 border-emerald-400 ring-2 ring-emerald-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="kerapihan_seragam" value="HIJAU" x-model="seragam" class="text-emerald-600 focus:ring-emerald-500">
                                <div class="text-xs">
                                    <span class="font-extrabold text-emerald-800">🟢 HIJAU</span>
                                    <p class="text-[11px] text-slate-500">Seragam SMP lengkap, bersih & rapi</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="seragam === 'OREN' ? 'bg-amber-50/80 border-amber-400 ring-2 ring-amber-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="kerapihan_seragam" value="OREN" x-model="seragam" class="text-amber-500 focus:ring-amber-400">
                                <div class="text-xs">
                                    <span class="font-extrabold text-amber-800">🟡 OREN</span>
                                    <p class="text-[11px] text-slate-500">Atribut kurang lengkap / lusuh</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="seragam === 'MERAH' ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="kerapihan_seragam" value="MERAH" x-model="seragam" class="text-rose-600 focus:ring-rose-500">
                                <div class="text-xs">
                                    <span class="font-extrabold text-rose-800">🔴 MERAH</span>
                                    <p class="text-[11px] text-slate-500">Bukan seragam / tidak sopan / sobek</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 3. Status Pendengaran -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <label class="block text-xs font-bold text-slate-800 uppercase">3. Respon & Pendengaran</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="pendengaran === 'HIJAU' ? 'bg-emerald-50/80 border-emerald-400 ring-2 ring-emerald-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="status_pendengaran" value="HIJAU" x-model="pendengaran" class="text-emerald-600 focus:ring-emerald-500">
                                <div class="text-xs">
                                    <span class="font-extrabold text-emerald-800">🟢 HIJAU</span>
                                    <p class="text-[11px] text-slate-500">Jelas, fokus, respon cepat</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="pendengaran === 'OREN' ? 'bg-amber-50/80 border-amber-400 ring-2 ring-amber-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="status_pendengaran" value="OREN" x-model="pendengaran" class="text-amber-500 focus:ring-amber-400">
                                <div class="text-xs">
                                    <span class="font-extrabold text-amber-800">🟡 OREN</span>
                                    <p class="text-[11px] text-slate-500">Harus diulang / volume agak pelan</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="pendengaran === 'MERAH' ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-300/30' : 'bg-white border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="status_pendengaran" value="MERAH" x-model="pendengaran" class="text-rose-600 focus:ring-rose-500">
                                <div class="text-xs">
                                    <span class="font-extrabold text-rose-800">🔴 MERAH</span>
                                    <p class="text-[11px] text-slate-500">Gangguan pendengaran signifikan</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 4. Status Penglihatan -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 uppercase">4. Status Penglihatan</label>
                            <p class="text-xs text-slate-500">Kondisi penglihatan calon siswa (berkacamata / minus / silinder).</p>
                        </div>
                        <div class="w-full sm:w-60">
                            <select id="status_penglihatan" name="status_penglihatan" x-model="penglihatan"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                <option value="NORMAL">Normal (Tanpa Kacamata)</option>
                                <option value="MINUS">Minus (Rabun Jauh)</option>
                                <option value="PLUS">Plus (Rabun Dekat)</option>
                                <option value="LAINNYA">Lainnya (Silinder / Buta Warna)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Mata Kiri & Kanan (Jika Minus atau Plus) -->
                    <div x-show="penglihatan === 'MINUS' || penglihatan === 'PLUS'" x-transition class="pt-3 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase">Ukuran Mata Kiri</label>
                            <input type="text" name="mata_kiri" value="{{ old('mata_kiri', $wawancara->mata_kiri) }}" placeholder="Contoh: -1.50 atau +0.75"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase">Ukuran Mata Kanan</label>
                            <input type="text" name="mata_kanan" value="{{ old('mata_kanan', $wawancara->mata_kanan) }}" placeholder="Contoh: -1.25 atau +0.50"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                        </div>
                    </div>

                    <!-- Input Keterangan Lainnya -->
                    <div x-show="penglihatan === 'LAINNYA'" x-transition class="pt-3 border-t border-slate-200/60">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase">Keterangan Khusus Penglihatan</label>
                        <input type="text" name="status_penglihatan_lainnya" value="{{ old('status_penglihatan_lainnya', $wawancara->status_penglihatan_lainnya) }}" placeholder="Contoh: Silinder 0.50, Buta Warna Parsial"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- BAGIAN E: CATATAN PEWAWANCARA & REKOMENDASI          -->
            <!-- ==================================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6"
                 x-data="{ rekomendasi: '{{ old('rekomendasi', $wawancara->rekomendasi ?? 'TERIMA') }}' }">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">E. Catatan Pewawancara & Rekomendasi</h3>
                            <p class="text-xs text-slate-500">Evaluasi menyeluruh untuk pertimbangan sidang pleno keputusan kelulusan SPMB.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1">
                        <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>RAHASIA / INTERNAL</span>
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Hal yang Perlu Diperhatikan dari Siswa</label>
                        <textarea name="hal_perhatian_khusus" rows="2" placeholder="Catatan kebiasaan, potensi kendala belajar, atau komitmen calon siswa..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('hal_perhatian_khusus', $wawancara->hal_perhatian_khusus) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Kesan dari Pewawancara</label>
                        <textarea name="kesan_pewawancara" rows="2" placeholder="Sikap, cara berkomunikasi, kesantunan, dan motivasi siswa..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('kesan_pewawancara', $wawancara->kesan_pewawancara) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Penting Pewawancara Lainnya</label>
                        <textarea name="catatan_pewawancara" rows="2" placeholder="Catatan tambahan yang perlu diketahui panitia sidang seleksi..."
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('catatan_pewawancara', $wawancara->catatan_pewawancara) }}</textarea>
                    </div>

                    <!-- Rekomendasi Keputusan -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Rekomendasi Keputusan Akhir Pewawancara <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-center gap-3 select-none"
                                :class="rekomendasi === 'TERIMA' ? 'bg-emerald-50/80 border-emerald-400 ring-2 ring-emerald-300/30 shadow-xs' : 'bg-slate-50 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="rekomendasi" value="TERIMA" x-model="rekomendasi" class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block text-sm font-black text-emerald-800">TERIMA</span>
                                    <span class="text-[11px] text-slate-500">Direkomendasikan lulus seleksi</span>
                                </div>
                            </label>

                            <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-center gap-3 select-none"
                                :class="rekomendasi === 'PERTIMBANGKAN' ? 'bg-amber-50/80 border-amber-400 ring-2 ring-amber-300/30 shadow-xs' : 'bg-slate-50 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="rekomendasi" value="PERTIMBANGKAN" x-model="rekomendasi" class="text-amber-500 focus:ring-amber-400">
                                <div>
                                    <span class="block text-sm font-black text-amber-800">PERTIMBANGKAN</span>
                                    <span class="text-[11px] text-slate-500">Diputuskan di sidang pleno</span>
                                </div>
                            </label>

                            <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-center gap-3 select-none"
                                :class="rekomendasi === 'TOLAK' ? 'bg-rose-50/80 border-rose-400 ring-2 ring-rose-300/30 shadow-xs' : 'bg-slate-50 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="rekomendasi" value="TOLAK" x-model="rekomendasi" class="text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="block text-sm font-black text-rose-800">TOLAK</span>
                                    <span class="text-[11px] text-slate-500">Tidak direkomendasikan lulus</span>
                                </div>
                            </label>
                        </div>
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
                        onclick="return confirm('Apakah Anda yakin ingin menyelesaikan penilaian wawancara siswa ini? Data akan disimpan secara permanen.')"
                        class="w-1/2 sm:w-auto px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Selesai & Simpan Permanen</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
