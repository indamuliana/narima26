<x-layouts.app>
    <x-slot name="title">Manajemen Butir Kesepahaman Murid</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Butir Kesepahaman Murid</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola poin pakta integritas dan kesepahaman EULA untuk calon siswa baru berdasarkan program.</p>
            </div>
        </div>

        @if(session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if(session('warning'))
            <x-alert type="warning" title="Perhatian">{{ session('warning') }}</x-alert>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="{
            showAddKelompok: false, 
            addKelompokProgramId: null,
            showAddPoin: false,
            addPoinKelompokId: null,
            showEditPoin: false,
            editPoinData: { id: null, kode_poin: '', nomor: '', uraian: '', urutan: 1, formUrl: '' },
            showImportModal: false,
            importProgramData: { id: null, nama: '', formUrl: '', templateXlsxUrl: '', templateCsvUrl: '' },
            poinsMap: @js($programs->flatMap->kelompoks->flatMap->poins->keyBy('id')),
            openEditPoin(poinId, formUrl) {
                const poin = this.poinsMap[poinId];
                if (!poin) return;
                this.editPoinData.id = poin.id;
                this.editPoinData.kode_poin = poin.kode_poin;
                this.editPoinData.nomor = poin.nomor;
                this.editPoinData.uraian = poin.uraian;
                this.editPoinData.urutan = poin.urutan;
                this.editPoinData.formUrl = formUrl;
                this.showEditPoin = true;
            }
        }">
            @foreach($programs as $program)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Program: {{ $program->nama }}</h2>
                            <p class="text-xs text-slate-500">Tahun Pelajaran: {{ $program->tahun_pelajaran }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Dropdown Export & Template -->
                            <div class="relative" x-data="{ openMenu: false }">
                                <button @click="openMenu = !openMenu" @click.outside="openMenu = false" type="button" class="px-2.5 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-1 shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Excel / CSV</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="openMenu" x-transition class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-20" style="display: none;">
                                    <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Export Data</div>
                                    <a href="{{ route('admin.kesepahaman.export', [$program->id, 'format' => 'xlsx']) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Export Excel (.xlsx)
                                    </a>
                                    <a href="{{ route('admin.kesepahaman.export', [$program->id, 'format' => 'csv']) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Export CSV (.csv)
                                    </a>
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Download Template</div>
                                    <a href="{{ route('admin.kesepahaman.template', [$program->id, 'format' => 'xlsx']) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-indigo-600 font-semibold hover:bg-indigo-50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Template Excel (.xlsx)
                                    </a>
                                    <a href="{{ route('admin.kesepahaman.template', [$program->id, 'format' => 'csv']) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-indigo-600 font-semibold hover:bg-indigo-50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Template CSV (.csv)
                                    </a>
                                </div>
                            </div>

                            <!-- Tombol Import -->
                            <button @click="
                                importProgramData.id = {{ $program->id }};
                                importProgramData.nama = {{ json_encode($program->nama) }};
                                importProgramData.formUrl = {{ json_encode(route('admin.kesepahaman.import', $program->id)) }};
                                importProgramData.templateXlsxUrl = {{ json_encode(route('admin.kesepahaman.template', [$program->id, 'format' => 'xlsx'])) }};
                                importProgramData.templateCsvUrl = {{ json_encode(route('admin.kesepahaman.template', [$program->id, 'format' => 'csv'])) }};
                                showImportModal = true;
                            " type="button" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Import</span>
                            </button>

                            <!-- Tombol Print Draft -->
                            <a href="{{ route('admin.kesepahaman.preview', $program->id) }}" target="_blank" class="px-2.5 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Draft PDF</span>
                            </a>

                            <!-- Tombol Tambah Kelompok -->
                            <button @click="showAddKelompok = true; addKelompokProgramId = {{ $program->id }}" class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors">
                                + Kelompok
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        @forelse($program->kelompoks as $kelompok)
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <div class="bg-slate-100/50 px-4 py-3 border-b border-slate-200 flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-slate-200 text-slate-700 text-xs font-bold rounded">Kelompok {{ $kelompok->kode }}</span>
                                            <span class="text-xs text-slate-400">Urutan: {{ $kelompok->urutan }}</span>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800 mt-1">{{ $kelompok->judul }}</h3>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button @click="showAddPoin = true; addPoinKelompokId = {{ $kelompok->id }}" title="Tambah Poin" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.kesepahaman.kelompok.destroy', $kelompok->id) }}" method="POST" onsubmit="return confirm('Hapus kelompok ini dan semua poin di dalamnya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="divide-y divide-slate-100">
                                    @forelse($kelompok->poins as $poin)
                                        <div class="px-4 py-3 flex gap-3 hover:bg-slate-50 transition-colors group">
                                            <div class="shrink-0 font-bold text-sm text-slate-400 w-5">{{ $poin->nomor }}.</div>
                                            <div class="flex-1 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                                {!! nl2br(e($poin->uraian)) !!}
                                                <div class="mt-2 flex items-center gap-2">
                                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono">ID: {{ $poin->kode_poin }}</span>
                                                    <span class="text-[10px] text-slate-400">Urutan: {{ $poin->urutan }}</span>
                                                </div>
                                            </div>
                                            <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                                <button type="button" @click="openEditPoin({{ $poin->id }}, '{{ route('admin.kesepahaman.poin.update', $poin->id) }}')" title="Edit Poin" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form action="{{ route('admin.kesepahaman.poin.destroy', $poin->id) }}" method="POST" onsubmit="return confirm('Hapus poin ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-1 text-rose-600 hover:bg-rose-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-slate-500 italic text-center">Belum ada poin di kelompok ini.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-500">Belum ada kelompok kesepahaman.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <!-- MODAL TAMBAH KELOMPOK -->
            <div x-show="showAddKelompok" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
                <div @click.outside="showAddKelompok = false" class="bg-white rounded-2xl w-full max-w-md shadow-xl overflow-hidden">
                    <form action="{{ route('admin.kesepahaman.kelompok.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="program_id" x-model="addKelompokProgramId">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Tambah Kelompok</h3>
                            <button type="button" @click="showAddKelompok = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kode Kelompok (Misal: A)</label>
                                <input type="text" name="kode" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Kelompok</label>
                                <input type="text" name="judul" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange" placeholder="A. Dalam kaitannya dengan...">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
                                <input type="number" name="urutan" value="1" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" @click="showAddKelompok = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-nampi-orange rounded-xl hover:bg-orange-600">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL TAMBAH POIN -->
            <div x-show="showAddPoin" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
                <div @click.outside="showAddPoin = false" class="bg-white rounded-2xl w-full max-w-lg shadow-xl overflow-hidden">
                    <form action="{{ route('admin.kesepahaman.poin.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelompok_id" x-model="addPoinKelompokId">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Tambah Poin Kesepahaman</h3>
                            <button type="button" @click="showAddPoin = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Unik Poin (ID)</label>
                                    <input type="text" name="kode_poin" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange" placeholder="reg_a_1">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor (Tampil)</label>
                                    <input type="text" name="nomor" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange" placeholder="1">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Uraian / Naskah Poin</label>
                                <textarea name="uraian" rows="6" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange leading-relaxed" placeholder="Tuliskan uraian poin kesepahaman..."></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Dapat menggunakan Enter (baris baru) untuk butir/rincian bertingkat.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
                                <input type="number" name="urutan" value="1" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" @click="showAddPoin = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-nampi-orange rounded-xl hover:bg-orange-600">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL EDIT POIN -->
            <div x-show="showEditPoin" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
                <div @click.outside="showEditPoin = false" class="bg-white rounded-2xl w-full max-w-lg shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    <form :action="editPoinData.formUrl" method="POST">
                        @csrf @method('PUT')
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Edit Poin Kesepahaman</h3>
                            <button type="button" @click="showEditPoin = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Unik Poin (ID)</label>
                                    <input type="text" name="kode_poin" x-model="editPoinData.kode_poin" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange" placeholder="reg_a_1">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor (Tampil)</label>
                                    <input type="text" name="nomor" x-model="editPoinData.nomor" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange" placeholder="1">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Uraian / Naskah Poin</label>
                                <textarea name="uraian" x-model="editPoinData.uraian" rows="6" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange leading-relaxed" placeholder="Tuliskan uraian..."></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Dapat menggunakan Enter (baris baru) untuk butir/rincian bertingkat.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
                                <input type="number" name="urutan" x-model="editPoinData.urutan" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" @click="showEditPoin = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-nampi-orange rounded-xl hover:bg-orange-600">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL IMPORT SPREADSHEET -->
            <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
                <div @click.outside="showImportModal = false" class="bg-white rounded-2xl w-full max-w-lg shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    <form :action="importProgramData.formUrl" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-sm">Import Butir Kesepahaman</h3>
                                    <p class="text-xs text-slate-500">Program: <span class="font-semibold text-slate-700" x-text="importProgramData.nama"></span></p>
                                </div>
                            </div>
                            <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Download Template Hint -->
                            <div class="p-3 bg-amber-50/70 border border-amber-200/60 rounded-xl text-xs text-amber-900 flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="space-y-1">
                                    <p class="font-semibold">Gunakan template kolom yang sesuai agar data terbaca sempurna.</p>
                                    <div class="flex items-center gap-2 pt-0.5">
                                        <span class="text-slate-500">Unduh template:</span>
                                        <a :href="importProgramData.templateXlsxUrl" class="font-bold text-indigo-600 hover:underline flex items-center gap-0.5">
                                            Excel (.xlsx)
                                        </a>
                                        <span class="text-slate-300">|</span>
                                        <a :href="importProgramData.templateCsvUrl" class="font-bold text-indigo-600 hover:underline flex items-center gap-0.5">
                                            CSV (.csv)
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Input File -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih File Spreadsheet (.xlsx, .xls, .csv) <span class="text-rose-500">*</span></label>
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                <p class="text-[11px] text-slate-400 mt-1">Maksimal ukuran file: 5MB.</p>
                            </div>

                            <!-- Mode Import -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Metode Import Data <span class="text-rose-500">*</span></label>
                                <div class="space-y-2">
                                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                        <input type="radio" name="mode" value="replace" checked class="mt-0.5 text-nampi-orange focus:ring-nampi-orange">
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Ganti Semua (Replace) — Disarankan</span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Menghapus kelompok dan butir poin lama di program ini, lalu mengisi dengan butir-butir baru dari file. Cocok untuk revisi naskah tahun ajaran baru.</span>
                                        </div>
                                    </label>
                                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                        <input type="radio" name="mode" value="upsert" class="mt-0.5 text-nampi-orange focus:ring-nampi-orange">
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Tambah & Perbarui Saja (Upsert)</span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Memperbarui teks poin yang kodenya cocok dan menambahkan poin baru tanpa menghapus data yang sudah ada sebelumnya.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Format Kolom Note -->
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
                                <p class="font-bold text-slate-700">Urutan Kolom Spreadsheet:</p>
                                <p class="font-mono text-[10px] text-slate-500">1. Kode Kelompok | 2. Judul Kelompok | 3. Urutan Kelompok | 4. Nomor Poin | 5. Uraian Poin | 6. Kode Poin (Opsional) | 7. Urutan Poin (Opsional)</p>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" @click="showImportModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Unggah & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</x-layouts.app>
