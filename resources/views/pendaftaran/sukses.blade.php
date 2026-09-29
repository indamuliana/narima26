<x-layouts.guest title="Pendaftaran Berhasil — SPMB Nampi SMK Wikrama 1 Garut">
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Card Sukses Utama -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden text-center p-8 sm:p-12">
                
                <!-- Icon Checklist Animasi / Berwarna -->
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>

                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                    REGISTRASI BERHASIL
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang di Wikrama!</h1>
                <p class="mt-2 text-slate-600 max-w-lg mx-auto">
                    Data pendaftaran Anda telah berhasil dicatat ke sistem SPMB Nampi. Akun portal siswa Anda telah aktif secara otomatis.
                </p>

                <!-- Box Kredensial Login Siswa -->
                <div class="mt-8 bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-6 sm:p-8 text-left shadow-lg">
                    <div class="text-xs uppercase tracking-wider text-orange-400 font-bold mb-4 flex items-center justify-between">
                        <span>Kredensial Akses Portal Calon Siswa</span>
                        <span class="bg-orange-500/20 text-orange-300 px-2.5 py-0.5 rounded text-[10px]">SIMPAN BAIK-BAIK</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                            <span class="text-xs text-slate-400 block">Nomor Pendaftaran SPMB</span>
                            <span class="text-2xl font-black text-orange-400 tracking-wide mt-1 block font-mono">
                                {{ $calonSiswa->nomor_pendaftaran }}
                            </span>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                            <span class="text-xs text-slate-400 block">Nama Calon Murid</span>
                            <span class="text-lg font-bold text-white mt-1 block truncate">
                                {{ $calonSiswa->nama_lengkap }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">Username Login (NISN)</span>
                            <span class="font-mono font-bold text-white text-base">{{ $calonSiswa->nisn }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Password Awal (Nomor Pendaftaran)</span>
                            <span class="font-mono font-bold text-emerald-400 text-base">
                                {{ $sessionData['password_plain'] ?? $calonSiswa->nomor_pendaftaran }}
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Gunakan Nomor Pendaftaran sebagai password login pertama kali</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between text-xs text-slate-300 gap-2">
                        <div>Kompetensi: <strong>{{ $calonSiswa->jurusan?->nama_jurusan }} ({{ $calonSiswa->program?->nama_program }})</strong></div>
                        <div>Gelombang: <strong>{{ $calonSiswa->gelombang?->nama_gelombang }}</strong></div>
                    </div>
                </div>

                <!-- Petunjuk Langkah Selanjutnya: Biaya Seleksi -->
                <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-5 text-left text-sm text-amber-900">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-600 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold text-amber-900">Langkah Berikutnya: Pembayaran Biaya Seleksi</strong>
                            <p class="mt-1 text-xs text-amber-800 leading-relaxed">
                                Silakan lakukan pembayaran biaya seleksi sebesar <strong>Rp {{ number_format($calonSiswa->pembayaranSeleksi?->nominal_tagihan ?? 200000, 0, ',', '.') }}</strong> melalui transfer bank ke rekening resmi SMK Wikrama 1 Garut (Bank BNI: <strong>082-0083-086</strong>), kemudian unggah bukti transfer di dashboard calon siswa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('pendaftaran.cetak-akun', $calonSiswa->nomor_pendaftaran) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition border border-slate-300 text-sm">
                        <svg class="w-4 h-4 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Kartu Registrasi (PDF)
                    </a>

                    <a href="{{ route('pendaftaran.login-direct', $calonSiswa->nomor_pendaftaran) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-lg shadow-orange-500/25 transition transform active:scale-95 text-sm cursor-pointer">
                        <span>Lanjut ke Dashboard Siswa</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <x-whatsapp-helpdesk />
                </div>

            </div>

        </div>
    </div>
</x-layouts.guest>
