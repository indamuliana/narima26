<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Tunggu TPA — SPMB SMK Wikrama 1 Garut</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between p-4 sm:p-6 selection:bg-orange-500 selection:text-white">
    <!-- Topbar -->
    <header class="max-w-4xl w-full mx-auto flex items-center justify-between pb-6 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-600/20 border border-orange-500/30 text-orange-500 flex items-center justify-center font-black">
                W1
            </div>
            <div>
                <h1 class="text-sm font-bold text-white tracking-wide">TES POTENSI AKADEMIK (TPA)</h1>
                <p class="text-[11px] text-slate-400">SMK Wikrama 1 Garut &bull; T.A. 2027/2028</p>
            </div>
        </div>

        <form action="{{ route('tpa.logout') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-300 hover:text-white hover:bg-slate-700 transition-colors cursor-pointer">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl w-full mx-auto my-auto py-8 space-y-6">
        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-950/70 border border-rose-800 text-rose-300 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Identity & Status Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Student Card -->
            <div class="md:col-span-2 bg-slate-800/80 rounded-2xl border border-slate-700/80 p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-400">Identitas Peserta Ujian</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">
                        {{ $calonSiswa->nomor_pendaftaran }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <p class="text-slate-400 text-[11px]">Nama Lengkap:</p>
                        <p class="font-bold text-white text-sm mt-0.5">{{ $calonSiswa->nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-[11px]">NISN:</p>
                        <p class="font-bold text-white text-sm mt-0.5">{{ $calonSiswa->nisn ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-[11px]">Pilihan Kompetensi Keahlian:</p>
                        <p class="font-bold text-orange-300 text-sm mt-0.5">{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-[11px]">Gelombang Pendaftaran:</p>
                        <p class="font-bold text-white text-sm mt-0.5">{{ $calonSiswa->gelombang?->nama_gelombang ?? '-' }}</p>
                    </div>
                </div>

                <!-- Babak Info -->
                <div class="pt-4 border-t border-slate-700/60 grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 space-y-1">
                        <div class="flex items-center gap-1.5 text-blue-400 font-bold text-xs">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Babak 1: Literasi</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Durasi: <strong class="text-slate-200">{{ $pengaturan->durasi_literasi_menit }} Menit</strong> &bull; {{ $totalSoalLiterasi }} Soal</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 space-y-1">
                        <div class="flex items-center gap-1.5 text-emerald-400 font-bold text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Babak 2: Numerasi</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Durasi: <strong class="text-slate-200">{{ $pengaturan->durasi_numerasi_menit }} Menit</strong> &bull; {{ $totalSoalNumerasi }} Soal</p>
                    </div>
                </div>
            </div>

            <!-- Action & Schedule Card -->
            <div class="bg-slate-800/80 rounded-2xl border border-slate-700/80 p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Akses Masuk</span>

                    @if ($isBolehMasuk)
                        <div class="p-3 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Akses Ujian Terbuka</span>
                            </div>
                            <p class="text-[11px] text-emerald-400/80">Anda telah diizinkan untuk memulai ujian TPA saat ini.</p>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-amber-950/60 border border-amber-800/80 text-amber-300 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span>Akses Belum Dibuka</span>
                            </div>
                            <p class="text-[11px] text-amber-300/80 leading-relaxed">
                                @if ($jadwal && $jadwal->waktu_mulai && now()->lt($jadwal->waktu_mulai))
                                    Jadwal ujian Anda dimulai pada: <br>
                                    <strong class="text-white">{{ $jadwal->waktu_mulai->translatedFormat('d M Y H:i') }} WIB</strong>
                                @elseif ($jadwal && ! $jadwal->is_diizinkan)
                                    Akses akun Anda belum diizinkan oleh panitia. Silakan hubungi admin sekolah.
                                @else
                                    Jadwal ujian Anda belum aktif atau telah berakhir.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <div>
                    @if ($isBolehMasuk)
                        <form action="{{ route('tpa.mulai') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm transition-all shadow-xl shadow-orange-600/30 active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                                <span>Mulai Ujian Sekarang</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <button type="button" disabled
                                class="w-full py-3.5 px-4 rounded-xl bg-slate-700/60 text-slate-500 font-bold text-sm cursor-not-allowed">
                            Menunggu Jadwal Dibuka
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tata Tertib & Aturan Ujian -->
        <div class="p-6 bg-slate-800/50 rounded-2xl border border-slate-700/60 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-orange-400">Petunjuk & Tata Tertib Pengerjaan</h3>
            <ul class="text-xs text-slate-300 space-y-2 list-disc list-inside leading-relaxed">
                <li>Ujian terdiri dari <strong>2 Babak Berurutan</strong>: Babak 1 (Literasi) dan Babak 2 (Numerasi).</li>
                <li>Setelah Babak 1 diselesaikan atau waktu babak habis, Babak 1 akan <strong>dikunci permanen</strong> dan Anda tidak dapat kembali mengubah jawaban di babak tersebut.</li>
                <li>Jawaban tersimpan secara otomatis setiap kali Anda memilih opsi. Terdapat tombol <strong>"Ragu-ragu (Simpan Sementara)"</strong> untuk menandai soal yang ingin ditinjau kembali.</li>
                <li><strong class="text-rose-400">PENTING (Anti-Kecurangan):</strong> Dilarang berpindah tab browser, membuka aplikasi lain, atau meminimalkan jendela selama ujian berlangsung. Sistem mendeteksi perpindahan layar dan akan memberikan peringatan. Pelanggaran melebihi {{ $pengaturan->max_pelanggaran }} kali akan mengakibatkan ujian <strong>otomatis terkunci dan didiskualifikasi</strong>.</li>
                <li>Tes Potensi Akademik ini tidak menggugurkan kelulusan Anda, melainkan digunakan sekolah untuk memetakan minat dan gaya pembelajaran Anda di SMK Wikrama 1 Garut. Kerjakan secara mandiri dan jujur.</li>
            </ul>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-xs text-slate-500 pt-6">
        &copy; {{ date('Y') }} Panitia SPMB SMK Wikrama 1 Garut &bull; Sistem Tes Potensi Akademik Terpadu
    </footer>
</body>
</html>
