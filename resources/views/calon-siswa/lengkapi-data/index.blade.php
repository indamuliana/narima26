<x-layouts.app>
    <x-slot name="title">Lengkapi Data & Dokumen Persyaratan</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

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

        <!-- Card Progress Kelengkapan Data -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-nampi-orange uppercase tracking-wider">Tahap 3 SPMB</span>
                    <h1 class="text-2xl font-black text-slate-900 mt-1">Kelengkapan Data & Berkas Persyaratan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong> &bull; Jurusan: <strong>{{ $calonSiswa->jurusan?->nama ?? '-' }}</strong>
                    </p>
                </div>
                <div class="text-right flex items-center md:flex-col md:items-end gap-3 md:gap-1">
                    <span class="text-xs font-bold text-slate-500">Status Data:</span>
                    @if($calonSiswa->status_data === 'LENGKAP')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            DATA LENGKAP
                        </span>
                    @else
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                            BELUM LENGKAP ({{ $completion['total_percent'] }}%)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                    <span>Kemajuan Pengisian Data</span>
                    <span class="text-nampi-orange">{{ $completion['total_percent'] }}% Selesai</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $completion['total_percent'] }}%"></div>
                </div>
            </div>

            <!-- Status Badges Checklist (Dapat diklik untuk beralih antar tahap) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-2">
                <button type="button" onclick="switchTab('biodata')" id="badgeStep-biodata"
                    class="step-badge-card text-left p-3 rounded-2xl border transition duration-150 cursor-pointer hover:shadow-xs {{ $completion['biodata']['is_complete'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>1. Biodata</span>
                        <span>{{ $completion['biodata']['is_complete'] ? '✓' : $completion['biodata']['percent'].'%' }}</span>
                    </div>
                    <p class="text-[11px] mt-1 text-slate-500">{{ $completion['biodata']['filled'] }}/{{ $completion['biodata']['total'] }} field</p>
                </button>

                <button type="button" onclick="switchTab('orang_tua')" id="badgeStep-orang_tua"
                    class="step-badge-card text-left p-3 rounded-2xl border transition duration-150 cursor-pointer hover:shadow-xs {{ $completion['orang_tua']['is_complete'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>2. Orang Tua</span>
                        <span>{{ $completion['orang_tua']['is_complete'] ? '✓' : $completion['orang_tua']['percent'].'%' }}</span>
                    </div>
                    <p class="text-[11px] mt-1 text-slate-500">{{ $completion['orang_tua']['filled'] }}/{{ $completion['orang_tua']['total'] }} field</p>
                </button>

                <button type="button" onclick="switchTab('akademik')" id="badgeStep-akademik"
                    class="step-badge-card text-left p-3 rounded-2xl border transition duration-150 cursor-pointer hover:shadow-xs {{ $completion['akademik']['is_complete'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>3. Akademik</span>
                        <span>{{ $completion['akademik']['is_complete'] ? '✓' : $completion['akademik']['percent'].'%' }}</span>
                    </div>
                    <p class="text-[11px] mt-1 text-slate-500">{{ $completion['akademik']['filled'] }}/{{ $completion['akademik']['total'] }} nilai</p>
                </button>

                <button type="button" onclick="switchTab('seragam')" id="badgeStep-seragam"
                    class="step-badge-card text-left p-3 rounded-2xl border transition duration-150 cursor-pointer hover:shadow-xs {{ $completion['seragam']['is_complete'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>4. Seragam</span>
                        <span>{{ $completion['seragam']['is_complete'] ? '✓' : $completion['seragam']['percent'].'%' }}</span>
                    </div>
                    <p class="text-[11px] mt-1 text-slate-500">{{ $completion['seragam']['filled'] }}/{{ $completion['seragam']['total'] }} jenis</p>
                </button>

                <button type="button" onclick="switchTab('dokumen')" id="badgeStep-dokumen"
                    class="step-badge-card text-left p-3 rounded-2xl border transition duration-150 cursor-pointer hover:shadow-xs {{ $completion['dokumen']['is_complete'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>5. Dokumen</span>
                        <span>{{ $completion['dokumen']['is_complete'] ? '✓' : $completion['dokumen']['percent'].'%' }}</span>
                    </div>
                    <p class="text-[11px] mt-1 text-slate-500">{{ $completion['dokumen']['filled'] }}/{{ $completion['dokumen']['total'] }} berkas</p>
                </button>

                <button type="button" onclick="switchTab('kesehatan')" id="badgeStep-kesehatan"
                    class="step-badge-card text-left p-3 rounded-2xl border transition duration-150 cursor-pointer hover:shadow-xs {{ $completion['kesehatan']['is_complete'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>6. Kesehatan</span>
                        <span>{{ $completion['kesehatan']['is_complete'] ? '✓' : $completion['kesehatan']['percent'].'%' }}</span>
                    </div>
                    <p class="text-[11px] mt-1 text-slate-500">{{ $completion['kesehatan']['filled'] }}/{{ $completion['kesehatan']['total'] }} data</p>
                </button>
            </div>

            <!-- Tombol Finalisasi Jika Sudah Lengkap -->
            @if($completion['is_all_complete'])
                <div id="card-finalisasi" class="p-5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm scroll-mt-6">
                    <div class="space-y-1">
                        <div class="flex items-center font-black text-base gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Semua Data & Dokumen Wajib Telah Lengkap 100%!</span>
                        </div>
                        <p class="text-xs text-emerald-100">
                            Silakan klik tombol finalisasi untuk mengunci kelengkapan data pendaftaran Anda dan melanjutkan ke tahap Kesepahaman & Wawancara.
                        </p>
                    </div>
                    <div>
                        <form action="{{ route('calon-siswa.lengkapi-data.finalize') }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin data dan berkas yang dimasukkan sudah benar?')"
                                class="inline-flex items-center px-6 py-3 rounded-xl font-black text-sm bg-white text-emerald-800 hover:bg-emerald-50 shadow-md transition transform active:scale-95 cursor-pointer whitespace-nowrap">
                                <span>Finalisasi Data Pendaftaran</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        @php
            $currentTab = request('tab', 'biodata');
        @endphp

        <!-- Navigasi Khusus Tampilan HP / Mobile (Pilihan Cepat & Stepper) -->
        <div class="block md:hidden bg-white rounded-2xl p-4 border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span id="mobileStepBadge" class="w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-black inline-flex items-center justify-center">1</span>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Tahap Aktif (1-6)</span>
                        <span id="mobileStepTitle" class="text-xs font-black text-slate-800">1. Biodata & Wilayah</span>
                    </div>
                </div>
                <div id="mobileStepStatusBadge">
                    @if($completion[$currentTab]['is_complete'] ?? false)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            ✓ Lengkap
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                            Belum Selesai
                        </span>
                    @endif
                </div>
            </div>

            <!-- Dropdown Pilihan Tahap Cepat -->
            <div class="relative">
                <label for="mobileTabSelect" class="sr-only">Pilih Tahap Formulir</label>
                <select id="mobileTabSelect" onchange="switchTab(this.value)"
                    class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 pr-10 text-xs font-bold text-slate-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none cursor-pointer">
                    <option value="biodata" {{ $currentTab === 'biodata' ? 'selected' : '' }}>
                        Tahap 1: Biodata & Domisili {{ $completion['biodata']['is_complete'] ? '(✓ Lengkap)' : '('.$completion['biodata']['percent'].'%)' }}
                    </option>
                    <option value="orang_tua" {{ $currentTab === 'orang_tua' ? 'selected' : '' }}>
                        Tahap 2: Data Orang Tua / Wali {{ $completion['orang_tua']['is_complete'] ? '(✓ Lengkap)' : '('.$completion['orang_tua']['percent'].'%)' }}
                    </option>
                    <option value="akademik" {{ $currentTab === 'akademik' ? 'selected' : '' }}>
                        Tahap 3: Akademik & Rapor {{ $completion['akademik']['is_complete'] ? '(✓ Lengkap)' : '('.$completion['akademik']['percent'].'%)' }}
                    </option>
                    <option value="seragam" {{ $currentTab === 'seragam' ? 'selected' : '' }}>
                        Tahap 4: Ukuran Seragam {{ $completion['seragam']['is_complete'] ? '(✓ Lengkap)' : '('.$completion['seragam']['percent'].'%)' }}
                    </option>
                    <option value="dokumen" {{ $currentTab === 'dokumen' ? 'selected' : '' }}>
                        Tahap 5: Unggah Berkas {{ $completion['dokumen']['is_complete'] ? '(✓ Lengkap)' : '('.$completion['dokumen']['percent'].'%)' }}
                    </option>
                    <option value="kesehatan" {{ $currentTab === 'kesehatan' ? 'selected' : '' }}>
                        Tahap 6: Data Kesehatan {{ $completion['kesehatan']['is_complete'] ? '(✓ Lengkap)' : '('.$completion['kesehatan']['percent'].'%)' }}
                    </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <!-- Tombol Navigasi Sebelumnya & Selanjutnya -->
            <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100">
                <button type="button" id="mobilePrevBtn" onclick="navPrevTab()"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Sebelumnya</span>
                </button>
                <button type="button" id="mobileNextBtn" onclick="navNextTab()"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 transition disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                    <span>Selanjutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <!-- Navigasi Tab Formulir (Horizontal Scrollable dengan Auto-Center) -->
        <div class="relative">
            <div class="flex overflow-x-auto border-b border-slate-200 bg-white rounded-2xl p-1.5 shadow-xs gap-1.5 text-xs font-bold scroll-smooth" id="tabsNav">
                <button type="button" onclick="switchTab('biodata')" id="tabBtn-biodata"
                    class="tab-btn shrink-0 px-3.5 sm:px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'biodata' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="tab-num w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $currentTab === 'biodata' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">1</span>
                    <span>Biodata & Wilayah</span>
                    @if($completion['biodata']['is_complete'])
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>

                <button type="button" onclick="switchTab('orang_tua')" id="tabBtn-orang_tua"
                    class="tab-btn shrink-0 px-3.5 sm:px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'orang_tua' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="tab-num w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $currentTab === 'orang_tua' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">2</span>
                    <span>Data Orang Tua / Wali</span>
                    @if($completion['orang_tua']['is_complete'])
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>

                <button type="button" onclick="switchTab('akademik')" id="tabBtn-akademik"
                    class="tab-btn shrink-0 px-3.5 sm:px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'akademik' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="tab-num w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $currentTab === 'akademik' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">3</span>
                    <span>Akademik & Rapor</span>
                    @if($completion['akademik']['is_complete'])
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>

                <button type="button" onclick="switchTab('seragam')" id="tabBtn-seragam"
                    class="tab-btn shrink-0 px-3.5 sm:px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'seragam' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="tab-num w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $currentTab === 'seragam' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">4</span>
                    <span>Ukuran Seragam</span>
                    @if($completion['seragam']['is_complete'])
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>

                <button type="button" onclick="switchTab('dokumen')" id="tabBtn-dokumen"
                    class="tab-btn shrink-0 px-3.5 sm:px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'dokumen' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="tab-num w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $currentTab === 'dokumen' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">5</span>
                    <span>Unggah Berkas</span>
                    @if($completion['dokumen']['is_complete'])
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>
                
                <button type="button" onclick="switchTab('kesehatan')" id="tabBtn-kesehatan"
                    class="tab-btn shrink-0 px-3.5 sm:px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $currentTab === 'kesehatan' ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="tab-num w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $currentTab === 'kesehatan' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">6</span>
                    <span>Kesehatan</span>
                    @if($completion['kesehatan']['is_complete'])
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: BIODATA & WILAYAH                   -->
        <!-- ========================================== -->
        <div id="tabContent-biodata" class="tab-pane {{ $currentTab === 'biodata' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Formulir Biodata Calon Siswa & Domisili</h3>
                    <p class="text-xs text-slate-500">Pastikan NIK dan data kependudukan sesuai dengan Kartu Keluarga terbaru.</p>
                </div>

                <form action="{{ route('calon-siswa.lengkapi-data.biodata') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Identitas Dasar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $calonSiswa->nama_lengkap) }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $calonSiswa->nama_panggilan) }}"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                <option value="L" {{ old('jenis_kelamin', $calonSiswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $calonSiswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" value="{{ old('nik', $calonSiswa->nik) }}" maxlength="16" placeholder="16 digit NIK di KTP/KIA/KK" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nomor Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                            <input type="text" name="no_kk" value="{{ old('no_kk', $calonSiswa->no_kk) }}" maxlength="16" placeholder="16 digit Nomor Kartu Keluarga" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Agama <span class="text-red-500">*</span></label>
                            <select name="agama" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                <option value="">-- Pilih Agama --</option>
                                @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                                    <option value="{{ $agm }}" {{ old('agama', $calonSiswa->agama) === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $calonSiswa->tempat_lahir) }}" placeholder="Contoh: Garut" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $calonSiswa->tanggal_lahir?->format('Y-m-d')) }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Anak Ke- <span class="text-red-500">*</span></label>
                            <input type="number" name="anak_ke" min="1" max="9" value="{{ old('anak_ke', $calonSiswa->anak_ke) }}" placeholder="1" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Dari Berapa Bersaudara <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah_saudara" min="1" max="9" value="{{ old('jumlah_saudara', $calonSiswa->jumlah_saudara) }}" placeholder="2" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tahun Lulus SMP/Sederajat <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun_lulus" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('tahun_lulus', $calonSiswa->tahun_lulus) }}" placeholder="Misal: 2026" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">No. Handphone Siswa (WA)</label>
                            <input type="text" name="no_hp_siswa" value="{{ old('no_hp_siswa', $calonSiswa->no_hp_siswa) }}" placeholder="Contoh: 08123456789"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>
                    </div>

                    <!-- Domisili & Wilayah Cascading -->
                    <div class="pt-6 border-t border-slate-100 space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Alamat Tempat Tinggal (Domisili)</h4>
                                <p class="text-xs text-slate-500">Pilih wilayah domisili untuk pendaftar dalam negeri atau luar negeri.</p>
                            </div>

                            <!-- Toggle Domisili Switcher -->
                            <div class="inline-flex rounded-2xl bg-slate-100 p-1 border border-slate-200 text-xs font-bold shrink-0 self-start sm:self-auto" id="domisiliToggleGroup">
                                <button type="button" onclick="setDomisiliMode(false)" id="btnDomisiliDalam"
                                    class="px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                                    <span>🇮🇩 Dalam Negeri</span>
                                </button>
                                <button type="button" onclick="setDomisiliMode(true)" id="btnDomisiliLuar"
                                    class="px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                                    <span>🌐 Luar Negeri (Isi Manual)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Hidden Input Status Luar Negeri -->
                        <input type="hidden" name="is_luar_negeri" id="input_is_luar_negeri" value="{{ old('is_luar_negeri', $calonSiswa->is_luar_negeri ? '1' : '0') }}">

                        <!-- ============================================== -->
                        <!-- 1. KONTEN WILAYAH DALAM NEGERI (INDONESIA)    -->
                        <!-- ============================================== -->
                        <div id="container_wilayah_dalam" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Provinsi (Input Manual dengan Saran Cepat Datalist) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Provinsi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="provinsi_nama" id="input_provinsi" list="list_provinsi"
                                        value="{{ old('provinsi_nama', $calonSiswa->nama_provinsi) }}"
                                        placeholder="Contoh: Jawa Barat" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                    <datalist id="list_provinsi">
                                        @foreach($provinsi as $p)
                                            <option value="{{ $p->nama }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Kabupaten/Kota (Input Manual) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Kabupaten / Kota <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="kabupaten_nama" id="input_kabupaten"
                                        value="{{ old('kabupaten_nama', $calonSiswa->nama_kabupaten) }}"
                                        placeholder="Contoh: Kabupaten Garut" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Kecamatan (Input Manual) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Kecamatan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="kecamatan_nama" id="input_kecamatan"
                                        value="{{ old('kecamatan_nama', $calonSiswa->nama_kecamatan) }}"
                                        placeholder="Contoh: Tarogong Kidul" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Desa / Kelurahan (Input Manual) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Desa / Kelurahan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="desa_nama" id="input_desa"
                                        value="{{ old('desa_nama', $calonSiswa->nama_desa) }}"
                                        placeholder="Contoh: Sukagalih" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- RT -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">RT <span class="text-red-500">*</span></label>
                                    <input type="text" name="rt" id="input_rt" value="{{ old('rt', $calonSiswa->rt) }}" placeholder="Contoh: 01"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- RW -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">RW <span class="text-red-500">*</span></label>
                                    <input type="text" name="rw" id="input_rw" value="{{ old('rw', $calonSiswa->rw) }}" placeholder="Contoh: 05"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Kode Pos -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Kode Pos</label>
                                    <input type="text" name="kode_pos" id="input_kode_pos" value="{{ old('kode_pos', $calonSiswa->kode_pos) }}" placeholder="5 digit kode pos"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- 2. KONTEN WILAYAH LUAR NEGERI (ISI MANUAL)    -->
                        <!-- ============================================== -->
                        <div id="container_wilayah_luar" class="space-y-4 hidden">
                            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <span class="font-extrabold text-blue-950">Formulir Alamat Luar Negeri:</span>
                                    <p class="mt-0.5 text-blue-800">
                                        Silakan ketik nama Negara, Provinsi / State, dan Kota tempat tinggal Anda saat ini. Kolom RT/RW tidak diwajibkan untuk calon siswa dari luar negeri.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <!-- Negara -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Nama Negara <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="negara" id="input_negara" value="{{ old('negara', $calonSiswa->negara ?? ($calonSiswa->is_luar_negeri ? '' : '')) }}" placeholder="Contoh: Malaysia, Arab Saudi, Jepang"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Provinsi / State / Wilayah Bagian -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Provinsi / State / Bagian <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="provinsi_luar_negeri" id="input_provinsi_ln" value="{{ old('provinsi_luar_negeri', $calonSiswa->provinsi_luar_negeri) }}" placeholder="Contoh: Selangor, Tokyo, Makkah"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Kabupaten / Kota / City -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Kota / Kabupaten <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="kabupaten_luar_negeri" id="input_kabupaten_ln" value="{{ old('kabupaten_luar_negeri', $calonSiswa->kabupaten_luar_negeri) }}" placeholder="Contoh: Petaling Jaya, Jeddah, Yokohama"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Distrik / Kecamatan (Opsional) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Kecamatan / Distrik <span class="text-xs font-normal text-slate-400 lowercase">(opsional)</span>
                                    </label>
                                    <input type="text" name="kecamatan_luar_negeri" id="input_kecamatan_ln" value="{{ old('kecamatan_luar_negeri', $calonSiswa->kecamatan_luar_negeri) }}" placeholder="Nama distrik/area"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Desa / Sub-distrik (Opsional) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Desa / Kelurahan / Sub-Distrik <span class="text-xs font-normal text-slate-400 lowercase">(opsional)</span>
                                    </label>
                                    <input type="text" name="desa_luar_negeri" id="input_desa_ln" value="{{ old('desa_luar_negeri', $calonSiswa->desa_luar_negeri) }}" placeholder="Nama kelurahan/suburb"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <!-- Postal / Zip Code -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">
                                        Kode Pos / Postal Code
                                    </label>
                                    <input type="text" name="kode_pos_ln" id="input_kode_pos_ln" value="{{ old('kode_pos', $calonSiswa->kode_pos) }}" placeholder="Contoh: 50450 / 90210" oninput="document.getElementById('input_kode_pos').value = this.value"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- 3. EMAIL & ALAMAT LENGKAP JALAN (BERSAMA)      -->
                        <!-- ============================================== -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                            <!-- Email -->
                            <div class="sm:col-span-2 md:col-span-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Email Siswa</label>
                                <input type="email" name="email" value="{{ old('email', $calonSiswa->email) }}" placeholder="email@contoh.com"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <!-- Alamat Jalan / Rumah -->
                            <div class="sm:col-span-2 md:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase">
                                    Alamat Jalan / Komplek / No. Rumah <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat_lengkap" rows="2" placeholder="Nama jalan, nomor rumah, nama gang/komplek, gedung/apartemen..." required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('alamat_lengkap', $calonSiswa->alamat_lengkap) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <button type="submit" name="action" value="save"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 shadow-xs transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Simpan Progress</span>
                        </button>
                        <button type="submit" name="action" value="next"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs cursor-pointer">
                            <span>Lanjutkan ke Tahap Berikutnya</span>
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: DATA ORANG TUA / WALI               -->
        <!-- ========================================== -->
        @php
            $ortu = $calonSiswa->dataOrangtua;
        @endphp
        <div id="tabContent-orang_tua" class="tab-pane {{ $currentTab === 'orang_tua' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Formulir Data Orang Tua & Wali</h3>
                    <p class="text-xs text-slate-500">Lengkapi data ayah kandung, ibu kandung, dan wali (jika ada).</p>
                </div>

                <form action="{{ route('calon-siswa.lengkapi-data.orang-tua') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- 1. DATA AYAH KANDUNG -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <h4 class="text-sm font-bold text-slate-900">Data Ayah Kandung</h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4" x-data="{ statusAyah: '{{ old('status_ayah', $ortu?->status_ayah ?? 'MASIH_HIDUP') }}' }">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Status Ayah <span class="text-red-500">*</span></label>
                                <select name="status_ayah" x-model="statusAyah" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                                    <option value="MASIH_HIDUP">Masih Hidup</option>
                                    <option value="WAFAT">Wafat</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2 md:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Ayah Kandung <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ortu?->nama_ayah) }}" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">NIK Ayah</label>
                                <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $ortu?->nik_ayah) }}" maxlength="16" placeholder="16 digit NIK"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Tahun Lahir Ayah</label>
                                <input type="number" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah', $ortu?->tahun_lahir_ayah) }}" placeholder="Contoh: 1978"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pekerjaan Ayah <span class="text-red-500">*</span></label>
                                <select name="pekerjaan_ayah_id" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaanList as $pek)
                                        <option value="{{ $pek->id }}" {{ old('pekerjaan_ayah_id', $ortu?->pekerjaan_ayah_id) == $pek->id ? 'selected' : '' }}>
                                            {{ $pek->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pendidikan Terakhir</label>
                                <select name="pendidikan_ayah"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3', 'Tidak Sekolah'] as $pend)
                                        <option value="{{ $pend }}" {{ old('pendidikan_ayah', $ortu?->pendidikan_ayah) === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penghasilan Bulanan</label>
                                <select name="penghasilan_ayah"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Kisaran --</option>
                                    @foreach(['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan'] as $penghasilan)
                                        <option value="{{ $penghasilan }}" {{ old('penghasilan_ayah', $ortu?->penghasilan_ayah) === $penghasilan ? 'selected' : '' }}>{{ $penghasilan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">No. HP / WhatsApp Ayah <span class="text-red-500">*</span></label>
                                <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah', $ortu?->no_hp_ayah ?? $calonSiswa->no_hp_ayah) }}" placeholder="08123456789" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div class="sm:col-span-2 md:col-span-4">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Alamat Domisili Ayah</label>
                                <input type="text" name="alamat_ayah" value="{{ old('alamat_ayah', $ortu?->alamat_ayah) }}" placeholder="Kosongkan jika sama dengan alamat siswa"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- 2. DATA IBU KANDUNG -->
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <h4 class="text-sm font-bold text-slate-900">Data Ibu Kandung</h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4" x-data="{ statusIbu: '{{ old('status_ibu', $ortu?->status_ibu ?? 'MASIH_HIDUP') }}' }">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Status Ibu <span class="text-red-500">*</span></label>
                                <select name="status_ibu" x-model="statusIbu" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white font-medium">
                                    <option value="MASIH_HIDUP">Masih Hidup</option>
                                    <option value="WAFAT">Wafat</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2 md:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Ibu Kandung <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ortu?->nama_ibu) }}" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">NIK Ibu</label>
                                <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $ortu?->nik_ibu) }}" maxlength="16" placeholder="16 digit NIK"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Tahun Lahir Ibu</label>
                                <input type="number" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu', $ortu?->tahun_lahir_ibu) }}" placeholder="Contoh: 1980"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pekerjaan Ibu <span class="text-red-500">*</span></label>
                                <select name="pekerjaan_ibu_id" required
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaanList as $pek)
                                        <option value="{{ $pek->id }}" {{ old('pekerjaan_ibu_id', $ortu?->pekerjaan_ibu_id) == $pek->id ? 'selected' : '' }}>
                                            {{ $pek->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pendidikan Terakhir</label>
                                <select name="pendidikan_ibu"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3', 'Tidak Sekolah'] as $pend)
                                        <option value="{{ $pend }}" {{ old('pendidikan_ibu', $ortu?->pendidikan_ibu) === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penghasilan Bulanan</label>
                                <select name="penghasilan_ibu"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Kisaran --</option>
                                    @foreach(['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan'] as $penghasilan)
                                        <option value="{{ $penghasilan }}" {{ old('penghasilan_ibu', $ortu?->penghasilan_ibu) === $penghasilan ? 'selected' : '' }}>{{ $penghasilan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">No. HP / WhatsApp Ibu</label>
                                <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu', $ortu?->no_hp_ibu ?? $calonSiswa->no_hp_ibu) }}" placeholder="08123456789"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div class="sm:col-span-2 md:col-span-4">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Alamat Domisili Ibu</label>
                                <input type="text" name="alamat_ibu" value="{{ old('alamat_ibu', $ortu?->alamat_ibu) }}" placeholder="Kosongkan jika sama dengan alamat siswa"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- 3. DATA WALI (OPSIONAL) -->
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <h4 class="text-sm font-bold text-slate-900">Data Wali (Opsional / Jika Tinggal Bersama Wali)</h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $ortu?->nama_wali) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Hubungan dengan Siswa</label>
                                <input type="text" name="hubungan_wali" value="{{ old('hubungan_wali', $ortu?->hubungan_wali) }}" placeholder="Contoh: Paman / Kakek"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pekerjaan Wali</label>
                                <select name="pekerjaan_wali_id"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaanList as $pek)
                                        <option value="{{ $pek->id }}" {{ old('pekerjaan_wali_id', $ortu?->pekerjaan_wali_id) == $pek->id ? 'selected' : '' }}>
                                            {{ $pek->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">No. HP Wali</label>
                                <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali', $ortu?->no_hp_wali) }}" placeholder="08123456789"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>

                            <div class="sm:col-span-2 md:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Alamat Domisili Wali</label>
                                <input type="text" name="alamat_wali" value="{{ old('alamat_wali', $ortu?->alamat_wali) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" onclick="switchTab('biodata')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 shadow-xs transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Biodata</span>
                        </button>
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="submit" name="action" value="save"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 shadow-xs transition text-xs cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Simpan Progress</span>
                            </button>
                            <button type="submit" name="action" value="next"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs cursor-pointer">
                                <span>Lanjutkan ke Tahap Berikutnya</span>
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: AKADEMIK & PRESTASI                 -->
        <!-- ========================================== -->
        @php
            $akademik = $calonSiswa->dataAkademik;
            $prestasiList = $calonSiswa->prestasi;
            $nilaiRapor = $calonSiswa->nilaiRapor;
        @endphp
        <div id="tabContent-akademik" class="tab-pane {{ $currentTab === 'akademik' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Formulir Data Akademik & Prestasi</h3>
                    <p class="text-xs text-slate-500">Masukkan nilai rapor semester 1 sampai 5 serta prestasi perlombaan jika ada.</p>
                </div>

                <form action="{{ route('calon-siswa.lengkapi-data.akademik') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Asal Sekolah -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nama Asal SMP / MTs</label>
                            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $akademik?->nama_sekolah ?? $calonSiswa->asalSekolah?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya) }}"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">NPSN Sekolah Asal</label>
                            <input type="text" name="npsn" value="{{ old('npsn', $akademik?->npsn ?? $calonSiswa->asalSekolah?->npsn) }}" placeholder="8 digit NPSN"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>
                    </div>

                    <!-- MATRIX NILAI RAPOR (SEMESTER 1 - 5) -->
                    <div class="space-y-3 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Matrix Nilai Rapor Semester 1 - 5</h4>
                                <p class="text-xs text-slate-500">Isikan nilai rapor skala 0 - 100 untuk tiap mata pelajaran dari Semester 1 sampai Semester 5.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 bg-orange-50 border border-orange-200 px-3 py-1.5 rounded-xl self-start sm:self-auto">
                                <span class="text-xs font-bold text-orange-800">Rata-rata Rapor:</span>
                                <span id="display_overall_avg" class="text-xs font-black text-orange-600 font-mono">{{ old('nilai_rata_rata', $akademik?->nilai_rata_rata ?? '-') }}</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-2xs">
                            <table class="w-full text-left text-xs border-collapse bg-white">
                                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 w-44">Mata Pelajaran</th>
                                        <th scope="col" class="py-3 px-2 text-center w-24">Semester 1</th>
                                        <th scope="col" class="py-3 px-2 text-center w-24">Semester 2</th>
                                        <th scope="col" class="py-3 px-2 text-center w-24">Semester 3</th>
                                        <th scope="col" class="py-3 px-2 text-center w-24">Semester 4</th>
                                        <th scope="col" class="py-3 px-2 text-center w-24">Semester 5</th>
                                        <th scope="col" class="py-3 px-3 text-center w-28 bg-orange-50/50 text-orange-800">Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <!-- 1. Matematika -->
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                                <span>Matematika</span>
                                            </div>
                                        </td>
                                        @for($sem = 1; $sem <= 5; $sem++)
                                            <td class="py-2 px-1.5">
                                                <input type="number" step="0.01" min="0" max="100" name="mtk_sem{{ $sem }}" id="mtk_sem{{ $sem }}"
                                                    value="{{ old('mtk_sem'.$sem, $nilaiRapor?->{'mtk_sem'.$sem}) }}" placeholder="0 - 100"
                                                    oninput="calculateMatrixRow('mtk')"
                                                    class="w-full text-center rounded-lg border border-slate-300 px-1 py-1.5 text-xs font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                            </td>
                                        @endfor
                                        <td class="py-2 px-3 text-center font-mono font-bold text-slate-700 bg-orange-50/30" id="avg_mtk">
                                            {{ old('nilai_matematika', $akademik?->nilai_matematika ?? '-') }}
                                        </td>
                                    </tr>

                                    <!-- 2. Indonesia -->
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                                <span>Indonesia</span>
                                            </div>
                                        </td>
                                        @for($sem = 1; $sem <= 5; $sem++)
                                            <td class="py-2 px-1.5">
                                                <input type="number" step="0.01" min="0" max="100" name="ind_sem{{ $sem }}" id="ind_sem{{ $sem }}"
                                                    value="{{ old('ind_sem'.$sem, $nilaiRapor?->{'ind_sem'.$sem}) }}" placeholder="0 - 100"
                                                    oninput="calculateMatrixRow('ind')"
                                                    class="w-full text-center rounded-lg border border-slate-300 px-1 py-1.5 text-xs font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                            </td>
                                        @endfor
                                        <td class="py-2 px-3 text-center font-mono font-bold text-slate-700 bg-orange-50/30" id="avg_ind">
                                            {{ old('nilai_bahasa_indonesia', $akademik?->nilai_bahasa_indonesia ?? '-') }}
                                        </td>
                                    </tr>

                                    <!-- 3. Bahasa Inggris -->
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                                <span>Bahasa Inggris</span>
                                            </div>
                                        </td>
                                        @for($sem = 1; $sem <= 5; $sem++)
                                            <td class="py-2 px-1.5">
                                                <input type="number" step="0.01" min="0" max="100" name="eng_sem{{ $sem }}" id="eng_sem{{ $sem }}"
                                                    value="{{ old('eng_sem'.$sem, $nilaiRapor?->{'eng_sem'.$sem}) }}" placeholder="0 - 100"
                                                    oninput="calculateMatrixRow('eng')"
                                                    class="w-full text-center rounded-lg border border-slate-300 px-1 py-1.5 text-xs font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                            </td>
                                        @endfor
                                        <td class="py-2 px-3 text-center font-mono font-bold text-slate-700 bg-orange-50/30" id="avg_eng">
                                            {{ old('nilai_bahasa_inggris', $akademik?->nilai_bahasa_inggris ?? '-') }}
                                        </td>
                                    </tr>

                                    <!-- 4. Pendidikan Agama -->
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                                <span>Pendidikan Agama</span>
                                            </div>
                                        </td>
                                        @for($sem = 1; $sem <= 5; $sem++)
                                            <td class="py-2 px-1.5">
                                                <input type="number" step="0.01" min="0" max="100" name="pai_sem{{ $sem }}" id="pai_sem{{ $sem }}"
                                                    value="{{ old('pai_sem'.$sem, $nilaiRapor?->{'pai_sem'.$sem}) }}" placeholder="0 - 100"
                                                    oninput="calculateMatrixRow('pai')"
                                                    class="w-full text-center rounded-lg border border-slate-300 px-1 py-1.5 text-xs font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                            </td>
                                        @endfor
                                        <td class="py-2 px-3 text-center font-mono font-bold text-slate-700 bg-orange-50/30" id="avg_pai">
                                            -
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- NILAI SUMMARY & TAMBAHAN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nilai Rata-Rata Rapor Akhir <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="nilai_rata_rata" id="input_nilai_rata_rata" value="{{ old('nilai_rata_rata', $akademik?->nilai_rata_rata) }}" min="0" max="100" placeholder="0 - 100" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-slate-50">
                            <p class="text-[11px] text-slate-400 mt-1">Terhitung otomatis saat matrix nilai diisi.</p>
                        </div>

                        <!-- Hidden synced values for data_akademik backward compatibility -->
                        <input type="hidden" name="nilai_matematika" id="input_nilai_matematika" value="{{ old('nilai_matematika', $akademik?->nilai_matematika) }}">
                        <input type="hidden" name="nilai_bahasa_indonesia" id="input_nilai_bahasa_indonesia" value="{{ old('nilai_bahasa_indonesia', $akademik?->nilai_bahasa_indonesia) }}">
                        <input type="hidden" name="nilai_bahasa_inggris" id="input_nilai_bahasa_inggris" value="{{ old('nilai_bahasa_inggris', $akademik?->nilai_bahasa_inggris) }}">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nilai IPA (Opsional)</label>
                            <input type="number" step="0.01" name="nilai_ipa" id="input_nilai_ipa" value="{{ old('nilai_ipa', $akademik?->nilai_ipa) }}" min="0" max="100" placeholder="0 - 100"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>



                        <div class="sm:col-span-2 md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Prestasi Akademik / Nilai Khusus</label>
                            <textarea name="catatan" rows="2" placeholder="Catatan ranking kelas atau keunggulan akademik tertentu..."
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">{{ old('catatan', $akademik?->catatan) }}</textarea>
                        </div>
                    </div>

                    <!-- PRESTASI NON-AKADEMIK & KEJUARAAN -->
                    <div class="pt-6 border-t border-slate-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Prestasi & Penghargaan (Opsional)</h4>
                                <p class="text-xs text-slate-500">Masukkan sertifikat kejuaraan sains, olahraga, seni, atau keagamaan.</p>
                            </div>
                            <button type="button" onclick="addPrestasiRow()"
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition cursor-pointer">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Prestasi</span>
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
                                        <input type="text" name="prestasi[{{ $idx }}][nama_prestasi]" value="{{ $pres->nama_prestasi }}" placeholder="Contoh: Juara 1 FLS2N Musik Tradisional" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600">Tingkat</label>
                                        <select name="prestasi[{{ $idx }}][tingkat]" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs bg-white">
                                            @foreach(['Sekolah', 'Kecamatan', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'] as $tk)
                                                <option value="{{ strtolower($tk) }}" {{ strtolower($pres->tingkat) === strtolower($tk) ? 'selected' : '' }}>{{ $tk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600">Peringkat / Tahun</label>
                                        <input type="text" name="prestasi[{{ $idx }}][peringkat]" value="{{ $pres->peringkat }}" placeholder="Juara 1 / 2025" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs">
                                        <input type="hidden" name="prestasi[{{ $idx }}][tahun]" value="{{ $pres->tahun ?? date('Y') }}">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" onclick="this.closest('.prestasi-row').remove()" class="w-full py-2 px-3 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p id="emptyPrestasiMsg" class="text-xs text-slate-400 italic">Belum ada prestasi yang ditambahkan. Klik "Tambah Prestasi" jika memiliki piagam perlombaan.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" onclick="switchTab('orang_tua')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 shadow-xs transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Data Orang Tua</span>
                        </button>
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="submit" name="action" value="save"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 shadow-xs transition text-xs cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Simpan Progress</span>
                            </button>
                            <button type="submit" name="action" value="next"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs cursor-pointer">
                                <span>Lanjutkan ke Tahap Berikutnya</span>
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: UKURAN & PEMILIHAN SERAGAM          -->
        <!-- ========================================== -->
        <div id="tabContent-seragam" class="tab-pane {{ $currentTab === 'seragam' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Formulir Pemilihan & Ukuran Seragam</h3>
                        <p class="text-xs text-slate-500">Pilih ukuran dan waktu pemesanan untuk setiap seragam sekolah. Anda dapat mencicil dengan memesan sebagian seragam sekarang dan memesan sisa seragam nanti.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200 shrink-0 self-start sm:self-auto">
                        👕 Pemesanan Seragam Awal
                    </span>
                </div>

                <!-- Panduan Ukuran Singkat -->
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="space-y-1">
                        <span class="font-bold">Panduan Ukuran (Standar SMK Wikrama):</span>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 font-mono text-[11px] text-amber-800">
                            <div><strong>S:</strong> LD 92cm &bull; PB 68cm</div>
                            <div><strong>M:</strong> LD 98cm &bull; PB 70cm</div>
                            <div><strong>L:</strong> LD 104cm &bull; PB 72cm</div>
                            <div><strong>XL:</strong> LD 110cm &bull; PB 74cm</div>
                            <div><strong>XXL:</strong> LD 116cm &bull; PB 76cm</div>
                        </div>
                        <p class="text-[11px] text-amber-700 italic pt-1">
                            * Seluruh ukuran seragam tetap wajib dipilih untuk keperluan arsip konveksi sekolah, meskipun Anda memilih "Pesan Nanti".
                        </p>
                    </div>
                </div>

                @php
                    $getItemPrice = function($nama) use ($biayaSeragamList) {
                        $found = $biayaSeragamList->first(function($b) use ($nama) {
                            return stripos($b->nama_biaya, $nama) !== false || stripos($nama, $b->nama_biaya) !== false;
                        });
                        return $found ? (float)$found->nominal : 0;
                    };

                    $klasterConfig = [
                        'MPLS' => [
                            'nama' => 'Klaster 1 prioritas',
                            'badge' => 'Prioritas 1: Wajib Utama',
                            'badge_color' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'desc' => 'Perlengkapan seragam utama siswa. Pilihan: Pesan Sekarang atau Pesan Nanti.',
                            'border' => 'border-orange-200 bg-orange-50/20',
                            'allow_tidak_pesan' => false,
                        ],
                        'KBM' => [
                            'nama' => 'Klaster 2',
                            'badge' => 'Prioritas 2',
                            'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'desc' => 'Seragam pembelajaran KBM reguler. Pilihan: Pesan Sekarang atau Pesan Nanti.',
                            'border' => 'border-blue-200 bg-blue-50/20',
                            'allow_tidak_pesan' => false,
                        ],
                        'OPSIONAL' => [
                            'nama' => 'Klaster 3',
                            'badge' => 'Pilihan: Pesan sekarang, Pesan nanti, Tidak Pesan',
                            'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'desc' => 'Perlengkapan seragam pelengkap. Anda dapat memilih Pesan sekarang, Pesan nanti, atau Tidak Pesan jika ingin beli mandiri di luar.',
                            'border' => 'border-slate-200 bg-slate-50/50',
                            'allow_tidak_pesan' => true,
                        ],
                    ];

                    // Urutkan klaster: MPLS, KBM, OPSIONAL
                    $orderedKlasters = ['MPLS', 'KBM', 'OPSIONAL'];
                    $groupedItems = [];
                    foreach ($orderedKlasters as $kKey) {
                        $groupedItems[$kKey] = [];
                    }

                    foreach ($seragamTypes as $namaJenis => $items) {
                        $first = $items->first();
                        $kKey = $first->klaster ?? ($first->wajib ? 'KBM' : 'OPSIONAL');
                        if (!isset($groupedItems[$kKey])) {
                            $groupedItems[$kKey] = [];
                        }
                        $groupedItems[$kKey][$namaJenis] = $items;
                    }
                @endphp

                <form action="{{ route('calon-siswa.lengkapi-data.seragam') }}" method="POST" class="space-y-8"
                      x-data="{
                          items: {},
                          register(idx, status, price) {
                              this.items[idx] = { status: status, price: price };
                          },
                          setStatus(idx, status) {
                              if (this.items[idx]) {
                                  this.items[idx].status = status;
                              }
                          },
                          setGroup(indices, status) {
                              indices.forEach(idx => {
                                  if (this.items[idx]) {
                                      this.items[idx].status = status;
                                  }
                              });
                          },
                          totalSekarang() {
                              return Object.values(this.items)
                                  .filter(i => i.status === 'PESAN_SEKARANG')
                                  .reduce((acc, curr) => acc + curr.price, 0);
                          },
                          countSekarang() {
                              return Object.values(this.items)
                                  .filter(i => i.status === 'PESAN_SEKARANG').length;
                          },
                          totalNanti() {
                              return Object.values(this.items)
                                  .filter(i => i.status === 'PESAN_NANTI')
                                  .reduce((acc, curr) => acc + curr.price, 0);
                          },
                          countNanti() {
                              return Object.values(this.items)
                                  .filter(i => i.status === 'PESAN_NANTI').length;
                          },
                          countTidakPesan() {
                              return Object.values(this.items)
                                  .filter(i => i.status === 'TIDAK_PESAN').length;
                          },
                          formatRupiah(val) {
                              return 'Rp ' + Number(val).toLocaleString('id-ID');
                          }
                      }">
                    @csrf

                    @php
                        $loopIndex = 0;
                    @endphp

                    @foreach($orderedKlasters as $kKey)
                        @if(!empty($groupedItems[$kKey]))
                            @php
                                $cfg = $klasterConfig[$kKey] ?? [
                                    'nama' => 'Klaster Seragam',
                                    'badge' => 'Seragam Sekolah',
                                    'badge_color' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'desc' => '',
                                    'border' => 'border-slate-200 bg-slate-50',
                                    'allow_tidak_pesan' => false,
                                ];
                                $allowTidakPesan = !empty($cfg['allow_tidak_pesan']);
                                $groupIndices = [];
                            @endphp

                            <div class="rounded-3xl border {{ $cfg['border'] }} p-5 sm:p-6 space-y-4">
                                <!-- Klaster Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-black text-slate-900">{{ $cfg['nama'] }}</h4>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $cfg['badge_color'] }}">
                                                {{ $cfg['badge'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $cfg['desc'] }}</p>
                                    </div>
                                </div>

                                <!-- Klaster Items -->
                                <div class="space-y-4">
                                    @foreach($groupedItems[$kKey] as $namaJenis => $items)
                                        @php
                                            $firstItem = $items->first();
                                            $availableSizes = $items->pluck('ukuran')->unique();
                                            $itemPrice = $getItemPrice($namaJenis);

                                            // Ambil entri tersimpan jika ada
                                            $existingEntry = null;
                                            foreach($items as $it) {
                                                if(isset($chosenSeragam[$it->id])) {
                                                    $existingEntry = $chosenSeragam[$it->id];
                                                    break;
                                                }
                                            }

                                            if ($existingEntry) {
                                                $selectedSize = $existingEntry->ukuran;
                                                $selectedItemId = $existingEntry->jenis_seragam_id;
                                                $initialStatus = $existingEntry->status_pemesanan ?? ($existingEntry->beli_di_sekolah ? 'PESAN_SEKARANG' : 'PESAN_NANTI');
                                            } else {
                                                $selectedSize = $availableSizes->first() ?? 'M';
                                                $selectedItemId = $firstItem->id;
                                                $initialStatus = ($kKey === 'OPSIONAL') ? 'PESAN_NANTI' : 'PESAN_SEKARANG';
                                            }

                                            $currentIndex = $loopIndex;
                                            $groupIndices[] = $currentIndex;
                                            $loopIndex++;
                                        @endphp

                                        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3.5 transition-all hover:border-slate-300"
                                             x-data="{
                                                 status: '{{ $initialStatus }}',
                                                 idx: {{ $currentIndex }},
                                                 price: {{ $itemPrice }}
                                             }"
                                             x-init="register(idx, status, price)">
                                            
                                            <!-- Top Line: Item Name & Price & Status Pills -->
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h5 class="text-sm font-bold text-slate-900">{{ $namaJenis }}</h5>
                                                        @if($itemPrice > 0)
                                                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-mono font-bold bg-slate-100 text-slate-700">
                                                                Rp {{ number_format($itemPrice, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                                        {{ $firstItem->keterangan ?? 'Standar seragam resmi SMK Wikrama 1 Garut' }}
                                                    </p>
                                                </div>

                                                <!-- Status Pemesanan Pills -->
                                                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                                    <label class="cursor-pointer">
                                                        <input type="radio" 
                                                               name="seragam[{{ $currentIndex }}][status_pemesanan]" 
                                                               value="PESAN_SEKARANG" 
                                                               x-model="status" 
                                                               @change="setStatus({{ $currentIndex }}, 'PESAN_SEKARANG')"
                                                               class="sr-only">
                                                        <div :class="status === 'PESAN_SEKARANG' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                                             class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5">
                                                            <span x-show="status === 'PESAN_SEKARANG'">✓</span>
                                                            <span>Pesan Sekarang</span>
                                                        </div>
                                                    </label>

                                                    <label class="cursor-pointer">
                                                        <input type="radio" 
                                                               name="seragam[{{ $currentIndex }}][status_pemesanan]" 
                                                               value="PESAN_NANTI" 
                                                               x-model="status" 
                                                               @change="setStatus({{ $currentIndex }}, 'PESAN_NANTI')"
                                                               class="sr-only">
                                                        <div :class="status === 'PESAN_NANTI' ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                                             class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5">
                                                            <span x-show="status === 'PESAN_NANTI'">⏳</span>
                                                            <span>Pesan Nanti</span>
                                                        </div>
                                                    </label>

                                                    @if($allowTidakPesan)
                                                        <label class="cursor-pointer">
                                                            <input type="radio" 
                                                                   name="seragam[{{ $currentIndex }}][status_pemesanan]" 
                                                                   value="TIDAK_PESAN" 
                                                                   x-model="status" 
                                                                   @change="setStatus({{ $currentIndex }}, 'TIDAK_PESAN')"
                                                                   class="sr-only">
                                                            <div :class="status === 'TIDAK_PESAN' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                                                 class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5">
                                                                <span x-show="status === 'TIDAK_PESAN'">✕</span>
                                                                <span>Tidak Pesan</span>
                                                            </div>
                                                        </label>
                                                    @endif

                                                    <!-- Hidden compatibility field -->
                                                    <input type="hidden" name="seragam[{{ $currentIndex }}][beli_di_sekolah]" :value="status === 'PESAN_SEKARANG' ? '1' : '0'">
                                                    <input type="hidden" name="seragam[{{ $currentIndex }}][jenis_seragam_id]" value="{{ $selectedItemId }}">
                                                </div>
                                            </div>

                                            <!-- Size Picker -->
                                            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-600 mr-1">Pilih Ukuran:</span>
                                                    @foreach($availableSizes as $size)
                                                        <label class="cursor-pointer">
                                                            <input type="radio" name="seragam[{{ $currentIndex }}][ukuran]" value="{{ $size }}" {{ ($selectedSize === $size) ? 'checked' : '' }} class="peer sr-only">
                                                            <div class="px-3.5 py-1.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 peer-checked:shadow-xs transition">
                                                                {{ $size }}
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>

                                                <div class="text-[11px] font-medium" :class="status === 'PESAN_SEKARANG' ? 'text-emerald-700' : (status === 'PESAN_NANTI' ? 'text-amber-700' : 'text-slate-500')">
                                                    <span x-show="status === 'PESAN_SEKARANG'">
                                                        &bull; Masuk ke Tagihan Seragam Tahap 1
                                                    </span>
                                                    <span x-show="status === 'PESAN_NANTI'">
                                                        &bull; Ukuran dicatat, pembayaran ditunda (bisa diaktifkan nanti)
                                                    </span>
                                                    <span x-show="status === 'TIDAK_PESAN'">
                                                        &bull; Tidak memesan di sekolah (membeli mandiri / sudah punya)
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Live Summary Estimasi Biaya Seragam -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-900 text-white shadow-md space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📊</span>
                                <div>
                                    <h4 class="text-sm font-black text-white">Ringkasan Estimasi Biaya Seragam</h4>
                                    <p class="text-xs text-slate-400">Total di bawah ini menyesuaikan secara langsung dengan pilihan Anda di atas.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-1">
                                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-bold">1. Pesan Sekarang:</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xl sm:text-2xl font-black text-emerald-400" x-text="formatRupiah(totalSekarang())"></span>
                                    <span class="text-xs text-slate-400" x-text="'(' + countSekarang() + ' item)'"></span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Item ini akan diterbitkan dalam tagihan seragam awal Anda.</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-1">
                                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-bold">2. Pesan Nanti:</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xl sm:text-2xl font-black text-amber-400" x-text="formatRupiah(totalNanti())"></span>
                                    <span class="text-xs text-slate-400" x-text="'(' + countNanti() + ' item)'"></span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Dapat Anda aktifkan dan pesan sewaktu-waktu sebelum KBM dimulai.</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-1">
                                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-bold">3. Tidak Pesan:</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xl sm:text-2xl font-black text-slate-300" x-text="countTidakPesan() + ' item'"></span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Item tidak dipesan di sekolah (membeli mandiri di luar).</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" onclick="switchTab('akademik')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 shadow-xs transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Akademik</span>
                        </button>
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="submit" name="action" value="save"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 shadow-xs transition text-xs cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Simpan Pilihan Seragam</span>
                            </button>
                            <button type="submit" name="action" value="next"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs cursor-pointer">
                                <span>Lanjutkan ke Tahap Berikutnya</span>
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 5: UNGGAH BERKAS DOKUMEN               -->
        <!-- ========================================== -->
        @php
            $docs = $calonSiswa->dokumenPendaftaran;
        @endphp
        <div id="tabContent-dokumen" class="tab-pane {{ $currentTab === 'dokumen' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Upload Berkas Persyaratan Pendaftaran</h3>
                    <p class="text-xs text-slate-500">Unggah berkas resmi dalam format PDF, JPG, atau PNG (Maks. 10 MB per berkas/foto).</p>
                </div>

                <form action="{{ route('calon-siswa.lengkapi-data.dokumen') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- 1. KARTU KELUARGA (KK) -->
                        <div class="p-5 rounded-2xl border {{ $docs?->kk_path ? 'bg-emerald-50/50 border-emerald-300' : 'bg-slate-50 border-slate-200' }} space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase">1. Kartu Keluarga (KK) <span class="text-red-500">*</span></span>
                                @if($docs?->kk_path)
                                    <span class="inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        ✓ Sudah Diunggah
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold text-slate-500 bg-slate-200 px-2.5 py-0.5 rounded-full">
                                        Wajib
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">Scan atau foto jelas lembar Kartu Keluarga asli/legalisir.</p>

                            @if($docs?->kk_path)
                                <div class="flex items-center gap-2 pt-1 text-xs">
                                    <a href="{{ Storage::url($docs->kk_path) }}" target="_blank" class="font-bold text-orange-600 hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Berkas Tersimpan</span>
                                    </a>
                                </div>
                            @endif

                            <div class="pt-2">
                                <input type="file" name="file_kartu_keluarga" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- 2. AKTA KELAHIRAN -->
                        <div class="p-5 rounded-2xl border {{ $docs?->akta_path ? 'bg-emerald-50/50 border-emerald-300' : 'bg-slate-50 border-slate-200' }} space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase">2. Akta Kelahiran</span>
                                @if($docs?->akta_path)
                                    <span class="inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        ✓ Sudah Diunggah
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold text-slate-500 bg-slate-200 px-2.5 py-0.5 rounded-full">
                                        Opsional
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">Scan atau foto jelas Akta Kelahiran calon siswa.</p>

                            @if($docs?->akta_path)
                                <div class="flex items-center gap-2 pt-1 text-xs">
                                    <a href="{{ Storage::url($docs->akta_path) }}" target="_blank" class="font-bold text-orange-600 hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Berkas Tersimpan</span>
                                    </a>
                                </div>
                            @endif

                            <div class="pt-2">
                                <input type="file" name="file_akta_kelahiran" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- 3. IJAZAH / SURAT KETERANGAN LULUS (SKL) -->
                        <div class="p-5 rounded-2xl border {{ $docs?->ijazah_skl_path ? 'bg-emerald-50/50 border-emerald-300' : 'bg-slate-50 border-slate-200' }} space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase">3. Ijazah / SKL / Ket. Kelas 9</span>
                                @if($docs?->ijazah_skl_path)
                                    <span class="inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        ✓ Sudah Diunggah
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold text-slate-500 bg-slate-200 px-2.5 py-0.5 rounded-full">
                                        Opsional
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">Ijazah SMP/MTs, SKL, atau Surat Keterangan Aktif Siswa Kelas 9.</p>

                            @if($docs?->ijazah_skl_path)
                                <div class="flex items-center gap-2 pt-1 text-xs">
                                    <a href="{{ Storage::url($docs->ijazah_skl_path) }}" target="_blank" class="font-bold text-orange-600 hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Berkas Tersimpan</span>
                                    </a>
                                </div>
                            @endif

                            <div class="pt-2">
                                <input type="file" name="file_ijazah_atau_skl" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- 4. PAS FOTO RESMI (3X4) -->
                        <div class="p-5 rounded-2xl border {{ $docs?->pas_foto_path ? 'bg-emerald-50/50 border-emerald-300' : 'bg-slate-50 border-slate-200' }} space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase">4. Pas Foto / Foto Wajah Non-Formal <span class="text-red-500">*</span></span>
                                @if($docs?->pas_foto_path)
                                    <span class="inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        ✓ Sudah Diunggah
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold text-slate-500 bg-slate-200 px-2.5 py-0.5 rounded-full">
                                        Wajib
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">Pas foto berseragam sekolah dengan latar belakang merah atau biru (JPG/PNG maks. 10MB). atau foto tidak resmi sopan menampilkan wajah</p>

                            @if($docs?->pas_foto_path)
                                <div class="flex items-center gap-3 pt-1 text-xs">
                                    <img src="{{ Storage::url($docs->pas_foto_path) }}" alt="Pas Foto" class="w-10 h-12 object-cover rounded-lg border border-slate-300 shadow-2xs">
                                    <a href="{{ Storage::url($docs->pas_foto_path) }}" target="_blank" class="font-bold text-orange-600 hover:underline">
                                        Lihat Foto Penuh
                                    </a>
                                </div>
                            @endif

                            <div class="pt-2">
                                <input type="file" name="file_pas_foto" accept=".jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- 5. DOKUMEN PENDUKUNG (OPSIONAL) -->
                        <div class="p-5 rounded-2xl border {{ $docs?->dokumen_pendukung_path ? 'bg-emerald-50/50 border-emerald-300' : 'bg-slate-50 border-slate-200' }} md:col-span-2 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase">5. Dokumen Pendukung / Sertifikat Prestasi (Opsional)</span>
                                @if($docs?->dokumen_pendukung_path)
                                    <span class="inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        ✓ Sudah Diunggah
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold text-slate-500 bg-slate-200 px-2.5 py-0.5 rounded-full">
                                        Opsional
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500"><strong>wajib jika mencantumkan prestasi</strong> Scan sertifikat/piagam penghargaan, KIP/PKH (jika ada), atau dokumen pendukung lainnya.</p>

                            @if($docs?->dokumen_pendukung_path)
                                <div class="flex items-center gap-2 pt-1 text-xs">
                                    <a href="{{ Storage::url($docs->dokumen_pendukung_path) }}" target="_blank" class="font-bold text-orange-600 hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Dokumen Pendukung</span>
                                    </a>
                                </div>
                            @endif

                            <div class="pt-2">
                                <input type="file" name="file_dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>
                        </div>

                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" onclick="switchTab('seragam')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 shadow-xs transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Ukuran Seragam</span>
                        </button>
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="submit" name="action" value="save"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 shadow-xs transition text-xs cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Simpan Progress</span>
                            </button>
                            <button type="submit" name="action" value="next"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs cursor-pointer">
                                <span>Lanjutkan ke Tahap Berikutnya</span>
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 6: KESEHATAN                           -->
        <!-- ========================================== -->
        @php
            $kesehatan = $calonSiswa->dataKesehatan;
        @endphp
        <div id="tabContent-kesehatan" class="tab-pane {{ $currentTab === 'kesehatan' ? '' : 'hidden' }}">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Formulir Data Kesehatan</h3>
                    <p class="text-xs text-slate-500">Lengkapi data kesehatan calon siswa.</p>
                </div>

                <form action="{{ route('calon-siswa.lengkapi-data.kesehatan') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Tinggi Badan (cm)</label>
                            <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $kesehatan?->tinggi_badan) }}" placeholder="Misal: 160"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Berat Badan (kg)</label>
                            <input type="number" name="berat_badan" value="{{ old('berat_badan', $kesehatan?->berat_badan) }}" placeholder="Misal: 50"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Golongan Darah</label>
                            <select name="golongan_darah"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                <option value="">-- Pilih --</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Tidak Tahu'] as $g)
                                    <option value="{{ $g }}" {{ old('golongan_darah', $kesehatan?->golongan_darah) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Buta Warna</label>
                            <select name="buta_warna"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                <option value="">-- Pilih --</option>
                                @foreach(['Tidak buta warna', 'Buta warna parsial', 'Buta warna'] as $bw)
                                    <option value="{{ $bw }}" {{ old('buta_warna', $kesehatan?->buta_warna) === $bw ? 'selected' : '' }}>{{ $bw }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Kesehatan Mata</label>
                            <select name="kesehatan_mata"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                <option value="">-- Pilih --</option>
                                @foreach(['Normal', 'Minus', 'Plus', 'Silinder'] as $m)
                                    <option value="{{ $m }}" {{ old('kesehatan_mata', $kesehatan?->kesehatan_mata) === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Jenis Alergi</label>
                            <input type="text" name="jenis_alergi" value="{{ old('jenis_alergi', $kesehatan?->jenis_alergi) }}" placeholder="Misal: Debu, Udang (Kosongkan jika tidak ada)"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4 border p-4 rounded-xl border-slate-200">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penyakit Berat Pernah Diderita</label>
                                <select name="penyakit_pernah_diderita" onchange="togglePenyakitLainnya('pernah', this.value)"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Asma', 'Hepatitis', 'Tipes', 'TBC', 'Penyakit jantung', 'Usus buntu', 'Diabetes', 'Lupus', 'Patah tulang', 'Tidak ada', 'Lainnya'] as $p)
                                        <option value="{{ $p }}" {{ old('penyakit_pernah_diderita', $kesehatan?->penyakit_pernah_diderita) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="container_penyakit_pernah_lainnya" class="{{ old('penyakit_pernah_diderita', $kesehatan?->penyakit_pernah_diderita) === 'Lainnya' ? '' : 'hidden' }}">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Sebutkan (Pernah Diderita)</label>
                                <input type="text" name="penyakit_pernah_diderita_lainnya" id="penyakit_pernah_diderita_lainnya" value="{{ old('penyakit_pernah_diderita_lainnya', $kesehatan?->penyakit_pernah_diderita_lainnya) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>
                        </div>

                        <div class="space-y-4 border p-4 rounded-xl border-slate-200">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Penyakit Berat Sedang Diderita</label>
                                <select name="penyakit_sedang_diderita" onchange="togglePenyakitLainnya('sedang', this.value)"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Asma', 'Hepatitis', 'Tipes', 'TBC', 'Penyakit jantung', 'Usus buntu', 'Diabetes', 'Lupus', 'Patah tulang', 'Tidak ada', 'Lainnya'] as $p)
                                        <option value="{{ $p }}" {{ old('penyakit_sedang_diderita', $kesehatan?->penyakit_sedang_diderita) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="container_penyakit_sedang_lainnya" class="{{ old('penyakit_sedang_diderita', $kesehatan?->penyakit_sedang_diderita) === 'Lainnya' ? '' : 'hidden' }}">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Sebutkan (Sedang Diderita)</label>
                                <input type="text" name="penyakit_sedang_diderita_lainnya" id="penyakit_sedang_diderita_lainnya" value="{{ old('penyakit_sedang_diderita_lainnya', $kesehatan?->penyakit_sedang_diderita_lainnya) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-5 flex flex-col sm:flex-row gap-3 justify-between items-center">
                        <button type="button" onclick="switchTab('dokumen')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 shadow-xs transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Unggah Berkas</span>
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition text-xs cursor-pointer">
                            <span>Simpan Data Kesehatan</span>
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- SCRIPT TABS, CASCADING WILAYAH & PRESTASI DYNAMIC -->
    <script>
        const tabSteps = [
            { id: 'biodata', num: 1, title: '1. Biodata & Wilayah', complete: {{ $completion['biodata']['is_complete'] ? 'true' : 'false' }} },
            { id: 'orang_tua', num: 2, title: '2. Data Orang Tua / Wali', complete: {{ $completion['orang_tua']['is_complete'] ? 'true' : 'false' }} },
            { id: 'akademik', num: 3, title: '3. Akademik & Prestasi', complete: {{ $completion['akademik']['is_complete'] ? 'true' : 'false' }} },
            { id: 'seragam', num: 4, title: '4. Ukuran Seragam', complete: {{ $completion['seragam']['is_complete'] ? 'true' : 'false' }} },
            { id: 'dokumen', num: 5, title: '5. Unggah Berkas', complete: {{ $completion['dokumen']['is_complete'] ? 'true' : 'false' }} },
            { id: 'kesehatan', num: 6, title: '6. Data Kesehatan', complete: {{ $completion['kesehatan']['is_complete'] ? 'true' : 'false' }} }
        ];

        // Tab Switcher
        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-600', 'hover:bg-slate-100');
                const numBadge = btn.querySelector('.tab-num');
                if (numBadge) {
                    numBadge.classList.remove('bg-white/25', 'text-white');
                    numBadge.classList.add('bg-slate-200', 'text-slate-700');
                }
            });

            const activeContent = document.getElementById('tabContent-' + tabId);
            const activeBtn = document.getElementById('tabBtn-' + tabId);

            if (activeContent) activeContent.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.classList.remove('text-slate-600', 'hover:bg-slate-100');
                activeBtn.classList.add('bg-orange-500', 'text-white', 'shadow-xs');
                const numBadge = activeBtn.querySelector('.tab-num');
                if (numBadge) {
                    numBadge.classList.remove('bg-slate-200', 'text-slate-700');
                    numBadge.classList.add('bg-white/25', 'text-white');
                }

                // Smooth scroll active button to center for horizontal mobile view
                activeBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }

            // Highlight corresponding checklist badge at the top
            document.querySelectorAll('.step-badge-card').forEach(b => {
                b.classList.remove('ring-2', 'ring-orange-500', 'ring-offset-2');
            });
            const activeBadge = document.getElementById('badgeStep-' + tabId);
            if (activeBadge) {
                activeBadge.classList.add('ring-2', 'ring-orange-500', 'ring-offset-2');
            }

            // Update Mobile Stepper Controls
            const currentStepIdx = tabSteps.findIndex(s => s.id === tabId);
            if (currentStepIdx !== -1) {
                const step = tabSteps[currentStepIdx];
                const mobileBadge = document.getElementById('mobileStepBadge');
                const mobileTitle = document.getElementById('mobileStepTitle');
                const mobileSelect = document.getElementById('mobileTabSelect');
                const mobileStatus = document.getElementById('mobileStepStatusBadge');
                const prevBtn = document.getElementById('mobilePrevBtn');
                const nextBtn = document.getElementById('mobileNextBtn');

                if (mobileBadge) mobileBadge.textContent = step.num;
                if (mobileTitle) mobileTitle.textContent = step.title;
                if (mobileSelect) mobileSelect.value = tabId;

                if (mobileStatus) {
                    mobileStatus.innerHTML = step.complete
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Lengkap</span>'
                        : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">Belum Selesai</span>';
                }

                if (prevBtn) prevBtn.disabled = currentStepIdx === 0;
                if (nextBtn) nextBtn.disabled = currentStepIdx === tabSteps.length - 1;
            }

            // Update URL hash/query without reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        }

        function navPrevTab() {
            const currentTab = new URL(window.location).searchParams.get('tab') || 'biodata';
            const idx = tabSteps.findIndex(s => s.id === currentTab);
            if (idx > 0) {
                switchTab(tabSteps[idx - 1].id);
            }
        }

        function navNextTab() {
            const currentTab = new URL(window.location).searchParams.get('tab') || 'biodata';
            const idx = tabSteps.findIndex(s => s.id === currentTab);
            if (idx < tabSteps.length - 1) {
                switchTab(tabSteps[idx + 1].id);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const initialTab = new URL(window.location).searchParams.get('tab') || '{{ $currentTab }}';
            switchTab(initialTab);
        });

        // Domisili Mode Switcher (Dalam Negeri vs Luar Negeri)
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

            const negaraInput = document.getElementById('input_negara');
            const provLnInput = document.getElementById('input_provinsi_ln');
            const kabLnInput = document.getElementById('input_kabupaten_ln');

            if (isLuar) {
                if (btnLuar) btnLuar.className = 'px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 bg-white text-orange-600 shadow-xs font-black';
                if (btnDalam) btnDalam.className = 'px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 text-slate-500 hover:text-slate-800 font-bold';

                if (dalamContainer) dalamContainer.classList.add('hidden');
                if (luarContainer) luarContainer.classList.remove('hidden');

                if (provInput) provInput.removeAttribute('required');
                if (kabInput) kabInput.removeAttribute('required');
                if (kecInput) kecInput.removeAttribute('required');
                if (desaInput) desaInput.removeAttribute('required');
                if (rtInput) rtInput.removeAttribute('required');
                if (rwInput) rwInput.removeAttribute('required');

                if (negaraInput) negaraInput.setAttribute('required', 'required');
                if (provLnInput) provLnInput.setAttribute('required', 'required');
                if (kabLnInput) kabLnInput.setAttribute('required', 'required');
            } else {
                if (btnDalam) btnDalam.className = 'px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 bg-white text-orange-600 shadow-xs font-black';
                if (btnLuar) btnLuar.className = 'px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 text-slate-500 hover:text-slate-800 font-bold';

                if (dalamContainer) dalamContainer.classList.remove('hidden');
                if (luarContainer) luarContainer.classList.add('hidden');

                if (provInput) provInput.setAttribute('required', 'required');
                if (kabInput) kabInput.setAttribute('required', 'required');
                if (kecInput) kecInput.setAttribute('required', 'required');
                if (desaInput) desaInput.setAttribute('required', 'required');
                if (rtInput) rtInput.setAttribute('required', 'required');
                if (rwInput) rwInput.setAttribute('required', 'required');

                if (negaraInput) negaraInput.removeAttribute('required');
                if (provLnInput) provLnInput.removeAttribute('required');
                if (kabLnInput) kabLnInput.removeAttribute('required');
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
                    <input type="text" name="prestasi[${idx}][peringkat]" placeholder="Juara 1 / ${new Date().getFullYear()}" class="mt-1 w-full rounded-lg border border-slate-300 p-2 text-xs">
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

            // Sync hidden inputs for backward compatibility
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

        function togglePenyakitLainnya(type, value) {
            const container = document.getElementById('container_penyakit_' + type + '_lainnya');
            const input = document.getElementById('penyakit_' + type + '_diderita_lainnya');
            if (value === 'Lainnya') {
                container.classList.remove('hidden');
                input.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                input.removeAttribute('required');
                input.value = '';
            }
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
                if (inputAvg) {
                    inputAvg.value = overallAvg;
                }
            }
        }

        // Initialize calculations & domisili mode on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            ['mtk', 'ind', 'eng', 'pai'].forEach(p => calculateMatrixRow(p));
            const isLn = document.getElementById('input_is_luar_negeri')?.value === '1';
            setDomisiliMode(isLn);
        });
    </script>
</x-layouts.app>