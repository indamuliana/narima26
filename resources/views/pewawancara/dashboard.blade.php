<x-layouts.app>
    <x-slot name="title">Dashboard Pewawancara</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('pewawancara.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>Antrian Wawancara</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>Instrumen & Rubrik</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-nampi-cyan tracking-wider">Panel Penguji & Wawancara</span>
                <h1 class="text-2xl font-black mt-1">Selamat Datang, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Lakukan wawancara calon siswa & orang tua serta isi rubrik indikator penilaian.</p>
            </div>
            <span class="px-3 py-1.5 rounded-lg bg-cyan-500/20 text-xs font-semibold text-cyan-300 border border-cyan-500/30">
                Role: Pewawancara
            </span>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Antrian Menunggu</p>
                <p class="text-2xl font-black text-amber-500 mt-2">0</p>
                <div class="mt-2 text-[11px] text-slate-400">Siap diuji hari ini</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Selesai Wawancara</p>
                <p class="text-2xl font-black text-emerald-600 mt-2">0</p>
                <div class="mt-2 text-[11px] text-slate-400">Hasil tersimpan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-medium text-slate-500">Kriteria Penilaian Aktif</p>
                <p class="text-2xl font-black text-indigo-600 mt-2">7</p>
                <div class="mt-2 text-[11px] text-slate-400">Indikator rubrik</div>
            </div>
        </div>
    </div>
</x-layouts.app>
