<x-layouts.app>
    <x-slot name="title">Portal Calon Murid</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Student Info Header Banner -->
        <div class="p-6 bg-gradient-to-r from-nampi-orange via-amber-500 to-amber-600 rounded-3xl text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold backdrop-blur-xs">
                    <span>NISN: {{ $calonSiswa?->nisn ?? auth()->user()->username }}</span>
                    <span>&bull;</span>
                    <span>{{ $calonSiswa?->nomor_pendaftaran ?? '-' }}</span>
                </div>
                <h1 class="text-2xl font-black">{{ $calonSiswa?->nama_lengkap ?? auth()->user()->name }}</h1>
                <p class="text-xs text-amber-100">
                    Program: <strong>{{ $calonSiswa?->program?->nama ?? '-' }}</strong> &bull;
                   <br> Jurusan: <strong>{{ $calonSiswa?->jurusan?->nama ?? '-' }}</strong> &bull;
                    <br>Gelombang: <strong>{{ $calonSiswa?->gelombang?->nama ?? '-' }}</strong>
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-right">
                <span class="text-[11px] uppercase font-bold text-amber-100 tracking-wider">Status SPMB Saat Ini</span>
                <div class="mt-1">
                    <span class="inline-block px-3 py-1 rounded-full bg-white text-nampi-orange font-extrabold text-xs shadow-xs">
                        {{ $calonSiswa?->status_spmb?->label() ?? 'REGISTRASI' }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $statusVal = $calonSiswa?->status_spmb?->value ?? 'REGISTRASI';
            $isPaymentDone = !in_array($statusVal, ['REGISTRASI', 'MENUNGGU_PEMBAYARAN_SELEKSI']);
            $isDataComplete = in_array($statusVal, ['DATA_LENGKAP', 'MENUNGGU_WAWANCARA', 'SUDAH_DIWAWANCARA', 'MENUNGGU_KEPUTUSAN', 'DITERIMA', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']) || ($calonSiswa?->status_data === 'LENGKAP');
            $isDataActive = in_array($statusVal, ['PEMBAYARAN_SELEKSI_DIVERIFIKASI', 'MELENGKAPI_DATA']);

            $latestWawancara = $calonSiswa?->relationLoaded('latestWawancara') ? $calonSiswa->latestWawancara : $calonSiswa?->latestWawancara;
            $pewawancara = $latestWawancara?->pewawancara;
            $isWawancaraScheduled = ($latestWawancara && !empty($latestWawancara->pewawancara_id));
            $tglWawancaraFormatted = $latestWawancara?->tanggal_wawancara ? \Carbon\Carbon::parse($latestWawancara->tanggal_wawancara)->locale('id')->isoFormat('dddd, D MMMM Y') : null;

            $phoneService = app(\App\Services\PhoneNumberService::class);
            $waUrlPewawancara = null;
            if ($pewawancara?->phone && $phoneService->isValid($pewawancara->phone)) {
                $namaSiswa = $calonSiswa->nama_lengkap ?? 'Calon Siswa';
                $noPendaftaran = $calonSiswa->nomor_pendaftaran ?? '-';
                $namaGuru = $pewawancara->name;
                $tglStr = $tglWawancaraFormatted ? " pada {$tglWawancaraFormatted}" : "";
                $waMsg = "Assalamu'alaikum Wr. Wb. Yth. Bapak/Ibu {$namaGuru}, saya {$namaSiswa} (No. Pendaftaran: {$noPendaftaran}) calon siswa SMK Wikrama 1 Garut ingin mengonfirmasi terkait jadwal sesi wawancara kami{$tglStr}. Terima kasih.";
                $waUrlPewawancara = $phoneService->toWhatsappUrl($pewawancara->phone, $waMsg);
            }
        @endphp

        <!-- Quick CTA Banners based on status -->
        @if($isDataActive)
            <div class="p-6 rounded-3xl bg-orange-50 border border-orange-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-orange-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Langkah Selanjutnya: Lengkapi Data & Upload Berkas</span>
                    </div>
                    <p class="text-xs text-orange-800">
                        Pembayaran seleksi Anda telah diverifikasi! Silakan lengkapi biodata, data orang tua, nilai rapor, ukuran seragam, dan upload dokumen persyaratan.
                    </p>
                </div>
                <div>
                    <a href="{{ route('calon-siswa.lengkapi-data.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-black text-xs bg-orange-500 text-white hover:bg-orange-600 shadow-md transition transform active:scale-95 whitespace-nowrap">
                        <span>Lengkapi Data Sekarang</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @elseif($statusVal === 'DATA_LENGKAP')
            <div class="p-6 rounded-3xl bg-purple-50 border border-purple-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-purple-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Langkah Selanjutnya: Persetujuan Lembar Kesepahaman (EULA)</span>
                    </div>
                    <p class="text-xs text-purple-800">
                        Data dan berkas Anda telah lengkap 100%! Silakan baca dan setujui lembar kesepahaman bersama orang tua untuk membuka pencetakan Kartu Peserta Ujian/Wawancara.
                    </p>
                </div>
                <div>
                    <a href="{{ route('calon-siswa.kesepahaman.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-black text-xs bg-purple-600 text-white hover:bg-purple-700 shadow-md transition transform active:scale-95 whitespace-nowrap">
                        <span>Buka Lembar Kesepahaman</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @elseif($statusVal === 'MENUNGGU_WAWANCARA')
            @if($isWawancaraScheduled)
                <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-50 via-teal-50 to-white border border-emerald-300 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-200 text-emerald-900 border border-emerald-300">
                                Sudah Dijadwalkan
                            </span>
                            <span class="text-xs text-emerald-700 font-semibold">&bull; Tahap Wawancara</span>
                        </div>
                        <h2 class="text-base font-black text-slate-900">
                            Wawancara & TPA Telah Dijadwalkan
                        </h2>
                        <div class="text-xs text-slate-700 space-y-0.5">
                            <p>Guru Pewawancara: <strong class="text-slate-900">{{ $pewawancara?->name ?? 'Pewawancara SPMB' }}</strong></p>
                            <p>Tanggal Pelaksanaan: <strong class="text-slate-900">{{ $tglWawancaraFormatted ?? 'Sesuai Jadwal' }}</strong></p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($waUrlPewawancara)
                            <a href="{{ $waUrlPewawancara }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition transform active:scale-95 whitespace-nowrap">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Hubungi Via Whatsapp</span>
                            </a>
                        @endif
                        <a href="{{ route('calon-siswa.wawancara.index') }}"
                            class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-xs bg-slate-900 text-white hover:bg-slate-800 shadow-sm transition transform active:scale-95 whitespace-nowrap">
                            <span>Detail Wawancara &rarr;</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="p-6 rounded-3xl bg-blue-50 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                    <div class="space-y-1">
                        <div class="flex items-center text-blue-900 font-black text-base gap-2">
                            <svg class="w-5 h-5 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Status: Menunggu Penjadwalan Wawancara</span>
                        </div>
                        <p class="text-xs text-blue-800">
                            Data dan Lembar Kesepahaman Anda telah lengkap. Panitia SPMB sedang mengatur guru pewawancara dan waktu jadwal wawancara untuk Anda.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('calon-siswa.wawancara.index') }}"
                            class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition transform active:scale-95 whitespace-nowrap">
                            <span>Buka Modul Wawancara &rarr;</span>
                        </a>
                        <a href="{{ route('calon-siswa.dokumen.kesepahaman') }}"
                            class="inline-flex items-center px-3.5 py-2.5 rounded-xl font-bold text-xs bg-white text-purple-700 hover:bg-purple-50 border border-purple-200 transition whitespace-nowrap">
                            <span>Unduh Kesepahaman (PDF)</span>
                        </a>
                    </div>
                </div>
            @endif
        @elseif($statusVal === 'SUDAH_DIWAWANCARA')
            <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-emerald-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Sesi Wawancara Telah Selesai Dilaksanakan</span>
                    </div>
                    <p class="text-xs text-emerald-800">
                        Penilaian wawancara calon murid dan orang tua telah selesai diinput oleh pewawancara. Saat ini hasil evaluasi sedang menunggu penetapan keputusan kelulusan oleh Kepala Sekolah dan Komite Seleksi.
                    </p>
                </div>
                <div>
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Menunggu Penetapan Kelulusan
                    </span>
                </div>
            </div>
        @elseif($statusVal === 'DITERIMA')
            <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-emerald-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Selamat! Anda Dinyatakan LULUS / DITERIMA di SMK Wikrama 1 Garut</span>
                    </div>
                    <p class="text-xs text-emerald-800">
                        Berdasarkan hasil sidang pleno komite seleksi, Anda berhak melanjutkan ke tahap pendaftaran ulang. Silakan unduh Surat Keputusan resmi dan selesaikan daftar ulang.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('calon-siswa.dokumen.kelulusan') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-xs bg-white text-emerald-800 border border-emerald-300 hover:bg-emerald-100 shadow-xs transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh SK (PDF)</span>
                    </a>
                    <a href="{{ route('calon-siswa.daftar-ulang.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-black text-xs bg-emerald-600 text-white hover:bg-emerald-700 shadow-md transition transform active:scale-95 whitespace-nowrap">
                        <span>Lanjut Daftar Ulang &rarr;</span>
                    </a>
                </div>
            </div>
        @elseif($statusVal === 'DITOLAK')
            <div class="p-6 rounded-3xl bg-rose-50 border border-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-rose-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pemberitahuan Hasil Seleksi SPMB</span>
                    </div>
                    <p class="text-xs text-rose-800">
                        Mohon maaf, berdasarkan hasil evaluasi sidang pleno seleksi, Anda belum berhasil diterima pada periode ini. Tetap semangat dalam menggapai cita-cita di jenjang pendidikan berikutnya.
                    </p>
                </div>
                <div>
                    <a href="{{ route('calon-siswa.dokumen.kelulusan') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-xs bg-white text-rose-800 border border-rose-200 hover:bg-rose-100 shadow-xs transition">
                        <span>Unduh Surat Keputusan (PDF)</span>
                    </a>
                </div>
            </div>
        @elseif($statusVal === 'MENGUNDURKAN_DIRI')
            <div class="p-6 rounded-3xl bg-slate-100 border border-slate-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-slate-800 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Status Pendaftaran: Telah Mengundurkan Diri</span>
                    </div>
                    <p class="text-xs text-slate-600">
                        Berkas dan status pendaftaran Anda telah tercatat mengundurkan diri. Jika ini merupakan kekeliruan atau ingin membatalkan penarikan berkas, silakan hubungi Helpdesk Panitia SPMB.
                    </p>
                </div>
            </div>
        @elseif($statusVal === 'MENUNGGU_DAFTAR_ULANG')
            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-amber-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Tahap Daftar Ulang: Tagihan Biaya Pendidikan Siap Dibayar</span>
                    </div>
                    <p class="text-xs text-amber-800">
                        Tagihan biaya daftar ulang telah diterbitkan oleh panitia/bendahara. Silakan periksa rincian biaya dan lakukan transfer konfirmasi (lunas atau cicilan bertahap).
                    </p>
                </div>
                <div>
                    <a href="{{ route('calon-siswa.daftar-ulang.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-black text-xs bg-nampi-orange text-white hover:bg-orange-600 shadow-md transition transform active:scale-95 whitespace-nowrap">
                        <span>Buka Tagihan & Bayar</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @elseif(in_array($statusVal, ['DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']))
            <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center text-emerald-900 font-black text-base gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Daftar Ulang Telah Berhasil Diverifikasi Bendahara</span>
                    </div>
                    <p class="text-xs text-emerald-800">
                        Pembayaran daftar ulang Anda telah divalidasi sah oleh Bendahara Sekolah. Silakan unduh kwitansi resmi tanda terima pembayaran.
                    </p>
                </div>
                <div>
                    <a href="{{ route('calon-siswa.daftar-ulang.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl font-black text-xs bg-emerald-600 text-white hover:bg-emerald-700 shadow-md transition transform active:scale-95 whitespace-nowrap">
                        <span>Lihat Tagihan & Unduh Kwitansi</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Stepper Timeline Alur SPMB -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Progres Pendaftaran Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Step 1: Registrasi -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>1. Registrasi Akun</span>
                        <span class="text-emerald-600">✓ Selesai</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">Akun dan nomor pendaftaran telah terbuat.</p>
                </div>

                <!-- Step 2: Pembayaran Seleksi -->
                @if($isPaymentDone)
                    <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}" class="block p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100/70 hover:border-emerald-300 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>2. Pembayaran Seleksi</span>
                            <span class="text-emerald-600">✓ Diverifikasi</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Biaya pendaftaran seleksi lunas.</p>
                    </a>
                @else
                    <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}" class="block p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 ring-2 ring-amber-400/30 hover:bg-amber-100/70 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>2. Pembayaran Seleksi</span>
                            <span class="text-amber-600 font-extrabold">Tahap Aktif &rarr;</span>
                        </div>
                        <p class="text-[11px] text-amber-800">Upload bukti transfer biaya seleksi (Rp 200.000).</p>
                    </a>
                @endif

                <!-- Step 3: Lengkapi Data & Kesepahaman -->
                @if($isDataComplete)
                    <a href="{{ route('calon-siswa.lengkapi-data.index') }}" class="block p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100/70 hover:border-emerald-300 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>3. Lengkapi Data & EULA</span>
                            <span class="text-emerald-600">✓ Lengkap</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Data, berkas, & kesepahaman telah disetujui.</p>
                    </a>
                @elseif($isDataActive)
                    <a href="{{ route('calon-siswa.lengkapi-data.index') }}" class="block p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 ring-2 ring-amber-400/30 hover:bg-amber-100/70 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>3. Lengkapi Data & EULA</span>
                            <span class="text-amber-600 font-extrabold">Tahap Aktif &rarr;</span>
                        </div>
                        <p class="text-[11px] text-amber-800">Lengkapi formulir biodata & upload berkas.</p>
                    </a>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>3. Lengkapi Data & EULA</span>
                            <span>Terkunci</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Terbuka setelah pembayaran diverifikasi.</p>
                    </div>
                @endif

                <!-- Step 4: Wawancara & Daftar Ulang -->
                @if($statusVal === 'MENUNGGU_WAWANCARA')
                    <a href="{{ route('calon-siswa.wawancara.index') }}" class="block p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 ring-2 ring-blue-400/30 hover:bg-blue-100/70 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>4. Tes Wawancara & TPA</span>
                            @if($isWawancaraScheduled)
                                <span class="text-emerald-700 font-extrabold">Terjadwal &rarr;</span>
                            @else
                                <span class="text-blue-600 font-extrabold">Menunggu Jadwal &rarr;</span>
                            @endif
                        </div>
                        @if($isWawancaraScheduled)
                            <p class="text-[11px] text-blue-800">
                                <strong>{{ $pewawancara?->name }}</strong> &bull; {{ $tglWawancaraFormatted }}
                            </p>
                        @else
                            <p class="text-[11px] text-blue-700">Menunggu penetapan jadwal & pewawancara dari panitia.</p>
                        @endif
                    </a>
                @elseif($statusVal === 'SUDAH_DIWAWANCARA')
                    <a href="{{ route('calon-siswa.wawancara.index') }}" class="block p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 ring-2 ring-indigo-400/30 hover:bg-indigo-100/70 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>4. Tes Wawancara & TPA</span>
                            <span class="text-indigo-600 font-extrabold">✓ Selesai Diuji &rarr;</span>
                        </div>
                        <p class="text-[11px] text-indigo-700">Wawancara selesai. Menunggu keputusan sidang kelulusan panitia SPMB.</p>
                    </a>
                @elseif(in_array($statusVal, ['DITERIMA', 'MENUNGGU_DAFTAR_ULANG', 'DAFTAR_ULANG_DIVERIFIKASI', 'RESMI_TERDAFTAR']))
                    <a href="{{ route('calon-siswa.daftar-ulang.index') }}" class="block p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100/70 hover:border-emerald-300 transition-all cursor-pointer">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>4. Hasil & Daftar Ulang</span>
                            <span class="text-emerald-600 font-extrabold">✓ Lulus Seleksi &rarr;</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Selamat! Anda dinyatakan diterima di SMK Wikrama.</p>
                    </a>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span>4. Wawancara & Hasil</span>
                            <span>Terkunci</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Terbuka setelah kesepahaman disetujui.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
