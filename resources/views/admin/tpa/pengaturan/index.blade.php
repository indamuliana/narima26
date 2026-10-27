<x-layouts.app>
    <x-slot name="title">Pengaturan Tes Potensi Akademik (TPA) — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Pengaturan Tes Potensi Akademik (TPA)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Konfigurasi batas durasi waktu per babak, toleransi anti-kecurangan, dan petunjuk teknis pengerjaan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.tpa.soal.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Bank Soal TPA</span>
                </a>

                <!-- Tombol Coba Ujian (Simulasi Admin) -->
                <button type="button" onclick="document.getElementById('modalSimulasiUjian').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition-all shadow-xs cursor-pointer">
                    <svg class="w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Coba Ujian (Simulasi)</span>
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

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Parameter Ujian & Toleransi Integritas</h2>
                    <p class="text-xs text-slate-500">Perubahan parameter akan langsung berlaku untuk sesi ujian baru</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $pengaturan->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $pengaturan->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    {{ $pengaturan->is_active ? 'Modul TPA Aktif' : 'Modul TPA Nonaktif' }}
                </span>
            </div>

            <form action="{{ route('admin.tpa.pengaturan.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Durasi Babak 1 (Literasi) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Durasi Babak 1 (Literasi) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="durasi_literasi_menit" min="5" max="240" required
                                   value="{{ old('durasi_literasi_menit', $pengaturan->durasi_literasi_menit) }}"
                                   class="w-full pl-3.5 pr-14 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Menit</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Batas pengerjaan soal literasi (default 45 menit)</p>
                    </div>

                    <!-- Durasi Babak 2 (Numerasi) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Durasi Babak 2 (Numerasi) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="durasi_numerasi_menit" min="5" max="240" required
                                   value="{{ old('durasi_numerasi_menit', $pengaturan->durasi_numerasi_menit) }}"
                                   class="w-full pl-3.5 pr-14 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Menit</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Batas pengerjaan soal numerasi (default 45 menit)</p>
                    </div>

                    <!-- Toleransi Pelanggaran -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Toleransi Tab-Switch / Blur <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="max_pelanggaran" min="1" max="20" required
                                   value="{{ old('max_pelanggaran', $pengaturan->max_pelanggaran) }}"
                                   class="w-full pl-3.5 pr-14 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Kali</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Ujian otomatis disubmit/kunci jika pelanggaran mencapai batas ini</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <!-- Petunjuk Literasi -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Petunjuk Babak 1 (Literasi)
                        </label>
                        <textarea name="petunjuk_literasi" rows="4"
                                  class="w-full p-3 rounded-xl border border-slate-200 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"
                                  placeholder="Tuliskan petunjuk pengerjaan soal literasi...">{{ old('petunjuk_literasi', $pengaturan->petunjuk_literasi) }}</textarea>
                    </div>

                    <!-- Petunjuk Numerasi -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Petunjuk Babak 2 (Numerasi)
                        </label>
                        <textarea name="petunjuk_numerasi" rows="4"
                                  class="w-full p-3 rounded-xl border border-slate-200 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"
                                  placeholder="Tuliskan petunjuk pengerjaan soal numerasi...">{{ old('petunjuk_numerasi', $pengaturan->petunjuk_numerasi) }}</textarea>
                    </div>
                </div>

                <!-- Status Aktif Toggle -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $pengaturan->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                        <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer select-none">
                            Aktifkan Sistem Tes Potensi Akademik (Siswa diizinkan login jika jadwal cocok)
                        </label>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.tpa.partials.modal-simulasi')
</x-layouts.app>
