<x-layouts.app>
    <x-slot name="title">Portal Calon Siswa</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('calon-siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Biodata Diri</span>
        </a>
        <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Pembayaran Seleksi</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Tagihan Daftar Ulang</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
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
        <!-- Student Info Header Banner -->
        <div class="p-6 bg-gradient-to-r from-nampi-orange via-amber-500 to-amber-600 rounded-3xl text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold backdrop-blur-xs">
                    <span>NISN: {{ auth()->user()->username ?? '0012345678' }}</span>
                </div>
                <h1 class="text-2xl font-black">{{ auth()->user()->name }}</h1>
                <p class="text-xs text-amber-100">Selamat datang di portal pendaftaran SPMB SMK Wikrama 1 Garut</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-right">
                <span class="text-[11px] uppercase font-bold text-amber-100 tracking-wider">Status SPMB Saat Ini</span>
                <div class="mt-1">
                    <span class="inline-block px-3 py-1 rounded-full bg-white text-nampi-orange font-extrabold text-xs shadow-xs">
                        MENUNGGU PEMBAYARAN SELEKSI
                    </span>
                </div>
            </div>
        </div>

        <!-- Stepper Timeline Alur SPMB -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Progres Pendaftaran Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>1. Registrasi Akun</span>
                        <span class="text-emerald-600">✓ Selesai</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">Akun dan nomor pendaftaran telah terbuat.</p>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 ring-2 ring-amber-400/30">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>2. Pembayaran Seleksi</span>
                        <span class="text-amber-600">Tahap Aktif</span>
                    </div>
                    <p class="text-[11px] text-amber-800">Upload bukti transfer biaya seleksi (Rp 250.000).</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>3. Lengkapi Data</span>
                        <span>Terkunci</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Biodata, data ortu, akademik, & ukuran seragam.</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>4. Daftar Ulang</span>
                        <span>Terkunci</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Pengumuman kelulusan dan pembayaran tagihan.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
