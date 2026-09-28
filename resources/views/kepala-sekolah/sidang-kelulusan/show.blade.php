<x-layouts.app>
    <x-slot name="title">Evaluasi Sidang Kelulusan - {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('kepala-sekolah.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Back & Action Bar -->
        <div class="flex items-center justify-between">
            <a href="{{ route('kepala-sekolah.sidang-kelulusan.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <span>&larr; Kembali ke Daftar Sidang Pleno</span>
            </a>
            <div class="flex items-center gap-2">
                @if ($keputusan)
                    <a href="{{ route('kepala-sekolah.sidang-kelulusan.cetak-sk', $calonSiswa) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                        <span>📄 Unduh SK Kelulusan Resmi (PDF)</span>
                    </a>
                @endif
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Candidate & Interview Review -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Candidate Profile Summary -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="font-mono text-xs font-bold text-nampi-orange">{{ $calonSiswa->nomor_pendaftaran }}</span>
                            <h2 class="text-lg font-black text-slate-800">{{ $calonSiswa->nama_lengkap }}</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            NISN: {{ $calonSiswa->nisn }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs text-slate-600">
                        <div>
                            <span class="text-slate-400 block">Pilihan Kompetensi Keahlian</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $calonSiswa->jurusan?->nama_jurusan }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Program & Gelombang</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $calonSiswa->program?->nama_program }} &bull; {{ $calonSiswa->gelombang?->nama_gelombang }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Asal Sekolah SMP</span>
                            <span class="font-medium text-slate-800 mt-0.5 block">{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nama Orang Tua / Wali</span>
                            <span class="font-medium text-slate-800 mt-0.5 block">{{ $calonSiswa->dataOrangtua?->nama_ayah ?? $calonSiswa->dataOrangtua?->nama_ibu ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hasil Evaluasi Wawancara -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Hasil Evaluasi Tes Wawancara</h3>
                            <p class="text-[11px] text-slate-400">Penguji: {{ $wawancara?->pewawancara?->name ?? 'Pewawancara' }} &bull; {{ $wawancara?->tanggal_wawancara?->format('d F Y') }}</p>
                        </div>
                        @if ($wawancara)
                            @php
                                $avgScore = $wawancara->details->count() > 0 ? round($wawancara->details->avg('skor')) : null;
                            @endphp
                            <span class="px-3 py-1 rounded-xl text-xs font-black {{ $avgScore >= 75 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                Skor Total: {{ $avgScore }}/100
                            </span>
                        @endif
                    </div>

                    @if ($wawancara && $wawancara->details->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-600">
                                <thead class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                                    <tr>
                                        <th class="px-4 py-2.5">Kriteria Penilaian</th>
                                        <th class="px-3 py-2.5 text-center">Skor</th>
                                        <th class="px-3 py-2.5 text-center">Indikator</th>
                                        <th class="px-4 py-2.5">Catatan Penguji</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($wawancara->details as $item)
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-800">
                                                {{ $item->kriteria?->nama_kriteria ?? $item->nama_kriteria_snapshot ?? '-' }}
                                            </td>
                                            <td class="px-3 py-3 text-center font-bold text-slate-900">
                                                {{ $item->skor }}
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                @if ($item->warna === 'HIJAU')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Sangat Baik</span>
                                                @elseif ($item->warna === 'ORANYE')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Cukup</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Kurang</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">
                                                {{ $item->catatan_kriteria ?: '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Catatan Ringkasan Pewawancara -->
                        <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs space-y-2">
                            @if ($wawancara->catatan_siswa)
                                <p><strong class="text-slate-800">Catatan Wawancara Siswa:</strong> {{ $wawancara->catatan_siswa }}</p>
                            @endif
                            @if ($wawancara->catatan_orangtua)
                                <p><strong class="text-slate-800">Catatan Orang Tua:</strong> {{ $wawancara->catatan_orangtua }}</p>
                            @endif
                            @if ($wawancara->kesepahaman_orangtua)
                                <p class="text-emerald-700 font-medium">✓ Orang tua telah menyepakati komitmen pendampingan dan pembiayaan sekolah.</p>
                            @endif
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Kandidat ini belum memiliki instrumen penilaian wawancara yang tersimpan.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Decision Form & Status -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Status Keputusan Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-slate-800">Keputusan Sidang Pleno</h3>
                        @if ($keputusan)
                            @if ($keputusan->isDiterima())
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ LULUS / DITERIMA
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">
                                    ✕ TIDAK DITERIMA
                                </span>
                            @endif
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                ⏳ Menunggu Keputusan
                            </span>
                        @endif
                    </div>

                    @if ($keputusan)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5 text-slate-600">
                            <p><strong class="text-slate-800">Ditetapkan Oleh:</strong> {{ $keputusan->ditetapkanOleh?->name ?? 'Kepala Sekolah' }}</p>
                            <p><strong class="text-slate-800">Waktu Penetapan:</strong> {{ $keputusan->ditetapkan_at?->format('d F Y H:i') }}</p>
                            <p class="pt-2 border-t border-slate-200"><strong class="text-slate-800">Catatan Sidang:</strong> {{ $keputusan->alasan_catatan }}</p>
                        </div>
                    @endif

                    <!-- Decision Form -->
                    <form method="POST" action="{{ route('kepala-sekolah.sidang-kelulusan.putuskan', $calonSiswa) }}" class="space-y-4 pt-2">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tetapkan / Perbarui Keputusan <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="p-3 rounded-xl border-2 cursor-pointer flex items-center gap-2.5 transition-colors {{ ($keputusan?->keputusan ?? 'DITERIMA') === 'DITERIMA' ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200' }}">
                                    <input type="radio" name="keputusan" value="DITERIMA" {{ ($keputusan?->keputusan ?? 'DITERIMA') === 'DITERIMA' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-black text-emerald-800">DITERIMA</span>
                                </label>
                                <label class="p-3 rounded-xl border-2 cursor-pointer flex items-center gap-2.5 transition-colors {{ ($keputusan?->keputusan ?? '') === 'DITOLAK' ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }}">
                                    <input type="radio" name="keputusan" value="DITOLAK" {{ ($keputusan?->keputusan ?? '') === 'DITOLAK' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                    <span class="text-xs font-black text-rose-800">DITOLAK</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Keputusan / Alasan Sidang</label>
                            <textarea name="alasan_catatan" rows="3"
                                      placeholder="Tuliskan catatan hasil sidang pleno kelulusan..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">{{ old('alasan_catatan', $keputusan?->alasan_catatan) }}</textarea>
                        </div>

                        <button type="submit" onclick="return confirm('Tetapkan hasil sidang pleno ini untuk calon siswa?')"
                                class="w-full py-3 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-wider hover:bg-slate-800 transition-colors shadow-xs cursor-pointer">
                            Simpan Keputusan Sidang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
