<div class="space-y-6">
    <!-- 1. Funnel Konversi Pendaftaran SPMB -->
    <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 gap-2">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-[11px] font-bold text-blue-700">
                    <span>⚡ Analisis Konversi Alur</span>
                </div>
                <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                    Funnel Konversi Alur Seleksi & Pendaftaran
                </h3>
                <p class="text-xs text-slate-500">
                    Tingkat kelanjutan calon peserta didik baru dari registrasi awal hingga resmi daftar ulang (retensi peserta).
                </p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl self-start sm:self-auto">
                Basis Data: {{ $funnel['total'] }} Akun Pendaftar
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            @foreach ($funnel['steps'] as $index => $step)
                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 flex flex-col justify-between space-y-3 relative overflow-hidden group hover:bg-white hover:border-slate-300 hover:shadow-xs transition-all h-full min-h-[145px]">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-sm shadow-xs border border-slate-200/60">
                            {{ $step['icon'] }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black border {{ $step['badge_class'] }}">
                            {{ $step['pct'] }}%
                        </span>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block leading-tight">
                            {{ $step['name'] }}
                        </span>
                        <p class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                            {{ number_format($step['count']) }}
                            <span class="text-[11px] font-semibold text-slate-400">siswa</span>
                        </p>
                    </div>

                    <!-- Mini Progress bar -->
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full bg-gradient-to-r {{ $step['color'] }} transition-all duration-500"
                             style="width: {{ $step['pct'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2. Grid Wawasan Tambahan: Demografi & Asal Sekolah Feeder -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Demografi & Jalur Program (6 Cols) -->
        <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-5 h-full">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Demografi & Preferensi Pilihan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Proporsi gender dan peminatan program pendidikan</p>
                </div>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold">Profil Siswa</span>
            </div>

            <!-- Gender Distribution -->
            <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-800">Distribusi Jenis Kelamin</span>
                    <span class="text-slate-500 text-[11px]">
                        Laki-laki: <strong>{{ $demografi['gender']['laki'] }}</strong> &bull; Perempuan: <strong>{{ $demografi['gender']['perempuan'] }}</strong>
                    </span>
                </div>

                <!-- Combined Progress Bar -->
                <div class="w-full h-3 rounded-full bg-slate-200 overflow-hidden flex">
                    <div class="bg-blue-600 h-full transition-all duration-500" style="width: {{ $demografi['gender']['laki_pct'] }}%" title="Laki-laki: {{ $demografi['gender']['laki_pct'] }}%"></div>
                    <div class="bg-pink-500 h-full transition-all duration-500" style="width: {{ $demografi['gender']['perempuan_pct'] }}%" title="Perempuan: {{ $demografi['gender']['perempuan_pct'] }}%"></div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-600 pt-0.5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <span>Laki-laki ({{ $demografi['gender']['laki_pct'] }}%)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                        <span>Perempuan ({{ $demografi['gender']['perempuan_pct'] }}%)</span>
                    </div>
                </div>
            </div>

            <!-- Program Peminatan (Reguler vs Unggulan) -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-700 block">Peminatan Program Sekolah</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($demografi['programs'] as $prg)
                        <div class="p-3.5 rounded-2xl border {{ strtolower($prg['nama']) === 'unggulan' ? 'border-amber-200 bg-amber-50/40' : 'border-blue-200 bg-blue-50/40' }} flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-black text-slate-900">Program {{ $prg['nama'] }}</span>
                                    @if (strtolower($prg['nama']) === 'unggulan')
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-200 text-amber-900">+ Asrama</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">{{ $prg['pct'] }}% dari total</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black text-slate-900">{{ $prg['count'] }}</span>
                                <span class="text-[10px] text-slate-400 block">Siswa</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sumber Informasi / Promotor -->
            @if ($demografi['referensi_stats']->isNotEmpty())
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-700 block">Saluran Referensi & Promosi Teratas</span>
                    <div class="space-y-1.5">
                        @foreach ($demografi['referensi_stats']->take(3) as $ref)
                            <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50">
                                <span class="text-slate-700 font-medium truncate pr-2">{{ $ref['label'] }}</span>
                                <span class="font-bold text-slate-900 shrink-0">{{ $ref['count'] }} calon ({{ $ref['pct'] }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Asal Sekolah SMP / MTs Teratas (Feeder Schools) (6 Cols) -->
        <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-5 h-full">
            <div>
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Top Sekolah Asal (Feeder Schools)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Asal SMP/MTs calon peserta didik dengan jumlah pendaftar tertinggi</p>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                        Top 5 SMP
                    </span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($topSekolah['top'] as $idx => $sch)
                        <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/80 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0 pr-3">
                                <span class="w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $idx === 0 ? 'bg-amber-400 text-slate-900 shadow-xs' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : ($idx === 2 ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700')) }}">
                                    #{{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 text-xs truncate">{{ $sch['nama'] }}</h4>
                                    <span class="text-[11px] text-slate-500 block truncate">{{ $sch['kota'] }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-slate-900 text-sm block">{{ $sch['total_siswa'] }} Siswa</span>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $sch['persentase'] }}% dari total</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <p class="font-semibold text-slate-600">Belum ada data asal sekolah terdata</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Data terisi otomatis ketika pendaftar melengkapi biodata.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-200 text-indigo-950 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span>💡</span>
                    <span class="text-[11px] leading-tight">Data feeder school berguna untuk program apresiasi & target sosialisasi SPMB berikutnya.</span>
                </div>
            </div>
        </div>

    </div>
</div>
