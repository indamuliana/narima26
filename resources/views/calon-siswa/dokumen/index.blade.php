<x-layouts.app>
    <x-slot name="title">Pusat Dokumen & Cetak PDF SPMB</x-slot>

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
        <a href="{{ route('calon-siswa.kesepahaman.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Kesepahaman SPMB</span>
        </a>
        <a href="{{ route('calon-siswa.dokumen.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Perhatian">{{ session('error') }}</x-alert>
        @endif

        <!-- Header Banner -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-nampi-orange uppercase tracking-wider">Pusat Cetak Dokumen Resmi</span>
                <h1 class="text-2xl font-black text-slate-900 mt-1">Dokumen & Berkas PDF SPMB</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Seluruh dokumen resmi ber-KOP resmi SMK Wikrama 1 Garut dapat diunduh langsung di bawah ini.
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block">No. Pendaftaran</span>
                <strong class="text-base text-slate-900 font-mono">{{ $calonSiswa->nomor_pendaftaran }}</strong>
            </div>
        </div>

        <!-- Grid Kartu Dokumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- 1. KARTU TANDA PESERTA SPMB -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Dokumen Utama Seleksi</span>
                        @if($eligibleForCard)
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Siap Dicetak
                            </span>
                        @else
                            <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span> Belum Siap
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Kartu Tanda Peserta SPMB</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Kartu identitas resmi peserta seleksi yang memuat foto 3x4, nomor pendaftaran, barcode/QR verifikasi, dan jadwal pelaksanaan tes wawancara. Wajib dicetak dan dibawa saat tes.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    @if($eligibleForCard)
                        <a href="{{ route('calon-siswa.dokumen.kartu') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Kartu Peserta (PDF)
                        </a>
                    @else
                        <span class="inline-flex items-center text-xs text-slate-400 font-semibold">
                            Selesaikan Pengisian Data Terlebih Dahulu
                        </span>
                    @endif
                </div>
            </div>

            <!-- 2. SURAT KESEPAHAMAN / EULA -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-purple-600 bg-purple-50 px-3 py-1 rounded-full border border-purple-200">Pakta Integritas</span>
                        @if($eula && $eula->setuju)
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Disetujui
                            </span>
                        @else
                            <span class="text-xs font-bold text-amber-500 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Belum Disetujui
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Surat Kesepahaman & Pernyataan (EULA)</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Surat pernyataan kepatuhan tata tertib, komitmen pembiayaan pendidikan, dan persetujuan data Dapodik yang telah ditandatangani secara digital oleh orang tua dan calon siswa.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    @if($eula && $eula->setuju)
                        <a href="{{ route('calon-siswa.dokumen.kesepahaman') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-purple-600 hover:bg-purple-700 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Surat Kesepahaman (PDF)
                        </a>
                    @else
                        <a href="{{ route('calon-siswa.kesepahaman.index') }}"
                            class="inline-flex items-center text-xs text-purple-600 hover:text-purple-800 font-bold">
                            Buka Lembar Kesepahaman &rarr;
                        </a>
                    @endif
                </div>
            </div>

            <!-- 3. INFORMASI AKUN PENDAFTARAN -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-200">Akses Sistem</span>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tersedia
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Informasi Akun Calon Siswa</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Bukti registrasi awal yang berisi nomor pendaftaran, NISN (username login), petunjuk kata sandi default, dan pilihan kejuruan yang dipilih.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <a href="{{ route('calon-siswa.dokumen.akun') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 transition shadow-sm text-xs cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Unduh Bukti Akun (PDF)
                    </a>
                </div>
            </div>

            <!-- 4. KWITANSI BIAYA SELEKSI -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">Keuangan Seleksi</span>
                        @if($pembayaran && $pembayaran->status === 'DIVERIFIKASI')
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lunas
                            </span>
                        @else
                            <span class="text-xs font-bold text-amber-500 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Belum Lunas
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Kwitansi Pembayaran Biaya Seleksi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tanda bukti pembayaran resmi biaya seleksi masuk (Rp 250.000) yang telah diverifikasi dan disahkan oleh Bendahara SPMB SMK Wikrama 1 Garut.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if($pembayaran && $pembayaran->status === 'DIVERIFIKASI')
                        <a href="{{ route('calon-siswa.pembayaran-seleksi.cetak') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Kwitansi Seleksi (PDF)
                        </a>
                    @else
                        <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}"
                            class="inline-flex items-center text-xs text-emerald-600 hover:text-emerald-800 font-bold">
                            Lihat Status Pembayaran &rarr;
                        </a>
                    @endif
                </div>
            </div>

            <!-- 5. SURAT KEPUTUSAN HASIL SELEKSI (SK KELULUSAN) -->
            @php
                $statusVal = is_string($calonSiswa->status_spmb) ? $calonSiswa->status_spmb : $calonSiswa->status_spmb->value;
                $hasSk = $keputusan !== null || in_array($statusVal, ['DITERIMA', 'DITOLAK', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']);
            @endphp
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">Hasil Seleksi</span>
                        @if($hasSk)
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Telah Diterbitkan
                            </span>
                        @else
                            <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span> Menunggu Sidang
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Surat Keputusan Kelulusan (SK)</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Surat keputusan resmi yang ditandatangani oleh Kepala Sekolah mengenai hasil seleksi wawancara dan penetapan penerimaan calon peserta didik baru.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if($hasSk)
                        <a href="{{ route('calon-siswa.dokumen.kelulusan') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Surat Keputusan (PDF)
                        </a>
                    @else
                        <span class="inline-flex items-center text-xs text-slate-400 font-semibold">
                            Tersedia setelah sidang pleno kelulusan
                        </span>
                    @endif
                </div>
            </div>

            <!-- 6. INVOICE RINCIAN TAGIHAN DAFTAR ULANG -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">Keuangan Daftar Ulang</span>
                        @if($tagihan)
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tagihan Terbit
                            </span>
                        @else
                            <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span> Belum Terbit
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Invoice Rincian Biaya Pendidikan</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Dokumen rincian biaya daftar ulang resmi (DSP, SPP, seragam, kegiatan) yang dibekukan untuk calon siswa beserta nomor rekening sekolah.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if($tagihan)
                        <a href="{{ route('calon-siswa.daftar-ulang.cetak-tagihan') }}" target="_blank"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-slate-900 hover:bg-slate-800 transition shadow-sm text-xs cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Invoice Tagihan (PDF)
                        </a>
                    @else
                        <span class="inline-flex items-center text-xs text-slate-400 font-semibold">
                            Diterbitkan pada tahap daftar ulang
                        </span>
                    @endif
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>
