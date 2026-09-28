<x-layouts.app>
    <x-slot name="title">Portal Calon Siswa</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('calon-siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Tagihan Daftar Ulang</span>
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
        <!-- Student Info Header Banner -->
        <div class="p-6 bg-gradient-to-r from-nampi-orange via-amber-500 to-amber-600 rounded-3xl text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold backdrop-blur-xs">
                    <span>NISN: {{ $calonSiswa?->nisn ?? auth()->user()->username }}</span>
                    <span>&bull;</span>
                    <span>{{ $calonSiswa?->nomor_pendaftaran ?? '-' }}</span>
                </div>
                <h1 class="text-2xl font-black">{{ $calonSiswa?->nama_lengkap ?? auth()->user()->name }}</h1>
                <p class="text-xs text-amber-100">
                    Pilihan Jurusan: <strong>{{ $calonSiswa?->jurusan?->nama ?? '-' }}</strong> &bull; Gelombang: <strong>{{ $calonSiswa?->gelombang?->nama ?? '-' }}</strong>
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-right">
                <span class="text-[11px] uppercase font-bold text-amber-100 tracking-wider">Status SPMB Saat Ini</span>
                <div class="mt-1">
                    <span class="inline-block px-3 py-1 rounded-full bg-white text-nampi-orange font-extrabold text-xs shadow-xs">
                        {{ $calonSiswa?->status_spmb?->label() ?? 'REGISTRASI' }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $statusVal = $calonSiswa?->status_spmb?->value ?? 'REGISTRASI';
            $isPaymentDone = !in_array($statusVal, ['REGISTRASI', 'MENUNGGU_PEMBAYARAN_SELEKSI']);
            $isDataComplete = in_array($statusVal, ['DATA_LENGKAP', 'MENUNGGU_WAWANCARA', 'SUDAH_DIWAWANCARA', 'MENUNGGU_KEPUTUSAN', 'DITERIMA', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']);
            $isDataActive = in_array($statusVal, ['PEMBAYARAN_SELEKSI_DIVERIFIKASI', 'MELENGKAPI_DATA']);
        @endphp

        <!-- Quick CTA Banner: Jika sudah verifikasi bayar tapi belum lengkap data -->
        @if($isDataActive)
            <div class="p-6 rounded-3xl bg-orange-50 border border-orange-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-orange-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Langkah Selanjutnya: Lengkapi Data & Upload Berkas</span>
                    </div>
                    <p class="text-xs text-orange-800">
                        Pembayaran seleksi Anda telah diverifikasi! Silakan lengkapi biodata, data orang tua, nilai rapor, ukuran seragam, dan upload dokumen persyaratan.
                    </p>
                </div>
                <div>
                    <a href="{{ route('calon-siswa.lengkapi-data.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-black text-xs bg-orange-500 text-white hover:bg-orange-600 shadow-md transition transform active:scale-95 whitespace-nowrap">
                        <span>Lengkapi Data Sekarang</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Stepper Timeline Alur SPMB -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Progres Pendaftaran Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Step 1: Registrasi -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>1. Registrasi Akun</span>
                        <span class="text-emerald-600">✓ Selesai</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">Akun dan nomor pendaftaran telah terbuat.</p>
                </div>

                <!-- Step 2: Pembayaran Seleksi -->
                @if($isPaymentDone)
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>2. Pembayaran Seleksi</span>
                            <span class="text-emerald-600">✓ Diverifikasi</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Biaya pendaftaran seleksi lunas.</p>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 ring-2 ring-amber-400/30">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>2. Pembayaran Seleksi</span>
                            <span class="text-amber-600">Tahap Aktif</span>
                        </div>
                        <p class="text-[11px] text-amber-800">Upload bukti transfer biaya seleksi (Rp 250.000).</p>
                    </div>
                @endif

                <!-- Step 3: Lengkapi Data -->
                @if($isDataComplete)
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>3. Lengkapi Data</span>
                            <span class="text-emerald-600">✓ Lengkap</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Biodata, orang tua, akademik, seragam, & berkas selesai.</p>
                    </div>
                @elseif($isDataActive)
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 ring-2 ring-amber-400/30">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>3. Lengkapi Data</span>
                            <span class="text-amber-600">Tahap Aktif</span>
                        </div>
                        <p class="text-[11px] text-amber-800">Lengkapi formulir biodata & upload berkas.</p>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>3. Lengkapi Data</span>
                            <span>Terkunci</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Terbuka setelah pembayaran diverifikasi.</p>
                    </div>
                @endif

                <!-- Step 4: Daftar Ulang -->
                @if($isDataComplete)
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>4. Wawancara & Hasil</span>
                            <span class="text-blue-600">Menunggu Jadwal</span>
                        </div>
                        <p class="text-[11px] text-blue-700">Menunggu jadwal wawancara dari panitia.</p>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>4. Daftar Ulang</span>
                            <span>Terkunci</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Pengumuman kelulusan dan pembayaran tagihan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
