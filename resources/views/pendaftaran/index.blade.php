<x-layouts.guest title="Formulir Pendaftaran Calon Murid Baru — SPMB Nampi SMK Wikrama 1 Garut">
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Header -->
            <div class="mb-8 text-center sm:text-left">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-orange-600 transition mb-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Pendaftaran SPMB 2026/2027</h1>
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
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form action="{{ route('pendaftaran.store') }}" method="POST" class="p-6 sm:p-10 space-y-8">
                    @csrf

                    <!-- Bagian 1: Identitas Calon Siswa -->
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
                                <label for="nama_panggilan" class="block text-sm font-semibold text-slate-700">Nama Panggilan</label>
                                <input type="text" name="nama_panggilan" id="nama_panggilan" value="{{ old('nama_panggilan') }}" placeholder="Contoh: Rizki"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <div class="mt-2.5 flex items-center space-x-6">
                                    <label class="inline-flex items-center text-sm text-slate-700 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') === 'L' ? 'checked' : '' }} required class="text-orange-500 focus:ring-orange-400">
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

                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('tanggal_lahir') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Tanggal lahir akan menjadi <strong>Password awal</strong> login Anda (format: <code>ddmmyyyy</code>).</p>
                                @error('tanggal_lahir') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 2: Kontak & Komunikasi -->
                    <div>
                        <div class="flex items-center pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm mr-3">2</div>
                            <h2 class="text-lg font-bold text-slate-900">Kontak & Komunikasi</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                            <div>
                                <label for="no_hp_siswa" class="block text-sm font-semibold text-slate-700">Nomor WhatsApp Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="no_hp_siswa" id="no_hp_siswa" value="{{ old('no_hp_siswa') }}" placeholder="Contoh: 081234567890" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('no_hp_siswa') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Digunakan untuk informasi seleksi & notifikasi jadwal tes.</p>
                                @error('no_hp_siswa') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="no_hp_ayah" class="block text-sm font-semibold text-slate-700">Nomor WhatsApp Orang Tua / Wali</label>
                                <input type="text" name="no_hp_ayah" id="no_hp_ayah" value="{{ old('no_hp_ayah') }}" placeholder="Contoh: 082198765432"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('no_hp_ayah') border-red-500 @enderror">
                                @error('no_hp_ayah') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email (Opsional)</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@gmail.com"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('email') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Kosongkan jika belum memiliki email, sistem akan membuatkan email identitas pendaftar secara otomatis.</p>
                                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 3: Asal Sekolah -->
                    <div>
                        <div class="flex items-center pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mr-3">3</div>
                            <h2 class="text-lg font-bold text-slate-900">Asal Sekolah (SMP / MTs)</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
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

                    <!-- Bagian 4: Pilihan Program & Kompetensi Keahlian -->
                    <div>
                        <div class="flex items-center pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm mr-3">4</div>
                            <h2 class="text-lg font-bold text-slate-900">Pilihan Program & Kompetensi Keahlian</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                            <div>
                                <label for="program_id" class="block text-sm font-semibold text-slate-700">Program Pendidikan <span class="text-red-500">*</span></label>
                                <select name="program_id" id="program_id" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('program_id') border-red-500 @enderror">
                                    <option value="">-- Pilih Program --</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->nama_program }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="jurusan_id" class="block text-sm font-semibold text-slate-700">Kompetensi Keahlian (Jurusan) <span class="text-red-500">*</span></label>
                                <select name="jurusan_id" id="jurusan_id" required
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition @error('jurusan_id') border-red-500 @enderror">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach($jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('jurusan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
</x-layouts.guest>
