<x-layouts.app>
    <x-slot name="title">Jadwal & Izin Masuk TPA Siswa — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Jadwal & Izin Akses TPA Siswa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Atur rentang waktu pengerjaan per siswa, buka/tutup izin pengerjaan secara manual atau massal</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Tombol Coba Ujian (Simulasi Admin) -->
                <button type="button" onclick="document.getElementById('modalSimulasiUjian').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition-all shadow-xs cursor-pointer">
                    <svg class="w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Coba Ujian (Simulasi)</span>
                </button>

                <!-- Tombol Salin Link Ujian Siswa -->
                <button type="button" onclick="salinLinkUjian()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
                        title="Salin link portal ujian untuk diberikan manual ke siswa">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    <span id="label-salin-link">Salin Link Ujian Siswa</span>
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
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Filter Card -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <form action="{{ route('admin.tpa.jadwal.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Pencarian Siswa</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Nama, No. Daftar, NISN..."
                               class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Gelombang</label>
                    <select name="gelombang_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                        <option value="SEMUA">Semua Gelombang</option>
                        @foreach ($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Pilihan Jurusan</label>
                    <select name="jurusan_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                        <option value="SEMUA">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Izin Masuk</label>
                    <select name="status_izin" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                        <option value="">Semua Status</option>
                        <option value="DI_IZINKAN" {{ request('status_izin') === 'DI_IZINKAN' ? 'selected' : '' }}>Diizinkan Masuk</option>
                        <option value="DITUTUP" {{ request('status_izin') === 'DITUTUP' ? 'selected' : '' }}>Ditutup / Belum Diizinkan</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Batch Action Toolbar (Muncul jika ada siswa yang dicentang) -->
        <form id="form-batch" action="{{ route('admin.tpa.jadwal.batch') }}" method="POST">
            @csrf
            <div id="batch-bar" class="p-4 bg-orange-50 border border-orange-200 rounded-2xl flex flex-wrap items-center justify-between gap-4 mb-4 hidden">
                <div class="flex items-center gap-2 text-xs font-bold text-orange-950">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                    <span id="selected-count">0</span> siswa terpilih untuk pengaturan jadwal massal:
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] font-bold text-slate-700">Mulai:</label>
                        <input type="datetime-local" name="waktu_mulai" class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 bg-white">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] font-bold text-slate-700">Selesai:</label>
                        <input type="datetime-local" name="waktu_selesai" class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 bg-white">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] font-bold text-slate-700">Izin:</label>
                        <select name="is_diizinkan" class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 bg-white">
                            <option value="1">Buka Izin (Aktif)</option>
                            <option value="0">Tutup Izin (Kunci)</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-colors shadow-xs cursor-pointer">
                        Terapkan Massal
                    </button>
                </div>
            </div>

            <!-- Tabel Siswa & Jadwal -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-4 w-10 text-center">
                                    <input type="checkbox" id="check-all" onclick="toggleSelectAll(this)"
                                           class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                                </th>
                                <th class="py-3.5 px-4">Calon Siswa</th>
                                <th class="py-3.5 px-4">Jurusan & Gelombang</th>
                                <th class="py-3.5 px-4">Jadwal Akses Pengerjaan</th>
                                <th class="py-3.5 px-4 w-32 text-center">Izin Masuk</th>
                                <th class="py-3.5 px-4 w-28 text-center">Status Sesi</th>
                                <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse ($calonSiswaList as $cs)
                                @php
                                    $jadwal = $cs->tpaJadwal;
                                    $sesi = $cs->tpaSesi;
                                    $isIzin = $jadwal ? $jadwal->is_diizinkan : false;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3.5 px-4 text-center">
                                        <input type="checkbox" name="siswa_ids[]" value="{{ $cs->id }}"
                                               onchange="updateBatchState()"
                                               class="checkbox-siswa w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $cs->nama_lengkap }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span>No: {{ $cs->nomor_pendaftaran }}</span>
                                            <span>&bull;</span>
                                            <span>NISN: {{ $cs->nisn ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-800">{{ $cs->jurusan?->nama_jurusan ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $cs->gelombang?->nama_gelombang ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if ($jadwal && ($jadwal->waktu_mulai || $jadwal->waktu_selesai))
                                            <div class="space-y-0.5">
                                                <div class="font-semibold text-slate-800">
                                                    {{ $jadwal->waktu_mulai ? $jadwal->waktu_mulai->translatedFormat('d M Y H:i') : 'Bebas Mulai' }}
                                                </div>
                                                <div class="text-[11px] text-slate-500">
                                                    s.d. {{ $jadwal->waktu_selesai ? $jadwal->waktu_selesai->translatedFormat('d M Y H:i') : 'Tanpa Batas Akhir' }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-slate-400 italic text-[11px]">
                                                Belum dijadwalkan khusus
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <button type="button" onclick="toggleIzinSingle({{ $cs->id }}, this)"
                                                class="px-3 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-colors {{ $isIzin ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}">
                                            {{ $isIzin ? 'Diizinkan' : 'Ditutup' }}
                                        </button>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if (! $sesi || $sesi->status === 'belum_mulai')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Belum Mulai</span>
                                        @elseif ($sesi->status === 'babak_1')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">Babak 1 (Lit)</span>
                                        @elseif ($sesi->status === 'transisi')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700">Transisi</span>
                                        @elseif ($sesi->status === 'babak_2')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700">Babak 2 (Num)</span>
                                        @elseif ($sesi->status === 'selesai')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700">Tuntas</span>
                                        @elseif ($sesi->status === 'didiskualifikasi')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700">Diskualifikasi</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Tombol Edit Jadwal Modal -->
                                            <button type="button" onclick="bukaModalEditJadwal({{ $cs->id }}, '{{ addslashes($cs->nama_lengkap) }}', '{{ $jadwal?->waktu_mulai?->format('Y-m-d\TH:i') }}', '{{ $jadwal?->waktu_selesai?->format('Y-m-d\TH:i') }}', {{ $isIzin ? 'true' : 'false' }}, '{{ addslashes($jadwal?->catatan ?? '') }}')"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-orange-600 hover:bg-orange-50 transition-colors cursor-pointer" title="Atur Jadwal Siswa">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>

                                            <!-- Tombol Reset Sesi jika kendala teknis -->
                                            @if ($sesi)
                                                <button type="button" onclick="resetSesiSiswa({{ $cs->id }}, '{{ addslashes($cs->nama_lengkap) }}')"
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Reset Sesi Pengerjaan">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                        Tidak ada calon siswa yang cocok dengan kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($calonSiswaList->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $calonSiswaList->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Modal Edit Jadwal Individual -->
    <div id="modal-jadwal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Atur Jadwal Akses Siswa</h3>
                    <p id="modal-nama-siswa" class="text-xs text-orange-600 font-semibold mt-0.5"></p>
                </div>
                <button type="button" onclick="tutupModalEditJadwal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="form-edit-jadwal" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Waktu Mulai Boleh Masuk</label>
                    <input type="datetime-local" id="input-waktu-mulai" name="waktu_mulai"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-[10px] text-slate-400">Kosongkan jika boleh masuk kapan saja</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Waktu Selesai (Batas Akhir Masuk)</label>
                    <input type="datetime-local" id="input-waktu-selesai" name="waktu_selesai"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Catatan Khusus (Opsional)</label>
                    <textarea id="input-catatan" name="catatan" rows="2"
                              class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none"
                              placeholder="Misal: Sesi Ujian Khusus Ruang Lab 1"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="input-is-diizinkan" name="is_diizinkan" value="1"
                               class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Izinkan Masuk Ujian</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="tutupModalEditJadwal()"
                                class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                            Simpan Jadwal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form untuk Reset Sesi -->
    <form id="form-reset-sesi" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function salinLinkUjian() {
            const url = "{{ url('/tpa-siswa') }}";
            navigator.clipboard.writeText(url).then(() => {
                const label = document.getElementById('label-salin-link');
                label.innerText = 'Link Disalin!';
                setTimeout(() => {
                    label.innerText = 'Salin Link Ujian Siswa';
                }, 2000);
            });
        }

        function toggleSelectAll(master) {
            const checkboxes = document.querySelectorAll('.checkbox-siswa');
            checkboxes.forEach(cb => cb.checked = master.checked);
            updateBatchState();
        }

        function updateBatchState() {
            const checkedBoxes = document.querySelectorAll('.checkbox-siswa:checked');
            const batchBar = document.getElementById('batch-bar');
            const countLabel = document.getElementById('selected-count');

            if (checkedBoxes.length > 0) {
                batchBar.classList.remove('hidden');
                countLabel.innerText = checkedBoxes.length;
            } else {
                batchBar.classList.add('hidden');
            }
        }

        function toggleIzinSingle(siswaId, btn) {
            fetch(`{{ url('admin/tpa/jadwal') }}/${siswaId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.is_diizinkan) {
                        btn.className = 'px-3 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-colors bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200';
                        btn.innerText = 'Diizinkan';
                    } else {
                        btn.className = 'px-3 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-colors bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200';
                        btn.innerText = 'Ditutup';
                    }
                }
            });
        }

        function bukaModalEditJadwal(id, nama, mulai, selesai, isIzin, catatan) {
            document.getElementById('modal-nama-siswa').innerText = nama;
            document.getElementById('input-waktu-mulai').value = mulai || '';
            document.getElementById('input-waktu-selesai').value = selesai || '';
            document.getElementById('input-is-diizinkan').checked = isIzin;
            document.getElementById('input-catatan').value = catatan || '';

            const form = document.getElementById('form-edit-jadwal');
            form.action = `{{ url('admin/tpa/jadwal') }}/${id}`;

            document.getElementById('modal-jadwal').classList.remove('hidden');
        }

        function tutupModalEditJadwal() {
            document.getElementById('modal-jadwal').classList.add('hidden');
        }

        function resetSesiSiswa(id, nama) {
            if (confirm(`Apakah Anda yakin ingin MERESET sesi pengerjaan untuk ${nama}? Seluruh jawaban siswa akan dikosongkan dan siswa dapat mengulang ujian.`)) {
                const form = document.getElementById('form-reset-sesi');
                form.action = `{{ url('admin/tpa/jadwal') }}/${id}/reset-sesi`;
                form.submit();
            }
        }
    </script>

    @include('admin.tpa.partials.modal-simulasi')
</x-layouts.app>
