<x-layouts.app>
    <x-slot name="title">Tata Kelola Pengunduran Diri Siswa</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6" x-data="{ modalTarik: false, modalRestore: false, currentStudent: null }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Pengunduran Diri & Penarikan Berkas</h1>
                <p class="text-xs text-slate-500 mt-1">Pengelolaan permohonan pengunduran diri resmi calon siswa dan pemulihan status pendaftaran.</p>
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

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <ul class="list-disc list-inside text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa Mengundurkan Diri</span>
                        <p class="text-2xl font-black text-slate-700 mt-1">{{ number_format($stats['total_withdrawn']) }} Siswa</p>
                    </div>
                    <span class="text-2xl">🚪</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendaftar Aktif</span>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['total_active']) }} Siswa</p>
                    </div>
                    <span class="text-2xl">👤</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('kepala-sekolah.pengunduran-diri.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="sm:col-span-10">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari nama calon siswa, nomor pendaftaran, atau NISN..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('kepala-sekolah.pengunduran-diri.index', ['tab' => $tab]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-slate-200">
            <a href="{{ route('kepala-sekolah.pengunduran-diri.index', ['tab' => 'withdrawn', 'q' => $search]) }}"
               class="px-5 py-3 text-xs font-bold border-b-2 transition-colors {{ $tab === 'withdrawn' ? 'border-nampi-orange text-nampi-orange' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Siswa Mengundurkan Diri ({{ $stats['total_withdrawn'] }})
            </a>
            <a href="{{ route('kepala-sekolah.pengunduran-diri.index', ['tab' => 'active', 'q' => $search]) }}"
               class="px-5 py-3 text-xs font-bold border-b-2 transition-colors {{ $tab === 'active' ? 'border-nampi-orange text-nampi-orange' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Daftar Siswa Aktif ({{ $stats['total_active'] }})
            </a>
        </div>

        @if ($tab === 'withdrawn')
            <!-- Withdrawn Candidates Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Calon Siswa</th>
                                <th class="px-4 py-3.5">Pilihan Jurusan</th>
                                <th class="px-4 py-3.5">Alasan Pengunduran Diri</th>
                                <th class="px-4 py-3.5">Waktu / Eksekutor</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($withdrawnList as $w)
                                @php
                                    $lastHistory = $w->statusHistory()->where('status_baru', 'MENGUNDURKAN_DIRI')->latest('changed_at')->first();
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-800">{{ $w->nama_lengkap }}</p>
                                        <p class="text-xs text-slate-400 font-mono mt-0.5">
                                            {{ $w->nomor_pendaftaran }} &bull; NISN: {{ $w->nisn }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-xs font-semibold text-slate-800 block">{{ $w->jurusan?->nama_jurusan }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $w->program?->nama_program }}</span>
                                    </td>
                                    <td class="px-4 py-4 max-w-xs">
                                        <p class="text-xs font-medium text-slate-700">{{ $lastHistory?->alasan ?? 'Pengunduran diri resmi' }}</p>
                                        @if ($lastHistory?->catatan)
                                            <p class="text-[11px] text-slate-400 mt-0.5 italic">{{ $lastHistory->catatan }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs">
                                        <span class="text-slate-800 font-medium block">{{ $lastHistory?->changed_at?->format('d/m/Y H:i') ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400">Oleh: {{ $lastHistory?->changedBy?->name ?? 'Admin/Kepsek' }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button @click="currentStudent = {{ json_encode($w) }}; modalRestore = true" type="button"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs cursor-pointer">
                                            ↺ Pulihkan Siswa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                        <p class="text-base font-bold text-slate-600">Tidak ada siswa yang mengundurkan diri</p>
                                        <p class="text-xs text-slate-400 mt-1">Seluruh pendaftar saat ini masih berstatus aktif dalam proses SPMB.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($withdrawnList->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $withdrawnList->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- Active Candidates Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Calon Siswa</th>
                                <th class="px-4 py-3.5">Kompetensi Keahlian</th>
                                <th class="px-4 py-3.5 text-center">Status Saat Ini</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($activeList as $act)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-800">{{ $act->nama_lengkap }}</p>
                                        <p class="text-xs text-slate-400 font-mono mt-0.5">
                                            {{ $act->nomor_pendaftaran }} &bull; NISN: {{ $act->nisn }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-xs font-semibold text-slate-800 block">{{ $act->jurusan?->nama_jurusan }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $act->program?->nama_program }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            {{ is_string($act->status_spmb) ? $act->status_spmb : $act->status_spmb->label() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button @click="currentStudent = {{ json_encode($act) }}; modalTarik = true" type="button"
                                                class="px-3 py-1.5 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs font-bold transition-colors cursor-pointer">
                                            Proses Pengunduran Diri
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-slate-400">
                                        <p class="text-base font-bold text-slate-600">Tidak ada calon siswa ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($activeList->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $activeList->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Modal Proses Pengunduran Diri -->
        <div x-show="modalTarik" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="modalTarik = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-black text-rose-800">Formulir Pengunduran Diri Siswa</h3>
                        <button @click="modalTarik = false" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form :action="'{{ url('kepala-sekolah/pengunduran-diri') }}/' + currentStudent?.id" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                            <p class="font-bold text-slate-800" x-text="currentStudent?.nama_lengkap"></p>
                            <p class="text-slate-500 font-mono" x-text="'No. Pendaftaran: ' + currentStudent?.nomor_pendaftaran"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pengunduran Diri <span class="text-rose-500">*</span></label>
                            <textarea name="alasan" rows="3" required
                                      placeholder="Contoh: Diterima di SMA Negeri, pindah domisili ke luar kota, permohonan orang tua..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Dokumen / Berita Acara (Opsional)</label>
                            <input type="text" name="catatan"
                                   placeholder="Contoh: Surat permohonan tanggal 28/09/2026 ditandatangani orang tua"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button @click="modalTarik = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" onclick="return confirm('Apakah Anda yakin akan menetapkan status Mengundurkan Diri untuk siswa ini?')"
                                    class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors shadow-xs">
                                Konfirmasi Pengunduran Diri
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Pemulihan / Restorasi Siswa -->
        <div x-show="modalRestore" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6);">
            <div class="min-h-screen px-4 flex items-center justify-center">
                <div @click.away="modalRestore = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-black text-emerald-800">Pemulihan Status Pendaftaran</h3>
                        <button @click="modalRestore = false" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form :action="'{{ url('kepala-sekolah/pengunduran-diri') }}/' + currentStudent?.id + '/restore'" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3 bg-emerald-50 rounded-xl text-xs space-y-1">
                            <p class="font-bold text-emerald-900" x-text="currentStudent?.nama_lengkap"></p>
                            <p class="text-emerald-700 font-mono" x-text="'No. Pendaftaran: ' + currentStudent?.nomor_pendaftaran"></p>
                            <p class="text-[11px] text-emerald-600 mt-1">Status siswa akan otomatis dikembalikan ke status aktif sebelum pengunduran diri.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pemulihan / Pembatalan Penarikan <span class="text-rose-500">*</span></label>
                            <textarea name="alasan_restorasi" rows="3" required
                                      placeholder="Contoh: Siswa membatalkan penarikan berkas dan bersedia melanjutkan proses SPMB..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button @click="modalRestore = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" onclick="return confirm('Pulihkan status calon siswa ini kembali ke alur SPMB aktif?')"
                                    class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs">
                                Pulihkan Status Aktif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
