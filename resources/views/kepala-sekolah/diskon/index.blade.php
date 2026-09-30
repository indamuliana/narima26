<x-layouts.app>
    <x-slot name="title">Kelola Diskon & Kebijakan Beasiswa — Kepala Sekolah</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6" x-data="kepsekDiskonHandler('{{ $tab }}', @js($siswaTagihanOptions), @js($masterDiskonList))">

        {{-- Header Eksekutif --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        Otoritas Kepala Sekolah
                    </span>
                </div>
                <h1 class="text-2xl font-black text-slate-800 mt-1">Diskon & Kebijakan Keringanan Biaya</h1>
                <p class="text-xs text-slate-500 mt-1">Pengawasan dan penetapan beasiswa, potongan biaya pendidikan, serta regulasi diskon SPMB.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button @click="openModalBeriDiskon()" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                    <span>🏷️ Berikan Diskon Murid</span>
                </button>
                <button @click="openModalTambahMaster()" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs cursor-pointer">
                    <span>➕ Tambah Kebijakan Baru</span>
                </button>
            </div>
        </div>

        {{-- Flash Messages --}}
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

        {{-- Kartu Ringkasan Metrik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Penerima Diskon</span>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_discounts']) }} Murid</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">🏷️</div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Total murid yang memperoleh potongan</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Keringanan</span>
                        <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">💰</div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Akumulasi subsidi pembiayaan</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kebijakan Aktif</span>
                        <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['total_master']) }} Program</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">📜</div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Template diskon baku resmi</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Program Terbanyak</span>
                @if ($stats['breakdown']->isEmpty())
                    <p class="text-xs text-slate-400 mt-3">Belum ada data diskon</p>
                @else
                    <div class="space-y-1.5">
                        @foreach ($stats['breakdown']->take(2) as $b)
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-700 truncate max-w-[120px]" title="{{ $b->jenis_diskon }}">{{ $b->jenis_diskon }}</span>
                                <span class="font-bold text-emerald-600">{{ $b->jumlah }}×</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab Navigasi --}}
        <div class="flex items-center border-b border-slate-200 gap-2">
            <button @click="activeTab = 'siswa'" type="button"
                    :class="activeTab === 'siswa' ? 'border-nampi-orange text-nampi-orange font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="pb-3 px-4 border-b-2 text-sm transition-colors cursor-pointer flex items-center gap-2">
                <span>📋 Calon Murid Penerima Diskon</span>
                <span class="px-2 py-0.5 rounded-full text-xs"
                      :class="activeTab === 'siswa' ? 'bg-orange-100 text-orange-800 font-black' : 'bg-slate-100 text-slate-600'">
                    {{ $stats['total_discounts'] }}
                </span>
            </button>
            <button @click="activeTab = 'master'" type="button"
                    :class="activeTab === 'master' ? 'border-nampi-orange text-nampi-orange font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="pb-3 px-4 border-b-2 text-sm transition-colors cursor-pointer flex items-center gap-2">
                <span>⚙️ Master Kebijakan Diskon (Baku)</span>
                <span class="px-2 py-0.5 rounded-full text-xs"
                      :class="activeTab === 'master' ? 'bg-orange-100 text-orange-800 font-black' : 'bg-slate-100 text-slate-600'">
                    {{ $stats['total_master'] }}
                </span>
            </button>
        </div>

        {{-- ================================================================= --}}
        {{-- TAB 1: CALON SISWA PENERIMA DISKON                                --}}
        {{-- ================================================================= --}}
        <div x-show="activeTab === 'siswa'" class="space-y-6">

            {{-- Filter Bar --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <form method="GET" action="{{ route('kepala-sekolah.diskon.index') }}" class="flex flex-wrap gap-3">
                    <input type="hidden" name="tab" value="siswa">

                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Cari nama siswa, nomor pendaftaran, alasan..."
                               class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                    </div>

                    <div>
                        <select name="jenis_diskon" class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                            <option value="">Semua Program Diskon</option>
                            @foreach ($jenisDiskonList as $jd)
                                <option value="{{ $jd }}" {{ $jenisDiskon === $jd ? 'selected' : '' }}>{{ $jd }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="jurusan_id" class="px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                            <option value="">Semua Jurusan</option>
                            @foreach ($jurusanList as $j)
                                <option value="{{ $j->id }}" {{ $jurusanId == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                            Filter
                        </button>
                        @if ($search || $jenisDiskon || $jurusanId || $tanggalDari || $tanggalSampai)
                            <a href="{{ route('kepala-sekolah.diskon.index', ['tab' => 'siswa']) }}"
                               class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Diskon Table --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Calon Murid</th>
                                <th class="px-4 py-3.5">Program Diskon / Beasiswa</th>
                                <th class="px-4 py-3.5">Metode & Nilai</th>
                                <th class="px-4 py-3.5 text-right">Potongan</th>
                                <th class="px-4 py-3.5">Tagihan Terkait</th>
                                <th class="px-4 py-3.5">Pemberi & Persetujuan</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($diskonList as $d)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-900">{{ $d->calonSiswa?->nama_lengkap ?? '—' }}</p>
                                        <p class="text-xs text-slate-400 font-mono">{{ $d->calonSiswa?->nomor_pendaftaran ?? '—' }}</p>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            @if ($d->calonSiswa?->jurusan)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                                                    {{ $d->calonSiswa->jurusan->nama }}
                                                </span>
                                            @endif
                                            @if ($d->calonSiswa?->program)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                    {{ $d->calonSiswa->program->nama }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-slate-800">{{ $d->jenis_diskon }}</span>
                                        @if ($d->alasan)
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $d->alasan }}">
                                                {{ $d->alasan }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($d->metode_diskon === 'persentase')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                {{ (float) $d->nilai_diskon }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Nominal Tetap
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-black text-emerald-600 block text-sm">
                                            - Rp {{ number_format($d->nominal_potongan, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-xs">
                                        @php
                                            $tagihan = $d->calonSiswa?->tagihan->firstWhere('diskon_id', $d->id);
                                        @endphp
                                        @if ($tagihan)
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    {{ $tagihan->jenis_tagihan === \App\Models\Tagihan::JENIS_DAFTAR_ULANG ? 'Daftar Ulang' : ($tagihan->jenis_tagihan === \App\Models\Tagihan::JENIS_SERAGAM ? 'Seragam' : $tagihan->jenis_tagihan) }}
                                                </span>
                                                <p class="font-mono font-bold text-slate-700">#{{ $tagihan->nomor_tagihan }}</p>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold
                                                    {{ $tagihan->status === 'LUNAS' ? 'bg-emerald-100 text-emerald-800' : ($tagihan->status === 'CICILAN' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                                    {{ $tagihan->status }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-500">
                                        <p class="font-semibold text-slate-700">{{ $d->disetujuiOleh?->name ?? $d->diberikanOleh?->name ?? 'Panitia SPMB' }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $d->created_at?->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="showDetail({{ json_encode($d) }})" type="button"
                                                    class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors cursor-pointer">
                                                Detail
                                            </button>
                                            @if (! $tagihan || $tagihan->status !== 'LUNAS')
                                                <button @click="confirmRevoke({{ json_encode(['id' => $d->id, 'nama' => $d->calonSiswa?->nama_lengkap, 'jenis' => $d->jenis_diskon, 'nominal' => (float)$d->nominal_potongan]) }})"
                                                        type="button"
                                                        class="px-2.5 py-1 rounded-lg border border-rose-200 text-rose-700 text-xs font-semibold hover:bg-rose-50 transition-colors cursor-pointer">
                                                    Cabut
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                        <p class="text-base font-bold text-slate-600">Belum ada diskon yang tercatat</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Berikan Diskon Murid" untuk menetapkan potongan biaya.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($diskonList->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $diskonList->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ================================================================= --}}
        {{-- TAB 2: MASTER KEBIJAKAN DISKON (BAKU)                             --}}
        {{-- ================================================================= --}}
        <div x-show="activeTab === 'master'" class="space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Master Template Program Diskon</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar skema diskon baku yang dapat dipilih cepat oleh Bendahara maupun Kepala Sekolah.</p>
                    </div>
                    <button @click="openModalTambahMaster()" type="button"
                            class="px-3.5 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                        + Tambah Kebijakan Baru
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Nama Program Diskon</th>
                                <th class="px-4 py-3.5">Metode</th>
                                <th class="px-4 py-3.5">Nilai / Nominal</th>
                                <th class="px-4 py-3.5">Deskripsi</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($masterDiskonPaged as $m)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-5 py-4 font-bold text-slate-800">{{ $m->nama_diskon }}</td>
                                    <td class="px-4 py-4 uppercase text-xs font-semibold">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $m->metode_diskon === 'persentase' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ $m->metode_diskon }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-black text-emerald-700">
                                        {{ $m->metode_diskon === 'persentase' ? (float)$m->nilai_diskon . '%' : 'Rp ' . number_format($m->nilai_diskon, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-500">{{ $m->deskripsi ?? '—' }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $m->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $m->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button @click="editMaster({{ json_encode($m) }})" class="text-nampi-orange hover:underline text-xs font-bold mr-3 cursor-pointer">Edit</button>
                                        <button @click="confirmDeleteMaster({{ json_encode($m) }})" class="text-rose-600 hover:underline text-xs font-bold cursor-pointer">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                        Belum ada kebijakan diskon baku. Klik "Tambah Kebijakan Baru" untuk membuatnya.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($masterDiskonPaged->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $masterDiskonPaged->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ================================================================= --}}
        {{-- MODAL 1: BERIKAN DISKON SISWA                                     --}}
        {{-- ================================================================= --}}
        <div x-show="modalBeriDiskon" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center py-8">
                <div @click.away="modalBeriDiskon = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-base font-black text-slate-800">Pemberian Diskon / Beasiswa Murid</h3>
                            <p class="text-xs text-slate-400">Dapat diaplikasikan ke semua jenis tagihan dengan otorisasi Kepala Sekolah.</p>
                        </div>
                        <button @click="modalBeriDiskon = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">✕</button>
                    </div>

                    <form method="POST" action="{{ route('kepala-sekolah.diskon.store') }}" class="space-y-4">
                        @csrf

                        {{-- 1. Pilih Calon Siswa --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Calon Murid (Memiliki Tagihan Aktif) <span class="text-rose-500">*</span></label>
                            <select name="calon_siswa_id" x-model="selectedSiswaId" required @change="onSelectSiswa()"
                                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white font-medium">
                                <option value="">— Pilih Calon Murid —</option>
                                <template x-for="cs in siswaList" :key="cs.id">
                                    <option :value="cs.id" x-text="`${cs.nomor_pendaftaran} - ${cs.nama_lengkap} (${cs.jurusan}) [${cs.tagihans.length} Tagihan]`"></option>
                                </template>
                            </select>
                        </div>

                        {{-- 2. Pilih Jenis Tagihan (Semua Jenis Tagihan: Daftar Ulang, Seragam, dll) --}}
                        <div x-show="selectedSiswa && availableTagihans.length > 0">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Pilih Tagihan Yang Ingin Diberi Diskon <span class="text-rose-500">*</span>
                            </label>
                            <select name="tagihan_id" x-model="selectedTagihanId" required @change="onSelectTagihan()"
                                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white font-semibold text-slate-800">
                                <option value="">— Pilih Tagihan (Daftar Ulang, Seragam, dll.) —</option>
                                <template x-for="t in availableTagihans" :key="t.id">
                                    <option :value="t.id" x-text="t.label"></option>
                                </template>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Diskon dapat diaplikasikan ke semua jenis tagihan (Daftar Ulang, Seragam, dll).</p>
                        </div>

                        {{-- Info Tagihan Siswa Terpilih --}}
                        <div x-show="selectedTagihanId && selectedBruto > 0" class="p-3 bg-amber-50 rounded-xl border border-amber-200/70 text-xs text-amber-900 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-950">Detail Tagihan Terpilih:</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-200 text-amber-900" x-text="selectedJenisTagihan"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-amber-200/60 text-[11px]">
                                <div>
                                    <span class="text-amber-700 block">Nomor Tagihan:</span>
                                    <strong class="font-mono text-slate-800" x-text="'#' + selectedNomorTagihan"></strong>
                                </div>
                                <div>
                                    <span class="text-amber-700 block">Total Bruto Tagihan:</span>
                                    <strong class="text-slate-900" x-text="'Rp ' + formatRupiah(selectedBruto)"></strong>
                                </div>
                            </div>
                        </div>

                        {{-- Template Cepat Master Diskon --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Gunakan Skema Baku (Opsional)</label>
                            <select @change="applyMasterTemplate($event.target.value)"
                                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                                <option value="">— Input Kustom Manual —</option>
                                <template x-for="m in masterList" :key="m.id">
                                    <option :value="JSON.stringify({nama: m.nama_diskon, metode: m.metode_diskon, nilai: parseFloat(m.nilai_diskon), alasan: m.deskripsi})"
                                            x-text="`${m.nama_diskon} (${m.metode_diskon === 'persentase' ? parseFloat(m.nilai_diskon) + '%' : 'Rp ' + formatRupiah(m.nilai_diskon)})`">
                                    </option>
                                </template>
                            </select>
                        </div>

                        {{-- Nama / Jenis Diskon --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Diskon / Beasiswa <span class="text-rose-500">*</span></label>
                            <input type="text" name="jenis_diskon" x-model="formDiskon.jenis_diskon" required placeholder="Contoh: Beasiswa Prestasi Tahfidz, Keringanan Khusus Kepsek"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        </div>

                        {{-- Metode & Nilai --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Potongan <span class="text-rose-500">*</span></label>
                                <select name="metode_diskon" x-model="formDiskon.metode_diskon" required
                                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                                    <option value="nominal">Nominal Tetap (Rp)</option>
                                    <option value="persentase">Persentase (%)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    <span x-text="formDiskon.metode_diskon === 'persentase' ? 'Besaran Diskon (%)' : 'Besaran Diskon (Rp)'"></span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="nilai_diskon" x-model.number="formDiskon.nilai_diskon" required min="1"
                                       :max="formDiskon.metode_diskon === 'persentase' ? 100 : (selectedBruto || 999999999)"
                                       placeholder="0"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-bold">
                            </div>
                        </div>

                        {{-- Estimasi Potongan & Sisa Tagihan --}}
                        <div x-show="selectedBruto > 0 && formDiskon.nilai_diskon > 0" class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900 space-y-1">
                            <div class="flex justify-between items-center">
                                <span>Estimasi Potongan:</span>
                                <strong class="text-sm font-black text-emerald-700" x-text="'- Rp ' + formatRupiah(hitungPotongan())"></strong>
                            </div>
                            <div class="flex justify-between items-center text-[11px] pt-1 border-t border-emerald-200/60 text-emerald-800">
                                <span>Estimasi Tagihan Bersih (Netto):</span>
                                <strong class="font-bold" x-text="'Rp ' + formatRupiah(Math.max(0, selectedBruto - hitungPotongan()))"></strong>
                            </div>
                        </div>

                        {{-- Alasan & Keterangan --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pemberian Beasiswa / Diskon <span class="text-rose-500">*</span></label>
                            <textarea name="alasan" x-model="formDiskon.alasan" required rows="2" placeholder="Tuliskan justifikasi rekomendasi atau dasar kebijakan Kepala Sekolah..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button @click="modalBeriDiskon = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="!selectedTagihanId"
                                    class="px-5 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                Sahkan Diskon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL 2: DETAIL DISKON                                            --}}
        {{-- ================================================================= --}}
        <div x-show="detailItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center py-8">
                <div @click.away="detailItem = null" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-black text-slate-800">Rincian Keputusan Diskon</h3>
                        <button @click="detailItem = null" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 block">Penerima Diskon:</span>
                            <p class="font-bold text-slate-900 text-sm" x-text="detailItem?.calon_siswa?.nama_lengkap"></p>
                            <p class="font-mono text-slate-500" x-text="detailItem?.calon_siswa?.nomor_pendaftaran"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-xl">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Jenis Diskon:</span>
                                <p class="font-bold text-slate-800" x-text="detailItem?.jenis_diskon"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nominal Potongan:</span>
                                <p class="font-black text-emerald-600" x-text="'Rp ' + formatRupiah(detailItem?.nominal_potongan)"></p>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 block">Alasan / Dasar Keputusan:</span>
                            <p class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-700 italic mt-1" x-text="detailItem?.alasan || '—'"></p>
                        </div>

                        <template x-if="detailItem?.calon_siswa?.tagihan">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 block text-[11px]">Tagihan Terkait:</span>
                                <template x-for="t in detailItem.calon_siswa.tagihan" :key="t.id">
                                    <div x-show="t.diskon_id == detailItem.id" class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-indigo-700" x-text="(t.jenis_tagihan === 'DAFTAR_ULANG' ? 'Daftar Ulang' : (t.jenis_tagihan === 'SERAGAM' ? 'Seragam' : t.jenis_tagihan)) + ' (#' + t.nomor_tagihan + ')'"></span>
                                        <span class="font-bold text-slate-700" x-text="'Rp ' + formatRupiah(t.total_bruto)"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div>
                            <span class="text-slate-400 block">Ditetapkan / Disetujui Oleh:</span>
                            <p class="font-bold text-slate-800 mt-0.5" x-text="detailItem?.disetujui_oleh?.name || detailItem?.diberikan_oleh?.name || 'Kepala Sekolah'"></p>
                            <p class="text-[11px] text-slate-400" x-text="detailItem?.created_at ? new Date(detailItem.created_at).toLocaleString('id-ID') : '—'"></p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-3 border-t border-slate-100">
                        <button @click="detailItem = null" type="button" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL 3: TAMBAH / EDIT MASTER DISKON                              --}}
        {{-- ================================================================= --}}
        <div x-show="modalTambahMaster || editMasterItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center py-8">
                <div @click.away="modalTambahMaster = false; editMasterItem = null" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-black text-slate-800" x-text="editMasterItem ? 'Edit Kebijakan Master Diskon' : 'Tambah Kebijakan Master Diskon'"></h3>
                        <button @click="modalTambahMaster = false; editMasterItem = null" type="button" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">✕</button>
                    </div>

                    <form :action="editMasterItem ? '{{ route('kepala-sekolah.diskon.index') }}/master/' + editMasterItem.id : '{{ route('kepala-sekolah.diskon.master.store') }}'" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_method" :value="editMasterItem ? 'PUT' : 'POST'">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kebijakan Diskon <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_diskon" x-model="formMaster.nama_diskon" required placeholder="Contoh: Beasiswa Tahfidz 3 Juz"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode <span class="text-rose-500">*</span></label>
                                <select name="metode_diskon" x-model="formMaster.metode_diskon" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 bg-white">
                                    <option value="nominal">Nominal (Rp)</option>
                                    <option value="persentase">Persentase (%)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nilai / Angka <span class="text-rose-500">*</span></label>
                                <input type="number" name="nilai_diskon" x-model.number="formMaster.nilai_diskon" required min="0" placeholder="0"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Kebijakan</label>
                            <textarea name="deskripsi" rows="2" x-model="formMaster.deskripsi" placeholder="Kriteria dan syarat penerima beasiswa/diskon..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" id="is_active_master" name="is_active" value="1"
                                   x-model="formMaster.is_active"
                                   class="rounded text-nampi-orange focus:ring-nampi-orange cursor-pointer">
                            <label for="is_active_master" class="text-xs font-semibold text-slate-700 cursor-pointer">Status Aktif (Tersedia saat penagihan)</label>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button @click="modalTambahMaster = false; editMasterItem = null" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs cursor-pointer">
                                Simpan Kebijakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL 4: KONFIRMASI CABUT DISKON SISWA                            --}}
        {{-- ================================================================= --}}
        <div x-show="confirmRevokeItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="confirmRevokeItem = null" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Konfirmasi Cabut Diskon</h3>
                            <p class="text-xs text-slate-500">Batalkan pemberian diskon ini?</p>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                        <p class="font-bold text-slate-800" x-text="confirmRevokeItem?.nama"></p>
                        <p class="text-slate-600" x-text="confirmRevokeItem?.jenis"></p>
                        <p class="text-emerald-700 font-bold" x-text="'Nominal: Rp ' + formatRupiah(confirmRevokeItem?.nominal)"></p>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Tagihan murid akan dikembalikan ke nilai bruto awal dan kewajiban pembayaran akan disesuaikan kembali.
                    </p>

                    <form :action="`{{ route('kepala-sekolah.diskon.index') }}/${confirmRevokeItem?.id}`" method="POST" x-ref="formRevoke">
                        @csrf
                        @method('DELETE')
                    </form>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button @click="confirmRevokeItem = null" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                            Batal
                        </button>
                        <button @click="$refs.formRevoke.submit()" type="button" class="px-5 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors shadow-xs">
                            Ya, Cabut Diskon
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL 5: KONFIRMASI HAPUS MASTER DISKON                           --}}
        {{-- ================================================================= --}}
        <div x-show="confirmDeleteMasterItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="confirmDeleteMasterItem = null" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">🗑️</div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Hapus Kebijakan Diskon</h3>
                            <p class="text-xs text-slate-500">Hapus master kebijakan baku?</p>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl text-xs">
                        <p class="font-bold text-slate-800" x-text="confirmDeleteMasterItem?.nama_diskon"></p>
                    </div>

                    <form :action="`{{ route('kepala-sekolah.diskon.index') }}/master/${confirmDeleteMasterItem?.id}`" method="POST" x-ref="formDeleteMaster">
                        @csrf
                        @method('DELETE')
                    </form>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button @click="confirmDeleteMasterItem = null" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                            Batal
                        </button>
                        <button @click="$refs.formDeleteMaster.submit()" type="button" class="px-5 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors shadow-xs">
                            Hapus Kebijakan
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function kepsekDiskonHandler(initialTab, siswaData, masterData) {
            return {
                activeTab: initialTab || 'siswa',
                modalBeriDiskon: false,
                modalTambahMaster: false,
                editMasterItem: null,
                detailItem: null,
                confirmRevokeItem: null,
                confirmDeleteMasterItem: null,

                siswaList: siswaData || [],
                masterList: masterData || [],

                selectedSiswaId: '',
                selectedSiswa: null,
                availableTagihans: [],
                selectedTagihanId: '',
                selectedTagihan: null,
                selectedBruto: 0,
                selectedNomorTagihan: '',
                selectedJenisTagihan: '',
                selectedStatusTagihan: '',

                formDiskon: {
                    jenis_diskon: '',
                    metode_diskon: 'nominal',
                    nilai_diskon: '',
                    alasan: ''
                },

                formMaster: {
                    id: null,
                    nama_diskon: '',
                    metode_diskon: 'nominal',
                    nilai_diskon: '',
                    deskripsi: '',
                    is_active: true
                },

                openModalBeriDiskon() {
                    this.selectedSiswaId = '';
                    this.selectedSiswa = null;
                    this.availableTagihans = [];
                    this.selectedTagihanId = '';
                    this.selectedTagihan = null;
                    this.selectedBruto = 0;
                    this.selectedNomorTagihan = '';
                    this.selectedJenisTagihan = '';
                    this.selectedStatusTagihan = '';
                    this.formDiskon = {
                        jenis_diskon: '',
                        metode_diskon: 'nominal',
                        nilai_diskon: '',
                        alasan: ''
                    };
                    this.modalBeriDiskon = true;
                },

                onSelectSiswa() {
                    this.selectedTagihanId = '';
                    this.selectedTagihan = null;
                    this.selectedBruto = 0;
                    this.selectedNomorTagihan = '';
                    this.selectedJenisTagihan = '';
                    this.selectedStatusTagihan = '';

                    if (!this.selectedSiswaId) {
                        this.selectedSiswa = null;
                        this.availableTagihans = [];
                        return;
                    }

                    this.selectedSiswa = this.siswaList.find(s => s.id == this.selectedSiswaId) || null;
                    this.availableTagihans = this.selectedSiswa ? (this.selectedSiswa.tagihans || []) : [];

                    // Auto-select if candidate only has 1 active invoice
                    if (this.availableTagihans.length === 1) {
                        this.selectedTagihanId = this.availableTagihans[0].id;
                        this.onSelectTagihan();
                    }
                },

                onSelectTagihan() {
                    if (!this.selectedTagihanId) {
                        this.selectedTagihan = null;
                        this.selectedBruto = 0;
                        this.selectedNomorTagihan = '';
                        this.selectedJenisTagihan = '';
                        this.selectedStatusTagihan = '';
                        return;
                    }

                    this.selectedTagihan = this.availableTagihans.find(t => t.id == this.selectedTagihanId) || null;
                    if (this.selectedTagihan) {
                        this.selectedBruto = parseFloat(this.selectedTagihan.total_bruto || 0);
                        this.selectedNomorTagihan = this.selectedTagihan.nomor_tagihan || '';
                        this.selectedJenisTagihan = this.selectedTagihan.jenis_label || this.selectedTagihan.jenis_tagihan || '';
                        this.selectedStatusTagihan = this.selectedTagihan.status || '';
                    }
                },

                applyMasterTemplate(jsonStr) {
                    if (!jsonStr) return;
                    try {
                        const data = JSON.parse(jsonStr);
                        this.formDiskon.jenis_diskon = data.nama;
                        this.formDiskon.metode_diskon = data.metode;
                        this.formDiskon.nilai_diskon = data.nilai;
                        if (data.alasan && !this.formDiskon.alasan) {
                            this.formDiskon.alasan = data.alasan;
                        }
                    } catch (e) {
                        console.error('Failed to parse master template', e);
                    }
                },

                hitungPotongan() {
                    if (!this.selectedBruto || !this.formDiskon.nilai_diskon) return 0;
                    const nilai = parseFloat(this.formDiskon.nilai_diskon) || 0;
                    if (this.formDiskon.metode_diskon === 'persentase') {
                        const pct = Math.min(100, Math.max(0, nilai));
                        return Math.round((this.selectedBruto * pct) / 100);
                    } else {
                        return Math.min(this.selectedBruto, nilai);
                    }
                },

                formatRupiah(num) {
                    return Number(num || 0).toLocaleString('id-ID');
                },

                showDetail(item) {
                    this.detailItem = item;
                },

                confirmRevoke(item) {
                    this.confirmRevokeItem = item;
                },

                openModalTambahMaster() {
                    this.editMasterItem = null;
                    this.formMaster = {
                        id: null,
                        nama_diskon: '',
                        metode_diskon: 'nominal',
                        nilai_diskon: '',
                        deskripsi: '',
                        is_active: true
                    };
                    this.modalTambahMaster = true;
                },

                editMaster(item) {
                    this.editMasterItem = item;
                    this.formMaster = {
                        id: item.id,
                        nama_diskon: item.nama_diskon || '',
                        metode_diskon: item.metode_diskon || 'nominal',
                        nilai_diskon: parseFloat(item.nilai_diskon) || 0,
                        deskripsi: item.deskripsi || '',
                        is_active: !!item.is_active
                    };
                    this.modalTambahMaster = true;
                },

                confirmDeleteMaster(item) {
                    this.confirmDeleteMasterItem = item;
                }
            };
        }
    </script>
</x-layouts.app>
