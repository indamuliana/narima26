<x-layouts.app>
    <x-slot name="title">Manajemen Kompetensi Keahlian (Jurusan) — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Manajemen Kompetensi Keahlian (Jurusan)</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola pilihan program keahlian, status buka/tutup pendaftaran, dan pantau jumlah peminat</p>
            </div>
            <div>
                <button type="button" onclick="document.getElementById('modalTambahJurusan').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Jurusan Baru</span>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Total Jurusan</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $jurusanList->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-emerald-600 uppercase">Status Aktif Dibuka</span>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $jurusanList->where('aktif', true)->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-nampi-orange uppercase">Total Calon Murid Terdaftar</span>
                <p class="text-2xl font-black text-nampi-orange mt-1">{{ $jurusanList->sum('calon_siswa_count') }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-black text-slate-800">Daftar Kompetensi Keahlian</h2>
                <p class="text-xs text-slate-400 mt-0.5">Program keahlian SMK Wikrama 1 Garut</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-5 py-3.5" style="width: 12%;">Kode</th>
                            <th class="px-5 py-3.5" style="width: 32%;">Nama Kompetensi Keahlian</th>
                            <th class="px-5 py-3.5" style="width: 26%;">Keterangan</th>
                            <th class="px-4 py-3.5 text-center" style="width: 12%;">Peminat</th>
                            <th class="px-4 py-3.5 text-center" style="width: 10%;">Status</th>
                            <th class="px-5 py-3.5 text-right" style="width: 18%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($jurusanList as $jur)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 text-sm">
                                    {{ $jur->kode }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $jur->nama }}</span>
                                </td>
                                <td class="px-5 py-4 text-slate-500">
                                    {{ $jur->keterangan ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                        {{ $jur->calon_siswa_count }} Siswa
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($jur->aktif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                onclick="openEditModal({{ json_encode($jur) }})"
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.jurusan.toggle', $jur) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors cursor-pointer {{ $jur->aktif ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                {{ $jur->aktif ? 'Tutup' : 'Buka' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">Belum ada master jurusan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Jurusan -->
    <div id="modalTambahJurusan" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-base">Tambah Kompetensi Keahlian</h3>
                <button type="button" onclick="document.getElementById('modalTambahJurusan').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.jurusan.store') }}" class="space-y-4 mt-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Jurusan *</label>
                    <input type="text" name="kode" required placeholder="Contoh: PPLG"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-mono uppercase">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Jurusan *</label>
                    <input type="text" name="nama" required placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Singkat</label>
                    <textarea name="keterangan" rows="2" placeholder="Deskripsi atau fokus kompetensi..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalTambahJurusan').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-nampi-orange text-white font-bold hover:bg-orange-600 transition-colors shadow-xs">
                        Simpan Jurusan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jurusan -->
    <div id="modalEditJurusan" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-base">Edit Kompetensi Keahlian</h3>
                <button type="button" onclick="document.getElementById('modalEditJurusan').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form id="formEditJurusan" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Jurusan *</label>
                    <input type="text" id="edit_kode" name="kode" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 font-mono uppercase">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Jurusan *</label>
                    <input type="text" id="edit_nama" name="nama" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Singkat</label>
                    <textarea id="edit_keterangan" name="keterangan" rows="2"
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalEditJurusan').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-colors shadow-xs">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(jurusan) {
            document.getElementById('edit_kode').value = jurusan.kode;
            document.getElementById('edit_nama').value = jurusan.nama;
            document.getElementById('edit_keterangan').value = jurusan.keterangan || '';
            document.getElementById('formEditJurusan').action = '/admin/jurusan/' + jurusan.id;
            document.getElementById('modalEditJurusan').classList.remove('hidden');
        }
    </script>
</x-layouts.app>
