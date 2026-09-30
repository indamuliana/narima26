<x-layouts.app>
    <x-slot name="title">Surat Pernyataan & Kesepahaman SPMB</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Perhatian">{{ session('error') }}</x-alert>
        @endif

        @php
            $isAgreed = $eula && $eula->setuju;
            $approvedPointIds = $eula?->poin_disetujui ?? [];
            $totalPoints = 0;
            foreach ($klausulData['kelompok'] as $k) {
                $totalPoints += count($k['poin'] ?? []);
            }
            $ortu = $calonSiswa->dataOrangtua ?: $calonSiswa->orangTua;
            $isUnggulan = ($klausulData['program_key'] ?? '') === 'unggulan';
        @endphp

        <!-- Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-nampi-orange uppercase tracking-wider">Tahap 4 SPMB &bull; Persetujuan Digital</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black {{ $isUnggulan ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                            PROGRAM {{ $klausulData['program_title'] }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">
                        Naskah Persetujuan & Kesepahaman Bersama (EULA)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        SMK Wikrama 1 Garut &bull; Tahun Pelajaran {{ $klausulData['tahun_pelajaran'] ?? '2027/2028' }}
                    </p>
                </div>
                <div>
                    @if($isAgreed)
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            TELAH DISETUJUI SECARA DIGITAL
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-black bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            MENUNGGU PERSETUJUAN
                        </span>
                    @endif
                </div>
            </div>

            <!-- Draft Identitas Pihak Bertandatangan -->
            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200 text-xs space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                    Pihak Yang Bertanda Tangan Dalam Naskah Persetujuan:
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Calon Peserta Didik</span>
                        <strong class="text-slate-900 block mt-0.5">{{ $calonSiswa->nama_lengkap }}</strong>
                        <span class="font-mono text-slate-500 text-[11px]">{{ $calonSiswa->nomor_pendaftaran }} &bull; NISN: {{ $calonSiswa->nisn }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Orang Tua (Ayah)</span>
                        <strong class="text-slate-900 block mt-0.5">{{ $ortu?->nama_ayah ?: '-' }}</strong>
                        <span class="text-slate-500 text-[11px] line-clamp-1" title="{{ $ortu?->alamat_ayah ?: $calonSiswa->alamat_lengkap }}">
                            Alamat: {{ $ortu?->alamat_ayah ?: ($calonSiswa->alamat_lengkap ?: '-') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Orang Tua (Ibu)</span>
                        <strong class="text-slate-900 block mt-0.5">{{ $ortu?->nama_ibu ?: '-' }}</strong>
                        <span class="text-slate-500 text-[11px] line-clamp-1" title="{{ $ortu?->alamat_ibu ?: $calonSiswa->alamat_lengkap }}">
                            Alamat: {{ $ortu?->alamat_ibu ?: ($calonSiswa->alamat_lengkap ?: '-') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Wali Siswa</span>
                        <strong class="text-slate-900 block mt-0.5">{{ $ortu?->nama_wali ?: '-' }}</strong>
                        <span class="text-slate-500 text-[11px] line-clamp-1">
                            {{ $ortu?->nama_wali ? 'Alamat: ' . ($ortu->alamat_wali ?: $calonSiswa->alamat_lengkap) : '(Tidak ada wali)' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Jika sudah disetujui -->
            @if($isAgreed)
                <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-3">
                    <div class="flex items-center text-emerald-900 font-bold text-sm">
                        <svg class="w-5 h-5 text-emerald-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Persetujuan Digital Anda Telah Tercatat Sah Pada Sistem SPMB (Wajib dicetak dan dibubuhi materai + tanda tangan basah)
                    </div>
                    <div class="text-xs text-emerald-800 space-y-1">
                        <p>Disetujui secara digital pada: <strong>{{ \Carbon\Carbon::parse($eula->agreed_at)->translatedFormat('d F Y H:i:s') }} WIB</strong></p>
                        <p>Versi Dokumen: <strong>{{ $eula->versi_dokumen }}</strong> &bull; Program: <strong>{{ $eula->program_snapshot ?? $klausulData['program_title'] }}</strong> &bull; IP Address: <strong>{{ $eula->ip_address }}</strong></p>
                    </div>
                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('calon-siswa.kesepahaman.cetak') }}" target="_blank"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Unduh Naskah Persetujuan Resmi (PDF)
                        </a>
                        <a href="{{ route('calon-siswa.dokumen.kartu') }}" target="_blank"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-orange-700 bg-orange-100 hover:bg-orange-200 transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            Cetak Kartu Tanda Peserta (PDF)
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Klausul & Tabel Checklist Kesepahaman -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900">
                        Butir-Butir Pakta Integritas & Kesepahaman Calon Siswa
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Bacalah bersama orang tua / wali. Ceklis setiap butir poin di bawah ini sebagai wujud kesediaan mengikuti ketentuan sekolah.
                    </p>
                </div>

            </div>

            <!-- Form Persetujuan -->
            <form id="formKesepahaman" action="{{ route('calon-siswa.kesepahaman.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Progress Centang Live -->
                @if(!$isAgreed)
                    <div class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="font-bold">Status Checklist:</span>
                            <span id="counterStatus" class="font-bold text-amber-800">
                                <span id="countChecked">0</span> dari {{ $totalPoints }} butir telah dicentang
                            </span>
                        </div>
                        <div class="w-32 bg-amber-200/70 rounded-full h-2 overflow-hidden shrink-0">
                            <div id="progressBar" class="bg-amber-600 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                        </div>
                    </div>
                @endif

                <!-- Tabel Klausul (No | Uraian | Checklis) -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 uppercase font-black tracking-wider text-[11px] border-b border-slate-200">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-5">Uraian Butir Kesepahaman</th>
                                <th class="py-3 px-5 w-28 text-center">Checklis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($klausulData['kelompok'] as $kelompok)
                                <!-- Kelompok Sub-header Row -->
                                <tr class="bg-slate-50/80 font-black text-slate-900 border-t-2 border-slate-200">
                                    <td colspan="3" class="py-3 px-5 text-xs text-slate-800 uppercase tracking-wide">
                                        {{ $kelompok['judul'] ?? $kelompok['nama_kelompok'] }}
                                    </td>
                                </tr>

                                <!-- Poin Rows -->
                                @foreach($kelompok['poin'] ?? [] as $poin)
                                    @php
                                        $poinId = $poin['id'];
                                        $isChecked = $isAgreed ? (in_array($poinId, $approvedPointIds) || true) : false;
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 text-center font-bold text-slate-500 align-top">
                                            {{ $poin['nomor'] ?? $loop->iteration }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-800 leading-relaxed align-top">
                                            <label for="chk_{{ $poinId }}" class="{{ !$isAgreed ? 'cursor-pointer select-none' : '' }}">
                                                {!! nl2br(e($poin['uraian'])) !!}
                                            </label>
                                        </td>
                                        <td class="py-3 px-5 text-center align-top">
                                            @if($isAgreed)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                    ✓ Disetujui
                                                </span>
                                            @else
                                                <input type="checkbox"
                                                       name="checklist_poin[]"
                                                       id="chk_{{ $poinId }}"
                                                       value="{{ $poinId }}"
                                                       onchange="updateProgress()"
                                                       class="point-checkbox w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pernyataan Konsensus & Tombol Submit (Hanya jika belum disetujui) -->
                @if(!$isAgreed)
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-300 space-y-3">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="setuju" id="checkSetuju" value="1" onchange="updateProgress()"
                                   class="mt-1 w-5 h-5 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer shrink-0">
                            <label for="checkSetuju" class="text-xs text-amber-950 font-bold leading-normal cursor-pointer select-none">
                                Kami menyatakan bahwa Saya bersama Orang Tua / Wali Calon Siswa telah membaca, memahami secara sadar, dan MENYETUJUI seluruh butir ketentuan dan pakta integritas di atas tanpa paksaan dari pihak manapun, serta siap menandatangani Naskah Persetujuan resmi bermaterai.
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                        <span id="btnAlert" class="text-xs text-slate-500 italic">
                            * Anda harus mencentang seluruh {{ $totalPoints }} butir dan pernyataan persetujuan untuk mengaktifkan tombol.
                        </span>
                        <button type="submit" id="btnSubmitConsent" disabled
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-slate-400 cursor-not-allowed transition transform shadow-xs">
                            <span>Setujui Kesepahaman & Lanjut ke Wawancara</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </form>
        </div>

    </div>

    @if(!$isAgreed)
        <script>


            function updateProgress() {
                const checkboxes = document.querySelectorAll('.point-checkbox');
                const total = checkboxes.length;
                let checkedCount = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) checkedCount++;
                });

                const countElem = document.getElementById('countChecked');
                if (countElem) countElem.innerText = checkedCount;

                const progressBar = document.getElementById('progressBar');
                if (progressBar) {
                    const pct = total > 0 ? Math.round((checkedCount / total) * 100) : 0;
                    progressBar.style.width = pct + '%';
                }

                const checkSetuju = document.getElementById('checkSetuju');
                const isAllChecked = (checkedCount === total) && checkSetuju && checkSetuju.checked;

                const btn = document.getElementById('btnSubmitConsent');
                const alertMsg = document.getElementById('btnAlert');

                if (isAllChecked) {
                    btn.disabled = false;
                    btn.classList.remove('bg-slate-400', 'cursor-not-allowed');
                    btn.classList.add('bg-orange-600', 'hover:bg-orange-700', 'cursor-pointer', 'shadow-md', 'active:scale-95');
                    if (alertMsg) alertMsg.innerText = '✓ Seluruh butir telah dicentang. Anda dapat menyimpan persetujuan sekarang.';
                    if (alertMsg) alertMsg.classList.add('text-emerald-600', 'font-bold');
                } else {
                    btn.disabled = true;
                    btn.classList.add('bg-slate-400', 'cursor-not-allowed');
                    btn.classList.remove('bg-orange-600', 'hover:bg-orange-700', 'cursor-pointer', 'shadow-md', 'active:scale-95');
                    if (alertMsg) alertMsg.innerText = '* Anda harus mencentang seluruh ' + total + ' butir dan pernyataan persetujuan untuk mengaktifkan tombol.';
                    if (alertMsg) alertMsg.classList.remove('text-emerald-600', 'font-bold');
                }
            }

            // Run initial check on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateProgress();
            });
        </script>
    @endif
</x-layouts.app>
