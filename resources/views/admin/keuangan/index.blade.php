<x-layouts.app>
    <x-slot name="title">Manajemen Master Keuangan & Tarif SPMB — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Manajemen Master Keuangan</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola snapshot tarif biaya daftar ulang, komponen SPP/DSP, dan kebijakan potongan beasiswa</p>
            </div>
            <div>
                <button type="button" onclick="document.getElementById('modalTambahBiaya').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Komponen Biaya</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Fee Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-800">Tarif Master Biaya Pendidikan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Komponen tagihan resmi penerimaan siswa baru</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    Total: {{ $biayaList->count() }} komponen
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-5 py-3.5" style="width: 14%;">Kode</th>
                            <th class="px-5 py-3.5" style="width: 28%;">Nama Komponen Biaya</th>
                            <th class="px-4 py-3.5" style="width: 16%;">Kategori</th>
                            <th class="px-5 py-3.5 text-right" style="width: 18%;">Nominal</th>
                            <th class="px-4 py-3.5 text-center" style="width: 10%;">Status</th>
                            <th class="px-5 py-3.5 text-right" style="width: 14%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($biayaList as $b)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-slate-900">
                                    {{ $b->kode_biaya }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $b->nama_biaya }}</span>
                                    <span class="text-slate-400 text-[11px]">{{ $b->keterangan ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">
                                        {{ str_replace('_', ' ', $b->kategori) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right font-black text-slate-900 text-sm">
                                    Rp {{ number_format($b->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($b->aktif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('admin.keuangan.toggle', $b) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors cursor-pointer {{ $b->aktif ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                {{ $b->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">Belum ada komponen master biaya terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Diskon List -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-black text-slate-800">Riwayat Potongan & Diskon Beasiswa</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar Calon Murid yang memperoleh keringanan atau beasiswa</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Calon Murid</th>
                            <th class="px-4 py-3">Jenis Diskon</th>
                            <th class="px-4 py-3 text-right">Nominal Potongan</th>
                            <th class="px-5 py-3">Alasan / Dasar Pertimbangan</th>
                            <th class="px-4 py-3">Diberikan Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($diskonList as $d)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-900 block">{{ $d->calonSiswa?->nama_lengkap ?? 'Calon Siswa' }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $d->calonSiswa?->nomor_pendaftaran }}</span>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-slate-800">
                                    {{ $d->jenis_diskon }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-emerald-600">
                                    Rp {{ number_format($d->nominal_potongan, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">
                                    {{ $d->alasan ?: '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 font-medium">
                                    {{ $d->diberikanOleh?->name ?? 'Panitia' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">Belum ada riwayat diskon yang diberikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Komponen Biaya -->
    <div id="modalTambahBiaya" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-base">Tambah Komponen Biaya</h3>
                <button type="button" onclick="document.getElementById('modalTambahBiaya').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.keuangan.store') }}" class="space-y-4 mt-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Biaya *</label>
                    <input type="text" name="kode_biaya" required placeholder="Contoh: DSP-G1"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-mono uppercase">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Komponen Biaya *</label>
                    <input type="text" name="nama_biaya" required placeholder="Contoh: Dana Sumbangan Pendidikan (DSP)"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori *</label>
                        <select name="kategori" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                            <option value="DAFTAR_ULANG">Daftar Ulang</option>
                            <option value="DSP">DSP / Uang Pangkal</option>
                            <option value="SPP">SPP Bulanan</option>
                            <option value="ASRAMA">Biaya Asrama / Pondok</option>
                            <option value="SERAGAM">Seragam & Atribut</option>
                            <option value="PRAKTIK">Praktik Kejuruan</option>
                            <option value="LAINNYA">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal (Rp) *</label>
                        <input type="number" name="nominal" min="0" required placeholder="Contoh: 1500000"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan atau rincian komponen..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="wajib" name="wajib" value="1" checked class="rounded text-nampi-orange">
                    <label for="wajib" class="font-medium text-slate-700">Wajib bagi semua Calon Murid</label>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalTambahBiaya').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-nampi-orange text-white font-bold hover:bg-orange-600 transition-colors shadow-xs">
                        Simpan Komponen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
