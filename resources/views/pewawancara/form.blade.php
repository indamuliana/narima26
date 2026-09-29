<x-layouts.app>
    <x-slot name="title">Wawancara Calon Siswa — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('pewawancara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Top Back Bar -->
        <div class="flex items-center justify-between">
            <a href="{{ route('pewawancara.antrian') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                ← Kembali ke Antrian
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Status Wawancara:</span>
                @if ($wawancara->status === 'SELESAI')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        SELESAI
                    </span>
                @elseif ($wawancara->status === 'PROSES')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                        DRAFT / SEDANG PROSES
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        BELUM DIMULAI
                    </span>
                @endif
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

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <p class="text-xs font-bold uppercase tracking-wider mb-1">Terdapat kesalahan pengisian:</p>
                <ul class="text-xs list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Two-column Assessment Workspace -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Sisi Kiri: Profil & Data Calon Siswa (Section 21.3) -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Identitas Utama Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center gap-4">
                        @if ($calonSiswa->dokumenPendaftaran?->pas_foto_path && file_exists(public_path('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path)))
                            <img src="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path) }}"
                                 alt="Pas Foto"
                                 class="w-16 h-20 object-cover rounded-xl border-2 border-white/30 shadow-md shrink-0">
                        @else
                            <div class="w-16 h-20 rounded-xl bg-slate-700 border-2 border-dashed border-slate-500 flex flex-col items-center justify-center text-slate-400 shrink-0">
                                <span class="text-xl">👤</span>
                                <span class="text-[9px] mt-1">3x4</span>
                            </div>
                        @endif

                        <div class="min-w-0">
                            <span class="text-[10px] font-mono uppercase tracking-widest text-nampi-cyan font-bold">
                                {{ $calonSiswa->nomor_pendaftaran }}
                            </span>
                            <h2 class="text-lg font-black truncate text-white mt-0.5">{{ $calonSiswa->nama_lengkap }}</h2>
                            <p class="text-xs text-slate-300 mt-0.5">NISN: <span class="font-mono">{{ $calonSiswa->nisn }}</span></p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                    Data Lengkap
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-400/30">
                                    {{ $calonSiswa->jurusan?->kode_jurusan ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-slate-400">Pilihan Jurusan</span>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400">Program Belajar</span>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $calonSiswa->programBelajar?->nama_program ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-slate-400">Asal Sekolah</span>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->sekolah_asal_text ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400">Jenis Kelamin / Usia</span>
                                <p class="font-bold text-slate-800 mt-0.5">
                                    {{ $calonSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    @if ($calonSiswa->tanggal_lahir)
                                        ({{ \Carbon\Carbon::parse($calonSiswa->tanggal_lahir)->age }} thn)
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-slate-400">Alamat Tempat Tinggal</span>
                            <p class="font-medium text-slate-700 mt-0.5">{{ $calonSiswa->alamat ?? 'Belum diisi' }}</p>
                        </div>

                        <!-- Data Orang Tua -->
                        <div class="pt-1">
                            <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-2">Profil Orang Tua / Wali</p>
                            @if ($calonSiswa->dataOrangtua)
                                @php
                                    $ot = $calonSiswa->dataOrangtua;
                                    $pekAyah = $ot->pekerjaanAyah?->nama ?? $ot->pekerjaan_ayah ?? '-';
                                    $pekIbu = $ot->pekerjaanIbu?->nama ?? $ot->pekerjaan_ibu ?? '-';
                                @endphp
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Ayah:</span>
                                        <span class="font-semibold text-slate-800">{{ $ot->nama_ayah ?? '-' }} ({{ $pekAyah }})</span>
                                    </div>
                                    @if ($ot->penghasilan_ayah)
                                        <div class="flex justify-between text-[11px]">
                                            <span class="text-slate-500">Penghasilan Ayah:</span>
                                            <span class="font-medium text-slate-700">{{ $ot->penghasilan_ayah }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Ibu:</span>
                                        <span class="font-semibold text-slate-800">{{ $ot->nama_ibu ?? '-' }} ({{ $pekIbu }})</span>
                                    </div>
                                    @if ($ot->penghasilan_ibu)
                                        <div class="flex justify-between text-[11px]">
                                            <span class="text-slate-500">Penghasilan Ibu:</span>
                                            <span class="font-medium text-slate-700">{{ $ot->penghasilan_ibu }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">No HP Orang Tua:</span>
                                        <span class="font-mono text-slate-800">{{ $ot->no_hp_ayah ?? $ot->no_hp_ibu ?? $calonSiswa->no_hp_siswa ?? $calonSiswa->no_hp }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-slate-400 italic">Data orang tua belum tersedia.</p>
                            @endif
                        </div>

                        <!-- Data Akademik & Prestasi -->
                        <div class="pt-1">
                            <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-2">Nilai Rapor & Prestasi</p>
                            @if ($calonSiswa->dataAkademik)
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-indigo-50/70 p-2 rounded-lg border border-indigo-100">
                                        <span class="text-[10px] text-indigo-500 font-bold block">Rata Rapor</span>
                                        <span class="text-sm font-black text-indigo-800">{{ $calonSiswa->dataAkademik->nilai_rata_rata ?? $calonSiswa->dataAkademik->nilai_rata_rata_rapor ?? '-' }}</span>
                                    </div>
                                    <div class="bg-blue-50/70 p-2 rounded-lg border border-blue-100">
                                        <span class="text-[10px] text-blue-500 font-bold block">Matematika</span>
                                        <span class="text-sm font-black text-blue-800">{{ $calonSiswa->dataAkademik->nilai_matematika ?? '-' }}</span>
                                    </div>
                                    <div class="bg-cyan-50/70 p-2 rounded-lg border border-cyan-100">
                                        <span class="text-[10px] text-cyan-500 font-bold block">B. Inggris</span>
                                        <span class="text-sm font-black text-cyan-800">{{ $calonSiswa->dataAkademik->nilai_bahasa_inggris ?? '-' }}</span>
                                    </div>
                                </div>
                            @endif

                            @if ($calonSiswa->prestasi && $calonSiswa->prestasi->count() > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach ($calonSiswa->prestasi as $pres)
                                        <div class="p-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 flex items-center justify-between">
                                            <div>
                                                <span class="font-bold">{{ $pres->nama_prestasi }}</span>
                                                <span class="text-[10px] text-amber-700 block">{{ $pres->tingkat }} • Juara {{ $pres->juara }}</span>
                                            </div>
                                            <span class="text-[10px] font-mono">{{ $pres->tahun }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Ukuran Seragam -->
                        @if ($calonSiswa->ukuranSeragam && $calonSiswa->ukuranSeragam->count() > 0)
                            <div class="pt-1">
                                <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-2">Ukuran Seragam</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($calonSiswa->ukuranSeragam as $us)
                                        <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200 text-[11px]">
                                            {{ $us->seragam?->nama_seragam ?? 'Seragam' }}: <strong>{{ $us->ukuran }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Dokumen Link -->
                        @if ($calonSiswa->dokumenPendaftaran)
                            <div class="pt-2 border-t border-slate-100">
                                <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-2">Berkas Persyaratan</p>
                                <div class="flex flex-wrap gap-2">
                                    @if ($calonSiswa->dokumenPendaftaran->rapor_path)
                                        <a href="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->rapor_path) }}" target="_blank"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors inline-flex items-center gap-1">
                                            📄 Rapor Siswa
                                        </a>
                                    @endif
                                    @if ($calonSiswa->dokumenPendaftaran->kartu_keluarga_path)
                                        <a href="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->kartu_keluarga_path) }}" target="_blank"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors inline-flex items-center gap-1">
                                            📄 Kartu Keluarga
                                        </a>
                                    @endif
                                    @if ($calonSiswa->dokumenPendaftaran->akta_kelahiran_path)
                                        <a href="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->akta_kelahiran_path) }}" target="_blank"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors inline-flex items-center gap-1">
                                            📄 Akta Kelahiran
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Form Penilaian Rubrik Wawancara (Section 21.3) -->
            <div class="lg:col-span-7">
                <form method="POST" action="{{ route('pewawancara.wawancara.store', $calonSiswa) }}" id="formWawancara" class="space-y-6">
                    @csrf

                    <!-- Tanggal Wawancara & Metadata -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-black text-slate-800">Instrumen Penilaian Wawancara</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Pewawancara: <strong>{{ auth()->user()->name }}</strong></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="tanggal_wawancara" class="text-xs font-semibold text-slate-600">Tanggal Uji:</label>
                                <input type="date" id="tanggal_wawancara" name="tanggal_wawancara"
                                       value="{{ old('tanggal_wawancara', $wawancara->tanggal_wawancara?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                       class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-medium">
                            </div>
                        </div>
                    </div>

                    <!-- Kategori 1: Rubrik Wawancara Siswa -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-nampi-orange text-white flex items-center justify-center font-bold text-xs">A</span>
                                <h4 class="font-bold text-sm">Penilaian Calon Siswa (Indikator & Rubrik)</h4>
                            </div>
                            <span class="text-xs text-slate-400">{{ $kriteriaSiswa->count() }} Kriteria</span>
                        </div>

                        <div class="p-5 space-y-6 divide-y divide-slate-100">
                            @foreach ($kriteriaSiswa as $idx => $k)
                                @php
                                    $detail = $existingDetails->get($k->id);
                                    $oldNilai = old("penilaian.{$k->id}.nilai", $detail?->nilai ?? 80);
                                    $oldWarna = old("penilaian.{$k->id}.warna", $detail?->warna ?? 'HIJAU');
                                    $oldIndikator = old("penilaian.{$k->id}.indikator", $detail?->indikator ?? 'Baik');
                                    $oldCatatan = old("penilaian.{$k->id}.catatan", $detail?->catatan ?? '');
                                @endphp
                                <div class="{{ $loop->first ? '' : 'pt-5' }} space-y-3" data-kriteria-item="{{ $k->id }}">
                                    <input type="hidden" name="penilaian[{{ $k->id }}][kriteria_id]" value="{{ $k->id }}">

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-xs font-mono font-bold text-slate-400">[{{ $k->kode }}]</span>
                                            <h5 class="text-sm font-bold text-slate-800 inline ml-1">{{ $k->nama_kriteria }}</h5>
                                            @if ($k->keterangan)
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $k->keterangan }}</p>
                                            @endif
                                        </div>

                                        <!-- Nilai Score Input -->
                                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                                            <label class="text-xs font-semibold text-slate-500">Skor (0-100):</label>
                                            <input type="number" min="0" max="100"
                                                   name="penilaian[{{ $k->id }}][nilai]"
                                                   id="nilai_{{ $k->id }}"
                                                   value="{{ $oldNilai }}"
                                                   oninput="updateRubrikUI({{ $k->id }}, this.value)"
                                                   class="w-20 px-3 py-1.5 text-center text-sm font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                                        </div>
                                    </div>

                                    <!-- Indikator Warna Buttons (Section 21.2) -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        <span class="text-xs font-semibold text-slate-500 mr-1">Indikator:</span>

                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                               id="label_hijau_{{ $k->id }}">
                                            <input type="radio" name="penilaian[{{ $k->id }}][warna]" value="HIJAU"
                                                   {{ $oldWarna === 'HIJAU' ? 'checked' : '' }}
                                                   onchange="selectWarna({{ $k->id }}, 'HIJAU', 'Sangat Baik')"
                                                   class="text-emerald-600 focus:ring-emerald-500">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>HIJAU (Baik/Sangat Baik)</span>
                                        </label>

                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                               id="label_oranye_{{ $k->id }}">
                                            <input type="radio" name="penilaian[{{ $k->id }}][warna]" value="ORANYE"
                                                   {{ $oldWarna === 'ORANYE' ? 'checked' : '' }}
                                                   onchange="selectWarna({{ $k->id }}, 'ORANYE', 'Cukup')"
                                                   class="text-amber-500 focus:ring-amber-500">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span>ORANYE (Cukup)</span>
                                        </label>

                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                               id="label_merah_{{ $k->id }}">
                                            <input type="radio" name="penilaian[{{ $k->id }}][warna]" value="MERAH"
                                                   {{ $oldWarna === 'MERAH' ? 'checked' : '' }}
                                                   onchange="selectWarna({{ $k->id }}, 'MERAH', 'Kurang')"
                                                   class="text-rose-600 focus:ring-rose-500">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span>MERAH (Kurang)</span>
                                        </label>

                                        <input type="hidden" name="penilaian[{{ $k->id }}][indikator]"
                                               id="indikator_{{ $k->id }}"
                                               value="{{ $oldIndikator }}">
                                    </div>

                                    <!-- Catatan per Kriteria -->
                                    <div>
                                        <input type="text" name="penilaian[{{ $k->id }}][catatan]"
                                               placeholder="Catatan pengamatan kriteria {{ $k->nama_kriteria }} (opsional)..."
                                               value="{{ $oldCatatan }}"
                                               class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-300">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kategori 2: Rubrik Wawancara Orang Tua (Section 21.3) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="p-4 bg-slate-800 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-cyan-500 text-white flex items-center justify-center font-bold text-xs">B</span>
                                <h4 class="font-bold text-sm">Penilaian Orang Tua / Wali Siswa</h4>
                            </div>
                            <span class="text-xs text-slate-400">{{ $kriteriaOrangTua->count() }} Kriteria</span>
                        </div>

                        <div class="p-5 space-y-6 divide-y divide-slate-100">
                            @foreach ($kriteriaOrangTua as $k)
                                @php
                                    $detail = $existingDetails->get($k->id);
                                    $oldNilai = old("penilaian.{$k->id}.nilai", $detail?->nilai ?? 85);
                                    $oldWarna = old("penilaian.{$k->id}.warna", $detail?->warna ?? 'HIJAU');
                                    $oldIndikator = old("penilaian.{$k->id}.indikator", $detail?->indikator ?? 'Baik');
                                    $oldCatatan = old("penilaian.{$k->id}.catatan", $detail?->catatan ?? '');
                                @endphp
                                <div class="{{ $loop->first ? '' : 'pt-5' }} space-y-3" data-kriteria-item="{{ $k->id }}">
                                    <input type="hidden" name="penilaian[{{ $k->id }}][kriteria_id]" value="{{ $k->id }}">

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-xs font-mono font-bold text-slate-400">[{{ $k->kode }}]</span>
                                            <h5 class="text-sm font-bold text-slate-800 inline ml-1">{{ $k->nama_kriteria }}</h5>
                                            @if ($k->keterangan)
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $k->keterangan }}</p>
                                            @endif
                                        </div>

                                        <!-- Nilai Score Input -->
                                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                                            <label class="text-xs font-semibold text-slate-500">Skor (0-100):</label>
                                            <input type="number" min="0" max="100"
                                                   name="penilaian[{{ $k->id }}][nilai]"
                                                   id="nilai_{{ $k->id }}"
                                                   value="{{ $oldNilai }}"
                                                   oninput="updateRubrikUI({{ $k->id }}, this.value)"
                                                   class="w-20 px-3 py-1.5 text-center text-sm font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                                        </div>
                                    </div>

                                    <!-- Indikator Warna -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        <span class="text-xs font-semibold text-slate-500 mr-1">Indikator:</span>

                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                               id="label_hijau_{{ $k->id }}">
                                            <input type="radio" name="penilaian[{{ $k->id }}][warna]" value="HIJAU"
                                                   {{ $oldWarna === 'HIJAU' ? 'checked' : '' }}
                                                   onchange="selectWarna({{ $k->id }}, 'HIJAU', 'Sangat Baik')"
                                                   class="text-emerald-600 focus:ring-emerald-500">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>HIJAU (Mendukung Penuh)</span>
                                        </label>

                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                               id="label_oranye_{{ $k->id }}">
                                            <input type="radio" name="penilaian[{{ $k->id }}][warna]" value="ORANYE"
                                                   {{ $oldWarna === 'ORANYE' ? 'checked' : '' }}
                                                   onchange="selectWarna({{ $k->id }}, 'ORANYE', 'Cukup')"
                                                   class="text-amber-500 focus:ring-amber-500">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span>ORANYE (Cukup)</span>
                                        </label>

                                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                               id="label_merah_{{ $k->id }}">
                                            <input type="radio" name="penilaian[{{ $k->id }}][warna]" value="MERAH"
                                                   {{ $oldWarna === 'MERAH' ? 'checked' : '' }}
                                                   onchange="selectWarna({{ $k->id }}, 'MERAH', 'Kurang')"
                                                   class="text-rose-600 focus:ring-rose-500">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span>MERAH (Kurang Mendukung)</span>
                                        </label>

                                        <input type="hidden" name="penilaian[{{ $k->id }}][indikator]"
                                               id="indikator_{{ $k->id }}"
                                               value="{{ $oldIndikator }}">
                                    </div>

                                    <!-- Catatan per Kriteria -->
                                    <div>
                                        <input type="text" name="penilaian[{{ $k->id }}][catatan]"
                                               placeholder="Catatan respon kriteria {{ $k->nama_kriteria }} (opsional)..."
                                               value="{{ $oldCatatan }}"
                                               class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-300">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Catatan Keseluruhan (Section 21.3) -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                        <h4 class="font-bold text-sm text-slate-800">Catatan Kesimpulan Pewawancara</h4>

                        <div>
                            <label for="catatan_umum" class="block text-xs font-semibold text-slate-700 mb-1">
                                Catatan Umum & Rekomendasi Siswa:
                            </label>
                            <textarea id="catatan_umum" name="catatan_umum" rows="3"
                                      placeholder="Tuliskan catatan umum mengenai potensi, karakter, kesiapan belajar, atau rekomendasi program keahlian..."
                                      class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">{{ old('catatan_umum', $wawancara->catatan_umum) }}</textarea>
                        </div>

                        <div>
                            <label for="catatan_orang_tua" class="block text-xs font-semibold text-slate-700 mb-1">
                                Hasil / Kesepahaman Wawancara Orang Tua:
                            </label>
                            <textarea id="catatan_orang_tua" name="catatan_orang_tua" rows="3"
                                      placeholder="Tuliskan hasil wawancara dengan orang tua/wali mengenai kesanggupan mendampingi siswa, komitmen pembayaran, dan pembinaan karakter..."
                                      class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">{{ old('catatan_orang_tua', $wawancara->catatan_orang_tua) }}</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons (Section 21.3: Simpan Draft & Selesai Wawancara) -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500">
                            <p><strong>Perhatian:</strong></p>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                "Selesai Wawancara" akan mengunci penilaian awal dan mengubah status calon siswa menjadi <strong>SUDAH_DIWAWANCARA</strong>.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="submit" name="action" value="draft"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs cursor-pointer">
                                💾 Simpan Draft
                            </button>

                            <button type="submit" name="action" value="selesai"
                                    onclick="return confirm('Apakah Anda yakin ingin menyelesaikan wawancara ini? Status calon siswa akan diperbarui menjadi SUDAH_DIWAWANCARA.')"
                                    class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors shadow-md cursor-pointer flex items-center justify-center gap-2">
                                <span>✅ Selesai Wawancara</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for Dynamic Score & Color Synchronization -->
    <script>
        function updateRubrikUI(kriteriaId, val) {
            const score = parseInt(val) || 0;
            let warna = 'HIJAU';
            let label = 'Baik';

            if (score >= 85) {
                warna = 'HIJAU';
                label = 'Sangat Baik';
            } else if (score >= 75) {
                warna = 'HIJAU';
                label = 'Baik';
            } else if (score >= 60) {
                warna = 'ORANYE';
                label = 'Cukup';
            } else {
                warna = 'MERAH';
                label = 'Kurang';
            }

            selectWarna(kriteriaId, warna, label);
        }

        function selectWarna(kriteriaId, warna, label) {
            const radio = document.querySelector(`input[name="penilaian[${kriteriaId}][warna]"][value="${warna}"]`);
            if (radio) {
                radio.checked = true;
            }

            const indikatorInput = document.getElementById(`indikator_${kriteriaId}`);
            if (indikatorInput && label) {
                indikatorInput.value = label;
            }

            // Update UI styles
            highlightLabel(kriteriaId, warna);
        }

        function highlightLabel(kriteriaId, activeWarna) {
            const colors = ['hijau', 'oranye', 'merah'];
            colors.forEach(c => {
                const el = document.getElementById(`label_${c}_${kriteriaId}`);
                if (!el) return;

                if (c.toUpperCase() === activeWarna) {
                    if (c === 'hijau') el.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border-2 border-emerald-500 bg-emerald-50 text-emerald-800 cursor-pointer text-xs font-bold transition-all';
                    if (c === 'oranye') el.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border-2 border-amber-500 bg-amber-50 text-amber-800 cursor-pointer text-xs font-bold transition-all';
                    if (c === 'merah') el.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border-2 border-rose-500 bg-rose-50 text-rose-800 cursor-pointer text-xs font-bold transition-all';
                } else {
                    el.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-600 cursor-pointer text-xs font-medium transition-all hover:bg-slate-50';
                }
            });
        }

        // Initialize label highlights on load
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-kriteria-item]').forEach(item => {
                const kriteriaId = item.getAttribute('data-kriteria-item');
                const checkedRadio = document.querySelector(`input[name="penilaian[${kriteriaId}][warna]"]:checked`);
                if (checkedRadio) {
                    highlightLabel(kriteriaId, checkedRadio.value);
                }
            });
        });
    </script>
</x-layouts.app>
