<x-layouts.app>
    <x-slot name="title">Lembar Jawaban TPA — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.tpa.hasil.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Rekapitulasi Nilai</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800">Lembar Jawaban & Analisis TPA</h1>
                <p class="text-xs text-slate-500 mt-0.5">Audit butir jawaban per soal, kunci penilaian, dan catatan aktivitas integritas pengerjaan</p>
            </div>
        </div>

        <!-- Student Profile & Score Summary Card -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1 md:border-r md:border-slate-100 md:pr-6">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                    Calon Peserta Didik
                </span>
                <h2 class="text-lg font-black text-slate-900 mt-2">{{ $calonSiswa->nama_lengkap }}</h2>
                <p class="text-xs text-slate-500">No. Daftar: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong> &bull; NISN: {{ $calonSiswa->nisn ?? '-' }}</p>
                <p class="text-xs text-slate-500">Jurusan: <strong>{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</strong></p>
                <p class="text-xs text-slate-500">Gelombang: {{ $calonSiswa->gelombang?->nama_gelombang ?? '-' }}</p>
            </div>

            <div class="space-y-2 md:border-r md:border-slate-100 md:pr-6">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Status & Waktu Pengerjaan</p>
                @if ($sesi)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-600">Status Ujian:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $sesi->status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($sesi->status === 'didiskualifikasi' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                            {{ strtoupper($sesi->status) }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 space-y-0.5 pt-1">
                        <p>Babak 1 (Lit): {{ $sesi->babak_1_mulai ? $sesi->babak_1_mulai->format('H:i:s') : '-' }} s.d. {{ $sesi->babak_1_selesai ? $sesi->babak_1_selesai->format('H:i:s') : '-' }}</p>
                        <p>Babak 2 (Num): {{ $sesi->babak_2_mulai ? $sesi->babak_2_mulai->format('H:i:s') : '-' }} s.d. {{ $sesi->babak_2_selesai ? $sesi->babak_2_selesai->format('H:i:s') : '-' }}</p>
                        <p>IP Address: <span class="font-mono text-slate-700">{{ $sesi->ip_address ?? '-' }}</span></p>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Siswa belum memulai sesi ujian TPA.</p>
                @endif
            </div>

            <div class="flex flex-col justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Skor Akhir</p>
                @if ($sesi && in_array($sesi->status, ['selesai', 'didiskualifikasi']))
                    <div class="my-auto">
                        <div class="text-3xl font-black text-orange-600">{{ number_format($sesi->total_skor, 0) }} <span class="text-xs font-bold text-slate-400">Poin</span></div>
                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-3">
                            <span>Literasi: <strong>{{ number_format($sesi->skor_literasi, 0) }}</strong></span>
                            <span>&bull;</span>
                            <span>Numerasi: <strong>{{ number_format($sesi->skor_numerasi, 0) }}</strong></span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic my-auto">Skor belum terkalkulasi</p>
                @endif
            </div>
        </div>

        <!-- Audit Log Pelanggaran (Jika Ada) -->
        @if ($sesi && $sesi->jumlah_pelanggaran > 0)
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl space-y-2">
                <div class="flex items-center gap-2 text-rose-800 text-xs font-bold">
                    <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Terdeteksi {{ $sesi->jumlah_pelanggaran }} Pelanggaran Selama Ujian:</span>
                </div>
                <div class="space-y-1">
                    @foreach ($sesi->log_pelanggaran ?? [] as $idx => $log)
                        <div class="text-[11px] text-rose-700 flex items-center justify-between bg-white/60 p-2 rounded-lg border border-rose-200/50">
                            <span><strong>#{{ $idx + 1 }}</strong> {{ $log['alasan'] ?? 'Beralih tab/layar' }}</span>
                            <span class="text-slate-500 font-mono">{{ isset($log['waktu']) ? \Carbon\Carbon::parse($log['waktu'])->format('d/m/Y H:i:s') : '-' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Rincian Lembar Jawaban Babak 1: Literasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <h3 class="text-sm font-bold text-slate-800">Babak 1: Literasi</h3>
                </div>
                @if ($sesi)
                    <div class="text-xs font-semibold text-slate-600">
                        Skor: <strong class="text-blue-600">{{ number_format($sesi->skor_literasi, 0) }}</strong> &bull;
                        {{ $sesi->benar_literasi }} Benar, {{ $sesi->salah_literasi }} Salah, {{ $sesi->kosong_literasi }} Kosong
                    </div>
                @endif
            </div>

            <div class="p-6 divide-y divide-slate-100 space-y-6">
                @forelse ($soalLiterasi as $no => $soal)
                    @php
                        $jawaban = $jawabanMap->get($soal->id);
                        $dipilih = $jawaban?->jawaban_dipilih;
                        $kunci = $soal->kunci_jawaban;
                        $isBenar = $dipilih && (strtoupper($dipilih) === strtoupper($kunci));
                    @endphp
                    <div class="pt-4 first:pt-0 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs shrink-0">
                                    {{ $no + 1 }}
                                </span>
                                <div class="space-y-2">
                                    <p class="text-sm text-slate-800 leading-relaxed font-medium">{{ $soal->pertanyaan }}</p>
                                    @if ($soal->gambar_path)
                                        <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Gambar Soal"
                                             class="max-w-md max-h-56 object-contain rounded-xl border border-slate-200 my-2">
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 text-right space-y-1">
                                @if (empty($dipilih))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Tidak Dijawab
                                    </span>
                                @elseif ($isBenar)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Benar (+{{ $soal->bobot }} Poin)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Salah (0 Poin)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Opsi Jawaban -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-10">
                            @foreach ($soal->opsi_jawaban ?? [] as $op)
                                @php
                                    $isDipilih = ($dipilih === $op['key']);
                                    $isKunci = ($kunci === $op['key']);
                                @endphp
                                <div class="p-2.5 rounded-xl border text-xs flex items-center gap-2
                                    {{ $isKunci ? 'bg-emerald-50 border-emerald-300 font-bold text-emerald-900' : ($isDipilih ? 'bg-rose-50 border-rose-300 text-rose-900 font-medium' : 'bg-slate-50/50 border-slate-200 text-slate-600') }}">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center font-bold text-[11px] {{ $isKunci ? 'bg-emerald-600 text-white' : ($isDipilih ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700') }}">
                                        {{ $op['key'] }}
                                    </span>
                                    <span class="truncate">{{ $op['text'] }}</span>
                                    @if ($isKunci)
                                        <span class="ml-auto text-[10px] font-bold text-emerald-700 uppercase">(Kunci)</span>
                                    @elseif ($isDipilih)
                                        <span class="ml-auto text-[10px] font-bold text-rose-700 uppercase">(Dipilih)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada butir soal literasi yang dikerjakan.</p>
                @endforelse
            </div>
        </div>

        <!-- Rincian Lembar Jawaban Babak 2: Numerasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-bold text-slate-800">Babak 2: Numerasi</h3>
                </div>
                @if ($sesi)
                    <div class="text-xs font-semibold text-slate-600">
                        Skor: <strong class="text-emerald-600">{{ number_format($sesi->skor_numerasi, 0) }}</strong> &bull;
                        {{ $sesi->benar_numerasi }} Benar, {{ $sesi->salah_numerasi }} Salah, {{ $sesi->kosong_numerasi }} Kosong
                    </div>
                @endif
            </div>

            <div class="p-6 divide-y divide-slate-100 space-y-6">
                @forelse ($soalNumerasi as $no => $soal)
                    @php
                        $jawaban = $jawabanMap->get($soal->id);
                        $dipilih = $jawaban?->jawaban_dipilih;
                        $kunci = $soal->kunci_jawaban;
                        $isBenar = $dipilih && (strtoupper($dipilih) === strtoupper($kunci));
                    @endphp
                    <div class="pt-4 first:pt-0 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs shrink-0">
                                    {{ $no + 1 }}
                                </span>
                                <div class="space-y-2">
                                    <p class="text-sm text-slate-800 leading-relaxed font-medium">{{ $soal->pertanyaan }}</p>
                                    @if ($soal->gambar_path)
                                        <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Gambar Soal"
                                             class="max-w-md max-h-56 object-contain rounded-xl border border-slate-200 my-2">
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 text-right space-y-1">
                                @if (empty($dipilih))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Tidak Dijawab
                                    </span>
                                @elseif ($isBenar)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Benar (+{{ $soal->bobot }} Poin)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Salah (0 Poin)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Opsi Jawaban -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-10">
                            @foreach ($soal->opsi_jawaban ?? [] as $op)
                                @php
                                    $isDipilih = ($dipilih === $op['key']);
                                    $isKunci = ($kunci === $op['key']);
                                @endphp
                                <div class="p-2.5 rounded-xl border text-xs flex items-center gap-2
                                    {{ $isKunci ? 'bg-emerald-50 border-emerald-300 font-bold text-emerald-900' : ($isDipilih ? 'bg-rose-50 border-rose-300 text-rose-900 font-medium' : 'bg-slate-50/50 border-slate-200 text-slate-600') }}">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center font-bold text-[11px] {{ $isKunci ? 'bg-emerald-600 text-white' : ($isDipilih ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700') }}">
                                        {{ $op['key'] }}
                                    </span>
                                    <span class="truncate">{{ $op['text'] }}</span>
                                    @if ($isKunci)
                                        <span class="ml-auto text-[10px] font-bold text-emerald-700 uppercase">(Kunci)</span>
                                    @elseif ($isDipilih)
                                        <span class="ml-auto text-[10px] font-bold text-rose-700 uppercase">(Dipilih)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada butir soal numerasi yang dikerjakan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
