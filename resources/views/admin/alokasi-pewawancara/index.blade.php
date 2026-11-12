<x-layouts.app>
    <x-slot name="title">Manajemen Alokasi Pewawancara — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Manajemen Alokasi Pewawancara</h1>
                <p class="text-xs text-slate-400 mt-0.5">Penugasan guru penguji wawancara untuk calon murid yang telah melengkapi berkas persyaratan</p>
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

        <!-- Interviewers Workload Cards -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Beban Kerja Guru Pewawancara</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @forelse ($pewawancaraList as $pew)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <span class="font-bold text-slate-800 text-xs block truncate">{{ $pew->name }}</span>
                        <span class="text-lg font-black text-nampi-orange mt-0.5 block">{{ $pew->wawancara_count }}</span>
                        <span class="text-[10px] text-slate-400">murid dialokasikan</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 col-span-6 italic">Belum ada akun pewawancara aktif.</p>
                @endforelse
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.alokasi-pewawancara.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <input type="hidden" name="status_wawancara" value="{{ $statusWawancara }}">
                <div>
                    <label class="block font-bold text-slate-500 uppercase mb-1">Cari Peserta</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama, NISN, No. Daftar..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div>
                    <label class="block font-bold text-slate-500 uppercase mb-1">Jurusan</label>
                    <select name="jurusan_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-500 uppercase mb-1">Status Alokasi</label>
                    <select name="status_alokasi" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua</option>
                        <option value="BELUM" {{ request('status_alokasi') === 'BELUM' ? 'selected' : '' }}>Belum Dialokasikan</option>
                        <option value="SUDAH" {{ request('status_alokasi') === 'SUDAH' ? 'selected' : '' }}>Sudah Dialokasikan</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.alokasi-pewawancara.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Segmented Tabs: Status Pelaksanaan Wawancara -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="inline-flex p-1 bg-slate-200/80 rounded-2xl gap-1 text-xs font-bold self-start">
                <!-- Tab Belum Selesai (Default) -->
                <a href="{{ request()->fullUrlWithQuery(['status_wawancara' => 'BELUM']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all {{ $statusWawancara === 'BELUM' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                    <span class="w-2 h-2 rounded-full {{ $statusWawancara === 'BELUM' ? 'bg-amber-500' : 'bg-slate-400' }}"></span>
                    <span>Belum Selesai</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $statusWawancara === 'BELUM' ? 'bg-amber-100 text-amber-900' : 'bg-slate-300 text-slate-700' }}">
                        {{ $countBelumSelesai }}
                    </span>
                </a>

                <!-- Tab Sudah Selesai -->
                <a href="{{ request()->fullUrlWithQuery(['status_wawancara' => 'SELESAI']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all {{ $statusWawancara === 'SELESAI' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                    <span class="w-2 h-2 rounded-full {{ $statusWawancara === 'SELESAI' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    <span>Sudah Selesai</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $statusWawancara === 'SELESAI' ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-300 text-slate-700' }}">
                        {{ $countSudahSelesai }}
                    </span>
                </a>

                <!-- Tab Semua Data -->
                <a href="{{ request()->fullUrlWithQuery(['status_wawancara' => 'SEMUA']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all {{ $statusWawancara === 'SEMUA' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                    <span>Semua Calon Murid</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $statusWawancara === 'SEMUA' ? 'bg-slate-900 text-white' : 'bg-slate-300 text-slate-700' }}">
                        {{ $countSemua }}
                    </span>
                </a>
            </div>

            <!-- Keterangan Status Aktif -->
            <div class="text-xs text-slate-500">
                @if ($statusWawancara === 'BELUM')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 font-medium">
                        <span>⏳</span> Menampilkan calon yang wawancaranya belum lengkap (Siswa atau Ortu)
                    </span>
                @elseif ($statusWawancara === 'SELESAI')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-medium">
                        <span>✓</span> Menampilkan calon yang kedua wawancara telah selesai
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 font-medium">
                        <span>📋</span> Menampilkan seluruh riwayat calon murid pada tahapan wawancara
                    </span>
                @endif
            </div>
        </div>

        <!-- Batch Action Form -->
        <form method="POST" action="{{ route('admin.alokasi-pewawancara.batch') }}" id="formBatchAlokasi">
            @csrf
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Batch Action Bar -->
                <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="checkAll" onclick="toggleSelectAll(this)" class="rounded text-nampi-orange">
                        <label for="checkAll" class="font-bold text-slate-700">Pilih Semua di Halaman Ini</label>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="text-slate-500 font-medium">Tugaskan Terpilih Ke:</span>
                        <select name="pewawancara_id" required class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                            <option value="">-- Pilih Pewawancara --</option>
                            @foreach ($pewawancaraList as $pew)
                                <option value="{{ $pew->id }}">{{ $pew->name }} ({{ $pew->wawancara_count }} siswa)</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-nampi-orange text-white font-bold hover:bg-orange-600 transition-colors shadow-xs">
                            Alokasikan Massal
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center" style="width: 4%;">Pilih</th>
                                <th class="px-5 py-3" style="width: 28%;">calon murid</th>
                                <th class="px-4 py-3" style="width: 18%;">Kompetensi Keahlian</th>
                                <th class="px-4 py-3" style="width: 22%;">Pewawancara Bertugas</th>
                                <th class="px-4 py-3 text-center" style="width: 14%;">Status Wawancara</th>
                                <th class="px-5 py-3 text-right" style="width: 14%;">Aksi Individual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($calonSiswaList as $cs)
                                @php
                                    $w = $cs->latestWawancara;
                                    $pewawancaraAssigned = $w?->pewawancara ?? $cs->wawancaraSiswa?->pewawancara ?? $cs->wawancaraOrangTua?->pewawancara;

                                    $isSiswaSelesai = $cs->wawancaraSiswa?->status === 'SELESAI';
                                    $isOrtuSelesai = $cs->wawancaraOrangTua?->status === 'SELESAI';

                                    // Fallback bila data wawancara disimpan pada model Wawancara legacy
                                    if (!$cs->wawancaraSiswa && !$cs->wawancaraOrangTua && $w && ($w->status === 'SELESAI' || $w->details?->count() > 0)) {
                                        $isSiswaSelesai = true;
                                        $isOrtuSelesai = true;
                                    }

                                    $isKeduanyaSelesai = $isSiswaSelesai && $isOrtuSelesai;
                                @endphp
                                <tr class="transition-colors {{ $isKeduanyaSelesai ? 'bg-emerald-50/50 hover:bg-emerald-100/40 border-b border-emerald-100' : 'hover:bg-slate-50/70' }}">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" name="calon_siswa_ids[]" value="{{ $cs->id }}" class="candidate-checkbox rounded text-nampi-orange">
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 block text-sm">{{ $cs->nama_lengkap }}</span>
                                            @if ($isKeduanyaSelesai)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Wawancara Siswa & Ortu Selesai">
                                                    ✓ Selesai
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-slate-400 font-mono text-[11px]">{{ $cs->nomor_pendaftaran }} &bull; NISN: {{ $cs->nisn }}</span>
                                    </td>
                                    <td class="px-4 py-4 font-semibold text-slate-700">
                                        {{ $cs->jurusan?->nama ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($pewawancaraAssigned)
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span class="font-bold text-slate-800">{{ $pewawancaraAssigned->name }}</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Belum Ditugaskan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if ($isKeduanyaSelesai)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                ✓ Keduanya Selesai
                                            </span>
                                        @elseif ($isSiswaSelesai || $isOrtuSelesai || ($cs->wawancaraSiswa?->status === 'DRAFT' || $cs->wawancaraOrangTua?->status === 'DRAFT'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                ⏳ Belum Lengkap
                                            </span>
                                        @elseif ($pewawancaraAssigned)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Menunggu Uji
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum Wawancara
                                            </span>
                                        @endif

                                        <!-- Indikator Ikon Ringkas Murid & Ortu -->
                                        <div class="flex items-center justify-center gap-2 mt-1.5 text-[11px] font-medium">
                                            <span class="{{ $isSiswaSelesai ? 'text-emerald-700 font-bold' : 'text-slate-400' }}" title="{{ $isSiswaSelesai ? 'Wawancara Murid: Selesai' : 'Wawancara Murid: Belum Selesai' }}">
                                                Murid {!! $isSiswaSelesai ? '✓' : '⏳' !!}
                                            </span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="{{ $isOrtuSelesai ? 'text-emerald-700 font-bold' : 'text-slate-400' }}" title="{{ $isOrtuSelesai ? 'Wawancara Ortu: Selesai' : 'Wawancara Ortu: Belum Selesai' }}">
                                                Ortu {!! $isOrtuSelesai ? '✓' : '⏳' !!}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button type="button"
                                                onclick="openSingleModal({{ $cs->id }}, '{{ addslashes($cs->nama_lengkap) }}', {{ $pewawancaraAssigned->id ?? 'null' }})"
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-slate-900 hover:text-white transition-colors cursor-pointer text-xs">
                                            {{ $pewawancaraAssigned ? 'Ubah' : 'Tugaskan' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada calon murid pada antrian wawancara sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($calonSiswaList->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $calonSiswaList->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Modal Alokasi Tunggal -->
    <div id="modalSingleAlokasi" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 text-xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-base">Alokasikan Pewawancara</h3>
                <button type="button" onclick="document.getElementById('modalSingleAlokasi').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form id="formSingleAlokasi" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-500 uppercase mb-1">calon murid</label>
                    <p id="singleNamaSiswa" class="font-bold text-slate-900 text-sm"></p>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Guru Pewawancara *</label>
                    <select id="singlePewawancaraSelect" name="pewawancara_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">-- Pilih Pewawancara --</option>
                        @foreach ($pewawancaraList as $pew)
                            <option value="{{ $pew->id }}">{{ $pew->name }} ({{ $pew->wawancara_count }} siswa)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jadwal Tanggal Wawancara</label>
                    <input type="date" name="tanggal_wawancara" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalSingleAlokasi').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-nampi-orange text-white font-bold hover:bg-orange-600 transition-colors shadow-xs">
                        Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSelectAll(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.candidate-checkbox');
            checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        }

        function openSingleModal(calonSiswaId, namaSiswa, currentPewawancaraId) {
            document.getElementById('singleNamaSiswa').textContent = namaSiswa;
            document.getElementById('singlePewawancaraSelect').value = currentPewawancaraId || '';
            document.getElementById('formSingleAlokasi').action = '/admin/alokasi-pewawancara/' + calonSiswaId;
            document.getElementById('modalSingleAlokasi').classList.remove('hidden');
        }
    </script>
</x-layouts.app>
