<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeda Transisi Babak TPA — SPMB SMK Wikrama 1 Garut</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 selection:bg-orange-500 selection:text-white">
    <div class="max-w-lg w-full bg-slate-800/90 backdrop-blur-md rounded-3xl border border-slate-700/80 p-8 text-center space-y-6 shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <div class="space-y-2">
            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-950 text-blue-300 border border-blue-800">
                Babak 1: Literasi Tuntas
            </span>
            <h1 class="text-2xl font-black text-white">Babak 1 Telah Selesai & Dikunci</h1>
            <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                Jawaban Babak 1 Anda telah disimpan secara permanen. Anda tidak dapat kembali mengubah jawaban di babak tersebut.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/50 text-left space-y-2 text-xs">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                <span class="text-slate-400 font-bold uppercase text-[10px]">Babak Selanjutnya</span>
                <span class="font-bold text-emerald-400">Babak 2: Numerasi</span>
            </div>
            <div class="flex items-center justify-between text-slate-300">
                <span>Alokasi Waktu:</span>
                <strong class="text-white">{{ $pengaturan->durasi_numerasi_menit }} Menit</strong>
            </div>
            <div class="flex items-center justify-between text-slate-300">
                <span>Jumlah Soal:</span>
                <strong class="text-white">{{ count($sesi->urutan_soal_numerasi ?? []) }} Soal</strong>
            </div>
            <p class="text-[11px] text-slate-400 pt-1 leading-relaxed">
                Timer Babak 2 akan otomatis mulai berjalan begitu Anda menekan tombol di bawah.
            </p>
        </div>

        <form action="{{ route('tpa.mulai') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full py-3.5 px-6 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm transition-all shadow-xl shadow-orange-600/30 active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                <span>Lanjutkan ke Babak 2 (Numerasi)</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>
    </div>
</body>
</html>
