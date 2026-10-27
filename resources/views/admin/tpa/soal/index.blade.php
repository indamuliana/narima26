<x-layouts.app>
    <x-slot name="title">Bank Soal TPA — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Bank Soal Tes Potensi Akademik</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola butir soal pilihan ganda, bobot penilaian, gambar pendukung, dan kunci jawaban per babak</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Export CSV -->
                <a href="{{ route('admin.tpa.soal.export', ['babak' => $babak, 'format' => 'csv']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-xs"
                   title="Export bank soal babak {{ ucfirst($babak) }} ke format CSV">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Export CSV</span>
                </a>

                <!-- Tombol Import CSV -->
                <button type="button" onclick="document.getElementById('modalImportSoal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-xs cursor-pointer"
                        title="Import butir soal dari file CSV atau Excel ke babak {{ ucfirst($babak) }}">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Import CSV</span>
                </button>

                <a href="{{ route('admin.tpa.soal.create', ['babak' => $babak]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Soal {{ ucfirst($babak) }}</span>
                </a>
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

        @if (session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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

        <!-- Babak Switcher Tabs & Search Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl w-fit">
                <a href="{{ route('admin.tpa.soal.index', ['babak' => 'literasi']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $babak === 'literasi' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $babak === 'literasi' ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
                    <span>Babak 1: Literasi</span>
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $babak === 'literasi' ? 'bg-blue-50 text-blue-700' : 'bg-slate-200 text-slate-600' }}">{{ $countLiterasi }}</span>
                </a>
                <a href="{{ route('admin.tpa.soal.index', ['babak' => 'numerasi']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $babak === 'numerasi' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $babak === 'numerasi' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                    <span>Babak 2: Numerasi</span>
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $babak === 'numerasi' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $countNumerasi }}</span>
                </a>
            </div>

            <form action="{{ route('admin.tpa.soal.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="babak" value="{{ $babak }}">
                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari teks soal..."
                           class="w-64 pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none bg-white">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700 transition-colors cursor-pointer">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Bank Soal -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Pertanyaan & Opsi Jawaban</th>
                            <th class="py-3.5 px-4 w-28 text-center">Gambar</th>
                            <th class="py-3.5 px-4 w-24 text-center">Bobot</th>
                            <th class="py-3.5 px-4 w-28 text-center">Kunci</th>
                            <th class="py-3.5 px-4 w-24 text-center">Status</th>
                            <th class="py-3.5 px-4 w-32 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse ($soalList as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500">
                                    {{ $item->nomor_urut }}
                                </td>
                                <td class="py-3.5 px-4 max-w-lg space-y-2">
                                    <div class="font-medium text-slate-800 line-clamp-3 leading-relaxed">
                                        {{ $item->pertanyaan }}
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        @foreach ($item->opsi_jawaban ?? [] as $op)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] {{ $op['key'] === $item->kunci_jawaban ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                                <span class="font-bold">{{ $op['key'] }}.</span>
                                                <span class="max-w-xs truncate">{{ $op['text'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($item->gambar_path)
                                        <a href="{{ asset('storage/' . $item->gambar_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-semibold hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Lihat</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                    {{ $item->bobot }} Poin
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-600 text-white font-black text-xs shadow-2xs">
                                        {{ $item->kunci_jawaban }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <form action="{{ route('admin.tpa.soal.toggle', $item) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-colors {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-200' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.tpa.soal.edit', $item) }}"
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit Soal">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('admin.tpa.soal.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus butir soal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Hapus Soal">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-medium text-xs">Belum ada butir soal untuk babak {{ ucfirst($babak) }}.</p>
                                    <a href="{{ route('admin.tpa.soal.create', ['babak' => $babak]) }}" class="mt-2 inline-block text-xs font-bold text-orange-600 hover:underline">
                                        + Tambah Soal Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($soalList->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $soalList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Import Soal TPA -->
    <div id="modalImportSoal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-black text-slate-800 text-base">Import Butir Soal TPA (CSV / Excel)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Babak target: <strong class="text-indigo-600 uppercase">{{ $babak }}</strong> &bull; Format utama: <strong class="text-emerald-700">.csv</strong></p>
                </div>
                <button type="button" onclick="document.getElementById('modalImportSoal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.tpa.soal.import') }}" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf
                <input type="hidden" name="babak" value="{{ $babak }}">

                <!-- Unduh Template Panduan CSV -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-slate-800 text-xs">Belum punya format file?</p>
                        <p class="text-[11px] text-slate-500">Unduh template CSV resmi berisi contoh soal babak {{ ucfirst($babak) }}</p>
                    </div>
                    <a href="{{ route('admin.tpa.soal.template', ['babak' => $babak, 'format' => 'csv']) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Template CSV</span>
                    </a>
                </div>

                <!-- Input File -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih File CSV / Excel *</label>
                    <input type="file" name="file" required accept=".csv,.txt,.xlsx,.xls"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-200 rounded-xl cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Mendukung file .csv (direkomendasikan) atau .xlsx (maksimal 5MB)</p>
                </div>

                <!-- Opsi Strategi Import -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Metode / Mode Import *</label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50/70 cursor-pointer">
                            <input type="radio" name="mode" value="upsert" checked class="mt-0.5 text-orange-600">
                            <div>
                                <span class="font-bold text-slate-800 block text-xs">Tambah / Update (Upsert)</span>
                                <span class="text-[11px] text-slate-500">Perbarui soal yang memiliki nomor urut sama, dan tambahkan butir soal baru tanpa menghapus soal lain.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-2.5 p-3 rounded-xl border border-rose-200 bg-rose-50/30 hover:bg-rose-50/70 cursor-pointer">
                            <input type="radio" name="mode" value="replace" class="mt-0.5 text-rose-600">
                            <div>
                                <span class="font-bold text-rose-800 block text-xs">Ganti Semua (Replace)</span>
                                <span class="text-[11px] text-rose-600">Kosongkan seluruh butir soal babak {{ ucfirst($babak) }} yang ada saat ini, lalu gantikan seluruhnya dari file.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalImportSoal').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-nampi-orange text-white font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                        Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
