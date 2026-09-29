<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isValid && $dokumen ? 'Verifikasi Dokumen: ' . $dokumen->judul_dokumen : 'Verifikasi Dokumen SPMB' }} - SMK Wikrama 1 Garut</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col justify-between antialiased">
    <!-- Header Resmi -->
    <header class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white shadow-md">
        <div class="max-w-3xl mx-auto px-4 py-5 sm:px-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center p-2 border border-white/20 shadow-inner">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Wikrama" class="max-h-full max-w-full object-contain" onerror="this.style.display='none'">
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-bold tracking-tight text-white leading-tight">SMK WIKRAMA 1 GARUT</h1>
                    <p class="text-xs text-blue-200">Sistem Verifikasi Keabsahan Dokumen SPMB</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Aktif
            </span>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="max-w-3xl mx-auto px-4 py-8 sm:px-6 w-full flex-1">
        @if($isValid && $dokumen)
            <!-- CARD STATUS DOKUMEN VALID -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden transition-all">
                <!-- Status Banner -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-5 text-white flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0 shadow-inner border border-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white/25 text-white mb-1">
                            Sertifikat Keabsahan Resmi
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">DOKUMEN ASLI & TERVERIFIKASI</h2>
                        <p class="text-xs sm:text-sm text-emerald-100 mt-0.5">Dokumen ini sah dan tercatat resmi pada database Sistem SPMB SMK Wikrama 1 Garut.</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Ringkasan Dokumen -->
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-200/70">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jenis Dokumen</span>
                                <span class="text-base font-bold text-slate-900 mt-0.5 block">{{ $dokumen->judul_dokumen }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nomor Dokumen</span>
                                <span class="text-base font-mono font-bold text-blue-700 mt-0.5 block">{{ $dokumen->nomor_dokumen }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kode Keamanan Verifikasi</span>
                                <span class="text-xs font-mono font-semibold text-slate-700 mt-0.5 block bg-slate-200/70 px-2 py-1 rounded inline-block">
                                    {{ $dokumen->kode_verifikasi }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Waktu Pengesahan (TTE)</span>
                                <span class="text-sm font-medium text-slate-800 mt-0.5 block">
                                    {{ $dokumen->signed_at->translatedFormat('d F Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Calon Peserta Didik -->
                    @if($dokumen->calonSiswa)
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Identitas Pemilik Dokumen
                        </h3>
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 text-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3 bg-slate-50/50">
                                <span class="w-48 text-slate-500 font-medium">Nama Lengkap</span>
                                <span class="font-bold text-slate-900">{{ strtoupper($dokumen->calonSiswa->nama_lengkap) }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3">
                                <span class="w-48 text-slate-500 font-medium">Nomor Pendaftaran / NISN</span>
                                <span class="font-mono font-semibold text-slate-800">{{ $dokumen->calonSiswa->nomor_pendaftaran }} &bull; {{ $dokumen->calonSiswa->nisn }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3 bg-slate-50/50">
                                <span class="w-48 text-slate-500 font-medium">Kompetensi Keahlian</span>
                                <span class="font-medium text-slate-800">
                                    {{ $dokumen->calonSiswa->jurusan?->nama_jurusan ?? '-' }}
                                    <span class="text-xs text-slate-500">({{ $dokumen->calonSiswa->program?->nama_program ?? 'Reguler' }})</span>
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3">
                                <span class="w-48 text-slate-500 font-medium">Asal Sekolah</span>
                                <span class="text-slate-700">{{ $dokumen->calonSiswa->sekolahAsal?->nama_sekolah ?? $dokumen->calonSiswa->asal_sekolah_lainnya ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Detail Spesifik Metadata -->
                    @if(!empty($dokumen->metadata))
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Rincian Pengesahan Dokumen
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            @if(isset($dokumen->metadata['nominal']))
                                <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-200">
                                    <span class="text-xs font-semibold text-emerald-700 block">Jumlah Pembayaran Diterima</span>
                                    <span class="text-lg font-bold text-emerald-800">Rp {{ number_format($dokumen->metadata['nominal'], 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if(isset($dokumen->metadata['total_netto']))
                                <div class="p-3.5 bg-blue-50 rounded-xl border border-blue-200">
                                    <span class="text-xs font-semibold text-blue-700 block">Total Tagihan Bersih (Netto)</span>
                                    <span class="text-lg font-bold text-blue-900">Rp {{ number_format($dokumen->metadata['total_netto'], 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if(isset($dokumen->metadata['keputusan']))
                                <div class="p-3.5 rounded-xl border {{ $dokumen->metadata['keputusan'] === 'DITERIMA' ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }}">
                                    <span class="text-xs font-semibold {{ $dokumen->metadata['keputusan'] === 'DITERIMA' ? 'text-emerald-700' : 'text-rose-700' }} block">Hasil Keputusan Seleksi</span>
                                    <span class="text-lg font-bold {{ $dokumen->metadata['keputusan'] === 'DITERIMA' ? 'text-emerald-800' : 'text-rose-800' }}">
                                        {{ $dokumen->metadata['keputusan'] }}
                                    </span>
                                </div>
                            @endif
                            @if(isset($dokumen->metadata['metode_bayar']))
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-xs font-semibold text-slate-500 block">Metode Pembayaran</span>
                                    <span class="font-bold text-slate-800">{{ strtoupper($dokumen->metadata['metode_bayar']) }} {{ !empty($dokumen->metadata['bank_pengirim']) ? '('.$dokumen->metadata['bank_pengirim'].')' : '' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Informasi Penandatangan Elektronik -->
                    <div class="border-t border-slate-200 pt-5">
                        <div class="bg-gradient-to-br from-slate-50 to-blue-50/30 rounded-xl p-5 border border-slate-200/80">
                            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                        {{ substr($dokumen->penandatangan_nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">Ditandatangani Secara Elektronik Oleh:</span>
                                        <h4 class="text-base font-bold text-slate-900">{{ $dokumen->penandatangan_nama }}</h4>
                                        <p class="text-xs text-slate-600 font-medium">{{ $dokumen->penandatangan_jabatan }}</p>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        TTE Sah & Terdaftar
                                    </span>
                                    <span class="block text-xs text-slate-400 mt-1">Dicatat pada {{ $dokumen->created_at->translatedFormat('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Scan Counter -->
                    <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 border-t border-slate-100 pt-4 gap-2">
                        <span>Pemindaian ke-{{ $dokumen->scan_count }} &bull; Terakhir dipindai: {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
                        <span class="font-medium text-slate-500">Sistem SPMB Nampi &copy; SMK Wikrama 1 Garut</span>
                    </div>
                </div>
            </div>
        @else
            <!-- CARD STATUS DOKUMEN TIDAK VALID / PALSU -->
            <div class="bg-white rounded-2xl shadow-xl border border-rose-200 overflow-hidden">
                <div class="bg-gradient-to-r from-rose-600 to-red-700 px-6 py-6 text-white flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0 shadow-inner border border-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white/25 text-white mb-1">
                            Peringatan Keamanan
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">DOKUMEN TIDAK DITEMUKAN / TIDAK VALID</h2>
                        <p class="text-xs sm:text-sm text-rose-100 mt-0.5">Kode verifikasi yang dipindai tidak terdaftar di sistem SPMB SMK Wikrama 1 Garut.</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-sm text-rose-800 leading-relaxed">
                        <p class="font-bold mb-1">Perhatian:</p>
                        <p>Dokumen dengan kode referensi <strong>{{ $kode ?? 'N/A' }}</strong> tidak ditemukan dalam basis data arsip resmi kami, atau tanda tangan elektronik tersebut telah dicabut/dibatalkan.</p>
                        <p class="mt-2 text-xs text-rose-700">Waspadalah terhadap potensi penipuan atau pemalsuan dokumen bukti pendaftaran/pembayaran.</p>
                    </div>

                    <div class="text-center pt-2">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Kembali ke Portal SPMB
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-5 border-t border-slate-800 text-center">
        <div class="max-w-3xl mx-auto px-4 space-y-1">
            <p class="text-slate-300 font-semibold">SMK WIKRAMA 1 GARUT</p>
            <p>www.smkwikrama1garut.sch.id</p>
            <p class="pt-2 text-slate-500">&copy; {{ date('Y') }} SPMB Nampi &bull; Crafted with ♥ by Inda Muliana.</p>
        </div>
    </footer>
</body>
</html>
