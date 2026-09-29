<x-layouts.app>
    <x-slot name="title">Kelola Diskon & Keringanan</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6" x-data="diskonPageHandler()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Diskon & Keringanan Biaya</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar riwayat potongan biaya dan beasiswa yang telah disetujui untuk calon siswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="modalBeriDiskon = true; step = 1; selectedSiswa = null; filterSiswa = ''" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                    <span>🏷️ Berikan Diskon</span>
                </button>
                <a href="{{ route('bendahara.tagihan.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                    <span>Lihat Tagihan &rarr;</span>
                </a>
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

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Summary Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Diskon Diberikan</span>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_discounts']) }} Siswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-xl">🏷️</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Nominal Keringanan</span>
                        <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-xl">💰</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Breakdown Jenis Diskon</span>
                @if ($stats['breakdown']->isEmpty())
                    <p class="text-sm text-slate-400 mt-2">Belum ada data</p>
                @else
                    <div class="mt-2 space-y-1.5">
                        @foreach ($stats['breakdown'] as $b)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-slate-700 font-semibold truncate max-w-[140px]" title="{{ $b->jenis_diskon }}">{{ $b->jenis_diskon }}</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[10px] text-slate-400">{{ $b->jumlah }}×</span>
                                    <span class="text-xs font-bold text-emerald-600">Rp {{ number_format($b->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.diskon.index') }}" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari nama siswa, nomor pendaftaran, alasan..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>
                <div>
                    <select name="jenis_diskon" class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                        <option value="">Semua Jenis Diskon</option>
                        @foreach ($jenisDiskonList as $jd)
                            <option value="{{ $jd }}" {{ $jenisDiskon === $jd ? 'selected' : '' }}>{{ $jd }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="jurusan_id" class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ $jurusanId == $jur->id ? 'selected' : '' }}>{{ $jur->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}"
                           class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                    <span class="text-slate-400 text-xs">s/d</span>
                    <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}"
                           class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">Cari</button>
                    @if ($search || $jenisDiskon || $jurusanId || $tanggalDari || $tanggalSampai)
                        <a href="{{ route('bendahara.diskon.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">✕</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Riwayat Diskon --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Jenis Diskon</th>
                            <th class="px-4 py-3.5">Skema Potongan</th>
                            <th class="px-4 py-3.5 text-right">Nominal Potongan</th>
                            <th class="px-4 py-3.5">Tagihan Terkait</th>
                            <th class="px-4 py-3.5">Alasan / Catatan</th>
                            <th class="px-5 py-3.5">Diberikan Oleh</th>
                            <th class="px-4 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($diskonList as $d)
                            @php
                                $tagihanTerkait = $d->calonSiswa?->tagihan->firstWhere('diskon_id', $d->id);
                                $tagihanLunas = $tagihanTerkait && $tagihanTerkait->status === 'LUNAS';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $d->calonSiswa?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $d->calonSiswa?->nomor_pendaftaran }} • {{ $d->calonSiswa?->jurusan?->nama ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $d->jenis_diskon }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($d->metode_diskon === 'persentase')
                                        <span class="font-semibold text-slate-800">{{ $d->nilai_diskon }}%</span>
                                        <span class="text-xs text-slate-400 block">dari bruto tagihan</span>
                                    @else
                                        <span class="font-semibold text-slate-800">Nominal Tetap</span>
                                        <span class="text-xs text-slate-400 block">Rp {{ number_format($d->nilai_diskon, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-emerald-700 block">Rp {{ number_format($d->nominal_potongan, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($tagihanTerkait)
                                        <a href="{{ route('bendahara.tagihan.show', $tagihanTerkait) }}"
                                           class="text-xs font-mono font-semibold text-nampi-orange hover:underline">
                                            #{{ $tagihanTerkait->nomor_tagihan }}
                                        </a>
                                        <span class="text-[10px] block mt-0.5 {{ $tagihanTerkait->status === 'LUNAS' ? 'text-emerald-600 font-bold' : ($tagihanTerkait->status === 'CICILAN' ? 'text-amber-600 font-semibold' : 'text-slate-400') }}">
                                            {{ $tagihanTerkait->status }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 max-w-xs">
                                    <p class="text-xs text-slate-700 font-medium line-clamp-2">{{ $d->alasan }}</p>
                                    @if ($d->keterangan)
                                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 italic">{{ $d->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-xs font-semibold text-slate-800 block">{{ $d->diberikanOleh?->name ?? 'Sistem' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $d->created_at?->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if (! $tagihanLunas)
                                        <button @click="confirmHapus = {{ json_encode([
                                                    'id' => $d->id,
                                                    'jenis_diskon' => $d->jenis_diskon,
                                                    'nama_siswa' => $d->calonSiswa?->nama_lengkap ?? '—',
                                                    'nomor_tagihan' => $tagihanTerkait?->nomor_tagihan ?? ''
                                                ]) }}"
                                                type="button"
                                                class="px-2.5 py-1 rounded-lg border border-rose-200 text-rose-600 text-xs font-semibold hover:bg-rose-50 transition-colors cursor-pointer">
                                            Cabut
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada data diskon</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik "Berikan Diskon" untuk menambah keringanan biaya.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($diskonList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">{{ $diskonList->links() }}</div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- Modal Berikan Diskon — Step 1: Pilih Siswa | Step 2: Form    --}}
        {{-- ============================================================ --}}
        <div x-show="modalBeriDiskon" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 py-8 flex items-start justify-center">
                <div class="bg-white rounded-2xl w-full shadow-xl"
                     :class="step === 1 ? 'max-w-2xl' : 'max-w-lg'">

                    {{-- ---- STEP 1: Daftar Siswa ---- --}}
                    <div x-show="step === 1">
                        {{-- Header Step 1 --}}
                        <div class="flex items-center justify-between p-6 border-b border-slate-100">
                            <div>
                                <h3 class="text-base font-black text-slate-800">Pilih Siswa untuk Diberikan Diskon</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Hanya menampilkan siswa yang memiliki tagihan aktif dan belum mendapat diskon.
                                </p>
                            </div>
                            <button @click="tutupModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">✕</button>
                        </div>

                        {{-- Search filter lokal --}}
                        <div class="px-6 pt-4">
                            <input type="text" x-model="filterSiswa"
                                   placeholder="Ketik nama atau nomor pendaftaran untuk filter..."
                                   class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        </div>

                        {{-- Tabel Siswa --}}
                        <div class="p-6 overflow-y-auto" style="max-height: 460px;">
                            @if ($siswaWithTagihan->isEmpty())
                                <div class="text-center py-10 text-slate-400">
                                    <p class="font-bold text-slate-600">Tidak ada siswa yang memenuhi syarat</p>
                                    <p class="text-xs mt-1">Semua siswa sudah mendapat diskon atau belum memiliki tagihan aktif.</p>
                                </div>
                            @else
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                                        <tr>
                                            <th class="pb-3">Nama Siswa</th>
                                            <th class="pb-3">Jurusan</th>
                                            <th class="pb-3">No. Tagihan</th>
                                            <th class="pb-3 text-right">Total Tagihan</th>
                                            <th class="pb-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @foreach ($siswaWithTagihan as $cs)
                                            @php $tg = $cs->tagihan->first(); @endphp
                                            <tr class="hover:bg-slate-50 transition-colors"
                                                x-show="filterSiswa === '' ||
                                                         {{ json_encode(strtolower($cs->nama_lengkap)) }}.includes(filterSiswa.toLowerCase()) ||
                                                         {{ json_encode((string)$cs->nomor_pendaftaran) }}.includes(filterSiswa)">
                                                <td class="py-3 pr-4">
                                                    <p class="font-bold text-slate-900">{{ $cs->nama_lengkap }}</p>
                                                    <p class="text-[11px] text-slate-400 font-mono">{{ $cs->nomor_pendaftaran }}</p>
                                                </td>
                                                <td class="py-3 pr-4">
                                                    <span class="text-xs text-slate-600">{{ $cs->jurusan?->nama ?? '—' }}</span>
                                                </td>
                                                <td class="py-3 pr-4">
                                                    <span class="text-xs font-mono font-semibold text-slate-700">
                                                        {{ $tg ? '#'.$tg->nomor_tagihan : '—' }}
                                                    </span>
                                                    @if ($tg)
                                                        <span class="text-[10px] block {{ $tg->status === 'CICILAN' ? 'text-amber-600' : 'text-slate-400' }}">
                                                            {{ $tg->status }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 pr-4 text-right">
                                                    <span class="font-bold text-slate-900 text-xs">
                                                        Rp {{ $tg ? number_format($tg->total_bruto, 0, ',', '.') : '—' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 text-center">
                                                    <button type="button"
                                                            @click="pilihSiswa({{ json_encode([
                                                                'id' => $cs->id,
                                                                'nama_lengkap' => $cs->nama_lengkap,
                                                                'nomor_pendaftaran' => $cs->nomor_pendaftaran,
                                                                'jurusan' => $cs->jurusan?->nama ?? '—',
                                                                'nomor_tagihan' => $tg?->nomor_tagihan ?? '—',
                                                                'total_bruto' => $tg ? number_format($tg->total_bruto, 0, ',', '.') : '—'
                                                            ]) }})"
                                                            class="px-3 py-1.5 rounded-lg bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors cursor-pointer">
                                                        Pilih
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                            <button @click="tutupModal()" type="button"
                                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                                Batal
                            </button>
                        </div>
                    </div>

                    {{-- ---- STEP 2: Form Diskon ---- --}}
                    <div x-show="step === 2">
                        {{-- Header Step 2 --}}
                        <div class="flex items-center justify-between p-6 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <button @click="kembali()" type="button"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <div>
                                    <h3 class="text-base font-black text-slate-800">Masukkan Detail Diskon</h3>
                                    <p class="text-xs text-slate-500 mt-0.5" x-text="selectedSiswa?.nama_lengkap"></p>
                                </div>
                            </div>
                            <button @click="tutupModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">✕</button>
                        </div>

                        <div class="p-6">
                            {{-- Info Siswa Terpilih --}}
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 mb-4">
                                <p class="text-xs font-bold text-emerald-800">✅ Siswa dipilih:</p>
                                <p class="text-sm font-black text-slate-900 mt-1" x-text="selectedSiswa?.nama_lengkap"></p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">
                                    <span x-text="selectedSiswa?.nomor_pendaftaran"></span> •
                                    Tagihan <span x-text="'#' + selectedSiswa?.nomor_tagihan"></span> •
                                    Bruto: Rp <span x-text="selectedSiswa?.total_bruto"></span>
                                </p>
                            </div>

                            <form method="POST" action="{{ route('bendahara.diskon.store-from-index') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="calon_siswa_id" :value="selectedSiswa?.id ?? ''">

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Program Diskon <span class="text-rose-500">*</span></label>
                                    <select x-model="presetDiskon" @change="pilihPreset($event.target.value)" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                                        <option value="">-- Pilih Program Baku --</option>
                                        @foreach($masterDiskonList as $md)
                                            <option value="{{ $md->id }}">{{ $md->nama_diskon }} ({{ $md->metode_diskon === 'persentase' ? $md->nilai_diskon.'%' : 'Rp '.number_format($md->nilai_diskon,0,',','.') }})</option>
                                        @endforeach
                                        <option value="custom">Lainnya / Input Manual</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Diskon <span class="text-rose-500">*</span></label>
                                    <input type="text" name="jenis_diskon" x-model="jenisDiskon" required :readonly="presetDiskon !== 'custom'"
                                           placeholder="Contoh: Diskon Prestasi, Beasiswa Yatim, Diskon Saudara Kandung"
                                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"
                                           :class="presetDiskon !== 'custom' ? 'bg-slate-50' : 'bg-white'">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode <span class="text-rose-500">*</span></label>
                                        <select name="metode_diskon" x-model="metodeDiskon" required
                                                :class="presetDiskon !== 'custom' ? 'bg-slate-50 pointer-events-none' : 'bg-white'"
                                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                                            <option value="nominal">Nominal Tetap (Rp)</option>
                                            <option value="persentase">Persentase (%)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai <span class="text-rose-500">*</span></label>
                                        <input type="number" name="nilai_diskon" x-model="nilaiDiskon" required min="1" :readonly="presetDiskon !== 'custom'"
                                               placeholder="Contoh: 500000 atau 10"
                                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-bold"
                                               :class="presetDiskon !== 'custom' ? 'bg-slate-50' : 'bg-white'">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Alasan / Dasar Pemberian <span class="text-rose-500">*</span></label>
                                    <textarea name="alasan" required rows="2"
                                              placeholder="Contoh: Siswa berprestasi juara 1 OSN tingkat kabupaten..."
                                              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan</label>
                                    <textarea name="keterangan" rows="1"
                                              placeholder="Catatan internal..."
                                              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                                </div>

                                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                    <button @click="kembali()" type="button"
                                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                                        ← Kembali
                                    </button>
                                    <button type="submit"
                                            class="px-5 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                                        Terapkan Diskon
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Modal Konfirmasi Cabut Diskon                                --}}
        {{-- ============================================================ --}}
        <div x-show="confirmHapus" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="confirmHapus = null" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Cabut Diskon?</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tindakan ini akan menghapus diskon dan mengembalikan tagihan ke nilai semula.</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                        <p class="text-xs font-bold text-slate-700">Siswa: <span class="font-black text-slate-900" x-text="confirmHapus?.nama_siswa"></span></p>
                        <p class="text-xs text-slate-600">Jenis: <span class="font-semibold" x-text="confirmHapus?.jenis_diskon"></span></p>
                        <template x-if="confirmHapus?.nomor_tagihan">
                            <p class="text-xs text-slate-500">
                                Tagihan <span class="font-mono font-semibold" x-text="'#' + confirmHapus?.nomor_tagihan"></span> akan dikembalikan ke nilai penuh.
                            </p>
                        </template>
                    </div>

                    <form :action="'{{ url('bendahara/diskon') }}/' + confirmHapus?.id" method="POST" x-ref="hapusForm">
                        @csrf
                        @method('DELETE')
                    </form>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button @click="confirmHapus = null" type="button"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                            Batal
                        </button>
                        <button @click="$refs.hapusForm.submit()" type="button"
                                class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors shadow-xs cursor-pointer">
                            Ya, Cabut Diskon
                        </button>
                    </div>
                </div>
            </div>
    </div>

    <script>
        function diskonPageHandler() {
            const masterList = {!! json_encode($masterDiskonList) !!};
            return {
                modalBeriDiskon: false,
                step: 1,
                filterSiswa: '',
                selectedSiswa: null,

                pilihSiswa(siswa) {
                    this.selectedSiswa = siswa;
                    this.step = 2;
                },
                kembali() {
                    this.step = 1;
                    this.selectedSiswa = null;
                },
                confirmHapus: null,

                presetDiskon: 'custom',
                jenisDiskon: '',
                metodeDiskon: 'nominal',
                nilaiDiskon: '',

                pilihPreset(value) {
                    this.presetDiskon = value;
                    if (value === 'custom' || !value) {
                        this.jenisDiskon = '';
                        this.metodeDiskon = 'nominal';
                        this.nilaiDiskon = '';
                        return;
                    }
                    let diskon = masterList.find(item => item.id == value);
                    if (diskon) {
                        this.jenisDiskon = diskon.nama_diskon;
                        this.metodeDiskon = diskon.metode_diskon;
                        this.nilaiDiskon = diskon.nilai_diskon;
                    } else {
                        this.jenisDiskon = '';
                        this.metodeDiskon = 'nominal';
                        this.nilaiDiskon = '';
                    }
                },

                tutupModal() {
                    this.modalBeriDiskon = false;
                    this.step = 1;
                    this.selectedSiswa = null;
                    this.filterSiswa = '';
                    this.pilihPreset('custom');
                }
            };
        }
    </script>
</x-layouts.app>
