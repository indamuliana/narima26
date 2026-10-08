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
            editPoinData: { id: null, kode_poin: '', nomor: '', uraian: '', urutan: 1, formUrl: '' }
        }">
            @foreach($programs as $program)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Program: {{ $program->nama }}</h2>
                            <p class="text-xs text-slate-500">Tahun Pelajaran: {{ $program->tahun_pelajaran }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.kesepahaman.preview', $program->id) }}" target="_blank" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Print Draft
                            </a>
                            <button @click="showAddKelompok = true; addKelompokProgramId = {{ $program->id }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors">
                                + Tambah Kelompok
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
                                            <div class="flex-1 text-sm text-slate-700">
                                                {{ $poin->uraian }}
                                                <div class="mt-1 flex items-center gap-2">
                                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 rounded font-mono">ID: {{ $poin->kode_poin }}</span>
                                                    <span class="text-[10px] text-slate-400">Urutan: {{ $poin->urutan }}</span>
                                                </div>
                                            </div>
                                            <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                                <button type="button" @click="
                                                    editPoinData.id = {{ $poin->id }};
                                                    editPoinData.kode_poin = {!! json_encode($poin->kode_poin) !!};
                                                    editPoinData.nomor = {!! json_encode($poin->nomor) !!};
                                                    editPoinData.uraian = {!! json_encode($poin->uraian) !!};
                                                    editPoinData.urutan = {{ $poin->urutan }};
                                                    editPoinData.formUrl = {!! json_encode(route('admin.kesepahaman.poin.update', $poin->id)) !!};
                                                    showEditPoin = true;
                                                " class="p-1 text-indigo-600 hover:bg-indigo-50 rounded">
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
                                <textarea name="uraian" rows="4" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange"></textarea>
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
                <div @click.outside="showEditPoin = false" class="bg-white rounded-2xl w-full max-w-lg shadow-xl overflow-hidden">
                    <form :action="editPoinData.formUrl" method="POST">
                        @csrf @method('PUT')
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Edit Poin Kesepahaman</h3>
                            <button type="button" @click="showEditPoin = false" class="text-slate-400 hover:text-slate-600">&times;</button>
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
                                <textarea name="uraian" x-model="editPoinData.uraian" rows="4" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange"></textarea>
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
            
        </div>
    </div>
</x-layouts.app>
