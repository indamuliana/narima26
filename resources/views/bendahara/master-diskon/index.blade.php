<x-layouts.app>
    <x-slot name="title">Kelola Master Diskon</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6"
         x-data="{
             modalTambah: false,
             editItem: null,
             confirmHapus: null
         }">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Master Data Program Diskon</h1>
                <p class="text-xs text-slate-500 mt-1">Kelola jenis-jenis diskon dan beasiswa baku untuk digunakan pada saat penagihan calon siswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="modalTambah = true" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                    <span>➕ Tambah Diskon Baku</span>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
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
                        @forelse ($diskonList as $diskon)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 font-bold text-slate-800">{{ $diskon->nama_diskon }}</td>
                                <td class="px-4 py-4 uppercase text-xs font-semibold">{{ $diskon->metode_diskon }}</td>
                                <td class="px-4 py-4 font-black text-emerald-700">
                                    {{ $diskon->metode_diskon === 'persentase' ? $diskon->nilai_diskon . '%' : 'Rp ' . number_format($diskon->nilai_diskon, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-xs">{{ $diskon->deskripsi ?? '-' }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $diskon->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $diskon->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button @click="editItem = {{ json_encode($diskon) }}" class="text-nampi-orange hover:underline text-xs font-bold mr-3">Edit</button>
                                    <button @click="confirmHapus = {{ json_encode($diskon) }}" class="text-rose-600 hover:underline text-xs font-bold">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">Belum ada master data diskon.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($diskonList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $diskonList->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah / Edit -->
        <div x-show="modalTambah || editItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15,23,42,0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="modalTambah = false; editItem = null" class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl">
                    <h3 class="text-lg font-black text-slate-800 mb-4" x-text="editItem ? 'Edit Program Diskon' : 'Tambah Program Diskon'"></h3>
                    
                    <form :action="editItem ? `{{ route('bendahara.master-diskon.index') }}/${editItem.id}` : '{{ route('bendahara.master-diskon.store') }}'" method="POST" class="space-y-4">
                        @csrf
                        <template x-if="editItem">
                            @method('PUT')
                        </template>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Diskon <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_diskon" :value="editItem?.nama_diskon" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode <span class="text-rose-500">*</span></label>
                                <select name="metode_diskon" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200" 
                                        x-init="$watch('editItem', v => { $el.value = v ? v.metode_diskon : 'nominal' })">
                                    <option value="nominal">Nominal (Rp)</option>
                                    <option value="persentase">Persentase (%)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nilai <span class="text-rose-500">*</span></label>
                                <input type="number" name="nilai_diskon" :value="editItem?.nilai_diskon" required step="any" min="0" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200" x-text="editItem?.deskripsi"></textarea>
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="rounded text-nampi-orange focus:ring-nampi-orange" :checked="editItem ? editItem.is_active : true">
                                <span class="text-sm font-semibold text-slate-700">Aktif digunakan</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" @click="modalTambah = false; editItem = null" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-nampi-orange text-white hover:bg-orange-600">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <div x-show="confirmHapus" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15,23,42,0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="confirmHapus = null" class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-xl">
                    <h3 class="text-lg font-black text-rose-600 mb-2">Hapus Program Diskon?</h3>
                    <p class="text-xs text-slate-600 mb-6">Anda yakin ingin menghapus <strong x-text="confirmHapus?.nama_diskon"></strong>?</p>
                    <form :action="`{{ route('bendahara.master-diskon.index') }}/${confirmHapus?.id}`" method="POST" class="flex justify-end gap-2">
                        @csrf @method('DELETE')
                        <button type="button" @click="confirmHapus = null" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 text-white hover:bg-rose-700">Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
