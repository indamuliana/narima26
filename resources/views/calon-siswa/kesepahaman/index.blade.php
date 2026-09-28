<x-layouts.app>
    <x-slot name="title">Surat Pernyataan & Kesepahaman SPMB</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('calon-siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Pembayaran Seleksi</span>
        </a>
        <a href="{{ route('calon-siswa.lengkapi-data.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Lengkapi Data & Berkas</span>
        </a>
        <a href="{{ route('calon-siswa.kesepahaman.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Kesepahaman SPMB</span>
        </a>
        <a href="{{ route('calon-siswa.dokumen.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Dokumen & Cetak PDF</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Perhatian">{{ session('error') }}</x-alert>
        @endif

        <!-- Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-nampi-orange uppercase tracking-wider">Tahap 4 SPMB &bull; Persetujuan Digital</span>
                    <h1 class="text-2xl font-black text-slate-900 mt-1">Surat Pernyataan & Kesepahaman Bersama (EULA)</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        SMK Wikrama 1 Garut &bull; Tahun Pelajaran 2026/2027
                    </p>
                </div>
                <div>
                    @if($eula && $eula->setuju)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            TELAH DISETUJUI
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                            MENUNGGU PERSETUJUAN
                        </span>
                    @endif
                </div>
            </div>

            <!-- Identitas Siswa Singkat -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">No. Pendaftaran</span>
                    <strong class="text-slate-900 font-mono">{{ $calonSiswa->nomor_pendaftaran }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Nama Siswa</span>
                    <strong class="text-slate-900">{{ $calonSiswa->nama_lengkap }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Kompetensi / Jurusan</span>
                    <strong class="text-slate-900">{{ $calonSiswa->jurusan?->nama ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Orang Tua / Wali</span>
                    <strong class="text-slate-900">{{ $calonSiswa->orangTua?->nama_ayah ?? $calonSiswa->orangTua?->nama_ibu ?? $calonSiswa->orangTua?->nama_wali ?? '-' }}</strong>
                </div>
            </div>

            <!-- Jika sudah disetujui -->
            @if($eula && $eula->setuju)
                <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-3">
                    <div class="flex items-center text-emerald-900 font-bold text-sm">
                        <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Persetujuan Digital Anda Telah Tercatat Sah
                    </div>
                    <div class="text-xs text-emerald-800 space-y-1">
                        <p>Dokumen disetujui secara digital pada: <strong>{{ \Carbon\Carbon::parse($eula->agreed_at)->translatedFormat('d F Y H:i:s') }} WIB</strong></p>
                        <p>Versi Dokumen: <strong>{{ $eula->versi_dokumen }}</strong> &bull; IP Address: <strong>{{ $eula->ip_address }}</strong></p>
                    </div>
                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('calon-siswa.kesepahaman.cetak') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Surat Kesepahaman (PDF)
                        </a>
                        <a href="{{ route('calon-siswa.dokumen.kartu') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-orange-700 bg-orange-100 hover:bg-orange-200 transition text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            Cetak Kartu Tanda Peserta (PDF)
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Klausul & Isi Kesepahaman -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div>
                <h3 class="text-base font-black text-slate-900">Butir-Butir Pakta Integritas & Kesepahaman Calon Siswa</h3>
                <p class="text-xs text-slate-500">Bacalah seluruh klausul di bawah ini bersama orang tua / wali sebelum memberikan persetujuan elektronik.</p>
            </div>

            <div class="space-y-4 text-xs text-slate-700 leading-relaxed border border-slate-200 rounded-2xl p-5 bg-slate-50 max-h-96 overflow-y-auto">
                <div class="space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">PASAL 1 — KEABSAHAN & KEASLIAN DATA</h4>
                    <p>
                        Calon siswa dan orang tua/wali menyatakan bahwa seluruh data pribadi, kependudukan, nilai raport, sertifikat prestasi, dan berkas persyaratan yang diunggah ke dalam Sistem Penerimaan Murid Baru (SPMB) SMK Wikrama 1 Garut adalah sah, benar, dan dapat dipertanggungjawabkan secara hukum. Apabila di kemudian hari ditemukan pemalsuan identitas atau dokumen, pihak sekolah berhak membatalkan status kelulusan secara sepihak tanpa pengembalian biaya yang telah dibayarkan.
                    </p>
                </div>

                <div class="space-y-2 pt-3 border-t border-slate-200">
                    <h4 class="font-bold text-slate-900 text-sm">PASAL 2 — KEPATUHAN TATA TERTIB & KARAKTER WIKRAMA</h4>
                    <p>
                        Calon siswa bersedia mematuhi dan mengamalkan seluruh Tata Tertib Sekolah, Budaya Kerja Industri, Pembiasaan Adab & Karakter, Program 5S (Senyum, Salam, Sapa, Sopan, Santun), serta aturan kedisiplinan yang ditetapkan oleh SMK Wikrama 1 Garut, termasuk komitmen kehadiran, seragam lengkap, larangan merokok/vape, dan anti-perundungan (bullying).
                    </p>
                </div>

                <div class="space-y-2 pt-3 border-t border-slate-200">
                    <h4 class="font-bold text-slate-900 text-sm">PASAL 3 — KOMITMEN KEUANGAN & DAFTAR ULANG</h4>
                    <p>
                        Orang tua/wali calon siswa bersedia menyelesaikan seluruh kewajiban administrasi pembiayaan pendidikan (DSP, SPP bulanan, paket seragam, dan biaya kegiatan MPLS) sesuai dengan nominal snapshot tagihan resmi dan jadwal pembayaran daftar ulang yang telah ditetapkan oleh sekolah.
                    </p>
                </div>

                <div class="space-y-2 pt-3 border-t border-slate-200">
                    <h4 class="font-bold text-slate-900 text-sm">PASAL 4 — KEIKUTSERTAAN TAHAPAN SELEKSI</h4>
                    <p>
                        Calon siswa dan orang tua/wali bersedia menghadiri dan mengikuti seluruh rangkaian tes wawancara, observasi minat kejuruan, dan pemetaan potensi akademik sesuai jadwal yang ditentukan oleh panitia SPMB dengan membawa berkas fisik asli untuk verifikasi faktual.
                    </p>
                </div>

                <div class="space-y-2 pt-3 border-t border-slate-200">
                    <h4 class="font-bold text-slate-900 text-sm">PASAL 5 — PERSETUJUAN PENGGUNAAN DATA DAPODIK</h4>
                    <p>
                        Calon siswa dan orang tua/wali memberikan izin resmi kepada SMK Wikrama 1 Garut untuk memproses, menyimpan, dan mengintegrasikan data pendaftaran ke dalam sistem Data Pokok Pendidikan (Dapodik) Kementerian Pendidikan Dasar dan Menengah Republik Indonesia serta dinas pendidikan terkait.
                    </p>
                </div>
            </div>

            <!-- Form Persetujuan EULA (jika belum disetujui) -->
            @if(!$eula || !$eula->setuju)
                <form action="{{ route('calon-siswa.kesepahaman.store') }}" method="POST" class="pt-2 space-y-5">
                    @csrf

                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 flex items-start gap-3">
                        <input type="checkbox" name="setuju" id="checkSetuju" value="1" onchange="toggleConsent(this)"
                            class="mt-1 w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                        <label for="checkSetuju" class="text-xs text-amber-950 font-bold leading-normal cursor-pointer select-none">
                            Saya bersama Orang Tua / Wali Calon Siswa telah membaca, memahami, dan MENYETUJUI seluruh isi Surat Pernyataan, Tata Tertib, dan Kesepahaman SPMB SMK Wikrama 1 Garut Tahun Pelajaran 2026/2027 di atas tanpa ada paksaan dari pihak manapun.
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" id="btnSubmitConsent" disabled
                            class="inline-flex items-center px-7 py-3 rounded-xl font-bold text-sm text-white bg-slate-400 cursor-not-allowed transition transform shadow-xs">
                            <span>Setujui Kesepahaman & Lanjut ke Wawancara</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                <script>
                    function toggleConsent(checkbox) {
                        const btn = document.getElementById('btnSubmitConsent');
                        if (checkbox.checked) {
                            btn.disabled = false;
                            btn.classList.remove('bg-slate-400', 'cursor-not-allowed');
                            btn.classList.add('bg-orange-500', 'hover:bg-orange-600', 'cursor-pointer', 'shadow-md', 'active:scale-95');
                        } else {
                            btn.disabled = true;
                            btn.classList.add('bg-slate-400', 'cursor-not-allowed');
                            btn.classList.remove('bg-orange-500', 'hover:bg-orange-600', 'cursor-pointer', 'shadow-md', 'active:scale-95');
                        }
                    }
                </script>
            @endif
        </div>

    </div>
</x-layouts.app>
