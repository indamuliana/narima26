<x-layouts.app>
    <x-slot name="title">Dashboard Kepala Sekolah</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('kepala-sekolah.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Statistik SPMB</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Data Pendaftar</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Keputusan Kelulusan</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
            </svg>
            <span>Pengunduran Diri</span>
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
        <!-- Welcome Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-amber-400 tracking-wider">Executive Management Dashboard</span>
                <h1 class="text-2xl font-black mt-1">Selamat Datang, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Monitoring komprehensif progres SPMB, pendaftar per jurusan, keputusan kelulusan, dan status pengunduran diri.</p>
            </div>
            <span class="px-3 py-1.5 rounded-lg bg-amber-400/20 text-xs font-semibold text-amber-300 border border-amber-400/30">
                Role: Kepala Sekolah
            </span>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Total Pendaftar</p>
                <p class="text-2xl font-black text-slate-900 mt-2">1</p>
                <div class="mt-2 text-[11px] text-slate-400">Pendaftar baru</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Program Reguler</p>
                <p class="text-2xl font-black text-nampi-orange mt-2">1</p>
                <div class="mt-2 text-[11px] text-slate-400">Jalur reguler</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Program Unggulan</p>
                <p class="text-2xl font-black text-nampi-cyan mt-2">0</p>
                <div class="mt-2 text-[11px] text-slate-400">Jalur unggulan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Resmi Terdaftar</p>
                <p class="text-2xl font-black text-nampi-green mt-2">0</p>
                <div class="mt-2 text-[11px] text-slate-400">Murid baru resmi</div>
            </div>
        </div>
    </div>
</x-layouts.app>
