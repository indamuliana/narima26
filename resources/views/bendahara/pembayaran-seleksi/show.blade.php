<x-layouts.app>
    <x-slot name="title">Tinjau Bukti Pembayaran — Bendahara</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('bendahara.pembayaran-seleksi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Pembayaran Seleksi</span>
        </a>
    </x-slot>

    <div class="space-y-6">

        <!-- Breadcrumb & Title -->
        <div>
            <a href="{{ route('bendahara.pembayaran-seleksi.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-orange-600 transition mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Pembayaran Seleksi
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tinjau Pembayaran Seleksi</h1>
                <div>
                    @if($pembayaranSeleksi->status === 'DIVERIFIKASI')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                            DIVERIFIKASI
                        </span>
                    @elseif($pembayaranSeleksi->status === 'DITOLAK')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">
                            DITOLAK
                        </span>
                    @else
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                            MENUNGGU VERIFIKASI (PENDING)
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Col 1 & 2: Informasi Siswa, Data Transfer & Pratinjau Bukti -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informasi Calon Siswa -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-orange-600">Data Pendaftar</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block">Nomor Pendaftaran</span>
                            <span class="font-mono font-bold text-slate-900 text-sm mt-0.5 block">{{ $pembayaranSeleksi->calonSiswa?->nomor_pendaftaran }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nama Lengkap Siswa</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $pembayaranSeleksi->calonSiswa?->nama_lengkap }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">NISN</span>
                            <span class="font-mono text-slate-800 mt-0.5 block">{{ $pembayaranSeleksi->calonSiswa?->nisn }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Pilihan Jurusan & Program</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">
                                {{ $pembayaranSeleksi->calonSiswa?->jurusan?->nama }} ({{ $pembayaranSeleksi->calonSiswa?->program?->nama ?? 'Reguler' }})
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detail Pembayaran & Transfer -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-orange-600">Rincian Pengiriman Dana</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block">Bank / Kanal Pembayaran</span>
                            <span class="font-bold text-slate-900 mt-0.5 block">{{ $pembayaranSeleksi->bank_pengirim ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nama Pemilik Rekening Pengirim</span>
                            <span class="font-bold text-slate-900 mt-0.5 block">{{ $pembayaranSeleksi->nama_pengirim ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nomor Referensi Transaksi</span>
                            <span class="font-mono text-slate-800 mt-0.5 block">{{ $pembayaranSeleksi->nomor_referensi ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Tanggal Transfer</span>
                            <span class="font-medium text-slate-800 mt-0.5 block">
                                {{ $pembayaranSeleksi->tanggal_bayar ? \Carbon\Carbon::parse($pembayaranSeleksi->tanggal_bayar)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nominal Tagihan</span>
                            <span class="font-mono font-bold text-slate-600 mt-0.5 block">Rp {{ number_format($pembayaranSeleksi->nominal_tagihan, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nominal Ditransfer Pendaftar</span>
                            <span class="font-mono font-black text-emerald-600 text-base mt-0.5 block">
                                Rp {{ number_format($pembayaranSeleksi->nominal_dibayar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau File Bukti Transfer -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-orange-600">Lampiran Bukti Transfer</h3>
                        @if($pembayaranSeleksi->bukti_transfer_path)
                            <a href="{{ Storage::disk('public')->url($pembayaranSeleksi->bukti_transfer_path) }}" target="_blank"
                                class="inline-flex items-center text-xs font-bold text-orange-600 hover:underline">
                                Buka di Tab Baru
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @endif
                    </div>

                    @if($pembayaranSeleksi->bukti_transfer_path)
                        @php
                            $ext = strtolower(pathinfo($pembayaranSeleksi->bukti_transfer_path, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 p-2 text-center">
                                <img src="{{ Storage::disk('public')->url($pembayaranSeleksi->bukti_transfer_path) }}"
                                    alt="Bukti Transfer {{ $pembayaranSeleksi->calonSiswa?->nomor_pendaftaran }}"
                                    class="max-h-[500px] w-auto mx-auto object-contain rounded-xl shadow-xs">
                            </div>
                        @else
                            <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                                <svg class="w-12 h-12 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-semibold text-slate-700 block">Dokumen Format PDF</span>
                                <a href="{{ Storage::disk('public')->url($pembayaranSeleksi->bukti_transfer_path) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition">
                                    Unduh / Lihat PDF Bukti Transfer
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                            Calon siswa belum mengunggah file bukti transfer.
                        </div>
                    @endif
                </div>

            </div>

            <!-- Col 3: Panel Aksi Keputusan Bendahara -->
            <div class="space-y-6">

                <!-- Box Status & Petugas Verifikator -->
                @if($pembayaranSeleksi->verified_at)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Histori Verifikasi</span>
                        <div class="text-xs space-y-2 pt-1 border-t border-slate-100">
                            <div>Petugas: <strong>{{ $pembayaranSeleksi->verifikator?->name ?? 'Bendahara' }}</strong></div>
                            <div>Waktu: <strong>{{ \Carbon\Carbon::parse($pembayaranSeleksi->verified_at)->translatedFormat('d F Y H:i:s') }}</strong></div>
                            @if($pembayaranSeleksi->catatan_bendahara)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">
                                    Catatan: {{ $pembayaranSeleksi->catatan_bendahara }}
                                </div>
                            @endif
                        </div>

                        @if($pembayaranSeleksi->status === 'DIVERIFIKASI')
                            <div class="pt-3 border-t border-slate-100">
                                <a href="{{ route('bendahara.pembayaran-seleksi.cetak', $pembayaranSeleksi->id) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition text-xs shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Cetak Kwitansi PDF
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Form Verifikasi (Persetujuan) -->
                @if($pembayaranSeleksi->status !== 'DIVERIFIKASI')
                    <div class="bg-white rounded-3xl p-6 border border-emerald-200 shadow-sm space-y-4">
                        <div class="flex items-center text-emerald-700 font-bold text-sm pb-3 border-b border-emerald-100">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verifikasi & Terima Pembayaran
                        </div>

                        <form action="{{ route('bendahara.pembayaran-seleksi.verify', $pembayaranSeleksi->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="nominal_diterima" class="block text-xs font-bold text-slate-700 uppercase">Nominal Masuk Kas (Rp)</label>
                                <input type="number" name="nominal_diterima" id="nominal_diterima" value="{{ old('nominal_diterima', $pembayaranSeleksi->nominal_dibayar ?: $pembayaranSeleksi->nominal_tagihan) }}"
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none">
                            </div>

                            <div>
                                <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase">Catatan Verifikasi (Opsional)</label>
                                <textarea name="catatan" id="catatan" rows="2" placeholder="Catatan mutasi rekening koran..."
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none">{{ old('catatan') }}</textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Apakah Anda yakin data pembayaran ini telah sesuai dengan mutasi kas sekolah?')"
                                class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm text-xs cursor-pointer">
                                <span>Verifikasi Terima Pembayaran</span>
                            </button>
                        </form>
                    </div>

                    <!-- Form Penolakan Pembayaran -->
                    <div class="bg-white rounded-3xl p-6 border border-rose-200 shadow-sm space-y-4">
                        <div class="flex items-center text-rose-700 font-bold text-sm pb-3 border-b border-rose-100">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tolak Bukti Pembayaran
                        </div>

                        <form action="{{ route('bendahara.pembayaran-seleksi.reject', $pembayaranSeleksi->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="alasan" class="block text-xs font-bold text-slate-700 uppercase">Alasan Penolakan <span class="text-rose-500">*</span></label>
                                <textarea name="alasan" id="alasan" rows="3" required placeholder="Contoh: Bukti transfer buram tidak terbaca / mutasi bank tidak ditemukan / nominal kurang..."
                                    class="mt-1 w-full rounded-xl border border-rose-300 px-3.5 py-2 text-xs focus:border-rose-500 focus:ring-1 focus:ring-rose-200 outline-none">{{ old('alasan') }}</textarea>
                                @error('alasan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak bukti transfer ini? Pendaftar akan diminta mengunggah ulang.')"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 transition border border-rose-200 text-xs cursor-pointer">
                                <span>Tolak Bukti Transfer</span>
                            </button>
                        </form>
                    </div>
                @endif

            </div>

        </div>

    </div>
</x-layouts.app>
