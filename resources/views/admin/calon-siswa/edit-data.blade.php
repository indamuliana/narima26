<x-layouts.app>
    <x-slot name="title">Edit Data Calon Murid — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

        <!-- Top Navigation & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.calon-siswa.show', $calonSiswa) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Detail Siswa</span>
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($calonSiswa->user)
                    <form action="{{ route('admin.calon-siswa.impersonate', $calonSiswa) }}" method="POST" class="inline m-0"
                          onsubmit="return confirm('Apakah Anda yakin ingin masuk sebagai calon siswa {{ $calonSiswa->nama_lengkap }}?');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-sm cursor-pointer"
                                title="Masuk ke portal siswa sebagai user ini">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Masuk Sebagai Siswa</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.calon-siswa.show', $calonSiswa) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Lihat Halaman Detail</span>
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
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

        <!-- Identity Banner -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        {{ $calonSiswa->nomor_pendaftaran }}
                    </span>
                    <span class="px-2 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700">
                        NISN: {{ $calonSiswa->nisn ?? '-' }}
                    </span>
                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Mode Administrator
                    </span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1.5">{{ $calonSiswa->nama_lengkap }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Jurusan: <strong class="text-slate-700">{{ $calonSiswa->jurusan?->nama ?? '-' }}</strong> &bull;
                    Status SPMB: <span class="font-bold text-indigo-600">{{ str_replace('_', ' ', $calonSiswa->status_spmb?->value ?? $calonSiswa->status_spmb) }}</span> &bull;
                    Kelengkapan Data: <strong class="text-emerald-600">{{ $completion['total_percent'] }}%</strong>
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-400 block">Status Data Siswa:</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold {{ $calonSiswa->status_data === 'LENGKAP' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                    {{ $calonSiswa->status_data ?? 'BELUM LENGKAP' }}
                </span>
            </div>
        </div>

        @php
            $currentTab = request('tab', $activeTab ?? 'biodata');
        @endphp

        <!-- Navigasi Tab Formulir -->
        <div class="flex overflow-x-auto border-b border-slate-200 bg-white rounded-2xl p-1.5 shadow-xs gap-1.5 text-xs font-bold scrollbar-none" id="tabsNav">
            <button type="button" onclick="switchTab('biodata')" id="tabBtn-biodata"
                class="tab-btn px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'biodata' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>1. Biodata & Pilihan</span>
                @if($completion['biodata']['is_complete'])
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                @endif
            </button>

            <button type="button" onclick="switchTab('orang_tua')" id="tabBtn-orang_tua"
                class="tab-btn px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'orang_tua' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>2. Orang Tua / Wali</span>
                @if($completion['orang_tua']['is_complete'])
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                @endif
            </button>

            <button type="button" onclick="switchTab('akademik')" id="tabBtn-akademik"
                class="tab-btn px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'akademik' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>3. Nilai Rapor & Prestasi</span>
                @if($completion['akademik']['is_complete'])
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                @endif
            </button>

            <button type="button" onclick="switchTab('kesehatan')" id="tabBtn-kesehatan"
                class="tab-btn px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'kesehatan' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>4. Kesehatan & Fisik</span>
                @if($completion['kesehatan']['is_complete'])
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                @endif
            </button>

            <button type="button" onclick="switchTab('seragam')" id="tabBtn-seragam"
                class="tab-btn px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'seragam' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>5. Ukuran Seragam</span>
                @if($completion['seragam']['is_complete'])
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                @endif
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: BIODATA & PILIHAN                   -->
        <!-- ========================================== -->
        <div id="tabContent-biodata" class="tab-pane {{ $currentTab === 'biodata' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Formulir Biodata Calon Siswa & Identitas</h3>
                    <p class="text-xs text-slate-500">Perubahan yang disimpan di sini akan langsung memperbarui database pendaftaran calon siswa.</p>
                </div>

                <form action="{{ route('admin.calon-siswa.update-biodata', $calonSiswa) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Pilihan Jurusan & NISN (Admin Control) -->
                    <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">Hak Akses Khusus Admin (Jurusan & Pendaftaran)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">NISN Siswa</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $calonSiswa->nisn) }}" maxlength="10"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pilihan Jurusan 1 <span class="text-red-500">*</span></label>
                                <select name="jurusan_id" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih Jurusan 1 --</option>
                                    @foreach($jurusanList as $j)
                                        <option value="{{ $j->id }}" {{ old('jurusan_id', $calonSiswa->jurusan_id) == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama ?? $j->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pilihan Jurusan 2</label>
                                <select name="jurusan_id_2"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Tidak Memilih Pilihan 2 --</option>
                                    @foreach($jurusanList as $j)
                                        <option value="{{ $j->id }}" {{ old('jurusan_id_2', $calonSiswa->jurusan_id_2) == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama ?? $j->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Identitas Dasar Siswa -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $calonSiswa->nama_lengkap) }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $calonSiswa->nama_panggilan) }}"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                <option value="L" {{ old('jenis_kelamin', $calonSiswa->jenis_kelamin) === 'L' || old('jenis_kelamin', $calonSiswa->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $calonSiswa->jenis_kelamin) === 'P' || old('jenis_kelamin', $calonSiswa->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" value="{{ old('nik', $calonSiswa->nik) }}" maxlength="16" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nomor Kartu Keluarga (16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" name="no_kk" value="{{ old('no_kk', $calonSiswa->no_kk) }}" maxlength="16" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Agama <span class="text-red-500">*</span></label>
                            <select name="agama" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                                    <option value="{{ $agm }}" {{ old('agama', $calonSiswa->agama) === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $calonSiswa->tempat_lahir) }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $calonSiswa->tanggal_lahir ? date('Y-m-d', strtotime($calonSiswa->tanggal_lahir)) : '') }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Asal Sekolah (SMP/MTs)</label>
                            <input type="text" name="asal_sekolah_lainnya" value="{{ old('asal_sekolah_lainnya', $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya) }}"
                                placeholder="Nama Sekolah Asal"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">No. WhatsApp Siswa</label>
                            <input type="text" name="no_hp_siswa" value="{{ old('no_hp_siswa', $calonSiswa->no_hp_siswa) }}"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Email Siswa</label>
                            <input type="email" name="email" value="{{ old('email', $calonSiswa->email) }}"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tahun Lulus SMP</label>
                            <input type="text" name="tahun_lulus" value="{{ old('tahun_lulus', $calonSiswa->tahun_lulus ?? date('Y')) }}" maxlength="4"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Anak Ke-</label>
                            <input type="number" name="anak_ke" value="{{ old('anak_ke', $calonSiswa->anak_ke) }}" min="1" max="20"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Jumlah Saudara</label>
                            <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $calonSiswa->jumlah_saudara) }}" min="0" max="20"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>
                    </div>

                    <!-- Alamat & Domisili -->
                    <div class="border-t border-slate-200 pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-800">Alamat Tempat Tinggal (Domisili)</span>
                            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl">
                                <button type="button" id="btnDomisiliDalam" onclick="setDomisiliMode(false)"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition {{ !$calonSiswa->is_luar_negeri ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-500' }}">
                                    Dalam Negeri
                                </button>
                                <button type="button" id="btnDomisiliLuar" onclick="setDomisiliMode(true)"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition {{ $calonSiswa->is_luar_negeri ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-500' }}">
                                    Luar Negeri
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="is_luar_negeri" id="input_is_luar_negeri" value="{{ old('is_luar_negeri', $calonSiswa->is_luar_negeri ? '1' : '0') }}">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Alamat Lengkap (Jalan, No. Rumah, Kampung) <span class="text-red-500">*</span></label>
                            <textarea name="alamat_lengkap" rows="2" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">{{ old('alamat_lengkap', $calonSiswa->alamat_lengkap) }}</textarea>
                        </div>

                        <!-- Container Wilayah Dalam Negeri -->
                        <div id="container_wilayah_dalam" class="{{ $calonSiswa->is_luar_negeri ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">RT <span class="text-red-500">*</span></label>
                                <input type="text" name="rt" id="input_rt" value="{{ old('rt', $calonSiswa->rt) }}" placeholder="001"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">RW <span class="text-red-500">*</span></label>
                                <input type="text" name="rw" id="input_rw" value="{{ old('rw', $calonSiswa->rw) }}" placeholder="005"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Kode Pos</label>
                                <input type="text" name="kode_pos" value="{{ old('kode_pos', $calonSiswa->kode_pos) }}" placeholder="44151"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Provinsi <span class="text-red-500">*</span></label>
                                <input type="text" name="provinsi_nama" id="input_provinsi" list="list_provinsi"
                                    value="{{ old('provinsi_nama', $calonSiswa->provinsi?->nama ?? $calonSiswa->provinsi_nama) }}" placeholder="Jawa Barat"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                                <datalist id="list_provinsi">
                                    @foreach($provinsi as $p)
                                        <option value="{{ $p->nama }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Kabupaten / Kota <span class="text-red-500">*</span></label>
                                <input type="text" name="kabupaten_nama" id="input_kabupaten"
                                    value="{{ old('kabupaten_nama', $calonSiswa->kabupaten?->nama ?? $calonSiswa->kabupaten_nama) }}" placeholder="Garut"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Kecamatan <span class="text-red-500">*</span></label>
                                <input type="text" name="kecamatan_nama" id="input_kecamatan"
                                    value="{{ old('kecamatan_nama', $calonSiswa->kecamatan?->nama ?? $calonSiswa->kecamatan_nama) }}" placeholder="Tarogong Kidul"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Desa / Kelurahan <span class="text-red-500">*</span></label>
                                <input type="text" name="desa_nama" id="input_desa"
                                    value="{{ old('desa_nama', $calonSiswa->desa?->nama ?? $calonSiswa->desa_nama) }}" placeholder="Sukagalih"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                            </div>
                        </div>

                        <!-- Container Wilayah Luar Negeri -->
                        <div id="container_wilayah_luar" class="{{ !$calonSiswa->is_luar_negeri ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Negara</label>
                                <input type="text" name="negara" id="input_negara" value="{{ old('negara', $calonSiswa->negara) }}" placeholder="Malaysia, Arab Saudi, dll."
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">State / Provinsi Luar Negeri</label>
                                <input type="text" name="provinsi_luar_negeri" id="input_provinsi_ln" value="{{ old('provinsi_luar_negeri', $calonSiswa->provinsi_luar_negeri) }}" placeholder="Selangor"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">City / Kabupaten Luar Negeri</label>
                                <input type="text" name="kabupaten_luar_negeri" id="input_kabupaten_ln" value="{{ old('kabupaten_luar_negeri', $calonSiswa->kabupaten_luar_negeri) }}" placeholder="Kuala Lumpur"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="submit" name="action" value="save"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan Biodata
                        </button>
                        <button type="submit" name="action" value="next"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan & Lanjut ke Orang Tua &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: DATA ORANG TUA / WALI               -->
        <!-- ========================================== -->
        <div id="tabContent-orang_tua" class="tab-pane {{ $currentTab === 'orang_tua' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Formulir Data Orang Tua & Wali</h3>
                    <p class="text-xs text-slate-500">Perbarui data identitas ayah, ibu, dan wali siswa.</p>
                </div>

                @php $ortu = $calonSiswa->dataOrangtua; @endphp

                <form action="{{ route('admin.calon-siswa.update-orang-tua', $calonSiswa) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- DATA AYAH -->
                    <div class="p-5 rounded-2xl bg-blue-50/50 border border-blue-200/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-900">👨 Data Ayah Kandung</span>
                            <div class="flex items-center gap-4 text-xs font-bold">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="status_ayah" value="MASIH_HIDUP" {{ old('status_ayah', $ortu?->status_ayah ?? 'MASIH_HIDUP') === 'MASIH_HIDUP' ? 'checked' : '' }} class="text-indigo-600">
                                    <span>Masih Hidup</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="status_ayah" value="WAFAT" {{ old('status_ayah', $ortu?->status_ayah) === 'WAFAT' ? 'checked' : '' }} class="text-indigo-600">
                                    <span>Wafat</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Ayah <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ortu?->nama_ayah) }}" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">NIK Ayah (16 Digit)</label>
                                <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $ortu?->nik_ayah) }}" maxlength="16"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">No. WhatsApp / HP Ayah</label>
                                <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah', $ortu?->no_hp_ayah ?? $calonSiswa->no_hp_ayah) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" list="pekerjaan_list" value="{{ old('pekerjaan_ayah', $ortu?->pekerjaan_ayah) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pendidikan Terakhir Ayah</label>
                                <select name="pendidikan_ayah" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['SD / Sederajat', 'SMP / Sederajat', 'SMA / SMK / Sederajat', 'D1/D2/D3', 'D4/S1', 'S2', 'S3', 'Tidak Sekolah'] as $pnd)
                                        <option value="{{ $pnd }}" {{ old('pendidikan_ayah', $ortu?->pendidikan_ayah) === $pnd ? 'selected' : '' }}>{{ $pnd }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penghasilan Bulanan Ayah</label>
                                <select name="penghasilan_ayah" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan'] as $ph)
                                        <option value="{{ $ph }}" {{ old('penghasilan_ayah', $ortu?->penghasilan_ayah) === $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- DATA IBU -->
                    <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-200/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-900">👩 Data Ibu Kandung</span>
                            <div class="flex items-center gap-4 text-xs font-bold">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="status_ibu" value="MASIH_HIDUP" {{ old('status_ibu', $ortu?->status_ibu ?? 'MASIH_HIDUP') === 'MASIH_HIDUP' ? 'checked' : '' }} class="text-indigo-600">
                                    <span>Masih Hidup</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="status_ibu" value="WAFAT" {{ old('status_ibu', $ortu?->status_ibu) === 'WAFAT' ? 'checked' : '' }} class="text-indigo-600">
                                    <span>Wafat</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Ibu <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ortu?->nama_ibu) }}" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">NIK Ibu (16 Digit)</label>
                                <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $ortu?->nik_ibu) }}" maxlength="16"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">No. WhatsApp / HP Ibu</label>
                                <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu', $ortu?->no_hp_ibu ?? $calonSiswa->no_hp_ibu) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" list="pekerjaan_list" value="{{ old('pekerjaan_ibu', $ortu?->pekerjaan_ibu) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pendidikan Terakhir Ibu</label>
                                <select name="pendidikan_ibu" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['SD / Sederajat', 'SMP / Sederajat', 'SMA / SMK / Sederajat', 'D1/D2/D3', 'D4/S1', 'S2', 'S3', 'Tidak Sekolah'] as $pnd)
                                        <option value="{{ $pnd }}" {{ old('pendidikan_ibu', $ortu?->pendidikan_ibu) === $pnd ? 'selected' : '' }}>{{ $pnd }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penghasilan Bulanan Ibu</label>
                                <select name="penghasilan_ibu" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan', 'Ibu Rumah Tangga'] as $ph)
                                        <option value="{{ $ph }}" {{ old('penghasilan_ibu', $ortu?->penghasilan_ibu) === $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- DATA WALI -->
                    <div class="p-5 rounded-2xl bg-purple-50/50 border border-purple-200/80 space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-900">🤝 Data Wali (Opsional / Jika Ada)</span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $ortu?->nama_wali) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Hubungan Wali dengan Siswa</label>
                                <input type="text" name="hubungan_wali" value="{{ old('hubungan_wali', $ortu?->hubungan_wali) }}" placeholder="Paman, Kakek, Kakak, dll."
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">No. WhatsApp / HP Wali</label>
                                <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali', $ortu?->no_hp_wali) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pekerjaan Wali</label>
                                <input type="text" name="pekerjaan_wali" list="pekerjaan_list" value="{{ old('pekerjaan_wali', $ortu?->pekerjaan_wali) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penghasilan Bulanan Wali</label>
                                <select name="penghasilan_wali" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan'] as $ph)
                                        <option value="{{ $ph }}" {{ old('penghasilan_wali', $ortu?->penghasilan_wali) === $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <datalist id="pekerjaan_list">
                        @foreach($pekerjaanList as $pk)
                            <option value="{{ $pk->nama }}"></option>
                        @endforeach
                    </datalist>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="submit" name="action" value="save"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan Data Orang Tua
                        </button>
                        <button type="submit" name="action" value="next"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan & Lanjut ke Nilai Rapor &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: NILAI RAPOR & PRESTASI              -->
        <!-- ========================================== -->
        <div id="tabContent-akademik" class="tab-pane {{ $currentTab === 'akademik' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Formulir Nilai Rapor SMP & Prestasi</h3>
                    <p class="text-xs text-slate-500">Masukkan nilai rapor semester 1 hingga semester 5 untuk mata pelajaran utama.</p>
                </div>

                @php
                    $akademik = $calonSiswa->dataAkademik;
                    $rapor = $calonSiswa->nilaiRapor;
                    $prestasiList = $calonSiswa->prestasi ?? collect();
                @endphp

                <form action="{{ route('admin.calon-siswa.update-akademik', $calonSiswa) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Matrix Nilai Rapor -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 font-extrabold border-b border-slate-200">
                                    <th class="p-3">Mata Pelajaran</th>
                                    <th class="p-3 text-center w-24">Sem 1</th>
                                    <th class="p-3 text-center w-24">Sem 2</th>
                                    <th class="p-3 text-center w-24">Sem 3</th>
                                    <th class="p-3 text-center w-24">Sem 4</th>
                                    <th class="p-3 text-center w-24">Sem 5</th>
                                    <th class="p-3 text-center w-28 bg-indigo-50 text-indigo-900">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <!-- Matematika -->
                                <tr>
                                    <td class="p-3 font-bold text-slate-800">Matematika</td>
                                    @for($i=1; $i<=5; $i++)
                                        <td class="p-2 text-center">
                                            <input type="number" step="0.01" min="0" max="100" id="mtk_sem{{ $i }}" name="mtk_sem{{ $i }}"
                                                value="{{ old('mtk_sem'.$i, $rapor?->{'mtk_sem'.$i}) }}"
                                                oninput="calculateMatrixRow('mtk')"
                                                class="w-20 text-center rounded-lg border border-slate-300 p-1.5 font-mono focus:border-indigo-600 outline-none">
                                        </td>
                                    @endfor
                                    <td class="p-3 text-center font-bold text-indigo-700 bg-indigo-50/50" id="avg_mtk">-</td>
                                </tr>

                                <!-- Bahasa Indonesia -->
                                <tr>
                                    <td class="p-3 font-bold text-slate-800">Bahasa Indonesia</td>
                                    @for($i=1; $i<=5; $i++)
                                        <td class="p-2 text-center">
                                            <input type="number" step="0.01" min="0" max="100" id="ind_sem{{ $i }}" name="ind_sem{{ $i }}"
                                                value="{{ old('ind_sem'.$i, $rapor?->{'ind_sem'.$i}) }}"
                                                oninput="calculateMatrixRow('ind')"
                                                class="w-20 text-center rounded-lg border border-slate-300 p-1.5 font-mono focus:border-indigo-600 outline-none">
                                        </td>
                                    @endfor
                                    <td class="p-3 text-center font-bold text-indigo-700 bg-indigo-50/50" id="avg_ind">-</td>
                                </tr>

                                <!-- Bahasa Inggris -->
                                <tr>
                                    <td class="p-3 font-bold text-slate-800">Bahasa Inggris</td>
                                    @for($i=1; $i<=5; $i++)
                                        <td class="p-2 text-center">
                                            <input type="number" step="0.01" min="0" max="100" id="eng_sem{{ $i }}" name="eng_sem{{ $i }}"
                                                value="{{ old('eng_sem'.$i, $rapor?->{'eng_sem'.$i}) }}"
                                                oninput="calculateMatrixRow('eng')"
                                                class="w-20 text-center rounded-lg border border-slate-300 p-1.5 font-mono focus:border-indigo-600 outline-none">
                                        </td>
                                    @endfor
                                    <td class="p-3 text-center font-bold text-indigo-700 bg-indigo-50/50" id="avg_eng">-</td>
                                </tr>

                                <!-- PAI -->
                                <tr>
                                    <td class="p-3 font-bold text-slate-800">Pendidikan Agama (PAI)</td>
                                    @for($i=1; $i<=5; $i++)
                                        <td class="p-2 text-center">
                                            <input type="number" step="0.01" min="0" max="100" id="pai_sem{{ $i }}" name="pai_sem{{ $i }}"
                                                value="{{ old('pai_sem'.$i, $rapor?->{'pai_sem'.$i}) }}"
                                                oninput="calculateMatrixRow('pai')"
                                                class="w-20 text-center rounded-lg border border-slate-300 p-1.5 font-mono focus:border-indigo-600 outline-none">
                                        </td>
                                    @endfor
                                    <td class="p-3 text-center font-bold text-indigo-700 bg-indigo-50/50" id="avg_pai">-</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-indigo-50 font-black text-slate-900 border-t border-slate-200">
                                    <td colspan="6" class="p-3 text-right uppercase tracking-wider text-xs">Rata-rata Keseluruhan Rapor:</td>
                                    <td class="p-3 text-center text-sm font-extrabold text-indigo-800" id="display_overall_avg">
                                        {{ $akademik?->nilai_rata_rata ?? '-' }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Hidden Inputs for backwards-compatibility -->
                    <input type="hidden" name="nilai_matematika" id="input_nilai_matematika" value="{{ old('nilai_matematika', $akademik?->nilai_matematika) }}">
                    <input type="hidden" name="nilai_bahasa_indonesia" id="input_nilai_bahasa_indonesia" value="{{ old('nilai_bahasa_indonesia', $akademik?->nilai_bahasa_indonesia) }}">
                    <input type="hidden" name="nilai_bahasa_inggris" id="input_nilai_bahasa_inggris" value="{{ old('nilai_bahasa_inggris', $akademik?->nilai_bahasa_inggris) }}">
                    <input type="hidden" name="nilai_rata_rata" id="input_nilai_rata_rata" value="{{ old('nilai_rata_rata', $akademik?->nilai_rata_rata) }}">

                    <!-- Prestasi Siswa -->
                    <div class="border-t border-slate-200 pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-800">🏆 Prestasi & Kejuaraan (Opsional)</span>
                            <button type="button" onclick="addPrestasiRow()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">
                                <span>+ Tambah Prestasi</span>
                            </button>
                        </div>

                        <div id="prestasiContainer" class="space-y-3">
                            @forelse($prestasiList as $idx => $pres)
                                <div class="prestasi-row p-4 rounded-2xl bg-slate-50 border border-slate-200 relative grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600">Jenis</label>
                                        <select name="prestasi[{{ $idx }}][jenis_prestasi]" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs bg-white">
                                            <option value="akademik" {{ $pres->jenis_prestasi === 'akademik' ? 'selected' : '' }}>Akademik</option>
                                            <option value="non-akademik" {{ $pres->jenis_prestasi === 'non-akademik' ? 'selected' : '' }}>Non-Akademik</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600">Nama Lomba / Prestasi</label>
                                        <input type="text" name="prestasi[{{ $idx }}][nama_prestasi]" value="{{ $pres->nama_prestasi }}" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs" required>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600">Tingkat</label>
                                        <select name="prestasi[{{ $idx }}][tingkat]" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs bg-white">
                                            @foreach(['sekolah', 'kecamatan', 'kabupaten/kota', 'provinsi', 'nasional', 'internasional'] as $tk)
                                                <option value="{{ $tk }}" {{ strtolower($pres->tingkat) === $tk ? 'selected' : '' }}>{{ ucfirst($tk) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600">Peringkat / Tahun</label>
                                        <input type="text" name="prestasi[{{ $idx }}][peringkat]" value="{{ $pres->peringkat }}" placeholder="Juara 1" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs">
                                        <input type="hidden" name="prestasi[{{ $idx }}][tahun]" value="{{ $pres->tahun ?? date('Y') }}">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" onclick="this.closest('.prestasi-row').remove()" class="w-full py-2 px-3 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p id="emptyPrestasiMsg" class="text-xs text-slate-400 italic">Belum ada data prestasi yang ditambahkan.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="submit" name="action" value="save"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan Nilai Rapor
                        </button>
                        <button type="submit" name="action" value="next"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan & Lanjut ke Kesehatan &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: KESEHATAN & FISIK                   -->
        <!-- ========================================== -->
        <div id="tabContent-kesehatan" class="tab-pane {{ $currentTab === 'kesehatan' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Formulir Data Kesehatan & Fisik Siswa</h3>
                    <p class="text-xs text-slate-500">Perbarui informasi fisik, riwayat penyakit, dan penglihatan calon siswa.</p>
                </div>

                @php $kes = $calonSiswa->dataKesehatan; @endphp

                <form action="{{ route('admin.calon-siswa.update-kesehatan', $calonSiswa) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tinggi Badan (cm)</label>
                            <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $kes?->tinggi_badan) }}" min="50" max="250" placeholder="Contoh: 165"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Berat Badan (kg)</label>
                            <input type="number" name="berat_badan" value="{{ old('berat_badan', $kes?->berat_badan) }}" min="10" max="200" placeholder="Contoh: 55"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Golongan Darah</label>
                            <select name="golongan_darah" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                <option value="">-- Tidak Tahu / Belum Cek --</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Tidak Tahu'] as $gd)
                                    <option value="{{ $gd }}" {{ old('golongan_darah', $kes?->golongan_darah) === $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Kondisi Buta Warna</label>
                            <select name="buta_warna" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                @foreach(['Tidak buta warna', 'Buta warna parsial', 'Buta warna'] as $bw)
                                    <option value="{{ $bw }}" {{ old('buta_warna', $kes?->buta_warna ?? 'Tidak buta warna') === $bw ? 'selected' : '' }}>{{ $bw }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Kesehatan Mata</label>
                            <input type="text" name="kesehatan_mata" value="{{ old('kesehatan_mata', $kes?->kesehatan_mata ?? 'Normal') }}" placeholder="Normal / Minus 1.5 / Silinder"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Riwayat Penyakit Pernah Diderita</label>
                            <select name="penyakit_pernah_diderita" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                @foreach(['Tidak ada', 'Asma', 'Tipes', 'Hepatitis', 'TBC', 'Penyakit jantung', 'Usus buntu', 'Diabetes', 'Lupus', 'Patah tulang', 'Lainnya'] as $pyk)
                                    <option value="{{ $pyk }}" {{ old('penyakit_pernah_diderita', $kes?->penyakit_pernah_diderita ?? 'Tidak ada') === $pyk ? 'selected' : '' }}>{{ $pyk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2 md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Penyakit yang Sedang Diderita / Catatan Khusus</label>
                            <input type="text" name="penyakit_sedang_diderita" value="{{ old('penyakit_sedang_diderita', $kes?->penyakit_sedang_diderita ?? 'Tidak ada') }}" placeholder="Tidak ada / Asma bila dingin"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none">
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="submit" name="action" value="save"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan Data Kesehatan
                        </button>
                        <button type="submit" name="action" value="next"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan & Lanjut ke Seragam &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 5: UKURAN SERAGAM                      -->
        <!-- ========================================== -->
        <div id="tabContent-seragam" class="tab-pane {{ $currentTab === 'seragam' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Formulir Ukuran Seragam Siswa</h3>
                    <p class="text-xs text-slate-500">Pilih ukuran seragam sekolah sesuai postur calon siswa.</p>
                </div>

                <form action="{{ route('admin.calon-siswa.update-seragam', $calonSiswa) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($seragamTypes as $namaJenis => $items)
                            @php
                                $firstItem = $items->first();
                                $currentUkuran = $chosenSeragam->get($firstItem->id)?->ukuran ?? null;
                            @endphp
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900">{{ $namaJenis }}</span>
                                    @if($firstItem->wajib)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Wajib</span>
                                    @endif
                                </div>
                                <input type="hidden" name="seragam[{{ $loop->index }}][jenis_seragam_id]" value="{{ $firstItem->id }}">
                                <select name="seragam[{{ $loop->index }}][ukuran]" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-indigo-600 focus:ring-1 focus:ring-indigo-200 outline-none bg-white">
                                    <option value="">-- Pilih Ukuran --</option>
                                    @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL', 'Custom'] as $sz)
                                        <option value="{{ $sz }}" {{ $currentUkuran === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="submit" name="action" value="save"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                            Simpan Ukuran Seragam
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script>
        // Tab Switcher
        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-600', 'hover:bg-slate-100');
            });

            const activeContent = document.getElementById('tabContent-' + tabId);
            const activeBtn = document.getElementById('tabBtn-' + tabId);

            if(activeContent) activeContent.classList.remove('hidden');
            if(activeBtn) {
                activeBtn.classList.remove('text-slate-600', 'hover:bg-slate-100');
                activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            }

            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        }

        // Domisili Mode Switcher
        function setDomisiliMode(isLuar) {
            const hiddenInput = document.getElementById('input_is_luar_negeri');
            const dalamContainer = document.getElementById('container_wilayah_dalam');
            const luarContainer = document.getElementById('container_wilayah_luar');
            const btnDalam = document.getElementById('btnDomisiliDalam');
            const btnLuar = document.getElementById('btnDomisiliLuar');

            if (!hiddenInput) return;
            hiddenInput.value = isLuar ? '1' : '0';

            const provInput = document.getElementById('input_provinsi');
            const kabInput = document.getElementById('input_kabupaten');
            const kecInput = document.getElementById('input_kecamatan');
            const desaInput = document.getElementById('input_desa');
            const rtInput = document.getElementById('input_rt');
            const rwInput = document.getElementById('input_rw');

            if (isLuar) {
                if (btnLuar) btnLuar.className = 'px-3 py-1 rounded-lg text-xs font-bold transition bg-white text-indigo-700 shadow-2xs';
                if (btnDalam) btnDalam.className = 'px-3 py-1 rounded-lg text-xs font-bold transition text-slate-500 hover:text-slate-800';

                if (dalamContainer) dalamContainer.classList.add('hidden');
                if (luarContainer) luarContainer.classList.remove('hidden');

                if (provInput) provInput.removeAttribute('required');
                if (kabInput) kabInput.removeAttribute('required');
                if (kecInput) kecInput.removeAttribute('required');
                if (desaInput) desaInput.removeAttribute('required');
                if (rtInput) rtInput.removeAttribute('required');
                if (rwInput) rwInput.removeAttribute('required');
            } else {
                if (btnDalam) btnDalam.className = 'px-3 py-1 rounded-lg text-xs font-bold transition bg-white text-indigo-700 shadow-2xs';
                if (btnLuar) btnLuar.className = 'px-3 py-1 rounded-lg text-xs font-bold transition text-slate-500 hover:text-slate-800';

                if (dalamContainer) dalamContainer.classList.remove('hidden');
                if (luarContainer) luarContainer.classList.add('hidden');

                if (provInput) provInput.setAttribute('required', 'required');
                if (kabInput) kabInput.setAttribute('required', 'required');
                if (kecInput) kecInput.setAttribute('required', 'required');
                if (desaInput) desaInput.setAttribute('required', 'required');
                if (rtInput) rtInput.setAttribute('required', 'required');
                if (rwInput) rwInput.setAttribute('required', 'required');
            }
        }

        // Prestasi Dynamic Rows
        let prestasiCount = {{ $prestasiList->count() ?? 0 }};
        function addPrestasiRow() {
            const container = document.getElementById('prestasiContainer');
            const emptyMsg = document.getElementById('emptyPrestasiMsg');
            if(emptyMsg) emptyMsg.remove();

            const idx = prestasiCount++;
            const row = document.createElement('div');
            row.className = 'prestasi-row p-4 rounded-2xl bg-slate-50 border border-slate-200 relative grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3';
            row.innerHTML = `
                <div>
                    <label class="block text-[11px] font-bold text-slate-600">Jenis</label>
                    <select name="prestasi[${idx}][jenis_prestasi]" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs bg-white">
                        <option value="akademik">Akademik</option>
                        <option value="non-akademik" selected>Non-Akademik</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600">Nama Lomba / Prestasi</label>
                    <input type="text" name="prestasi[${idx}][nama_prestasi]" placeholder="Contoh: Juara 1 Bulutangkis O2SN" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600">Tingkat</label>
                    <select name="prestasi[${idx}][tingkat]" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs bg-white">
                        <option value="sekolah">Sekolah</option>
                        <option value="kecamatan">Kecamatan</option>
                        <option value="kabupaten/kota" selected>Kabupaten/Kota</option>
                        <option value="provinsi">Provinsi</option>
                        <option value="nasional">Nasional</option>
                        <option value="internasional">Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600">Peringkat / Tahun</label>
                    <input type="text" name="prestasi[${idx}][peringkat]" placeholder="Juara 1" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs">
                    <input type="hidden" name="prestasi[${idx}][tahun]" value="${new Date().getFullYear()}">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.prestasi-row').remove()" class="w-full py-2 px-3 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition cursor-pointer">
                        Hapus
                    </button>
                </div>
            `;
            container.appendChild(row);
        }

        // Matrix Nilai Rapor calculation
        function calculateMatrixRow(prefix) {
            let sum = 0;
            let count = 0;
            for (let sem = 1; sem <= 5; sem++) {
                const el = document.getElementById(prefix + '_sem' + sem);
                if (el && el.value !== '' && !isNaN(el.value)) {
                    sum += parseFloat(el.value);
                    count++;
                }
            }

            const avgEl = document.getElementById('avg_' + prefix);
            const avg = count > 0 ? (sum / count).toFixed(2) : '-';
            if (avgEl) avgEl.textContent = avg;

            if (prefix === 'mtk') {
                const hiddenInput = document.getElementById('input_nilai_matematika');
                if (hiddenInput && count > 0) hiddenInput.value = avg;
            } else if (prefix === 'ind') {
                const hiddenInput = document.getElementById('input_nilai_bahasa_indonesia');
                if (hiddenInput && count > 0) hiddenInput.value = avg;
            } else if (prefix === 'eng') {
                const hiddenInput = document.getElementById('input_nilai_bahasa_inggris');
                if (hiddenInput && count > 0) hiddenInput.value = avg;
            }

            calculateOverallAverage();
        }

        function calculateOverallAverage() {
            const prefixes = ['mtk', 'ind', 'eng', 'pai'];
            let totalSum = 0;
            let totalCount = 0;

            prefixes.forEach(p => {
                for (let sem = 1; sem <= 5; sem++) {
                    const el = document.getElementById(p + '_sem' + sem);
                    if (el && el.value !== '' && !isNaN(el.value)) {
                        totalSum += parseFloat(el.value);
                        totalCount++;
                    }
                }
            });

            if (totalCount > 0) {
                const overallAvg = (totalSum / totalCount).toFixed(2);
                const displayEl = document.getElementById('display_overall_avg');
                const inputAvg = document.getElementById('input_nilai_rata_rata');
                if (displayEl) displayEl.textContent = overallAvg;
                if (inputAvg) inputAvg.value = overallAvg;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            ['mtk', 'ind', 'eng', 'pai'].forEach(p => calculateMatrixRow(p));
            const isLn = document.getElementById('input_is_luar_negeri')?.value === '1';
            setDomisiliMode(isLn);
        });
    </script>
</x-layouts.app>
