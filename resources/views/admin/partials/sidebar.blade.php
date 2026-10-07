<a href="{{ route('admin.dashboard') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <span>Dashboard Utama</span>
</a>

<a href="{{ route('admin.calon-siswa.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.calon-siswa.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('admin.calon-siswa.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <span>Direktori Calon Murid</span>
</a>

<a href="{{ route('admin.laporan.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.laporan.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('admin.laporan.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span>Laporan & Rekapitulasi</span>
</a>

@if(auth()->user()?->isAdmin() || auth()->user()?->isOperator())
    <div class="pt-4 pb-2">
        <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Master & Operasional SPMB</p>
    </div>

    <a href="{{ route('admin.jurusan.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.jurusan.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('admin.jurusan.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <span>Manajemen Jurusan</span>
    </a>

    <a href="{{ route('admin.keuangan.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.keuangan.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('admin.keuangan.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Master Tarif Keuangan</span>
    </a>

    <a href="{{ route('admin.pembayaran.seleksi') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.pembayaran.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('admin.pembayaran.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Verifikasi Pembayaran</span>
    </a>

    <a href="{{ route('admin.alokasi-pewawancara.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.alokasi-pewawancara.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('admin.alokasi-pewawancara.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Alokasi Pewawancara</span>
    </a>
@endif

@if(auth()->user()?->isPewawancara() || auth()->user()?->isOperator())
    <div class="pt-4 pb-2">
        <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Wawancara SPMB</p>
    </div>

    <a href="{{ route('pewawancara.antrian') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ (request()->routeIs('pewawancara.antrian') || request()->routeIs('pewawancara.wawancara.*')) && !request()->routeIs('pewawancara.riwayat') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ (request()->routeIs('pewawancara.antrian') || request()->routeIs('pewawancara.wawancara.*')) && !request()->routeIs('pewawancara.riwayat') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <span>Antrean Wawancara</span>
    </a>

    <a href="{{ route('pewawancara.riwayat') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('pewawancara.riwayat') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pewawancara.riwayat') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Riwayat Wawancara</span>
    </a>

    <a href="{{ route('pewawancara.instrumen') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('pewawancara.instrumen') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pewawancara.instrumen') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span>Instrumen & Rubrik</span>
    </a>
@endif

@if(auth()->user()?->isAdmin())
    <div class="pt-4 pb-2">
        <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Sistem & Pengguna</p>
    </div>

    <a href="{{ route('admin.users.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Manajemen Pengguna</span>
    </a>

    <a href="{{ route('admin.audit-trail.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.audit-trail.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('admin.audit-trail.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Audit Trail Sistem</span>
    </a>

    <div class="pt-4 pb-2">
        <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Pintasan Antar Role</p>
    </div>

    <a href="{{ route('kepala-sekolah.sidang-kelulusan.index') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Sidang Pleno Kelulusan</span>
    </a>

    <a href="{{ route('pewawancara.dashboard') }}"
       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span>Panel Wawancara</span>
    </a>
@endif

<form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-slate-800">
    @csrf
    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors cursor-pointer">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span>Keluar (Logout)</span>
    </button>
</form>
