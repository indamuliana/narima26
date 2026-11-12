<x-layouts.app>
    <x-slot name="title">Backup & Restore Data Sistem — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6" x-data="{ 
        restoreModal: false, 
        restoreFilename: '', 
        restoreType: '', 
        confirmText: '',
        openRestoreModal(filename, type) {
            this.restoreFilename = filename;
            this.restoreType = type;
            this.confirmText = '';
            this.restoreModal = true;
        }
    }">
        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Backup & Restore Data Sistem</h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Kelola cadangan database MySQL dan arsip berkas unggahan pendaftar (bukti bayar & dokumen) secara mandiri.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                    <span>Storage Bebas: {{ $freeSpaceHuman }}</span>
                </span>
            </div>
        </div>

        <!-- Alert Notifikasi -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 3 KARTU AKSI UTAMA: DATABASE, STORAGE, RESTORE UPLOAD -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Kartu 1: Backup Database -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-200/60 text-nampi-orange flex items-center justify-center text-2xl">
                        🗄️
                    </div>
                    <h3 class="text-base font-black text-slate-800">Cadangan Database (.sql)</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Mencadangkan seluruh tabel database: data calon pendaftar, nilai rapor, TPA, data wawancara, akun login, dan transaksi keuangan.
                    </p>
                </div>
                <form action="{{ route('admin.backup.database') }}" method="POST" onsubmit="return confirm('Mulai proses pembuatan backup database?')">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-nampi-orange hover:bg-orange-600 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Buat Backup Database Sekarang</span>
                    </button>
                </form>
            </div>

            <!-- Kartu 2: Backup Berkas Dokumen -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200/60 text-teal-600 flex items-center justify-center text-2xl">
                        📁
                    </div>
                    <h3 class="text-base font-black text-slate-800">Cadangan Berkas Siswa (.zip)</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Mengarsipkan seluruh file fisik dokumen pendaftar: slip bukti transfer BNI, kartu keluarga, akta kelahiran, dan berkas persyaratan di storage.
                    </p>
                </div>
                <form action="{{ route('admin.backup.storage') }}" method="POST" onsubmit="return confirm('Mulai proses kompresi arsip berkas storage?')">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Buat Backup Berkas (.zip)</span>
                    </button>
                </form>
            </div>

            <!-- Kartu 3: Pulihkan dari File Luar (Upload & Restore) -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200/60 text-blue-600 flex items-center justify-center text-2xl">
                        🔄
                    </div>
                    <h3 class="text-base font-black text-slate-800">Unggah & Pulihkan (Restore)</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Memiliki file cadangan dari komputer Anda? Unggah file <code class="text-[11px] font-mono bg-slate-100 px-1 py-0.5 rounded">.sql</code> atau <code class="text-[11px] font-mono bg-slate-100 px-1 py-0.5 rounded">.zip</code> untuk dipulihkan ke sistem.
                    </p>
                </div>
                <button type="button" @click="openRestoreModal('', 'upload')"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span>Unggah File Restore</span>
                </button>
            </div>
        </div>

        <!-- TABEL DAFTAR FILE BACKUP TERSIMPAN -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-black text-slate-800">Daftar File Cadangan Tersimpan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        File tersimpan aman di direktori privat sistem (<code class="text-slate-600 font-mono text-[11px]">storage/app/backups</code>) dan tidak dapat diakses publik.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="px-3 py-1 rounded-full font-bold bg-white text-slate-700 border border-slate-200">
                        Total: {{ count($backups) }} File ({{ $totalSizeHuman }})
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/60 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200/80">
                        <tr>
                            <th class="px-5 py-3.5">Nama File Cadangan</th>
                            <th class="px-4 py-3.5">Tipe Arsip</th>
                            <th class="px-4 py-3.5">Ukuran</th>
                            <th class="px-4 py-3.5">Waktu Pembuatan</th>
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($backups as $b)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-4 font-mono font-bold text-slate-800 flex items-center gap-2.5">
                                    @if($b['type'] === 'database')
                                        <span class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center font-sans font-black text-xs shrink-0">SQL</span>
                                    @else
                                        <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-sans font-black text-xs shrink-0">ZIP</span>
                                    @endif
                                    <span class="truncate max-w-xs sm:max-w-md" title="{{ $b['filename'] }}">{{ $b['filename'] }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if($b['type'] === 'database')
                                        <span class="px-2 py-0.5 rounded-md font-bold bg-orange-50 text-orange-700 border border-orange-200/70 text-[11px]">Database SQL</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md font-bold bg-teal-50 text-teal-700 border border-teal-200/70 text-[11px]">Berkas Storage</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-mono font-bold text-slate-700">
                                    {{ $b['size_human'] }}
                                </td>
                                <td class="px-4 py-4 text-slate-500 whitespace-nowrap">
                                    {{ $b['created_at_human'] }}
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Tombol Unduh -->
                                        <a href="{{ route('admin.backup.download', $b['filename']) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-xs border border-slate-200"
                                           title="Unduh File">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            <span>Unduh</span>
                                        </a>

                                        <!-- Tombol Restore -->
                                        <button type="button" 
                                                @click="openRestoreModal('{{ $b['filename'] }}', '{{ $b['type'] }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold transition text-xs border border-amber-200/80 cursor-pointer"
                                                title="Pulihkan data dari file ini">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span>Restore</span>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('admin.backup.destroy', $b['filename']) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus file cadangan ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Hapus File Cadangan">
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
                                <td colspan="5" class="px-5 py-12 text-center text-slate-400 space-y-2">
                                    <span class="text-3xl block">📦</span>
                                    <p class="text-sm font-bold text-slate-600">Belum ada file cadangan yang dibuat</p>
                                    <p class="text-xs text-slate-400">Gunakan tombol di atas untuk membuat cadangan Database SQL atau Arsip Berkas Storage.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIWAYAT AUDIT TRAIL AKTIVITAS BACKUP -->
        @if($latestLogs->isNotEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-800">Aktivitas Backup & Restore Terkini</h3>
                    <a href="{{ route('admin.audit-trail.index') }}" class="text-xs font-bold text-nampi-orange hover:underline">
                        Lihat Seluruh Log &rarr;
                    </a>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach($latestLogs as $log)
                        <div class="p-3 bg-slate-50 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-slate-600">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-nampi-orange"></span>
                                <span class="font-medium text-slate-800">{{ $log->description }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono shrink-0">
                                {{ $log->causer?->name ?? 'Sistem' }} &bull; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- MODAL KONFIRMASI RESTORE DATA -->
        <div x-show="restoreModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
                 @click.away="restoreModal = false">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                            ⚠️
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">Konfirmasi Pemulihan (Restore)</h3>
                            <p class="text-xs text-slate-500">Operasi penting pemulihan data sistem</p>
                        </div>
                    </div>
                    <button type="button" @click="restoreModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">
                        &times;
                    </button>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1.5 leading-relaxed">
                    <p class="font-bold flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span>Peringatan Keselamatan Data:</span>
                    </p>
                    <p>
                        Memulihkan data akan menggantikan record database aktif saat ini dengan data yang ada pada file cadangan.
                    </p>
                    <p class="text-[11px] text-amber-800 italic">
                        *Sistem akan secara otomatis membuat <strong>Safety Snapshot Backup</strong> data saat ini sebelum proses pemulihan dieksekusi.
                    </p>
                </div>

                <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Mode Restore dari File Riwayat Tersimpan -->
                    <template x-if="restoreType !== 'upload'">
                        <div>
                            <input type="hidden" name="filename" :value="restoreFilename">
                            <div class="p-3 bg-slate-100 rounded-xl">
                                <span class="text-[11px] font-bold text-slate-500 uppercase block">File yang akan dipulihkan:</span>
                                <span class="text-xs font-mono font-bold text-slate-900 break-all" x-text="restoreFilename"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Mode Upload File Baru -->
                    <template x-if="restoreType === 'upload'">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Pilih File Backup (.sql atau .zip):</label>
                            <input type="file" name="backup_file" required accept=".sql,.zip"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-200 rounded-xl p-1">
                            <p class="text-[11px] text-slate-400">Maksimum ukuran file: 150 MB.</p>
                        </div>
                    </template>

                    <!-- Konfirmasi Ketik RESTORE -->
                    <div class="space-y-1.5 pt-2">
                        <label class="block text-xs font-bold text-slate-800">
                            Ketik kata <span class="text-rose-600 font-mono font-black">RESTORE</span> untuk mengonfirmasi:
                        </label>
                        <input type="text" name="confirm_text" x-model="confirmText" required placeholder="RESTORE"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold tracking-wider uppercase focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="restoreModal = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="confirmText !== 'RESTORE'"
                                :class="confirmText === 'RESTORE' ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-xs cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition">
                            <span>Mulai Proses Pemulihan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
