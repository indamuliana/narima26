<x-layouts.guest title="Formulir Pendaftaran Calon Murid Baru — SPMB Nampi SMK Wikrama 1 Garut">
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Header -->
            <div class="mb-8 text-center sm:text-left">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-orange-600 transition mb-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Pendaftaran SPMB 2027/2028</h1>
                <p class="mt-2 text-slate-600">Lengkapi formulir pendaftaran awal di bawah ini. Akun login calon siswa akan dibuat secara otomatis.</p>
            </div>

            <!-- Gelombang Info Banner -->
            @if($gelombangAktif)
                <div class="mb-8 bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-5 text-white shadow-md flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-orange-100">Gelombang Dibuka</div>
                        <div class="text-xl font-bold mt-0.5">{{ $gelombangAktif->nama_gelombang }} (T.P. {{ $gelombangAktif->tahun_ajaran }})</div>
                        <div class="text-xs text-orange-100 mt-1">
                            Periode: {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_mulai)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_selesai)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white text-orange-600 shadow-sm">
                        PENDAFTARAN DIBUKA
                    </span>
                </div>
            @endif

            <!-- Global Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="font-bold text-red-800 text-sm">Terdapat kesalahan pada data yang Anda masukkan:</span>
                    </div>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden"
                 x-data="{
                     referensiJenis: '{{ old('referensi_jenis', '') }}',
                     selectedProgram: '{{ old('program_id', $programs->first()?->id) }}'
                 }">
                <form action="{{ route('pendaftaran.store') }}" method="POST" class="p-6 sm:p-10 space-y-9">
                    @csrf

                    <!-- =============================================================== -->
                    <!-- 1. HIGHLIGHT PILIHAN PROGRAM PENDIDIKAN (PALING ATAS)           -->
                    <!-- =============================================================== -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-orange-50/70 via-white to-amber-50/70 border-2 border-orange-200/90 shadow-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-orange-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-black text-sm shadow-xs">★</div>
                                <div>
                                    <h2 class="text-base font-black text-slate-900 tracking-tight">Pilih Program Pendidikan</h2>
                                    <p class="text-xs text-slate-500">Tentukan jalur program pendidikan yang ingin Anda tempuh di SMK Wikrama 1 Garut</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-orange-100 text-orange-800 uppercase tracking-wider">
                                Wajib Dipilih
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                            @foreach($programs as $program)
                                @php
                                    $isDefaultChecked = old('program_id', $loop->first ? $program->id : '') == $program->id;
                                    $isUnggulan = str_contains(strtolower($program->nama_program ?? $program->nama), 'unggul');
                                @endphp
                                <label class="program-card group relative p-5 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between
                                              has-[:checked]:border-orange-500 has-[:checked]:bg-white has-[:checked]:ring-4 has-[:checked]:ring-orange-500/20 has-[:checked]:shadow-md
                                              border-slate-200 bg-white/70 hover:border-slate-300 hover:bg-white"
                                       :class="selectedProgram == '{{ $program->id }}' ? 'border-orange-500 bg-white ring-4 ring-orange-500/20 shadow-md' : 'border-slate-200 bg-white/70 hover:border-slate-300 hover:bg-white'">
                                    <input type="radio" name="program_id" id="program_{{ $program->id }}" value="{{ $program->id }}"
                                           x-model="selectedProgram"
                                           onchange="handleProgramChange('{{ $program->id }}')"
                                           {{ $isDefaultChecked ? 'checked' : '' }}
                                           required class="sr-only peer program-radio">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="program-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider transition
                                                         peer-checked:bg-orange-500 peer-checked:text-white bg-slate-100 text-slate-600"
                                                  :class="selectedProgram == '{{ $program->id }}' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600'">
                                                @if($isUnggulan)
                                                    ⭐ Program Unggulan
                                                @else
                                                    📘 Program Reguler
                                                @endif
                                            </span>
                                            <div class="program-check-circle w-6 h-6 rounded-full border-2 flex items-center justify-center transition
                                                        peer-checked:border-orange-500 peer-checked:bg-orange-500 peer-checked:text-white border-slate-300 text-transparent"
                                                 :class="selectedProgram == '{{ $program->id }}' ? 'border-orange-500 bg-orange-500 text-white' : 'border-slate-300 text-transparent'">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-black text-slate-900 mt-3">{{ $program->nama_program ?? $program->nama }}</h3>
                                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $program->keterangan }}</p>
                                    </div>
                                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-600">Jalur Pilihan</span>
                                        <span class="program-status font-bold transition peer-checked:text-orange-600 text-slate-400"
                                              :class="selectedProgram == '{{ $program->id }}' ? 'text-orange-600' : 'text-slate-400'"
                                              x-text="selectedProgram == '{{ $program->id }}' ? '✓ Terpilih' : 'Klik untuk memilih'">
                                            {{ $isDefaultChecked ? '✓ Terpilih' : 'Klik untuk memilih' }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('program_id') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- =============================================================== -->
                    <!-- 2. IDENTITAS CALON SISWA                                        -->
                    <!-- =============================================================== -->
                    <div>
                        <div class="flex items-center pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm mr-3">1</div>
                            <h2 class="text-lg font-bold text-slate-900">Identitas Calon Peserta Didik</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                            <div class="sm:col-span-2">
                                <label for="nisn" class="block text-sm font-semibold text-slate-700">NISN (Nomor Induk Siswa Nasional) <span class="text-red-500">*</span></label>
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" maxlength="10" placeholder="10 digit nomor NISN resmi dari Kemdikbud" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('nisn') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">NISN akan digunakan sebagai <strong>Username login</strong> portal SPMB Anda.</p>
                                @error('nisn') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="nama_lengkap" class="block text-sm font-semibold text-slate-700">Nama Lengkap (Sesuai Ijazah / Akta) <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Muhammad Rizki Pratama" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('nama_lengkap') border-red-500 @enderror">
                                @error('nama_lengkap') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <div class="mt-2.5 flex items-center space-x-6">
                                    <label class="inline-flex items-center text-sm text-slate-700 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', 'L') === 'L' ? 'checked' : '' }} required class="text-orange-500 focus:ring-orange-400">
                                        <span class="ml-2">Laki-laki</span>
                                    </label>
                                    <label class="inline-flex items-center text-sm text-slate-700 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') === 'P' ? 'checked' : '' }} required class="text-orange-500 focus:ring-orange-400">
                                        <span class="ml-2">Perempuan</span>
                                    </label>
                                </div>
                                @error('jenis_kelamin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="tempat_lahir" class="block text-sm font-semibold text-slate-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Garut" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('tempat_lahir') border-red-500 @enderror">
                                @error('tempat_lahir') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('tanggal_lahir') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Nomor Pendaftaran akan menjadi <strong>Password awal</strong> login akun Anda dan tercantum pada Kartu Peserta.</p>
                                @error('tanggal_lahir') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- =============================================================== -->
                    <!-- 3. KONTAK & KOMUNIKASI (WHATSAPP SISWA, AYAH, IBU, & EMAIL)    -->
                    <!-- =============================================================== -->
                    <div>
                        <div class="flex items-center pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm mr-3">2</div>
                            <h2 class="text-lg font-bold text-slate-900">Kontak & Komunikasi</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-5">
                            <div>
                                <label for="no_hp_siswa" class="block text-sm font-semibold text-slate-700">Nomor WhatsApp Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="no_hp_siswa" id="no_hp_siswa" value="{{ old('no_hp_siswa') }}" placeholder="Contoh: 081234567890" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('no_hp_siswa') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Nomor WhatsApp aktif milik calon siswa.</p>
                                @error('no_hp_siswa') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="no_hp_ayah" class="block text-sm font-semibold text-slate-700">Nomor WhatsApp Ayah <span class="text-red-500">*</span></label>
                                <input type="text" name="no_hp_ayah" id="no_hp_ayah" value="{{ old('no_hp_ayah') }}" placeholder="Contoh: 081398765432" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('no_hp_ayah') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Nomor WhatsApp ayah atau wali calon siswa.</p>
                                @error('no_hp_ayah') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="no_hp_ibu" class="block text-sm font-semibold text-slate-700">Nomor WhatsApp Ibu</label>
                                <input type="text" name="no_hp_ibu" id="no_hp_ibu" value="{{ old('no_hp_ibu') }}" placeholder="Contoh: 082198765432"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('no_hp_ibu') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Nomor WhatsApp ibu (opsional / cadangan).</p>
                                @error('no_hp_ibu') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email Aktif <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@gmail.com" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('email') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Alamat email aktif untuk menerima informasi akun login dan berkas bukti pendaftaran.</p>
                                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- =============================================================== -->
                    <!-- 4. PILIHAN JURUSAN & ASAL SEKOLAH                               -->
                    <!-- =============================================================== -->
                    <div>
                        <div class="flex items-center pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm mr-3">3</div>
                            <h2 class="text-lg font-bold text-slate-900">Kompetensi Keahlian & Asal Sekolah</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                            <div class="sm:col-span-2">
                                <label for="jurusan_id" class="block text-sm font-semibold text-slate-700">Kompetensi Keahlian (Jurusan) <span class="text-red-500">*</span></label>
                                <select name="jurusan_id" id="jurusan_id" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('jurusan_id') border-red-500 @enderror">
                                    <option value="">-- Pilih Jurusan Pilihan --</option>
                                    @foreach($jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('jurusan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="asal_sekolah_id" class="block text-sm font-semibold text-slate-700">Pilih dari Daftar Sekolah Terdata</label>
                                <select name="asal_sekolah_id" id="asal_sekolah_id"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                                    <option value="">-- Pilih Asal Sekolah --</option>
                                    @foreach($sekolahAsal as $sekolah)
                                        <option value="{{ $sekolah->id }}" {{ old('asal_sekolah_id') == $sekolah->id ? 'selected' : '' }}>
                                            {{ $sekolah->nama_sekolah }} ({{ $sekolah->kabupaten ?? 'Garut' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="asal_sekolah_lainnya" class="block text-sm font-semibold text-slate-700">Atau Ketik Nama Sekolah Jika Tidak Ada di Daftar</label>
                                <input type="text" name="asal_sekolah_lainnya" id="asal_sekolah_lainnya" value="{{ old('asal_sekolah_lainnya') }}" placeholder="Contoh: SMP Negeri 1 Tarogong Kidul"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                            </div>
                        </div>
                    </div>

                    <!-- =============================================================== -->
                    <!-- 5. REFERENSI / PROMOTOR (DROPDOWN BERSYARAT)                    -->
                    <!-- =============================================================== -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                        <div class="flex items-center pb-3 border-b border-slate-200">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mr-3">4</div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Referensi / Promotor Pendaftaran</h2>
                                <p class="text-xs text-slate-500">Dari mana Anda mengetahui informasi SPMB SMK Wikrama atau siapa yang mereferensikan Anda?</p>
                            </div>
                        </div>

                        <div>
                            <label for="referensi_jenis" class="block text-sm font-semibold text-slate-700">Pilih Kategori Referensi / Promotor</label>
                            <select name="referensi_jenis" id="referensi_jenis" x-model="referensiJenis"
                                onchange="handleReferensiChange(this.value)"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white @error('referensi_jenis') border-red-500 @enderror">
                                <option value="">-- Pilih Referensi / Promotor (Opsional) --</option>
                                <option value="GURU_WIKRAMA_GARUT" {{ old('referensi_jenis') === 'GURU_WIKRAMA_GARUT' ? 'selected' : '' }}>Guru SMK Wikrama 1 Garut</option>
                                <option value="GURU_WIKRAMA_BOGOR" {{ old('referensi_jenis') === 'GURU_WIKRAMA_BOGOR' ? 'selected' : '' }}>Guru SMK Wikrama Bogor</option>
                                <option value="SISWA_WIKRAMA_AKTIF" {{ old('referensi_jenis') === 'SISWA_WIKRAMA_AKTIF' ? 'selected' : '' }}>Siswa SMK Wikrama Aktif</option>
                                <option value="ALUMNI_WIKRAMA" {{ old('referensi_jenis') === 'ALUMNI_WIKRAMA' ? 'selected' : '' }}>Alumni SMK Wikrama</option>
                                <option value="CALON_SISWA_WIKRAMA" {{ old('referensi_jenis') === 'CALON_SISWA_WIKRAMA' ? 'selected' : '' }}>Calon Siswa Wikrama</option>
                                <option value="LAINNYA" {{ old('referensi_jenis') === 'LAINNYA' ? 'selected' : '' }}>Media Sosial / Brosur / Mandiri / Lainnya</option>
                            </select>
                            @error('referensi_jenis') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Sub-Form Bersyarat: Guru / Siswa / Alumni / Calon Siswa -->
                        @php
                            $initJenis = old('referensi_jenis', '');
                            $showSubform = in_array($initJenis, ['GURU_WIKRAMA_GARUT', 'GURU_WIKRAMA_BOGOR', 'SISWA_WIKRAMA_AKTIF', 'ALUMNI_WIKRAMA', 'CALON_SISWA_WIKRAMA']);
                        @endphp
                        <div id="referensi-subform"
                             x-show="['GURU_WIKRAMA_GARUT', 'GURU_WIKRAMA_BOGOR', 'SISWA_WIKRAMA_AKTIF', 'ALUMNI_WIKRAMA', 'CALON_SISWA_WIKRAMA'].includes(referensiJenis)"
                             class="pt-3 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-2 gap-4 {{ $showSubform ? '' : 'hidden' }}">
                            
                            <!-- Nomor Seleksi (Khusus Calon Siswa Wikrama) -->
                            <div id="referensi-nomor-seleksi-box"
                                 x-show="referensiJenis === 'CALON_SISWA_WIKRAMA'"
                                 class="sm:col-span-1 {{ $initJenis === 'CALON_SISWA_WIKRAMA' ? '' : 'hidden' }}">
                                <label for="referensi_nomor_seleksi" class="block text-sm font-semibold text-slate-700">Nomor Seleksi Calon Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="referensi_nomor_seleksi" id="referensi_nomor_seleksi" value="{{ old('referensi_nomor_seleksi') }}" placeholder="Contoh: A16260012"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('referensi_nomor_seleksi') border-red-500 @enderror">
                                @error('referensi_nomor_seleksi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Input Nama (Disesuaikan labelnya) -->
                            <div id="referensi-nama-box"
                                 :class="referensiJenis === 'SISWA_WIKRAMA_AKTIF' || referensiJenis === 'CALON_SISWA_WIKRAMA' ? 'sm:col-span-1' : 'sm:col-span-2'"
                                 class="{{ in_array($initJenis, ['SISWA_WIKRAMA_AKTIF', 'CALON_SISWA_WIKRAMA']) ? 'sm:col-span-1' : 'sm:col-span-2' }}">
                                <label id="referensi-nama-label" for="referensi_nama" class="block text-sm font-semibold text-slate-700">
                                    <span id="referensi-nama-title">
                                        @if($initJenis === 'GURU_WIKRAMA_GARUT') Nama Guru SMK Wikrama 1 Garut
                                        @elseif($initJenis === 'GURU_WIKRAMA_BOGOR') Nama Guru SMK Wikrama Bogor
                                        @elseif($initJenis === 'SISWA_WIKRAMA_AKTIF') Nama Siswa SMK Wikrama Aktif
                                        @elseif($initJenis === 'ALUMNI_WIKRAMA') Nama Alumni SMK Wikrama
                                        @elseif($initJenis === 'CALON_SISWA_WIKRAMA') Nama Calon Siswa Wikrama
                                        @else Nama Lengkap
                                        @endif
                                    </span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="referensi_nama" id="referensi_nama" value="{{ old('referensi_nama') }}"
                                    placeholder="Ketik nama lengkap..."
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('referensi_nama') border-red-500 @enderror">
                                @error('referensi_nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Dropdown Rayon (Khusus Siswa Aktif) -->
                            <div id="referensi-rayon-box"
                                 x-show="referensiJenis === 'SISWA_WIKRAMA_AKTIF'"
                                 class="sm:col-span-1 {{ $initJenis === 'SISWA_WIKRAMA_AKTIF' ? '' : 'hidden' }}">
                                <label for="referensi_rayon" class="block text-sm font-semibold text-slate-700">Rayon Siswa <span class="text-red-500">*</span></label>
                                <select name="referensi_rayon" id="referensi_rayon"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white @error('referensi_rayon') border-red-500 @enderror">
                                    <option value="">-- Pilih Rayon Siswa --</option>
                                    @foreach(['Ciawitali', 'Tarogong Kaler', 'Tarogong Kidul 1', 'Tarogong Kidul 2', 'Garut Kota 1', 'Garut Kota 2', 'Samarang', 'Leles', 'Kadungora', 'Bayongbong', 'Cilawu', 'Karangpawitan', 'Wanaraja', 'Cibatu', 'Limbangan', 'Banyuresmi', 'Cikajang', 'Cisurupan', 'Rayon Wikrama Bogor', 'Lainnya'] as $rayon)
                                        <option value="{{ $rayon }}" {{ old('referensi_rayon') == $rayon ? 'selected' : '' }}>Rayon {{ $rayon }}</option>
                                    @endforeach
                                </select>
                                @error('referensi_rayon') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Notice & Submit Button -->
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 text-center sm:text-left">
                            Dengan mengklik tombol Daftar Sekarang, data Anda akan didaftarkan ke sistem SPMB SMK Wikrama 1 Garut.
                        </div>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-lg shadow-orange-500/25 transition transform active:scale-95 cursor-pointer">
                            <span>Daftar Sekarang</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <!-- Script Pembantu Interaktivitas Responsif Dual-Layer (Alpine + Vanilla DOM) -->
    <script>
        function handleProgramChange(id) {
            // Memastikan sinkronisasi Alpine jika aktif
            try {
                const alpineScope = Alpine.$data(document.querySelector('[x-data]'));
                if (alpineScope) alpineScope.selectedProgram = String(id);
            } catch (e) {}

            // Sinkronisasi status teks pada masing-masing kartu
            document.querySelectorAll('.program-card').forEach(card => {
                const radio = card.querySelector('.program-radio');
                const statusEl = card.querySelector('.program-status');
                if (radio && radio.value == id) {
                    radio.checked = true;
                    if (statusEl) statusEl.textContent = '✓ Terpilih';
                } else {
                    if (statusEl) statusEl.textContent = 'Klik untuk memilih';
                }
            });
        }

        function handleReferensiChange(val) {
            // Memastikan sinkronisasi Alpine jika aktif
            try {
                const alpineScope = Alpine.$data(document.querySelector('[x-data]'));
                if (alpineScope) alpineScope.referensiJenis = String(val);
            } catch (e) {}

            const subform = document.getElementById('referensi-subform');
            const noSeleksiBox = document.getElementById('referensi-nomor-seleksi-box');
            const rayonBox = document.getElementById('referensi-rayon-box');
            const namaBox = document.getElementById('referensi-nama-box');
            const namaTitle = document.getElementById('referensi-nama-title');
            const namaInput = document.getElementById('referensi_nama');

            const allowed = ['GURU_WIKRAMA_GARUT', 'GURU_WIKRAMA_BOGOR', 'SISWA_WIKRAMA_AKTIF', 'ALUMNI_WIKRAMA', 'CALON_SISWA_WIKRAMA'];
            const showSub = allowed.includes(val);

            if (subform) {
                if (showSub) {
                    subform.classList.remove('hidden');
                } else {
                    subform.classList.add('hidden');
                }
            }

            if (noSeleksiBox) {
                if (val === 'CALON_SISWA_WIKRAMA') {
                    noSeleksiBox.classList.remove('hidden');
                } else {
                    noSeleksiBox.classList.add('hidden');
                }
            }

            if (rayonBox) {
                if (val === 'SISWA_WIKRAMA_AKTIF') {
                    rayonBox.classList.remove('hidden');
                } else {
                    rayonBox.classList.add('hidden');
                }
            }

            if (namaBox) {
                if (val === 'SISWA_WIKRAMA_AKTIF' || val === 'CALON_SISWA_WIKRAMA') {
                    namaBox.className = 'sm:col-span-1';
                } else {
                    namaBox.className = 'sm:col-span-2';
                }
            }

            if (namaTitle && namaInput) {
                if (val === 'GURU_WIKRAMA_GARUT') {
                    namaTitle.textContent = 'Nama Guru SMK Wikrama 1 Garut';
                    namaInput.placeholder = 'Ketik nama guru SMK Wikrama 1 Garut...';
                } else if (val === 'GURU_WIKRAMA_BOGOR') {
                    namaTitle.textContent = 'Nama Guru SMK Wikrama Bogor';
                    namaInput.placeholder = 'Ketik nama guru SMK Wikrama Bogor...';
                } else if (val === 'SISWA_WIKRAMA_AKTIF') {
                    namaTitle.textContent = 'Nama Siswa SMK Wikrama Aktif';
                    namaInput.placeholder = 'Ketik nama siswa aktif Wikrama...';
                } else if (val === 'ALUMNI_WIKRAMA') {
                    namaTitle.textContent = 'Nama Alumni SMK Wikrama';
                    namaInput.placeholder = 'Ketik nama alumni SMK Wikrama...';
                } else if (val === 'CALON_SISWA_WIKRAMA') {
                    namaTitle.textContent = 'Nama Calon Siswa Wikrama';
                    namaInput.placeholder = 'Ketik nama calon siswa Wikrama...';
                } else {
                    namaTitle.textContent = 'Nama Referensi / Promotor';
                    namaInput.placeholder = 'Ketik nama lengkap...';
                }
            }
        }

        // Jalankan sinkronisasi awal saat dokumen selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const refSelect = document.getElementById('referensi_jenis');
            if (refSelect && refSelect.value) {
                handleReferensiChange(refSelect.value);
            }
            const checkedProgram = document.querySelector('.program-radio:checked');
            if (checkedProgram) {
                handleProgramChange(checkedProgram.value);
            }
        });
    </script>
</x-layouts.guest>
