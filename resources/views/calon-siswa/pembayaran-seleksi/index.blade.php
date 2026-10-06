<x-layouts.app>
    <x-slot name="title">Pembayaran Biaya Seleksi SPMB</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
    </x-slot>

    <div class="space-y-6">

        <!-- Alerts -->
        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if($pembayaran && $pembayaran->status === 'DITOLAK')
            <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-rose-600 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <h3 class="text-sm font-bold text-rose-900">Pembayaran Seleksi Anda Ditolak</h3>
                        <p class="text-xs text-rose-700 mt-1">
                            Alasan dari Bendahara: <strong>{{ $pembayaran->catatan_bendahara }}</strong>
                        </p>
                        <p class="text-xs text-rose-600 mt-2">
                            Silakan periksa kembali mutasi atau struk transfer Anda dan unggah ulang bukti yang benar pada formulir di bawah ini.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Tagihan & Informasi Rekening -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">Tagihan Biaya Pendaftaran Seleksi</h2>
                        <p class="text-xs text-slate-500">Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></p>
                    </div>
                    <div>
                        @if($pembayaran && $pembayaran->status === 'DIVERIFIKASI')
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                DIVERIFIKASI
                            </span>
                        @elseif($pembayaran && $pembayaran->status === 'PENDING' && $pembayaran->bukti_transfer_path)
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                <svg class="w-4 h-4 mr-1 text-amber-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                MENUNGGU VERIFIKASI
                            </span>
                        @elseif($pembayaran && $pembayaran->status === 'DITOLAK')
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                DITOLAK
                            </span>
                        @else
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                BELUM DIBAYAR
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-baseline justify-between bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <span class="text-sm font-semibold text-slate-700">Total Biaya Seleksi</span>
                    <span class="text-3xl font-black text-slate-900">
                        Rp {{ number_format($pembayaran?->nominal_tagihan ?? 200000, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Jika sudah diverifikasi -->
                @if($pembayaran && $pembayaran->status === 'DIVERIFIKASI')
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-4">
                        <div class="flex items-center text-emerald-900 font-bold text-sm">
                            <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pembayaran Anda Telah Resmi Diverifikasi
                        </div>
                        <p class="text-xs text-emerald-800 leading-relaxed">
                            Terima kasih, pembayaran biaya pendaftaran seleksi Anda telah diverifikasi oleh Bendahara sekolah ({{ $pembayaran->verifikator?->name ?? 'Bendahara' }}) pada tanggal {{ \Carbon\Carbon::parse($pembayaran->verified_at)->translatedFormat('d F Y H:i') }}.
                        </p>
                        <div class="flex flex-wrap gap-3 pt-2">
                            <a href="{{ route('calon-siswa.pembayaran-seleksi.cetak') }}"
                                class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm text-xs">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Cetak Kwitansi Resmi (PDF)
                            </a>
                            <a href="{{ route('calon-siswa.lengkapi-data.index') }}"
                                class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-slate-800 bg-white hover:bg-slate-50 transition border border-slate-300 text-xs">
                                Lanjut Pengisian Biodata & Berkas
                                <svg class="w-4 h-4 ml-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Jika sedang menunggu verifikasi -->
                @if($pembayaran && $pembayaran->status === 'PENDING' && $pembayaran->bukti_transfer_path)
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 space-y-3">
                        <div class="flex items-center text-amber-900 font-bold text-sm">
                            <svg class="w-5 h-5 text-amber-600 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bukti Transfer Sedang Ditinjau Bendahara
                        </div>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Data transfer Anda via <strong>{{ $pembayaran->bank_pengirim }}</strong> (a.n. {{ $pembayaran->nama_pengirim }}) sebesar <strong>Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}</strong> telah masuk antrean verifikasi Bendahara. Mohon tunggu proses pengecekan mutasi 1x24 jam kerja.
                        </p>
                    </div>
                @endif

                <!-- Form Upload Bukti Transfer (tampil jika belum bayar, belum upload, atau ditolak) -->
                @if(!$pembayaran || $pembayaran->status === 'DITOLAK' || empty($pembayaran->bukti_transfer_path))
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 mb-4">Formulir Konfirmasi Bukti Transfer</h3>
                        
                        <form action="{{ route('calon-siswa.pembayaran-seleksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="bank_pengirim" class="block text-xs font-bold text-slate-700 uppercase">Bank / Kanal Pembayaran <span class="text-red-500">*</span></label>
                                    <input type="text" name="bank_pengirim" id="bank_pengirim" value="{{ old('bank_pengirim', $pembayaran?->bank_pengirim) }}" placeholder="Contoh: BCA / BRI / Mandiri / BSI / Dana" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                    @error('bank_pengirim') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="nama_pengirim" class="block text-xs font-bold text-slate-700 uppercase">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_pengirim" id="nama_pengirim" value="{{ old('nama_pengirim', $pembayaran?->nama_pengirim) }}" placeholder="Nama sesuai buku tabungan / rekening" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                    @error('nama_pengirim') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="nominal_dibayar" class="block text-xs font-bold text-slate-700 uppercase">Nominal Ditransfer (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" name="nominal_dibayar" id="nominal_dibayar" value="{{ old('nominal_dibayar', $pembayaran?->nominal_tagihan ?? 200000) }}" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                    @error('nominal_dibayar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="tanggal_bayar" class="block text-xs font-bold text-slate-700 uppercase">Tanggal Transfer <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" value="{{ old('tanggal_bayar', now()->toDateString()) }}" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                    @error('tanggal_bayar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="nomor_referensi" class="block text-xs font-bold text-slate-700 uppercase">Nomor Referensi Transfer / No Struk (Opsional)</label>
                                    <input type="text" name="nomor_referensi" id="nomor_referensi" value="{{ old('nomor_referensi', $pembayaran?->nomor_referensi) }}" placeholder="Nomor ref transaksi m-banking atau ATM"
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="bukti_transfer" class="block text-xs font-bold text-slate-700 uppercase">Unggah File Bukti Transfer <span class="text-red-500">*</span></label>
                                    <input type="file" name="bukti_transfer" id="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" required
                                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, atau PDF (Ukuran maksimal 10 MB).</p>
                                    @error('bukti_transfer') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-7 py-3 rounded-xl font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-md transition transform active:scale-95 text-sm cursor-pointer">
                                    <span>Kirim Konfirmasi Pembayaran</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Rekening Informasi Card -->
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-6 shadow-sm">
                    <span class="text-xs uppercase font-bold text-orange-400 tracking-wider">Rekening Resmi Sekolah</span>
                    <h3 class="text-lg font-black mt-2">Bank BNI</h3>
                    <div class="mt-4 p-3 bg-white/10 rounded-xl border border-white/10 font-mono text-xl font-bold text-amber-300 tracking-wider">
                        082-0083-086
                    </div>
                    <p class="text-xs text-slate-300 mt-2">Atas Nama: <strong>SMK WIKRAMA 1 GARUT</strong></p>

                    <div class="mt-6 pt-4 border-t border-white/10 text-xs text-slate-300 space-y-2">
                        <p><strong>Catatan Transfer:</strong></p>
                        <p>Tuliskan berita transfer dengan format:</p>
                        <p class="font-mono bg-white/10 p-2 rounded text-orange-200">
                            {{ $calonSiswa->nomor_pendaftaran }} - Seleksi
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                    <h4 class="text-sm font-bold text-slate-900 mb-2">Butuh Bantuan?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Jika ada kendala pembayaran atau butuh konfirmasi langsung, hubungi Helpdesk Panitia SPMB melalui WhatsApp.
                    </p>
                    <x-whatsapp-helpdesk />
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
