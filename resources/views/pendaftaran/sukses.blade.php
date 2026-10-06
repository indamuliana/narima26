<x-layouts.guest title="Pendaftaran Berhasil — SPMB Nampi SMK Wikrama 1 Garut">
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Card Sukses Utama -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden text-center p-8 sm:p-12">
                
                <!-- Icon Checklist Animasi / Berwarna -->
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>

                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 mb-3">
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
                        <div>Jurusan 1: <strong class="text-white">{{ $calonSiswa->jurusan?->nama_jurusan }}</strong> | Jurusan 2: <strong class="text-white">{{ $calonSiswa->jurusan2?->nama_jurusan ?? '-' }}</strong></div>
                        <div>Program: <strong class="text-white">{{ $calonSiswa->program?->nama_program }}</strong></div>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center justify-between text-xs text-slate-300 gap-2">
                        <div>Gelombang: <strong class="text-white">{{ $calonSiswa->gelombang?->nama_gelombang }}</strong></div>
                    </div>
                </div>

                <!-- Petunjuk Langkah Selanjutnya: Biaya Seleksi -->
                <div class="mt-6 bg-amber-50 border border-amber-300 rounded-2xl p-5 text-left text-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-600 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold text-amber-900">Langkah Berikutnya: Pembayaran Biaya Seleksi</strong>
                            <p class="mt-1 text-xs text-amber-800 leading-relaxed">
                                Silakan lakukan pembayaran biaya seleksi sebesar <strong class="text-amber-900">Rp {{ number_format($calonSiswa->pembayaranSeleksi?->nominal_tagihan ?? 200000, 0, ',', '.') }}</strong> melalui transfer bank ke rekening resmi SMK Wikrama 1 Garut (Bank BNI: <strong class="text-amber-900">082-0083-086</strong>), kemudian unggah bukti transfer di dashboard calon siswa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tombol WhatsApp Konfirmasi Pendaftaran -->
                @php
                    $programNama = $sessionData['program_nama'] ?? ($calonSiswa->program?->nama_program ?? $calonSiswa->program?->nama ?? '');
                    $isUnggulan = str_contains(strtolower($programNama), 'unggul');
                    $waNumber = $isUnggulan ? '628112232880' : '628112232880';
                    $username = $calonSiswa->nisn;
                    $password = $sessionData['password_plain'] ?? $calonSiswa->nomor_pendaftaran;
                    $namaCalon = $calonSiswa->nama_lengkap;
                    $waText = "Assalamualaikum, saya {$namaCalon} baru saja mendaftar di web SPMB SMK Wikrama 1 Garut dengan username {$username} dan password {$password}, mohon bantuannya, terima kasih";
                    $waUrl = "https://wa.me/{$waNumber}?text=" . urlencode($waText);
                @endphp

                <div class="mt-6 p-5 bg-green-50 border border-green-300 rounded-2xl text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-green-900 text-sm">Konfirmasi via WhatsApp</h3>
                            <p class="text-xs text-green-800 mt-1 leading-relaxed">
                                Kirim pesan konfirmasi pendaftaran Anda ke admin {{ $isUnggulan ? 'Program Unggulan' : 'Program Reguler' }} SMK Wikrama 1 Garut melalui WhatsApp.
                            </p>
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                                class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 shadow-md shadow-green-600/25 transition transform active:scale-95 text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Kirim Pesan WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('pendaftaran.cetak-akun', $calonSiswa->nomor_pendaftaran) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 transition border border-slate-300 text-sm">
                        <svg class="w-4 h-4 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Kartu Registrasi (PDF)
                    </a>

                    <a href="{{ route('pendaftaran.login-direct', $calonSiswa->nomor_pendaftaran) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-white bg-orange-600 hover:bg-orange-700 shadow-lg shadow-orange-600/25 transition transform active:scale-95 text-sm cursor-pointer">
                        <span>Lanjut ke Dashboard Siswa</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-200">
                    <x-whatsapp-helpdesk />
                </div>

            </div>

        </div>
    </div>
</x-layouts.guest>
