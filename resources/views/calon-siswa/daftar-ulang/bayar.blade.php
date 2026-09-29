<x-layouts.app>
    <x-slot name="title">Konfirmasi Pembayaran Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        @include('calon-siswa.partials.sidebar')
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
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">#{{ $tagihan->nomor_tagihan }}</span>
                    @if($tagihan->jenis_tagihan === 'SERAGAM')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">Seragam & Atribut</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Daftar Ulang (DSP & SPP)</span>
                    @endif
                </div>
                <h3 class="text-lg font-black mt-1">Sisa Kewajiban Tagihan</h3>
                <p class="text-xs text-slate-300">Calon Siswa: {{ $calonSiswa->nama_lengkap }} ({{ $calonSiswa->nomor_pendaftaran }})</p>
            </div>
            <div class="text-right sm:border-l sm:border-slate-700 sm:pl-6">
                <span class="text-xs text-slate-400 block">Sisa Belum Dibayar:</span>
                <span class="text-2xl font-black text-amber-400">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</span>
            </div>
        </div>

        @if ($tagihan->total_diskon > 0)
            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-900 flex items-start gap-3.5 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 text-xl border border-emerald-200">
                    🏷️
                </div>
                <div class="space-y-1 flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black uppercase tracking-wider text-emerald-800">Diskon Diterapkan</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-200 text-emerald-900">
                                HEMAT Rp {{ number_format($tagihan->total_diskon, 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="text-xs font-bold text-emerald-700">
                            {{ $tagihan->diskon?->jenis_diskon ?? 'Keringanan Biaya' }}
                            @if ($tagihan->diskon?->metode_diskon === 'persentase')
                                ({{ (float) $tagihan->diskon->nilai_diskon }}%)
                            @endif
                        </span>
                    </div>
                    <p class="text-xs text-emerald-800 leading-relaxed">
                        Tagihan bruto semula <strong>Rp {{ number_format($tagihan->total_bruto, 0, ',', '.') }}</strong>, dipotong diskon sebesar <strong class="text-emerald-700 font-black">- Rp {{ number_format($tagihan->total_diskon, 0, ',', '.') }}</strong>, sehingga total kewajiban netto Anda menjadi <strong>Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</strong>.
                    </p>
                    @if ($tagihan->diskon?->alasan)
                        <p class="text-[11px] text-emerald-700 italic pt-0.5">
                            Keterangan: {{ $tagihan->diskon->alasan }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Submission Form -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
            <form method="POST" action="{{ route('calon-siswa.daftar-ulang.store-bayar') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">

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
