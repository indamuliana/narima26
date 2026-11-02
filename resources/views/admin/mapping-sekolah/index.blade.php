<x-layouts.app>
    <x-slot name="title">Mapping Sekolah Asal (Feeder Schools) — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div x-data="mappingSekolahApp()" class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Mapping Sekolah Asal (Feeder Schools)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Sinkronkan input sekolah manual calon murid ke database Master Sekolah Asal agar data Kab/Kota akurat</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openAutoMatchModal()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition-colors shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Auto-Match Otomatis</span>
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

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Calon Murid</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalCalon) }}</p>
                <span class="text-[11px] text-slate-400">Calon murid terdaftar</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-xs bg-emerald-50/20">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Tertaut ke Master</span>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($tertautCount) }}</p>
                <span class="text-[11px] text-emerald-600 font-medium">
                    {{ $totalCalon > 0 ? round(($tertautCount / $totalCalon) * 100, 1) : 0 }}% dari total murid
                </span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-xs bg-amber-50/20">
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Belum Tertaut (Manual)</span>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($manualCount) }}</p>
                <span class="text-[11px] text-amber-600 font-medium">Perlu dipetakan ke Master</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama Input Unik</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ count($unmappedSchools) }}</p>
                <span class="text-[11px] text-slate-400">Variasi penulisan manual</span>
            </div>
        </div>

        <!-- Filter & Tabs Navigation -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <!-- Tabs -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.mapping-sekolah.index', ['tab' => 'unmapped']) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'unmapped' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Belum Dipetakan ({{ count($unmappedSchools) }})
                    </a>
                    <a href="{{ route('admin.mapping-sekolah.index', ['tab' => 'mapped']) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'mapped' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Sudah Tertaut Master
                    </a>
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('admin.mapping-sekolah.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Cari nama sekolah / kota..."
                               class="w-64 pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-orange-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition-colors">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('admin.mapping-sekolah.index', ['tab' => $tab]) }}" class="px-2.5 py-2 text-slate-400 hover:text-slate-600 text-xs">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tab 1: Unmapped Schools -->
            @if ($tab === 'unmapped')
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Input Manual Siswa</th>
                                <th class="px-4 py-3 text-center w-24">Jumlah Murid</th>
                                <th class="px-4 py-3">Saran Kecocokan di Master</th>
                                <th class="px-4 py-3 text-right">Aksi Penautan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($unmappedSchools as $idx => $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-4 py-3.5 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-bold text-slate-900 text-sm">{{ $item['nama_manual'] }}</p>
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Status: Belum Tertaut Master
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-block px-2.5 py-1 bg-slate-100 rounded-lg font-black text-slate-800 text-xs">
                                            {{ $item['total_siswa'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if (!empty($item['suggestion']))
                                            <div class="p-2.5 rounded-xl bg-indigo-50/70 border border-indigo-100">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-indigo-900 text-xs">
                                                        {{ $item['suggestion']['school']->nama_sekolah }}
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item['suggestion']['score'] >= 85 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                        {{ $item['suggestion']['type'] }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-indigo-700 mt-1">
                                                    Wilayah: <strong>{{ $item['suggestion']['school']->kokab }}</strong>
                                                    @if($item['suggestion']['school']->kecamatan)
                                                        ({{ $item['suggestion']['school']->kecamatan }})
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Belum ada saran otomatis</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                            @if (!empty($item['suggestion']))
                                                <form method="POST" action="{{ route('admin.mapping-sekolah.link-single') }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="raw_name" value="{{ $item['nama_manual'] }}">
                                                    <input type="hidden" name="master_id" value="{{ $item['suggestion']['school']->id }}">
                                                    <button type="submit"
                                                            title="Tautkan langsung ke saran di atas"
                                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition-all shadow-xs cursor-pointer">
                                                        ✓ Tautkan Saran
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button"
                                                    @click="openManualLinkModal('{{ addslashes($item['nama_manual']) }}')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-all cursor-pointer">
                                                Cari di Master
                                            </button>

                                            <button type="button"
                                                    @click="openAddMasterModal('{{ addslashes($item['nama_manual']) }}')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 text-[11px] font-bold transition-all cursor-pointer">
                                                + Master Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto text-emerald-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="font-bold text-slate-700 text-sm">Semua Sekolah Sudah Tertaut!</p>
                                        <p class="text-xs text-slate-500 mt-1">Tidak ada data sekolah calon murid yang masih berstatus input manual tanpa master.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Tab 2: Mapped Schools -->
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Sekolah Master</th>
                                <th class="px-4 py-3">Kab/Kota & Kecamatan</th>
                                <th class="px-4 py-3">Input Asli Calon Siswa</th>
                                <th class="px-4 py-3 text-center w-24">Jumlah Murid</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($mappedSchools as $idx => $m)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-4 py-3.5 text-center text-slate-400 font-mono">{{ $mappedSchools->firstItem() + $idx }}</td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-bold text-slate-900 text-sm">{{ $m->nama_sekolah }}</p>
                                        <span class="text-[10px] text-slate-400">ID Master: #{{ $m->master_id }} • Jenis: {{ $m->jenis ?? 'SMP' }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-semibold text-slate-800">{{ $m->kokab ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $m->kecamatan ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <code class="px-2 py-1 bg-slate-100 rounded text-slate-700 text-[11px]">
                                            {{ $m->legacy_input ?: $m->nama_sekolah }}
                                        </code>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-lg font-black text-xs">
                                            {{ $m->total_siswa }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <form method="POST" action="{{ route('admin.mapping-sekolah.unlink') }}"
                                              onsubmit="return confirm('Lepas tautan sekolah ini? Calon murid akan kembali berstatus input manual.')"
                                              class="inline">
                                            @csrf
                                            <input type="hidden" name="master_id" value="{{ $m->master_id }}">
                                            <input type="hidden" name="raw_name" value="{{ $m->legacy_input }}">
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold hover:underline cursor-pointer">
                                                Lepas Tautan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        Belum ada data sekolah yang tertaut.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $mappedSchools->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 1: PREVIEW AUTO-MATCH MASSAL                       -->
        <!-- ======================================================== -->
        <div x-show="showAutoMatchModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:block sm:p-0">
                <div x-show="showAutoMatchModal"
                     @click="showAutoMatchModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="showAutoMatchModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.stop
                     class="relative inline-block w-full max-w-4xl bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-slate-200 my-8">
                    
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                        <div>
                            <h3 class="text-base font-black text-slate-900">Preview Konfirmasi Auto-Match</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tinjau usulan kecocokan sebelum menautkannya ke database</p>
                        </div>
                        <button @click="showAutoMatchModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.mapping-sekolah.apply-auto-match') }}">
                        @csrf
                        <div class="p-6 max-h-[60vh] overflow-y-auto">
                            <!-- Loading indicator -->
                            <div x-show="loadingAutoMatch" class="py-12 text-center">
                                <div class="inline-block animate-spin w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full mb-3"></div>
                                <p class="text-xs font-semibold text-slate-600">Sedang mencocokkan input siswa dengan 11.658 database master sekolah...</p>
                            </div>

                            <!-- Preview Table -->
                            <div x-show="!loadingAutoMatch">
                                <div class="flex items-center justify-between mb-3 text-xs">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="toggleSelectAll(true)" class="text-indigo-600 hover:underline font-bold">Pilih Semua Rekomendasi</button>
                                        <span>•</span>
                                        <button type="button" @click="toggleSelectAll(false)" class="text-slate-500 hover:underline">Batal Semua</button>
                                    </div>
                                    <span class="text-slate-500">
                                        Terpilih: <strong class="text-indigo-600" x-text="selectedMatchesCount"></strong> dari <span x-text="autoMatchList.length"></span> sekolah
                                    </span>
                                </div>

                                <table class="w-full text-left text-xs text-slate-700">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-10">Pilih</th>
                                            <th class="px-3 py-2">Input Siswa</th>
                                            <th class="px-3 py-2">Rekomendasi Master</th>
                                            <th class="px-3 py-2">Kab/Kota Terdeteksi</th>
                                            <th class="px-3 py-2 text-center">Tingkat Kecocokan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <template x-for="(item, index) in autoMatchList" :key="index">
                                            <tr :class="{'bg-indigo-50/30': item.selected && item.master_id, 'opacity-50': !item.master_id}">
                                                <td class="px-3 py-3 text-center">
                                                    <input type="checkbox"
                                                           x-model="item.selected"
                                                           :disabled="!item.master_id"
                                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                    <!-- Hidden input for form submission -->
                                                    <template x-if="item.selected && item.master_id">
                                                        <div>
                                                            <input type="hidden" :name="'matches[' + index + '][raw_name]'" :value="item.nama_manual">
                                                            <input type="hidden" :name="'matches[' + index + '][master_id]'" :value="item.master_id">
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="px-3 py-3 font-semibold text-slate-900" x-text="item.nama_manual"></td>
                                                <td class="px-3 py-3">
                                                    <template x-if="item.master_id">
                                                        <div>
                                                            <span class="font-bold text-slate-800" x-text="item.master_nama"></span>
                                                            <span class="block text-[10px] text-slate-400" x-text="'ID #' + item.master_id"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.master_id">
                                                        <span class="text-rose-500 italic">Tidak ditemukan kecocokan</span>
                                                    </template>
                                                </td>
                                                <td class="px-3 py-3 font-medium text-slate-600" x-text="item.master_kokab"></td>
                                                <td class="px-3 py-3 text-center">
                                                    <template x-if="item.master_id">
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                              :class="item.score >= 85 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                                              x-text="item.type"></span>
                                                    </template>
                                                    <template x-if="!item.master_id">
                                                        <span class="text-slate-400">-</span>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="px-6 py-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                            <button @click="showAutoMatchModal = false" type="button"
                                    class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-all cursor-pointer">
                                Tutup
                            </button>
                            <button type="submit"
                                    :disabled="selectedMatchesCount === 0 || loadingAutoMatch"
                                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                                Terapkan <span x-text="selectedMatchesCount"></span> Penautan Terpilih
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 2: CARI & TAUTKAN MANUAL                           -->
        <!-- ======================================================== -->
        <div x-show="showManualModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:block sm:p-0">
                <div @click="showManualModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div @click.stop class="relative inline-block w-full max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-slate-200 my-8">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                        <div>
                            <h3 class="text-base font-black text-slate-900">Tautkan ke Master Sekolah</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Pilih sekolah dari database master yang sesuai</p>
                        </div>
                        <button @click="showManualModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">✕</button>
                    </div>

                    <form method="POST" action="{{ route('admin.mapping-sekolah.link-single') }}" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="raw_name" :value="currentRawName">

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase">Input Siswa Saat Ini</label>
                            <div class="mt-1 p-3 bg-slate-100 rounded-xl font-bold text-slate-900 text-sm" x-text="currentRawName"></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Cari Sekolah Master</label>
                            <div class="relative mt-1">
                                <input type="text"
                                       x-model="searchMasterQuery"
                                       @input.debounce.300ms="searchMasterSchools()"
                                       placeholder="Ketik minimal 3 huruf nama sekolah atau NPSN..."
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-600 outline-none">
                                <div x-show="searchingMaster" class="absolute right-3 top-2.5">
                                    <div class="w-4 h-4 border-2 border-indigo-600 border-t-transparent animate-spin rounded-full"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Results Dropdown List -->
                        <div x-show="masterResults.length > 0" class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl divide-y divide-slate-100">
                            <template x-for="sch in masterResults" :key="sch.id">
                                <div @click="selectMasterSchool(sch)"
                                     class="p-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between text-xs transition-colors"
                                     :class="{'bg-indigo-50 font-bold': selectedMasterId === sch.id}">
                                    <div>
                                        <p class="text-slate-900 font-bold" x-text="sch.nama_sekolah"></p>
                                        <p class="text-[11px] text-slate-500" x-text="(sch.kokab || 'Tanpa Kota') + (sch.npsn ? ' • NPSN: ' + sch.npsn : '')"></p>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-mono" x-text="'#' + sch.id"></span>
                                </div>
                            </template>
                        </div>

                        <div x-show="selectedMasterSchool" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800">
                            <p class="font-bold">Sekolah Master Terpilih:</p>
                            <p class="text-sm font-black mt-0.5" x-text="selectedMasterSchool?.nama_sekolah"></p>
                            <p class="text-[11px] text-emerald-700" x-text="'Kab/Kota: ' + (selectedMasterSchool?.kokab || '-')"></p>
                        </div>

                        <input type="hidden" name="master_id" :value="selectedMasterId">

                        <div class="pt-3 flex items-center justify-end gap-2">
                            <button @click="showManualModal = false" type="button" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl text-xs font-bold text-slate-700 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="!selectedMasterId"
                                    class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold cursor-pointer">
                                Tautkan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 3: TAMBAH KE MASTER BARU                           -->
        <!-- ======================================================== -->
        <div x-show="showAddMasterModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:block sm:p-0">
                <div @click="showAddMasterModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

                <div @click.stop class="relative inline-block w-full max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-slate-200 my-8">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                        <div>
                            <h3 class="text-base font-black text-slate-900">Tambah ke Master Sekolah Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan sekolah ini ke database master dengan Kab/Kota yang benar</p>
                        </div>
                        <button @click="showAddMasterModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">✕</button>
                    </div>

                    <form method="POST" action="{{ route('admin.mapping-sekolah.store-master-and-link') }}" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="raw_name" :value="currentRawName">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Nama Sekolah Master <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_sekolah" x-model="newMasterName" required
                                   class="mt-1 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            <span class="text-[10px] text-slate-400">Nama resmi SMP / MTs / Pesantren</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Kabupaten / Kota <span class="text-rose-500">*</span></label>
                                <input type="text" name="kokab" required placeholder="Contoh: Kab. Garut / Kab. Kuningan"
                                       class="mt-1 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-1 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Kecamatan</label>
                                <input type="text" name="kecamatan" placeholder="Contoh: Tarogong Kidul"
                                       class="mt-1 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-1 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Jenis Jenjang</label>
                                <select name="jenis" class="mt-1 w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white focus:ring-1 focus:ring-orange-500 outline-none">
                                    <option value="SMP">SMP</option>
                                    <option value="MTS">MTS</option>
                                    <option value="PKBM">PKBM / Pondok</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Status</label>
                                <select name="status" class="mt-1 w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white focus:ring-1 focus:ring-orange-500 outline-none">
                                    <option value="Swasta">Swasta</option>
                                    <option value="Negeri">Negeri</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="provinsi" value="Jawa Barat">

                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                            Sekolah baru akan tersimpan permanen di master dan semua calon murid dengan nama input <strong x-text="currentRawName"></strong> akan otomatis tertaut ke sekolah ini.
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2">
                            <button @click="showAddMasterModal = false" type="button" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl text-xs font-bold text-slate-700 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold cursor-pointer">
                                Simpan ke Master & Tautkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function mappingSekolahApp() {
            return {
                showAutoMatchModal: false,
                loadingAutoMatch: false,
                autoMatchList: [],

                showManualModal: false,
                currentRawName: '',
                searchMasterQuery: '',
                searchingMaster: false,
                masterResults: [],
                selectedMasterId: null,
                selectedMasterSchool: null,

                showAddMasterModal: false,
                newMasterName: '',

                get selectedMatchesCount() {
                    return this.autoMatchList.filter(item => item.selected && item.master_id).length;
                },

                toggleSelectAll(state) {
                    this.autoMatchList.forEach(item => {
                        if (item.master_id) item.selected = state;
                    });
                },

                openAutoMatchModal() {
                    this.showAutoMatchModal = true;
                    this.loadingAutoMatch = true;
                    fetch('{{ route('admin.mapping-sekolah.preview-auto-match') }}')
                        .then(res => res.json())
                        .then(data => {
                            this.autoMatchList = data.data || [];
                            this.loadingAutoMatch = false;
                        })
                        .catch(() => {
                            this.loadingAutoMatch = false;
                            alert('Gagal memuat rekomendasi auto-match.');
                        });
                },

                openManualLinkModal(rawName) {
                    this.currentRawName = rawName;
                    this.searchMasterQuery = rawName;
                    this.selectedMasterId = null;
                    this.selectedMasterSchool = null;
                    this.masterResults = [];
                    this.showManualModal = true;
                    this.searchMasterSchools();
                },

                searchMasterSchools() {
                    const q = this.searchMasterQuery.trim();
                    if (q.length < 2) {
                        this.masterResults = [];
                        return;
                    }
                    this.searchingMaster = true;
                    fetch('{{ route('referensi.sekolah') }}?q=' + encodeURIComponent(q))
                        .then(res => res.json())
                        .then(data => {
                            this.masterResults = data || [];
                            this.searchingMaster = false;
                        })
                        .catch(() => {
                            this.searchingMaster = false;
                        });
                },

                selectMasterSchool(sch) {
                    this.selectedMasterId = sch.id;
                    this.selectedMasterSchool = sch;
                },

                openAddMasterModal(rawName) {
                    this.currentRawName = rawName;
                    this.newMasterName = rawName;
                    this.showAddMasterModal = true;
                }
            };
        }
    </script>
    @endpush
</x-layouts.app>
