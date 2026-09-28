<x-layouts.app>
    <x-slot name="title">Master Tarif Biaya Sekolah</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    @php
        $kategoriMeta = [
            'DSP'      => ['label' => 'DSP / Uang Pangkal',  'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'SPP'      => ['label' => 'SPP Bulanan',          'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'SERAGAM'  => ['label' => 'Seragam & Atribut',   'color' => 'bg-teal-50 text-teal-700 border-teal-200'],
            'KEGIATAN' => ['label' => 'Kegiatan & MPLS',     'color' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'PRAKTIK'  => ['label' => 'Praktik Jurusan',     'color' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'LAINNYA'  => ['label' => 'Lain-lain',           'color' => 'bg-slate-100 text-slate-600 border-slate-200'],
        ];
    @endphp

    <div class="space-y-6"
         x-data="{
             modalTambah: false,
             editItem: null,
             confirmToggle: null,
         }">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Master Tarif Biaya Pendidikan</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar komponen tarif biaya SPMB Wikrama yang menjadi rujukan pembuatan tagihan.</p>
            </div>
            <button @click="modalTambah = true" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                <span>➕ Tambah Komponen Biaya</span>
            </button>
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

        {{-- Snapshot Notice --}}
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 flex items-start gap-3">
            <span class="text-xl">🔒</span>
            <div class="text-xs">
                <p class="font-bold">Prinsip Snapshot Immutability (Integritas Keuangan):</p>
                <p class="text-amber-800 mt-0.5 leading-relaxed">
                    Setiap perubahan tarif atau penonaktifan komponen biaya di menu ini hanya akan berdampak pada <strong>tagihan baru</strong> yang diterbitkan ke depannya. Tagihan siswa yang telah diterbitkan sebelumnya tidak akan berubah nilai atau rinciannya.
                </p>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.master-biaya.index') }}" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Cari nama atau kode biaya..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div>
                    <select name="kategori"
                            class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriMeta as $val => $meta)
                            <option value="{{ $val }}" {{ $kategori === $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="status"
                            class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                        <option value="">Semua Status</option>
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Cari
                    </button>
                    @if ($q || $kategori || $status !== null && $status !== '')
                        <a href="{{ route('bendahara.master-biaya.index') }}"
                           class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Master Biaya Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Kode</th>
                            <th class="px-4 py-3.5">Nama Komponen Biaya</th>
                            <th class="px-4 py-3.5">Kategori</th>
                            <th class="px-4 py-3.5">Program / Gelombang</th>
                            <th class="px-4 py-3.5 text-right">Nominal</th>
                            <th class="px-4 py-3.5 text-center">Sifat</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($biayaList as $b)
                            <tr class="hover:bg-slate-50/70 transition-colors {{ ! $b->aktif ? 'opacity-60 bg-slate-50/40' : '' }}">
                                <td class="px-5 py-4 font-mono font-bold text-xs text-slate-800">
                                    {{ $b->kode_biaya }}
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-900">{{ $b->nama_biaya }}</p>
                                    @if ($b->keterangan)
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $b->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $meta = $kategoriMeta[$b->kategori] ?? ['label' => $b->kategori, 'color' => 'bg-slate-100 text-slate-600 border-slate-200'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded border text-[11px] font-semibold {{ $meta['color'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="space-y-0.5">
                                        @if ($b->program)
                                            <p class="text-xs font-semibold text-slate-700">{{ $b->program->nama }}</p>
                                        @else
                                            <p class="text-xs text-slate-400">—</p>
                                        @endif
                                        @if ($b->gelombang)
                                            <p class="text-[11px] text-slate-400">{{ $b->gelombang->nama }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-slate-900 block">
                                        Rp {{ number_format($b->nominal, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($b->wajib)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Wajib
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Opsional
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($b->aktif)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editItem = {{ json_encode(['id' => $b->id, 'kode_biaya' => $b->kode_biaya, 'nama_biaya' => $b->nama_biaya, 'kategori' => $b->kategori, 'program_id' => $b->program_id, 'gelombang_id' => $b->gelombang_id, 'nominal' => $b->nominal, 'keterangan' => $b->keterangan]) }}"
                                                type="button"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors cursor-pointer">
                                            Ubah
                                        </button>
                                        {{-- Toggle dengan konfirmasi --}}
                                        <button @click="confirmToggle = {{ json_encode(['id' => $b->id, 'nama_biaya' => $b->nama_biaya, 'aktif' => $b->aktif]) }}"
                                                type="button"
                                                class="px-2.5 py-1 rounded-lg {{ $b->aktif ? 'border border-amber-200 text-amber-700 hover:bg-amber-50' : 'border border-emerald-200 text-emerald-700 hover:bg-emerald-50' }} text-xs font-semibold transition-colors cursor-pointer">
                                            {{ $b->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada master tarif biaya</p>
                                    <p class="text-xs text-slate-400 mt-1">Tambahkan komponen biaya pendidikan baru sekarang.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================================================================== --}}
        {{-- Modal Tambah Komponen Biaya --}}
        {{-- ================================================================== --}}
        <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="modalTambah = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-slate-800">Tambah Komponen Biaya Baru</h3>
                        <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form method="POST" action="{{ route('bendahara.master-biaya.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Biaya <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode_biaya" required placeholder="Contoh: DSP-2026, SPP-JUL"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 uppercase font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Komponen Biaya <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_biaya" required placeholder="Contoh: Dana Sumbangan Pendidikan (DSP)"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                                <select name="kategori" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                                    <option value="DSP">DSP / Uang Pangkal</option>
                                    <option value="SPP">SPP Bulanan</option>
                                    <option value="SERAGAM">Seragam & Atribut</option>
                                    <option value="KEGIATAN">Kegiatan & MPLS</option>
                                    <option value="PRAKTIK">Praktik Jurusan</option>
                                    <option value="LAINNYA">Lain-lain</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                                <input type="number" name="nominal" required min="0" step="1000" placeholder="0"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-bold">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Program (Opsional)</label>
                                <select name="program_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                                    <option value="">— Semua Program —</option>
                                    @foreach ($programList as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Gelombang (Opsional)</label>
                                <select name="gelombang_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                                    <option value="">— Semua Gelombang —</option>
                                    @foreach ($gelombangList as $gel)
                                        <option value="{{ $gel->id }}">{{ $gel->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" id="wajib_tambah" name="wajib" value="1" checked class="rounded text-nampi-orange focus:ring-nampi-orange">
                            <label for="wajib_tambah" class="text-xs font-semibold text-slate-700 cursor-pointer">Komponen Wajib (Otomatis masuk tagihan baku)</label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Catatan Tambahan</label>
                            <textarea name="keterangan" rows="2" placeholder="Keterangan singkat peruntukan biaya..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button @click="modalTambah = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                                Simpan Komponen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================== --}}
        {{-- Modal Edit Komponen Biaya --}}
        {{-- ================================================================== --}}
        <div x-show="editItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="editItem = null" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-slate-800">Ubah Komponen Biaya</h3>
                        <button @click="editItem = null" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form :action="'{{ url('bendahara/master-biaya') }}/' + editItem?.id" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Biaya</label>
                            <input type="text" :value="editItem?.kode_biaya" disabled
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-100 font-mono text-slate-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Komponen Biaya <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_biaya" :value="editItem?.nama_biaya" required
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                                <select name="kategori" required
                                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"
                                        x-init="$watch('editItem', v => { if (v) $el.value = v.kategori ?? '' })">
                                    <option value="DSP">DSP / Uang Pangkal</option>
                                    <option value="SPP">SPP Bulanan</option>
                                    <option value="SERAGAM">Seragam & Atribut</option>
                                    <option value="KEGIATAN">Kegiatan & MPLS</option>
                                    <option value="PRAKTIK">Praktik Jurusan</option>
                                    <option value="LAINNYA">Lain-lain</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Tarif (Rp) <span class="text-rose-500">*</span></label>
                                <input type="number" name="nominal" :value="editItem?.nominal" required min="0" step="1000"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-bold">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Program (Opsional)</label>
                                <select name="program_id"
                                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white"
                                        x-init="$watch('editItem', v => { if (v) $el.value = v.program_id ?? '' })">
                                    <option value="">— Semua Program —</option>
                                    @foreach ($programList as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Gelombang (Opsional)</label>
                                <select name="gelombang_id"
                                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white"
                                        x-init="$watch('editItem', v => { if (v) $el.value = v.gelombang_id ?? '' })">
                                    <option value="">— Semua Gelombang —</option>
                                    @foreach ($gelombangList as $gel)
                                        <option value="{{ $gel->id }}">{{ $gel->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                      x-effect="$el.value = editItem?.keterangan ?? ''"
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button @click="editItem = null" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                                Perbarui Tarif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================== --}}
        {{-- Modal Konfirmasi Toggle --}}
        {{-- ================================================================== --}}
        <div x-show="confirmToggle" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="confirmToggle = null" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                             :class="confirmToggle?.aktif ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Konfirmasi Perubahan Status</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span x-text="confirmToggle?.aktif ? 'Nonaktifkan' : 'Aktifkan'"></span>
                                komponen biaya ini?
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs font-bold text-slate-800" x-text="confirmToggle?.nama_biaya"></p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Status akan berubah menjadi
                            <strong x-text="confirmToggle?.aktif ? 'NONAKTIF' : 'AKTIF'"></strong>
                        </p>
                    </div>

                    <p class="text-xs text-slate-500">
                        Perubahan ini hanya berlaku untuk tagihan yang diterbitkan setelah perubahan. Tagihan yang sudah ada tidak akan terpengaruh.
                    </p>

                    {{-- Hidden form yang akan di-submit --}}
                    <form :action="'{{ url('bendahara/master-biaya') }}/' + confirmToggle?.id + '/toggle'" method="POST" x-ref="toggleForm">
                        @csrf
                        @method('PATCH')
                    </form>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button @click="confirmToggle = null" type="button"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                            Batal
                        </button>
                        <button @click="$refs.toggleForm.submit()" type="button"
                                :class="confirmToggle?.aktif
                                    ? 'bg-amber-500 hover:bg-amber-600 text-white'
                                    : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
                                class="px-5 py-2 rounded-xl text-xs font-bold transition-colors shadow-xs">
                            Ya, <span x-text="confirmToggle?.aktif ? 'Nonaktifkan' : 'Aktifkan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
