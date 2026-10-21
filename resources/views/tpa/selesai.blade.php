<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Potensi Akademik Selesai — SPMB SMK Wikrama 1 Garut</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 selection:bg-orange-500 selection:text-white">
    <div class="max-w-md w-full bg-slate-800/90 backdrop-blur-md rounded-3xl border border-slate-700/80 p-8 text-center space-y-6 shadow-2xl">

        @if ($sesi && $sesi->status === 'didiskualifikasi')
            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-400 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="space-y-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-950 text-rose-300 border border-rose-800">
                    Sesi Dihentikan
                </span>
                <h1 class="text-2xl font-black text-white">Ujian Telah Terkunci</h1>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Sesi ujian Anda dihentikan karena jumlah pelanggaran integritas (beralih tab/layar) melebihi batas toleransi. Jawaban terakhir yang tersimpan telah dikirimkan ke panitia SPMB.
                </p>
            </div>
        @else
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="space-y-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-950 text-emerald-300 border border-emerald-800">
                    Tuntas Dikerjakan
                </span>
                <h1 class="text-2xl font-black text-white">Terima Kasih!</h1>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Anda telah menyelesaikan seluruh rangkaian <strong>Tes Potensi Akademik (Literasi & Numerasi)</strong>. Seluruh jawaban Anda telah tersimpan dengan aman pada sistem panitia sekolah.
                </p>
            </div>
        @endif

        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/50 text-xs text-slate-300 space-y-1.5 text-left">
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Nama Siswa:</span>
                <strong class="text-white">{{ $calonSiswa?->nama_lengkap }}</strong>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">No. Pendaftaran:</span>
                <strong class="text-white">{{ $calonSiswa?->nomor_pendaftaran }}</strong>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Waktu Selesai:</span>
                <span class="text-slate-300">{{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
            </div>
        </div>

        <p class="text-[11px] text-slate-500 leading-relaxed">
            Hasil pemetaan potensi akademik ini akan diolah oleh tim pengembang kurikulum SMK Wikrama 1 Garut sebagai bekal merancang metode pembelajaran yang sesuai dengan potensi Anda.
        </p>

        <form action="{{ route('tpa.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs transition-colors cursor-pointer flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar dari Portal Ujian</span>
            </button>
        </form>
    </div>
</body>
</html>
