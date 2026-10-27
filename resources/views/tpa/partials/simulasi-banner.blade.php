@if (session('is_tpa_simulation'))
    <!-- Banner Mode Simulasi Admin -->
    <aside class="bg-amber-400 text-slate-950 px-3 sm:px-4 py-1.5 text-xs font-bold flex items-center justify-between shrink-0 border-b border-amber-500 shadow-sm z-50 select-none">
        <div class="flex items-center gap-2 min-w-0">
            <span class="relative flex h-2.5 w-2.5 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-slate-900 opacity-60"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-slate-950"></span>
            </span>
            <span class="truncate">
                <strong class="uppercase tracking-wider">Mode Coba Ujian (Simulasi Admin)</strong>
                <span class="hidden md:inline text-slate-800 font-medium">&mdash; Akses tampilan & pengerjaan calon murid (SIM-TPA-0001)</span>
            </span>
        </div>

        <div class="flex items-center gap-2 shrink-0 ml-2">
            <!-- Tombol Reset Sesi Ujian -->
            <form method="POST" action="{{ route('tpa.simulasi.reset') }}" class="inline" onsubmit="return confirm('Mulai ulang simulasi ujian dari awal? Soal akan diacak kembali.');">
                @csrf
                <button type="submit"
                        class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold transition-colors cursor-pointer flex items-center gap-1 shadow-2xs"
                        title="Acak ulang butir soal dan bersihkan jawaban simulasi">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="hidden sm:inline">Reset Ujian</span>
                </button>
            </form>

            <!-- Tombol Kembali ke Admin Panel -->
            <form method="POST" action="{{ route('tpa.simulasi.exit') }}" class="inline">
                @csrf
                <button type="submit"
                        class="px-3 py-1 rounded-lg bg-slate-950 hover:bg-black text-amber-300 hover:text-white text-[11px] font-black transition-colors cursor-pointer flex items-center gap-1 shadow-2xs"
                        title="Keluar dari mode simulasi dan kembali ke akun Administrator">
                    <span>Kembali ke Admin</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>
@endif
