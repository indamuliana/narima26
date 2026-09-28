<x-layouts.app>
    <x-slot name="title">Detail Calon Siswa — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Back Navigation & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('admin.calon-siswa.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <span>&larr; Kembali ke Direktori Calon Siswa</span>
            </a>
            <div class="flex items-center gap-2">
                @if ($calonSiswa->keputusanKelulusan)
                    <a href="{{ route('kepala-sekolah.sidang-kelulusan.cetak-sk', $calonSiswa) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                        <span>📄 Unduh SK Kelulusan PDF</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Candidate Primary Banner -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xl shrink-0 shadow-xs">
                    {{ strtoupper(substr($calonSiswa->nama_lengkap, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-xs font-bold text-nampi-orange">{{ $calonSiswa->nomor_pendaftaran }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">NISN: {{ $calonSiswa->nisn }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ $calonSiswa->jenis_kelamin }}</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 mt-1">{{ $calonSiswa->nama_lengkap }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $calonSiswa->jurusan?->nama_jurusan }} &bull; {{ $calonSiswa->program?->nama_program }} &bull; {{ $calonSiswa->gelombang?->nama_gelombang }}
                    </p>
                </div>
            </div>
            <div>
                @php
                    $statusStr = is_string($calonSiswa->status_spmb) ? $calonSiswa->status_spmb : ($calonSiswa->status_spmb?->value ?? '-');
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-black
                    {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                       (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                    <span class="w-2 h-2 rounded-full {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR']) ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    {{ str_replace('_', ' ', $statusStr) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Detailed Profile Data (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- 1. Biodata Pribadi -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                        1. Biodata Pribadi Calon Siswa
                    </h2>
                    <div class="grid grid-cols-2 gap-4 text-xs text-slate-600">
                        <div>
                            <span class="text-slate-400 block">Tempat, Tanggal Lahir</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">
                                {{ $calonSiswa->tempat_lahir ?? '-' }}, {{ $calonSiswa->tanggal_lahir ? $calonSiswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Agama</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $calonSiswa->agama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">NIK & No. KK</span>
                            <span class="font-mono font-semibold text-slate-800 mt-0.5 block">
                                NIK: {{ $calonSiswa->nik ?? '-' }}<br>KK: {{ $calonSiswa->no_kk ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nomor Telepon / WhatsApp</span>
                            <span class="font-mono font-semibold text-slate-800 mt-0.5 block">{{ $calonSiswa->no_hp_siswa ?? '-' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-400 block">Alamat Domisili Lengkap</span>
                            <span class="font-medium text-slate-800 mt-0.5 block leading-relaxed">
                                {{ $calonSiswa->alamat_lengkap ?? '-' }}
                                @if ($calonSiswa->rt || $calonSiswa->rw)
                                    (RT {{ $calonSiswa->rt ?? '0' }} / RW {{ $calonSiswa->rw ?? '0' }})
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Sekolah Asal SMP / MTs</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">
                                {{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Ukuran Seragam</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">
                                @if ($calonSiswa->ukuranSeragam->count() > 0)
                                    {{ $calonSiswa->ukuranSeragam->pluck('ukuran')->join(', ') }}
                                @else
                                    <span class="text-slate-400 italic">Belum diinput</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. Data Orang Tua / Wali -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                        2. Data Orang Tua / Wali
                    </h2>
                    @php $ortu = $calonSiswa->dataOrangtua; @endphp
                    @if ($ortu)
                        <div class="grid grid-cols-2 gap-4 text-xs text-slate-600">
                            <div>
                                <span class="text-slate-400 block">Nama Ayah</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $ortu->nama_ayah ?? '-' }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $ortu->pekerjaan_ayah ?? '-' }} &bull; HP: {{ $ortu->no_hp_ayah ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Nama Ibu</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $ortu->nama_ibu ?? '-' }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $ortu->pekerjaan_ibu ?? '-' }} &bull; HP: {{ $ortu->no_hp_ibu ?? '-' }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-slate-400 block">Penghasilan Gabungan Orang Tua</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $ortu->penghasilan_gabungan ?? '-' }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Data orang tua belum dilengkapi oleh calon siswa.</p>
                    @endif
                </div>

                <!-- 3. Berkas & Dokumen Persyaratan -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                        3. Berkas & Dokumen Persyaratan
                    </h2>
                    @php $doc = $calonSiswa->dokumenPendaftaran; @endphp
                    @if ($doc)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block">Kartu Keluarga (KK)</span>
                                    <span class="text-slate-400 text-[11px]">{{ $doc->kk_path ? '✓ Berkas Terunggah' : 'Belum Ada' }}</span>
                                </div>
                                @if ($doc->kk_path)
                                    <a href="{{ asset('storage/' . $doc->kk_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold text-[10px]">Lihat</a>
                                @endif
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block">Akta Kelahiran</span>
                                    <span class="text-slate-400 text-[11px]">{{ $doc->akta_path ? '✓ Berkas Terunggah' : 'Belum Ada' }}</span>
                                </div>
                                @if ($doc->akta_path)
                                    <a href="{{ asset('storage/' . $doc->akta_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold text-[10px]">Lihat</a>
                                @endif
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block">Ijazah / SKL SMP</span>
                                    <span class="text-slate-400 text-[11px]">{{ $doc->ijazah_skl_path ? '✓ Berkas Terunggah' : 'Belum Ada' }}</span>
                                </div>
                                @if ($doc->ijazah_skl_path)
                                    <a href="{{ asset('storage/' . $doc->ijazah_skl_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold text-[10px]">Lihat</a>
                                @endif
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 block">Pas Foto Calon Siswa</span>
                                    <span class="text-slate-400 text-[11px]">{{ $doc->pas_foto_path ? '✓ Berkas Terunggah' : 'Belum Ada' }}</span>
                                </div>
                                @if ($doc->pas_foto_path)
                                    <a href="{{ asset('storage/' . $doc->pas_foto_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold text-[10px]">Lihat</a>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum ada berkas digital yang diunggah oleh calon siswa.</p>
                    @endif
                </div>
            </div>

            <!-- Right Column: SPMB Workflow, Finance, Interview, Decision (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                <!-- 1. Status Pembayaran Seleksi -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Biaya Seleksi Pendaftaran</h2>
                        @php $bayarSeleksi = $calonSiswa->pembayaranSeleksi; @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $bayarSeleksi?->status === 'DIVERIFIKASI' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $bayarSeleksi?->status ?? 'BELUM' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Nominal Tagihan:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($bayarSeleksi?->nominal_tagihan ?? 250000, 0, ',', '.') }}</span>
                    </div>
                    @if ($bayarSeleksi && $bayarSeleksi->bukti_bayar)
                        <div class="pt-2">
                            <a href="{{ asset('storage/' . $bayarSeleksi->bukti_bayar) }}" target="_blank"
                               class="text-xs font-semibold text-nampi-orange hover:underline block">
                                ↗ Lihat Bukti Transfer Seleksi
                            </a>
                        </div>
                    @endif
                </div>

                <!-- 2. Hasil Tes Wawancara -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Hasil Evaluasi Wawancara</h2>
                        @php $wawancara = $calonSiswa->wawancaraTerakhir; @endphp
                        @if ($wawancara)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                Skor Rata: {{ round($wawancara->details->avg('skor')) }}/100
                            </span>
                        @endif
                    </div>
                    @if ($wawancara)
                        <div class="text-xs text-slate-600 space-y-1">
                            <p>Penguji: <strong>{{ $wawancara->pewawancara?->name ?? 'Pewawancara' }}</strong></p>
                            <p>Tanggal: <strong>{{ $wawancara->tanggal_wawancara?->format('d F Y') }}</strong></p>
                            @if ($wawancara->catatan_siswa)
                                <p class="text-slate-500 mt-1 italic">"{{ $wawancara->catatan_siswa }}"</p>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum ada evaluasi tes wawancara yang tersimpan.</p>
                    @endif
                </div>

                <!-- 3. Keputusan Sidang Pleno Kelulusan -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Keputusan Sidang Pleno</h2>
                        @php $keputusan = $calonSiswa->keputusanKelulusan; @endphp
                        @if ($keputusan)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $keputusan->keputusan === 'DITERIMA' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $keputusan->keputusan }}
                            </span>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Belum Ada Keputusan</span>
                        @endif
                    </div>
                    @if ($keputusan)
                        <div class="text-xs text-slate-600 space-y-1">
                            <p>Ditetapkan oleh: <strong>{{ $keputusan->ditetapkanOleh?->name ?? 'Kepala Sekolah' }}</strong></p>
                            <p>Waktu: <strong>{{ $keputusan->ditetapkan_at?->translatedFormat('d F Y H:i') }}</strong></p>
                            @if ($keputusan->alasan_catatan)
                                <p class="p-2.5 rounded-xl bg-slate-50 text-slate-700 text-[11px] mt-1">{{ $keputusan->alasan_catatan }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- 4. Keuangan Daftar Ulang -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Keuangan Daftar Ulang</h2>
                        @php $tagihan = $calonSiswa->tagihan->first(); @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $tagihan?->status === 'LUNAS' ? 'bg-emerald-50 text-emerald-700' : 'bg-purple-50 text-purple-700' }}">
                            {{ $tagihan?->status ?? 'BELUM ADA TAGIHAN' }}
                        </span>
                    </div>
                    @if ($tagihan)
                        @php
                            $totalBayarVerified = (float) $tagihan->pembayaran->where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
                            $sisaPiutangSiswa = max(0, (float) $tagihan->total_netto - $totalBayarVerified);
                        @endphp
                        <div class="text-xs text-slate-600 space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Total Tagihan Bersih:</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Telah Dibayar:</span>
                                <span class="font-bold text-emerald-600">Rp {{ number_format($totalBayarVerified, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-100 pt-1">
                                <span class="text-slate-400 font-bold">Sisa Piutang:</span>
                                <span class="font-black text-rose-600">Rp {{ number_format($sisaPiutangSiswa, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 5. Riwayat Status SPMB (Audit Trail) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                        Riwayat Transisi Status
                    </h2>
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                        @forelse ($calonSiswa->riwayatStatus->sortByDesc('id') as $riwayat)
                            <div class="text-xs border-l-2 border-nampi-orange pl-3 py-1 space-y-0.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ str_replace('_', ' ', $riwayat->status_baru) }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $riwayat->created_at?->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                <p class="text-slate-500 text-[11px]">{{ $riwayat->alasan ?: ($riwayat->catatan ?: 'Perubahan otomatis sistem') }}</p>
                                <span class="text-[10px] text-slate-400 block font-mono">Oleh: {{ $riwayat->diubahOleh?->name ?? 'Sistem' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada riwayat transisi status.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
