<x-layouts.app>
    <x-slot name="title">Hasil Wawancara — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('pewawancara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Nav -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('pewawancara.riwayat') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    ← Kembali ke Riwayat
                </a>
                <h1 class="text-2xl font-black text-slate-800">Hasil Penilaian Wawancara</h1>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan evaluasi seleksi calon siswa dan kesepahaman orang tua.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pewawancara.wawancara.form', $calonSiswa) }}"
                   class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                    ✏️ Edit Penilaian
                </a>
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

        <!-- Candidate & Interview Overview -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($calonSiswa->dokumenPendaftaran?->pas_foto_path && file_exists(public_path('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path)))
                        <img src="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path) }}"
                             alt="Pas Foto"
                             class="w-16 h-20 object-cover rounded-xl border-2 border-white/30 shadow-md shrink-0">
                    @else
                        <div class="w-16 h-20 rounded-xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-slate-400 shrink-0">
                            <span class="text-2xl">👤</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-nampi-cyan font-bold">
                            {{ $calonSiswa->nomor_pendaftaran }}
                        </span>
                        <h2 class="text-xl font-black text-white mt-0.5">{{ $calonSiswa->nama_lengkap }}</h2>
                        <p class="text-xs text-slate-300 mt-0.5 font-mono">NISN: {{ $calonSiswa->nisn }} • Asal: {{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->sekolah_asal_text ?? '-' }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-400/30">
                                Jurusan: {{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-500/20 text-purple-300 border border-purple-400/30">
                                Program: {{ $calonSiswa->programBelajar?->nama_program ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Status: {{ $wawancara?->status ?? 'SELESAI' }}
                    </span>
                    <p class="text-xs text-slate-300 mt-2">
                        Pewawancara: <strong>{{ $wawancara?->pewawancara?->name ?? '-' }}</strong>
                    </p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Tgl Uji: {{ $wawancara?->tanggal_wawancara?->format('d F Y') ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Scoring Breakdown Table -->
            <div class="p-6 space-y-6">
                <h3 class="text-base font-bold text-slate-800">Rincian Nilai & Indikator Rubrik</h3>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Kriteria Penilaian</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3 text-center">Skor (0-100)</th>
                                <th class="px-4 py-3 text-center">Indikator Warna</th>
                                <th class="px-4 py-3">Catatan Pengamatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $totalSkor = 0;
                                $count = 0;
                            @endphp
                            @forelse ($wawancara?->details ?? [] as $det)
                                @php
                                    $totalSkor += $det->nilai;
                                    $count++;
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-mono font-bold text-slate-500">
                                        {{ $det->kriteria?->kode ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $det->kriteria?->nama_kriteria ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($det->kriteria?->jenis_penilaian === 'orang_tua')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">Orang Tua</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">Siswa</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-black text-sm text-slate-800">
                                        {{ $det->nilai }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($det->warna === 'HIJAU')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                HIJAU ({{ $det->indikator ?? 'Baik' }})
                                            </span>
                                        @elseif ($det->warna === 'ORANYE')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                ORANYE ({{ $det->indikator ?? 'Cukup' }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                MERAH ({{ $det->indikator ?? 'Kurang' }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $det->catatan ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                        Rincian rubrik penilaian belum diinput.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($count > 0)
                            <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-xs">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right uppercase tracking-wider text-slate-700">
                                        Rata-rata Skor Wawancara:
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-black text-nampi-orange">
                                        {{ number_format($totalSkor / $count, 1) }} / 100
                                    </td>
                                    <td colspan="2" class="px-4 py-3 text-slate-500">
                                        Evaluasi terisi {{ $count }} indikator
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Notes Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan Umum & Rekomendasi Siswa</h4>
                        <p class="text-xs text-slate-600 whitespace-pre-line leading-relaxed">
                            {{ $wawancara?->catatan_umum ?: 'Tidak ada catatan umum.' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Hasil / Catatan Wawancara Orang Tua</h4>
                        <p class="text-xs text-slate-600 whitespace-pre-line leading-relaxed">
                            {{ $wawancara?->catatan_orang_tua ?: 'Tidak ada catatan wawancara orang tua.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
