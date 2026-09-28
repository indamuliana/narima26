<x-layouts.app>
    <x-slot name="title">Verifikasi Pembayaran Daftar Ulang - {{ $pembayaranDaftarUlang->calonSiswa?->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb / Back -->
        <div class="flex items-center justify-between">
            <a href="{{ route('bendahara.pembayaran-daftar-ulang.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <span>&larr; Kembali ke Pembayaran Daftar Ulang</span>
            </a>
            <div class="flex items-center gap-2">
                @if ($pembayaranDaftarUlang->status === 'DIVERIFIKASI')
                    <a href="{{ route('bendahara.pembayaran-daftar-ulang.cetak-kwitansi', $pembayaranDaftarUlang) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                        <span>📄 Unduh Kwitansi Resmi</span>
                    </a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <ul class="list-disc list-inside text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Transfer Proof Image/File -->
            <div class="lg:col-span-6 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-800">Berkas Bukti Transfer Bank</h2>
                        <a href="{{ asset('storage/' . $pembayaranDaftarUlang->bukti_transfer_path) }}" target="_blank"
                           class="text-xs font-semibold text-nampi-orange hover:underline">
                            Buka di Tab Baru &nearr;
                        </a>
                    </div>
                    <div class="p-4 flex items-center justify-center bg-slate-900/5 min-h-[380px]">
                        @php
                            $ext = strtolower(pathinfo($pembayaranDaftarUlang->bukti_transfer_path, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                            <img src="{{ asset('storage/' . $pembayaranDaftarUlang->bukti_transfer_path) }}"
                                 alt="Bukti Transfer"
                                 class="max-h-[500px] w-auto rounded-xl object-contain border border-slate-200 shadow-xs">
                        @else
                            <div class="text-center p-8">
                                <span class="text-5xl block mb-3">📄</span>
                                <p class="text-sm font-bold text-slate-700">Berkas Dokumen PDF</p>
                                <p class="text-xs text-slate-400 mt-1 mb-4">Bukti transfer diunggah dalam format dokumen PDF.</p>
                                <a href="{{ asset('storage/' . $pembayaranDaftarUlang->bukti_transfer_path) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                                    Unduh & Periksa Dokumen
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Verification Panel -->
                @if ($pembayaranDaftarUlang->status === 'PENDING')
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Keputusan Verifikasi Bendahara</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Pastikan mutasi dana telah masuk ke rekening yayasan sebelum memvalidasi.</p>
                        </div>

                        <!-- Approve Form -->
                        <form method="POST" action="{{ route('bendahara.pembayaran-daftar-ulang.verify', $pembayaranDaftarUlang) }}" class="space-y-4 pt-2 border-t border-slate-100">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikasi (Opsional)</label>
                                <input type="text" name="catatan_bendahara"
                                       placeholder="Contoh: Dana masuk rekening BSI a.n Panitia SPMB"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                            </div>
                            <button type="submit" onclick="return confirm('Apakah Anda yakin dana pembayaran daftar ulang ini valid dan telah masuk ke rekening sekolah?')"
                                    class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider transition-colors shadow-xs cursor-pointer">
                                ✓ Setujui & Verifikasi Pembayaran (Valid)
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form method="POST" action="{{ route('bendahara.pembayaran-daftar-ulang.reject', $pembayaranDaftarUlang) }}" class="space-y-4 pt-4 border-t border-slate-100">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-rose-700 mb-1">Tolak Pembayaran (Wajib Alasan) <span class="text-rose-500">*</span></label>
                                <textarea name="catatan_bendahara" rows="2" required
                                          placeholder="Tuliskan alasan penolakan agar siswa dapat mengunggah bukti yang benar..."
                                          class="w-full px-3.5 py-2 text-xs rounded-xl border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500"></textarea>
                            </div>
                            <button type="submit" onclick="return confirm('Tolak pembayaran ini? Calon siswa akan diminta memperbaiki bukti transfer.')"
                                    class="w-full py-2.5 rounded-xl border border-rose-300 text-rose-700 hover:bg-rose-50 text-xs font-bold transition-colors cursor-pointer">
                                ✕ Tolak Pembayaran Ini
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-slate-800">Status Pemeriksaan</h2>
                            @if ($pembayaranDaftarUlang->status === 'DIVERIFIKASI')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Diverifikasi Valid
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                        <div class="text-xs space-y-1.5 text-slate-600 bg-slate-50 p-3.5 rounded-xl">
                            <p><strong class="text-slate-800">Pemeriksa:</strong> {{ $pembayaranDaftarUlang->verifiedBy?->name ?? 'Bendahara' }}</p>
                            <p><strong class="text-slate-800">Waktu:</strong> {{ $pembayaranDaftarUlang->verified_at?->format('d/m/Y H:i') ?? '-' }}</p>
                            @if ($pembayaranDaftarUlang->catatan_bendahara)
                                <p class="pt-2 border-t border-slate-200"><strong class="text-slate-800">Catatan:</strong> {{ $pembayaranDaftarUlang->catatan_bendahara }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Candidate Info & Invoice Breakdown -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Transaction Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-base font-black text-slate-800">Detail Transaksi Pengirim</h2>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block font-medium">Nominal Dibayar</span>
                            <span class="text-base font-black text-slate-900 mt-0.5 block">
                                Rp {{ number_format($pembayaranDaftarUlang->nominal_dibayar, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block font-medium">Tanggal Transfer</span>
                            <span class="text-sm font-bold text-slate-800 mt-0.5 block">
                                {{ $pembayaranDaftarUlang->tanggal_bayar?->format('d F Y') }}
                            </span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block font-medium">Bank Pengirim</span>
                            <span class="text-sm font-bold text-slate-800 mt-0.5 block">
                                {{ $pembayaranDaftarUlang->bank_pengirim }}
                            </span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block font-medium">Nama Pemilik Rekening</span>
                            <span class="text-sm font-bold text-slate-800 mt-0.5 block">
                                {{ $pembayaranDaftarUlang->nama_pengirim }}
                            </span>
                        </div>
                    </div>
                    @if ($pembayaranDaftarUlang->nomor_referensi)
                        <div class="p-3 bg-slate-50 rounded-xl text-xs">
                            <span class="text-slate-400 block font-medium">Nomor Referensi Transaksi</span>
                            <span class="font-mono font-bold text-slate-800 mt-0.5 block">
                                {{ $pembayaranDaftarUlang->nomor_referensi }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Candidate & Parent Info -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-black text-slate-800">Profil Calon Siswa</h2>
                        <span class="font-mono text-xs font-bold text-nampi-orange">{{ $pembayaranDaftarUlang->calonSiswa?->nomor_pendaftaran }}</span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">Nama Lengkap</span>
                            <span class="font-bold text-slate-800">{{ $pembayaranDaftarUlang->calonSiswa?->nama_lengkap }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">Kompetensi Keahlian</span>
                            <span class="font-semibold text-slate-800">{{ $pembayaranDaftarUlang->calonSiswa?->jurusan?->nama_jurusan }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">Program / Jalur</span>
                            <span class="font-semibold text-slate-800">{{ $pembayaranDaftarUlang->calonSiswa?->program?->nama_program }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">No. WhatsApp Calon Siswa</span>
                            <span class="font-semibold text-slate-800">{{ $pembayaranDaftarUlang->calonSiswa?->no_hp_siswa ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-slate-400">Nama Orang Tua / Wali</span>
                            <span class="font-semibold text-slate-800">{{ $pembayaranDaftarUlang->calonSiswa?->dataOrangtua?->nama_ayah ?? $pembayaranDaftarUlang->calonSiswa?->dataOrangtua?->nama_ibu ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-black text-slate-800">Posisi Tagihan Daftar Ulang</h2>
                        <a href="{{ route('bendahara.tagihan.show', $tagihan) }}" class="text-xs font-semibold text-nampi-orange hover:underline">
                            Lihat Rincian Penuh &rarr;
                        </a>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nomor Tagihan</span>
                            <span class="font-mono font-bold text-slate-800">{{ $tagihan->nomor_tagihan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Biaya Netto</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Telah Diverifikasi</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-slate-200 font-bold">
                            <span class="text-slate-700">Sisa Tagihan Saat Ini</span>
                            <span class="text-nampi-orange font-black">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
