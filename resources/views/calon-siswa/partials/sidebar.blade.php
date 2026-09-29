@php
    $cs = $calonSiswa ?? auth()->user()?->calonSiswa;
    $statusVal = $cs?->status_spmb?->value ?? (is_string($cs?->status_spmb) ? $cs->status_spmb : 'REGISTRASI');
    $pembayaran = $cs?->pembayaranSeleksi;
    $eulaSetuju = $cs ? ($cs->relationLoaded('kesepahaman') ? $cs->kesepahaman->where('setuju', true)->isNotEmpty() : $cs->kesepahaman()->where('setuju', true)->exists()) : false;
    $isDataComplete = in_array($statusVal, ['DATA_LENGKAP', 'MENUNGGU_WAWANCARA', 'SUDAH_DIWAWANCARA', 'MENUNGGU_KEPUTUSAN', 'DITERIMA', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']);
    $isLulus = in_array($statusVal, ['DITERIMA', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']);
@endphp

<!-- Navigation Links for Calon Siswa -->
<div class="space-y-1.5">

    <!-- 1. Dashboard -->
    <a href="{{ route('calon-siswa.dashboard') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('calon-siswa.dashboard') ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('calon-siswa.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Dashboard</span>
    </a>

    <!-- 2. Pembayaran Seleksi -->
    <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('calon-siswa.pembayaran-seleksi.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('calon-siswa.pembayaran-seleksi.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="flex-1">Pembayaran Seleksi</span>
        @if($pembayaran?->status === 'DIVERIFIKASI')
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.pembayaran-seleksi.*') ? 'bg-white/20 text-white' : 'bg-emerald-500/20 text-emerald-400' }}">✓ Lunas</span>
        @elseif($pembayaran?->status === 'MENUNGGU_VERIFIKASI')
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.pembayaran-seleksi.*') ? 'bg-white/20 text-white' : 'bg-amber-500/20 text-amber-400' }}">Menunggu</span>
        @elseif($pembayaran?->status === 'DITOLAK')
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.pembayaran-seleksi.*') ? 'bg-white/20 text-white' : 'bg-rose-500/20 text-rose-400' }}">Ditolak</span>
        @endif
    </a>

    <!-- 3. Lengkapi Data & Berkas -->
    <a href="{{ route('calon-siswa.lengkapi-data.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('calon-siswa.lengkapi-data.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('calon-siswa.lengkapi-data.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        <span class="flex-1">Lengkapi Data</span>
        @if($isDataComplete)
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.lengkapi-data.*') ? 'bg-white/20 text-white' : 'bg-emerald-500/20 text-emerald-400' }}">✓ Lengkap</span>
        @endif
    </a>

    <!-- 4. Kesepahaman SPMB -->
    <a href="{{ route('calon-siswa.kesepahaman.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('calon-siswa.kesepahaman.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('calon-siswa.kesepahaman.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <span class="flex-1">Kesepahaman SPMB</span>
        @if($eulaSetuju)
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.kesepahaman.*') ? 'bg-white/20 text-white' : 'bg-emerald-500/20 text-emerald-400' }}">✓ Setuju</span>
        @endif
    </a>

    <!-- 5. Dokumen & Cetak PDF -->
    <a href="{{ route('calon-siswa.dokumen.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('calon-siswa.dokumen.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('calon-siswa.dokumen.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <span>Dokumen & Cetak PDF</span>
    </a>

    <!-- 6. Tagihan Daftar Ulang -->
    <a href="{{ route('calon-siswa.daftar-ulang.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('calon-siswa.daftar-ulang.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('calon-siswa.daftar-ulang.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="flex-1">Tagihan Daftar Ulang</span>
        @if(in_array($statusVal, ['DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']))
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.daftar-ulang.*') ? 'bg-white/20 text-white' : 'bg-emerald-500/20 text-emerald-400' }}">✓ Lunas</span>
        @elseif($isLulus)
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('calon-siswa.daftar-ulang.*') ? 'bg-white/20 text-white' : 'bg-amber-500/20 text-amber-300' }}">Tahap Ini</span>
        @endif
    </a>

</div>

<!-- Logout Action -->
<form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-slate-800">
    @csrf
    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors cursor-pointer">
        <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span>Keluar (Logout)</span>
    </button>
</form>
