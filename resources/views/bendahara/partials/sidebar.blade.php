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

<a href="{{ route('bendahara.dashboard') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('bendahara.dashboard') ? 'bg-nampi-orange text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <span>Dashboard</span>
</a>

<a href="{{ route('bendahara.pembayaran-seleksi.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('bendahara.pembayaran-seleksi.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.pembayaran-seleksi.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <span>Pembayaran Seleksi</span>
</a>

<a href="{{ route('bendahara.tagihan.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('bendahara.tagihan.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.tagihan.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span>Tagihan Daftar Ulang</span>
</a>

<a href="{{ route('bendahara.pembayaran-daftar-ulang.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('bendahara.pembayaran-daftar-ulang.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.pembayaran-daftar-ulang.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>Pembayaran Daftar Ulang</span>
</a>

<a href="{{ route('bendahara.diskon.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('bendahara.diskon.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.diskon.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
    </svg>
    <span>Kelola Diskon</span>
</a>

<a href="{{ route('bendahara.master-biaya.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('bendahara.master-biaya.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.master-biaya.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <span>Master Tarif Biaya</span>
</a>

<a href="{{ route('bendahara.master-diskon.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('bendahara.master-diskon.*') ? 'bg-nampi-orange text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.master-diskon.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>Master Diskon</span>
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
