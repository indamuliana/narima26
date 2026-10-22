<x-layouts.app>
    <x-slot name="title">Pengaturan SPMB & Manajemen Gelombang — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Pengaturan SPMB & Gelombang</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola gelombang pendaftaran, status buka/tutup pendaftaran online, tahun ajaran, dan narahubung CS</p>
            </div>
            @if ($activeTab === 'gelombang')
                <div>
                    <button type="button" onclick="document.getElementById('modalTambahGelombang').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Tambah Gelombang Baru</span>
                    </button>
                </div>
            @endif
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

        <!-- Tab Navigation Bar -->
        <div class="flex items-center gap-2 p-1.5 bg-slate-100/90 rounded-2xl w-fit border border-slate-200/60">
            <a href="{{ route('admin.pengaturan.index', ['tab' => 'gelombang']) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'gelombang' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'gelombang' ? 'text-orange-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Gelombang Pendaftaran</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'gelombang' ? 'bg-orange-50 text-orange-700' : 'bg-slate-200 text-slate-600' }}">
                    {{ $gelombangList->count() }}
                </span>
            </a>

            <a href="{{ route('admin.pengaturan.index', ['tab' => 'umum']) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'umum' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'umum' ? 'text-orange-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Pengaturan Umum & Helpdesk CS</span>
            </a>
        </div>

        @if ($activeTab === 'gelombang')
            <!-- TAB 1: MANAJEMEN GELOMBANG -->
            <div class="space-y-6">
                <!-- KPI Summary Cards -->
                @php
                    $gelAktif = $gelombangList->firstWhere('aktif', true);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Gelombang</span>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $gelombangList->count() }} Gelombang</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                            #
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Gelombang Aktif Saat Ini</span>
                            <p class="text-2xl font-black text-emerald-600 mt-1">
                                {{ $gelAktif ? $gelAktif->nama : 'Tidak Ada Aktif' }}
                            </p>
                            @if ($gelAktif)
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $gelAktif->periode_mulai?->format('d M Y') }} s.d. {{ $gelAktif->periode_selesai?->format('d M Y') }}
                                </p>
                            @endif
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            &check;
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-nampi-orange uppercase tracking-wider">Pendaftar di Gelombang Aktif</span>
                            <p class="text-2xl font-black text-nampi-orange mt-1">
                                {{ $gelAktif ? $gelAktif->calon_siswa_count : 0 }} Murid
                            </p>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Dari total {{ $gelombangList->sum('calon_siswa_count') }} seluruh pendaftar
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold">
                            👥
                        </div>
                    </div>
                </div>

                <!-- Table Gelombang -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-slate-800">Daftar Gelombang Pendaftaran</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Klik tombol "Aktifkan" untuk mengganti gelombang aktif yang menjadi acuan pendaftaran siswa baru</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                                <tr>
                                    <th class="px-5 py-3.5" style="width: 12%;">Kode</th>
                                    <th class="px-5 py-3.5" style="width: 25%;">Nama Gelombang</th>
                                    <th class="px-5 py-3.5" style="width: 25%;">Periode Tanggal Buka - Tutup</th>
                                    <th class="px-4 py-3.5 text-center" style="width: 12%;">Peminat Terdaftar</th>
                                    <th class="px-4 py-3.5 text-center" style="width: 12%;">Status</th>
                                    <th class="px-5 py-3.5 text-right" style="width: 14%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($gelombangList as $gel)
                                    <tr class="hover:bg-slate-50/70 transition-colors {{ $gel->aktif ? 'bg-emerald-50/20' : '' }}">
                                        <td class="px-5 py-4 font-mono font-bold text-slate-900 text-sm">
                                            {{ $gel->kode }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="font-bold text-slate-900 text-sm block">{{ $gel->nama }}</span>
                                            <span class="text-slate-400 text-[11px]">T.A. {{ $gel->tahun_ajaran }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-slate-800">
                                                {{ $gel->periode_mulai ? $gel->periode_mulai->translatedFormat('d F Y') : '-' }}
                                                <span class="text-slate-400 font-normal">s.d.</span>
                                                {{ $gel->periode_selesai ? $gel->periode_selesai->translatedFormat('d F Y') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                                {{ $gel->calon_siswa_count }} Murid
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if ($gel->aktif)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span>Sedang Aktif</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <!-- Tombol Aktifkan / Ganti Status -->
                                                <form method="POST" action="{{ route('admin.pengaturan.gelombang.toggle', $gel) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors cursor-pointer {{ $gel->aktif ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs' }}"
                                                            title="{{ $gel->aktif ? 'Klik untuk nonaktifkan' : 'Jadikan gelombang aktif utama' }}">
                                                        {{ $gel->aktif ? 'Tutup' : 'Aktifkan' }}
                                                    </button>
                                                </form>

                                                <!-- Tombol Edit Modal -->
                                                <button type="button" onclick="bukaModalEditGelombang({{ $gel->id }}, '{{ addslashes($gel->kode) }}', '{{ addslashes($gel->nama) }}', '{{ $gel->periode_mulai?->format('Y-m-d') }}', '{{ $gel->periode_selesai?->format('Y-m-d') }}', {{ $gel->aktif ? 'true' : 'false' }})"
                                                        class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer" title="Edit Gelombang">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>

                                                <!-- Tombol Hapus (Dicegah jika ada murid) -->
                                                @if ($gel->calon_siswa_count == 0)
                                                    <form method="POST" action="{{ route('admin.pengaturan.gelombang.destroy', $gel) }}"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $gel->nama }}?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Hapus Gelombang">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="p-1.5 text-slate-300 cursor-not-allowed" title="Tidak dapat dihapus karena ada {{ $gel->calon_siswa_count }} calon murid terdaftar">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                            Belum ada data gelombang pendaftaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- TAB 2: PENGATURAN UMUM & CS WHATSAPP -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden max-w-4xl">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Pengaturan Konfigurasi Umum SPMB</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi tahun ajaran resmi, status buka pendaftaran global, dan narahubung CS WhatsApp</p>
                    </div>
                </div>

                <form action="{{ route('admin.pengaturan.umum.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- 1. Tahun Ajaran & Status Global -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Tahun Ajaran SPMB <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="tahun_ajaran" required
                                   value="{{ old('tahun_ajaran', $pengaturan->tahun_ajaran) }}"
                                   placeholder="Contoh: 2027/2028"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                            <p class="text-[11px] text-slate-400">Digunakan pada kop surat, kartu peserta, dan cetak dokumen resmi</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Status Pendaftaran Online Global
                            </label>
                            <div class="pt-2">
                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_pendaftaran_buka" value="1"
                                           {{ old('is_pendaftaran_buka', $pengaturan->is_pendaftaran_buka) ? 'checked' : '' }}
                                           class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500">
                                    <span class="text-xs font-bold text-slate-800">Buka Pendaftaran Calon Siswa Baru di Website</span>
                                </label>
                                <p class="text-[11px] text-slate-400 mt-1">Jika dinonaktifkan, formulir pendaftaran baru di beranda tidak dapat diisi</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Pesan Penutupan Pendaftaran -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pesan Pengumuman / Saat Pendaftaran Ditutup
                        </label>
                        <textarea name="pesan_penutupan" rows="3"
                                  class="w-full p-3 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none leading-relaxed"
                                  placeholder="Pesan yang ditampilkan jika pendaftaran ditutup...">{{ old('pesan_penutupan', $pengaturan->pesan_penutupan) }}</textarea>
                    </div>

                    <!-- 3. Narahubung CS WhatsApp -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-orange-600">Narahubung & Helpdesk WhatsApp</h3>
                        <p class="text-xs text-slate-500">Nomor ini otomatis terhubung pada tombol melayang WhatsApp di seluruh portal pendaftaran dan dashboard siswa</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    WhatsApp CS Reguler / Umum <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="wa_cs_reguler" required
                                           value="{{ old('wa_cs_reguler', $pengaturan->wa_cs_reguler) }}"
                                           placeholder="628112232880"
                                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">WA</span>
                                </div>
                                <p class="text-[11px] text-slate-400">Gunakan format angka dengan kode negara 62 (contoh: 628112232880)</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    WhatsApp CS Program Unggulan (Asrama/Santri) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="wa_cs_unggulan" required
                                           value="{{ old('wa_cs_unggulan', $pengaturan->wa_cs_unggulan) }}"
                                           placeholder="6281122802880"
                                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">WA</span>
                                </div>
                                <p class="text-[11px] text-slate-400">Narahubung khusus konsultasi asrama, santri, dan tahfidz</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Pengaturan Umum</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Modal Tambah Gelombang -->
    <div id="modalTambahGelombang" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">+ Tambah Gelombang Baru</h3>
                <button type="button" onclick="document.getElementById('modalTambahGelombang').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pengaturan.gelombang.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Kode Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode" required placeholder="Contoh: GEL4"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 uppercase focus:ring-2 focus:ring-orange-500 outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Nama Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Gelombang 4 (Khusus)"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="periode_mulai" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="periode_selesai" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500">
                        <span class="text-xs font-semibold text-slate-700">Jadikan Gelombang Aktif Saat Ini</span>
                    </label>
                    <p class="text-[10px] text-slate-400 mt-0.5">Jika dicentang, gelombang aktif lainnya otomatis akan dinonaktifkan</p>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalTambahGelombang').classList.add('hidden')"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 shadow-xs cursor-pointer">
                        Simpan Gelombang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Gelombang -->
    <div id="modalEditGelombang" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Edit Gelombang Pendaftaran</h3>
                <button type="button" onclick="document.getElementById('modalEditGelombang').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="formEditGelombang" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Kode Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_kode" name="kode" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 uppercase focus:ring-2 focus:ring-orange-500 outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Nama Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_nama" name="nama" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" id="edit_periode_mulai" name="periode_mulai" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" id="edit_periode_selesai" name="periode_selesai" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="edit_aktif" name="aktif" value="1" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500">
                        <span class="text-xs font-semibold text-slate-700">Status Aktif (Gelombang Utama)</span>
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEditGelombang').classList.add('hidden')"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 shadow-xs cursor-pointer">
                        Perbarui Gelombang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalEditGelombang(id, kode, nama, mulai, selesai, aktif) {
            document.getElementById('edit_kode').value = kode;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_periode_mulai').value = mulai || '';
            document.getElementById('edit_periode_selesai').value = selesai || '';
            document.getElementById('edit_aktif').checked = aktif;

            const form = document.getElementById('formEditGelombang');
            form.action = `{{ url('admin/pengaturan/gelombang') }}/${id}`;

            document.getElementById('modalEditGelombang').classList.remove('hidden');
        }
    </script>
</x-layouts.app>
