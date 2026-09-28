<x-layouts.app>
    <x-slot name="title">Terbitkan Tagihan Daftar Ulang Baru</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('bendahara.tagihan.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                ← Kembali ke Daftar Tagihan
            </a>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="max-w-2xl bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
            <div>
                <h1 class="text-xl font-black text-slate-800">Penerbitan Tagihan Daftar Ulang</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Sistem akan mengambil item master tarif biaya aktif dan membekukannya ke dalam <strong>snapshot tagihan</strong> (Section 22 & 33).
                </p>
            </div>

            <form method="POST" action="{{ route('bendahara.tagihan.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="calon_siswa_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pilih Calon Siswa (Sudah Diuji / Diterima):
                    </label>
                    <select id="calon_siswa_id" name="calon_siswa_id" required
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="">-- Pilih Calon Siswa --</option>
                        @foreach ($eligibleCandidates as $cs)
                            <option value="{{ $cs->id }}">
                                {{ $cs->nama_lengkap }} ({{ $cs->nomor_pendaftaran }} - NISN: {{ $cs->nisn }}) — {{ $cs->jurusan?->nama_jurusan }} ({{ $cs->program?->nama_program }})
                            </option>
                        @endforeach
                    </select>
                    @if ($eligibleCandidates->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">
                            Saat ini tidak ada calon siswa tanpa tagihan yang memenuhi syarat penerbitan.
                        </p>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
                    <p class="font-bold text-slate-800">Ketentuan Snapshot Tagihan:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-500">
                        <li>Rincian item biaya dan nominal akan dikunci secara permanen pada saat tagihan dibuat.</li>
                        <li>Perubahan master biaya di masa depan tidak akan memengaruhi nominal tagihan yang sudah terbit.</li>
                        <li>Status calon siswa akan diperbarui menjadi <strong>MENUNGGU_DAFTAR_ULANG</strong>.</li>
                    </ul>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('bendahara.tagihan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit"
                            {{ $eligibleCandidates->isEmpty() ? 'disabled' : '' }}
                            class="px-5 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Terbitkan Snapshot Tagihan →
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
