<x-layouts.app>
    <x-slot name="title">Konfirmasi Pembayaran Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('calon-siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('calon-siswa.pembayaran-seleksi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Pembayaran Seleksi</span>
        </a>
        <a href="{{ route('calon-siswa.lengkapi-data.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Lengkapi Data & Berkas</span>
        </a>
        <a href="{{ route('calon-siswa.kesepahaman.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Kesepahaman SPMB</span>
        </a>
        <a href="{{ route('calon-siswa.dokumen.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Dokumen & Cetak PDF</span>
        </a>
        <a href="{{ route('calon-siswa.daftar-ulang.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Tagihan Daftar Ulang</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Back Link -->
        <a href="{{ route('calon-siswa.daftar-ulang.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <span>&larr; Kembali ke Rincian Tagihan</span>
        </a>

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-black text-slate-800">Formulir Konfirmasi Pembayaran</h1>
            <p class="text-xs text-slate-500 mt-1">Unggah bukti transfer pembayaran daftar ulang (bisa lunas atau bertahap / cicilan).</p>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <p class="text-xs font-bold mb-1">Terdapat kesalahan pada isian formulir:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Outstanding Position Card -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div>
                <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Nomor Tagihan: {{ $tagihan->nomor_tagihan }}</span>
                <h3 class="text-lg font-black mt-0.5">Sisa Kewajiban Tagihan</h3>
                <p class="text-xs text-slate-300">Calon Siswa: {{ $calonSiswa->nama_lengkap }} ({{ $calonSiswa->nomor_pendaftaran }})</p>
            </div>
            <div class="text-right sm:border-l sm:border-slate-700 sm:pl-6">
                <span class="text-xs text-slate-400 block">Sisa Belum Dibayar:</span>
                <span class="text-2xl font-black text-amber-400">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Submission Form -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
            <form method="POST" action="{{ route('calon-siswa.daftar-ulang.store-bayar') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Nominal Dibayar -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Nominal Yang Ditransfer (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="nominal_dibayar" value="{{ old('nominal_dibayar', $remainingBalance) }}"
                               min="10000" max="{{ $remainingBalance }}" step="1000" required
                               placeholder="Contoh: {{ $remainingBalance }}"
                               class="w-full pl-10 pr-4 py-2.5 text-sm font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Bisa diisi penuh sesuai sisa tagihan (<strong>Rp {{ number_format($remainingBalance, 0, ',', '.') }}</strong>) atau nominal cicilan yang Anda bayarkan saat ini (minimal Rp 10.000).
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Bayar -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Tanggal Transfer <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                    </div>

                    <!-- Bank Pengirim -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Bank / Channel Asal Transfer <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_pengirim" value="{{ old('bank_pengirim') }}" required
                               placeholder="Contoh: BCA, BSI, Mandiri, BRI, SeaBank"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Pengirim -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Nama Pemilik Rekening Pengirim <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim', $calonSiswa->nama_lengkap) }}" required
                               placeholder="Nama yang tertera pada rekening pengirim"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                    </div>

                    <!-- Nomor Referensi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Nomor Referensi Transaksi (Opsional)
                        </label>
                        <input type="text" name="nomor_referensi" value="{{ old('nomor_referensi') }}"
                               placeholder="No referensi / resi transfer dari mutasi bank"
                               class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                    </div>
                </div>

                <!-- Bukti Transfer File -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Unggah Berkas Bukti Transfer / Resi ATM <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" required
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">
                        Format file: JPG, PNG, atau PDF. Ukuran berkas maksimal 2MB. Pastikan gambar tajam dan terbaca jelas.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('calon-siswa.daftar-ulang.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" onclick="return confirm('Apakah Anda yakin data bukti transfer yang diisi sudah sesuai?')"
                            class="px-6 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                        Kirim Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
