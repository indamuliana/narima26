<div x-data="{ openPembanding: true, activeTab: '{{ $defaultTab ?? 'biodata' }}' }" class="bg-white rounded-3xl border-2 border-indigo-100 shadow-sm overflow-hidden">
    <!-- Header Panel Pembanding -->
    <div class="p-5 sm:p-6 bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center font-bold text-lg shrink-0">
                📋
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-black text-white tracking-wide">Data Pengisian Calon Siswa (Pembanding)</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-500/30 text-indigo-200 border border-indigo-400/30">
                        VERIFIKASI
                    </span>
                </div>
                <p class="text-xs text-indigo-200/80 mt-0.5">Gunakan informasi ini untuk memverifikasi dan mencocokkan jawaban lisan calon siswa / orang tua.</p>
            </div>
        </div>

        <button type="button" @click="openPembanding = !openPembanding"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition cursor-pointer self-start sm:self-auto">
            <span x-text="openPembanding ? 'Sembunyikan Data' : 'Tampilkan Data Pembanding'"></span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openPembanding }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <!-- Body Panel Pembanding -->
    <div x-show="openPembanding" x-transition class="p-5 sm:p-6 space-y-5 bg-indigo-50/20 border-b border-indigo-100">

        <!-- Tab Selector -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
            <button type="button" @click="activeTab = 'biodata'"
                :class="activeTab === 'biodata' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>👤 Biodata & Domisili</span>
            </button>

            <button type="button" @click="activeTab = 'ortu'"
                :class="activeTab === 'ortu' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>👨‍👩‍👧 Data Orang Tua & Wali</span>
            </button>

            <button type="button" @click="activeTab = 'akademik'"
                :class="activeTab === 'akademik' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>📊 Nilai Rapor & Prestasi</span>
            </button>

            <button type="button" @click="activeTab = 'dokumen'"
                :class="activeTab === 'dokumen' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>📁 Berkas Dokumen</span>
            </button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: BIODATA & DOMISILI                     -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'biodata'" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 bg-white p-4 rounded-2xl border border-slate-200 text-xs">
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Nama Lengkap</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $calonSiswa->nama_lengkap }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Nama Panggilan</span>
                    <span class="font-semibold text-slate-800">{{ $calonSiswa->nama_panggilan ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Jenis Kelamin</span>
                    <span class="font-semibold text-slate-800">{{ $calonSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($calonSiswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Tempat, Tgl Lahir</span>
                    <span class="font-semibold text-slate-800">{{ $calonSiswa->tempat_lahir ?? '-' }}, {{ $calonSiswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Agama</span>
                    <span class="font-semibold text-slate-800">{{ $calonSiswa->agama ?? 'Islam' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">NIK Siswa</span>
                    <span class="font-mono font-bold text-slate-800">{{ $calonSiswa->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">No. Kartu Keluarga</span>
                    <span class="font-mono font-bold text-slate-800">{{ $calonSiswa->no_kk ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Email Siswa</span>
                    <span class="font-mono text-slate-700">{{ $calonSiswa->email ?? '-' }}</span>
                </div>
            </div>

            <!-- Kontak & Referensi Promotor -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-white p-4 rounded-2xl border border-slate-200 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">📱 WhatsApp Siswa</span>
                    <span class="font-mono font-bold text-slate-800 text-sm">{{ $calonSiswa->no_hp_siswa ?? '-' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">📱 WhatsApp Ayah</span>
                    <span class="font-mono font-bold text-slate-800 text-sm">{{ $calonSiswa->no_hp_ayah ?? '-' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">📱 WhatsApp Ibu</span>
                    <span class="font-mono font-bold text-slate-800 text-sm">{{ $calonSiswa->no_hp_ibu ?? '-' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-orange-50/50 border border-orange-200">
                    <span class="block text-orange-700 font-bold uppercase text-[10px]">Referensi / Promotor</span>
                    <span class="font-bold text-slate-800">{{ $calonSiswa->referensi_nama ?? $calonSiswa->referensi_jenis ?? 'Bukan Referensi' }}</span>
                    @if($calonSiswa->referensi_rayon)
                        <span class="block text-[11px] text-slate-500 font-mono">Rayon: {{ $calonSiswa->referensi_rayon }}</span>
                    @endif
                </div>
            </div>

            <!-- Alamat Domisili Lengkap -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="block text-slate-400 font-bold uppercase text-[10px]">Alamat Tempat Tinggal (Domisili Siswa)</span>
                    @if($calonSiswa->is_luar_negeri)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200">
                            🌐 Luar Negeri
                        </span>
                    @endif
                </div>
                <p class="font-bold text-slate-800 text-sm">
                    {{ $calonSiswa->alamat_lengkap ?? 'Alamat belum diisi' }}
                    @if(!$calonSiswa->is_luar_negeri && ($calonSiswa->rt || $calonSiswa->rw))
                        <span class="font-normal text-slate-500">(RT {{ $calonSiswa->rt ?? '-' }} / RW {{ $calonSiswa->rw ?? '-' }})</span>
                    @endif
                </p>
                @if($calonSiswa->is_luar_negeri)
                    <p class="text-slate-600 font-medium">
                        @if($calonSiswa->desa_luar_negeri) {{ $calonSiswa->desa_luar_negeri }}, @endif
                        @if($calonSiswa->kecamatan_luar_negeri) Distrik: <strong>{{ $calonSiswa->kecamatan_luar_negeri }}</strong>, @endif
                        Kota: <strong>{{ $calonSiswa->kabupaten_luar_negeri ?? '-' }}</strong>,
                        Prov/State: <strong>{{ $calonSiswa->provinsi_luar_negeri ?? '-' }}</strong>,
                        Negara: <strong>{{ $calonSiswa->negara ?? '-' }}</strong>
                        @if($calonSiswa->kode_pos)
                            • Kode Pos: <span class="font-mono">{{ $calonSiswa->kode_pos }}</span>
                        @endif
                    </p>
                @else
                    <p class="text-slate-600 font-medium">
                        Desa/Kel: <strong>{{ $calonSiswa->desa?->nama ?? '-' }}</strong>,
                        Kec: <strong>{{ $calonSiswa->kecamatan?->nama ?? '-' }}</strong>,
                        Kab/Kota: <strong>{{ $calonSiswa->kabupaten?->nama ?? '-' }}</strong>,
                        Prov: <strong>{{ $calonSiswa->provinsi?->nama ?? '-' }}</strong>
                        @if($calonSiswa->kode_pos)
                            • Kode Pos: <span class="font-mono">{{ $calonSiswa->kode_pos }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: DATA ORANG TUA / WALI                  -->
        <!-- ============================================== -->
        @php
            $ortu = $calonSiswa->dataOrangtua;
        @endphp
        <div x-show="activeTab === 'ortu'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Data Ayah -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="font-bold text-slate-800 text-sm">Ayah Kandung</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($ortu?->status_ayah ?? 'MASIH_HIDUP') === 'WAFAT' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                            {{ ($ortu?->status_ayah ?? 'MASIH_HIDUP') === 'WAFAT' ? 'WAFAT (Alm)' : 'MASIH HIDUP' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Ayah</span>
                            <span class="font-bold text-slate-800">{{ $ortu?->nama_ayah ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">NIK / Th. Lahir</span>
                            <span class="font-mono text-slate-700">{{ $ortu?->nik_ayah ?? '-' }} ({{ $ortu?->tahun_lahir_ayah ?? '-' }})</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pekerjaan</span>
                            <span class="font-semibold text-slate-800">{{ $ortu?->pekerjaanAyah?->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Penghasilan</span>
                            <span class="font-bold text-emerald-700">{{ $ortu?->penghasilan_ayah ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pendidikan</span>
                            <span class="text-slate-700">{{ $ortu?->pendidikan_ayah ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">No. HP / WA</span>
                            <span class="font-mono font-bold text-slate-800">{{ $ortu?->no_hp_ayah ?? '-' }}</span>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-50">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Ayah</span>
                            <span class="text-slate-700">{{ $ortu?->alamat_ayah ?? 'Sama dengan siswa' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="font-bold text-slate-800 text-sm">Ibu Kandung</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($ortu?->status_ibu ?? 'MASIH_HIDUP') === 'WAFAT' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                            {{ ($ortu?->status_ibu ?? 'MASIH_HIDUP') === 'WAFAT' ? 'WAFAT (Almh)' : 'MASIH HIDUP' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Ibu</span>
                            <span class="font-bold text-slate-800">{{ $ortu?->nama_ibu ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">NIK / Th. Lahir</span>
                            <span class="font-mono text-slate-700">{{ $ortu?->nik_ibu ?? '-' }} ({{ $ortu?->tahun_lahir_ibu ?? '-' }})</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pekerjaan</span>
                            <span class="font-semibold text-slate-800">{{ $ortu?->pekerjaanIbu?->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Penghasilan</span>
                            <span class="font-bold text-emerald-700">{{ $ortu?->penghasilan_ibu ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pendidikan</span>
                            <span class="text-slate-700">{{ $ortu?->pendidikan_ibu ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">No. HP / WA</span>
                            <span class="font-mono font-bold text-slate-800">{{ $ortu?->no_hp_ibu ?? '-' }}</span>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-50">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Ibu</span>
                            <span class="text-slate-700">{{ $ortu?->alamat_ibu ?? 'Sama dengan siswa' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Wali (Jika Ada) -->
            @if(!empty($ortu?->nama_wali))
                <div class="bg-white p-4 rounded-2xl border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span class="font-bold text-slate-800">Wali Murid: {{ $ortu->nama_wali }} (Hubungan: {{ $ortu->hubungan_wali ?? '-' }})</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pekerjaan</span>
                            <span class="font-semibold text-slate-800">{{ $ortu->pekerjaanWali?->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Penghasilan</span>
                            <span class="font-bold text-emerald-700">{{ $ortu->penghasilan_wali ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">No. HP Wali</span>
                            <span class="font-mono font-bold text-slate-800">{{ $ortu->no_hp_wali ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Wali</span>
                            <span class="text-slate-700">{{ $ortu->alamat_wali ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: NILAI RAPOR & PRESTASI                  -->
        <!-- ============================================== -->
        @php
            $akademik = $calonSiswa->dataAkademik;
            $rapor = $calonSiswa->nilaiRapor;
            $prestasiList = $calonSiswa->prestasi;
        @endphp
        <div x-show="activeTab === 'akademik'" class="space-y-4">
            <!-- Header Rata-rata -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div>
                    <span class="text-slate-400 uppercase font-bold text-[10px]">Asal Sekolah SMP/MTs</span>
                    <p class="font-bold text-slate-900 text-sm">{{ $akademik?->nama_sekolah ?? $calonSiswa->asalSekolah?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }} (NPSN: {{ $akademik?->npsn ?? $calonSiswa->asalSekolah?->npsn ?? '-' }})</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-3.5 py-1.5 rounded-xl bg-orange-50 border border-orange-200">
                        <span class="text-[10px] uppercase font-bold text-orange-700 block">Rata-rata Rapor</span>
                        <span class="text-base font-black text-orange-600 font-mono">{{ $akademik?->nilai_rata_rata ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Matrix Nilai Rapor -->
            @if($rapor)
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Mata Pelajaran Pokok</th>
                                <th class="py-2.5 px-2 text-center">Sem 1</th>
                                <th class="py-2.5 px-2 text-center">Sem 2</th>
                                <th class="py-2.5 px-2 text-center">Sem 3</th>
                                <th class="py-2.5 px-2 text-center">Sem 4</th>
                                <th class="py-2.5 px-2 text-center">Sem 5</th>
                                <th class="py-2.5 px-2 text-center bg-orange-50 text-orange-800">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            <tr>
                                <td class="py-2 px-3 font-bold font-sans text-slate-800">1. Matematika</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->mtk_sem1 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->mtk_sem2 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->mtk_sem3 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->mtk_sem4 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->mtk_sem5 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center font-bold text-orange-700 bg-orange-50/40">{{ $akademik?->nilai_matematika ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold font-sans text-slate-800">2. Bahasa Indonesia</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->ind_sem1 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->ind_sem2 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->ind_sem3 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->ind_sem4 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->ind_sem5 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center font-bold text-orange-700 bg-orange-50/40">{{ $akademik?->nilai_bahasa_indonesia ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold font-sans text-slate-800">3. Bahasa Inggris</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->eng_sem1 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->eng_sem2 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->eng_sem3 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->eng_sem4 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->eng_sem5 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center font-bold text-orange-700 bg-orange-50/40">{{ $akademik?->nilai_bahasa_inggris ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold font-sans text-slate-800">4. Pendidikan Agama</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->pai_sem1 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->pai_sem2 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->pai_sem3 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->pai_sem4 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center">{{ $rapor->pai_sem5 ?? '-' }}</td>
                                <td class="py-2 px-2 text-center font-bold text-orange-700 bg-orange-50/40">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-white p-3 rounded-2xl border border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Matematika</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $akademik?->nilai_matematika ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Bahasa Indonesia</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $akademik?->nilai_bahasa_indonesia ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Bahasa Inggris</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $akademik?->nilai_bahasa_inggris ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">IPA</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $akademik?->nilai_ipa ?? '-' }}</span>
                    </div>
                </div>
            @endif

            <!-- Prestasi Perlombaan -->
            @if($prestasiList && $prestasiList->count() > 0)
                <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <span class="font-bold text-slate-800 block text-xs">🏆 Prestasi & Kejuaraan yang Dilampirkan:</span>
                    <div class="space-y-1.5">
                        @foreach($prestasiList as $pres)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $pres->nama_prestasi }}</span>
                                    <span class="text-slate-400 text-[11px]">({{ ucfirst($pres->jenis_prestasi) }} • Tingkat {{ ucfirst($pres->tingkat) }})</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg bg-orange-100 text-orange-800 font-bold text-[10px]">
                                    {{ $pres->peringkat ?? 'Peserta' }} ({{ $pres->tahun }})
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: BERKAS DOKUMEN                         -->
        <!-- ============================================== -->
        @php
            $dokumen = $calonSiswa->dokumenPendaftaran;
        @endphp
        <div x-show="activeTab === 'dokumen'" class="space-y-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div class="p-3 bg-white rounded-2xl border border-slate-200 text-xs text-center space-y-1.5">
                    <span class="block text-slate-400 font-bold text-[10px] uppercase">Kartu Keluarga</span>
                    @if($dokumen?->kk_path)
                        <a href="{{ asset('storage/' . $dokumen->kk_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition">
                            <span>Lihat Berkas</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @else
                        <span class="text-slate-400 italic text-[11px]">Belum diunggah</span>
                    @endif
                </div>

                <div class="p-3 bg-white rounded-2xl border border-slate-200 text-xs text-center space-y-1.5">
                    <span class="block text-slate-400 font-bold text-[10px] uppercase">Akta Kelahiran</span>
                    @if($dokumen?->akta_path)
                        <a href="{{ asset('storage/' . $dokumen->akta_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition">
                            <span>Lihat Berkas</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @else
                        <span class="text-slate-400 italic text-[11px]">Belum diunggah</span>
                    @endif
                </div>

                <div class="p-3 bg-white rounded-2xl border border-slate-200 text-xs text-center space-y-1.5">
                    <span class="block text-slate-400 font-bold text-[10px] uppercase">Ijazah / SKL</span>
                    @if($dokumen?->ijazah_skl_path)
                        <a href="{{ asset('storage/' . $dokumen->ijazah_skl_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition">
                            <span>Lihat Berkas</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @else
                        <span class="text-slate-400 italic text-[11px]">Belum diunggah</span>
                    @endif
                </div>

                <div class="p-3 bg-white rounded-2xl border border-slate-200 text-xs text-center space-y-1.5">
                    <span class="block text-slate-400 font-bold text-[10px] uppercase">Pas Foto Siswa</span>
                    @if($dokumen?->pas_foto_path)
                        <a href="{{ asset('storage/' . $dokumen->pas_foto_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition">
                            <span>Lihat Foto</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @else
                        <span class="text-slate-400 italic text-[11px]">Belum diunggah</span>
                    @endif
                </div>

                <div class="p-3 bg-white rounded-2xl border border-slate-200 text-xs text-center space-y-1.5 col-span-2 sm:col-span-1">
                    <span class="block text-slate-400 font-bold text-[10px] uppercase">Dokumen Pendukung</span>
                    @if($dokumen?->dokumen_pendukung_path)
                        <a href="{{ asset('storage/' . $dokumen->dokumen_pendukung_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition">
                            <span>Lihat Berkas</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @else
                        <span class="text-slate-400 italic text-[11px]">Belum diunggah</span>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
