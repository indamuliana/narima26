@if(session()->has('impersonate_admin_id'))
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 text-amber-950 px-4 py-2.5 shadow-md border-b-2 border-amber-600/40 sticky top-0 z-50 transition-all print:hidden">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5 text-xs sm:text-sm">
            <div class="flex items-center gap-2 font-semibold">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-950 text-white font-black text-xs shrink-0 animate-pulse">
                    !
                </span>
                <span>
                    <strong class="font-extrabold text-amber-950 uppercase tracking-wide">Mode Impersonasi Aktif:</strong>
                    Anda sedang mengakses sistem sebagai
                    <span class="underline decoration-amber-950 font-black">{{ auth()->user()->name }}</span>
                    ({{ auth()->user()->username ?? 'Calon Siswa' }})
                </span>
            </div>
            <form action="{{ route('impersonate.leave') }}" method="POST" class="shrink-0 m-0">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-950 hover:bg-black text-white font-bold text-xs shadow-sm hover:shadow transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    <span>Kembali ke Akun Admin</span>
                </button>
            </form>
        </div>
    </div>
@endif
