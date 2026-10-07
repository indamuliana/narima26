@if(auth()->user()->isAdmin() || auth()->user()->isOperator())
    <div class="pb-3 mb-2 border-b border-slate-800">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-nampi-orange bg-nampi-orange/10 hover:bg-nampi-orange/20 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>&larr; Kembali ke Portal Admin</span>
        </a>
    </div>
@endif

@if(!auth()->user()->isOperator())
<a href="{{ route('pewawancara.dashboard') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('pewawancara.dashboard') ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('pewawancara.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <span>Dashboard</span>
</a>
@endif

<a href="{{ route('pewawancara.antrian') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ (request()->routeIs('pewawancara.antrian') || request()->routeIs('pewawancara.wawancara.*')) && !request()->routeIs('pewawancara.riwayat') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ (request()->routeIs('pewawancara.antrian') || request()->routeIs('pewawancara.wawancara.*')) && !request()->routeIs('pewawancara.riwayat') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
    </svg>
    <span>Antrian Wawancara</span>
</a>

<a href="{{ route('pewawancara.instrumen') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('pewawancara.instrumen') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('pewawancara.instrumen') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
    </svg>
    <span>Instrumen & Rubrik</span>
</a>

<a href="{{ route('pewawancara.riwayat') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('pewawancara.riwayat') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('pewawancara.riwayat') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>Riwayat Wawancara</span>
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
