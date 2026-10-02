@php
    $pStats = $programStats ?? ($demografi['program_gender'] ?? app(\App\Services\DashboardMetricsService::class)->getProgramGenderStats());
@endphp

<div class="space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>📊</span>
                <span>Statistik Peminatan Program & Gender</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Distribusi komparatif pendaftar Program Unggulan dan Reguler berdasarkan jenis kelamin</p>
        </div>
        <span class="hidden sm:inline-flex text-[11px] font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
            Realtime SPMB
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- 1. KELOMPOK PROGRAM UNGGULAN -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent bg-white p-5 rounded-3xl border border-amber-200/80 shadow-xs hover:border-amber-400 transition-all flex flex-col justify-between">
            <div>
                <!-- Header Card Unggulan -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold shadow-xs">
                            🏆
                        </span>
                        <div>
                            <span class="text-xs font-black text-amber-950 uppercase tracking-wider block">Program Unggulan</span>
                            <span class="text-[10px] text-amber-700 font-semibold">+ Pembinaan Asrama</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300">
                        Intensif
                    </span>
                </div>

                <!-- Total Pendaftar Unggulan All -->
                <div class="mt-4 pt-3 border-t border-amber-100/80">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-bold text-slate-600">Total Pendaftar Unggulan All</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-amber-600 tracking-tight">{{ number_format($pStats['unggulan_all']) }}</span>
                            <span class="text-xs font-bold text-slate-400">Siswa</span>
                        </div>
                    </div>
                </div>

                <!-- Gender Breakdown: Laki-laki & Perempuan Unggulan -->
                <div class="grid grid-cols-2 gap-2.5 mt-3.5">
                    <!-- Laki-laki Unggulan -->
                    <div class="bg-white/90 p-3 rounded-2xl border border-amber-200/70 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                <span>👨</span> Laki-laki
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-blue-50 text-blue-700">
                                {{ $pStats['unggulan_laki_pct'] }}%
                            </span>
                        </div>
                        <p class="text-xl font-black text-slate-900 mt-1.5">
                            {{ number_format($pStats['unggulan_laki']) }} <span class="text-[10px] font-semibold text-slate-400">Siswa</span>
                        </p>
                    </div>

                    <!-- Perempuan Unggulan -->
                    <div class="bg-white/90 p-3 rounded-2xl border border-amber-200/70 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                <span>👩</span> Perempuan
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-pink-50 text-pink-700">
                                {{ $pStats['unggulan_perempuan_pct'] }}%
                            </span>
                        </div>
                        <p class="text-xl font-black text-slate-900 mt-1.5">
                            {{ number_format($pStats['unggulan_perempuan']) }} <span class="text-[10px] font-semibold text-slate-400">Siswi</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mini Progress Bar Unggulan -->
            @if ($pStats['unggulan_all'] > 0)
                <div class="mt-4 pt-3 border-t border-amber-100">
                    <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden flex">
                        <div class="bg-blue-600 h-full transition-all duration-500" style="width: {{ $pStats['unggulan_laki_pct'] }}%" title="Laki-laki: {{ $pStats['unggulan_laki_pct'] }}%"></div>
                        <div class="bg-pink-500 h-full transition-all duration-500" style="width: {{ $pStats['unggulan_perempuan_pct'] }}%" title="Perempuan: {{ $pStats['unggulan_perempuan_pct'] }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. KELOMPOK PROGRAM REGULER -->
        <div class="bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent bg-white p-5 rounded-3xl border border-blue-200/80 shadow-xs hover:border-blue-400 transition-all flex flex-col justify-between">
            <div>
                <!-- Header Card Reguler -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-sm font-bold shadow-xs">
                            📚
                        </span>
                        <div>
                            <span class="text-xs font-black text-blue-950 uppercase tracking-wider block">Program Reguler</span>
                            <span class="text-[10px] text-blue-700 font-semibold">Standar Vokasi Industri</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 border border-blue-300">
                        Reguler
                    </span>
                </div>

                <!-- Total Pendaftar Reguler All -->
                <div class="mt-4 pt-3 border-t border-blue-100/80">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-bold text-slate-600">Total Pendaftar Reguler All</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-blue-600 tracking-tight">{{ number_format($pStats['reguler_all']) }}</span>
                            <span class="text-xs font-bold text-slate-400">Siswa</span>
                        </div>
                    </div>
                </div>

                <!-- Gender Breakdown: Laki-laki & Perempuan Reguler -->
                <div class="grid grid-cols-2 gap-2.5 mt-3.5">
                    <!-- Laki-laki Reguler -->
                    <div class="bg-white/90 p-3 rounded-2xl border border-blue-200/70 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                <span>👨</span> Laki-laki
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-blue-50 text-blue-700">
                                {{ $pStats['reguler_laki_pct'] }}%
                            </span>
                        </div>
                        <p class="text-xl font-black text-slate-900 mt-1.5">
                            {{ number_format($pStats['reguler_laki']) }} <span class="text-[10px] font-semibold text-slate-400">Siswa</span>
                        </p>
                    </div>

                    <!-- Perempuan Reguler -->
                    <div class="bg-white/90 p-3 rounded-2xl border border-blue-200/70 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                <span>👩</span> Perempuan
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-pink-50 text-pink-700">
                                {{ $pStats['reguler_perempuan_pct'] }}%
                            </span>
                        </div>
                        <p class="text-xl font-black text-slate-900 mt-1.5">
                            {{ number_format($pStats['reguler_perempuan']) }} <span class="text-[10px] font-semibold text-slate-400">Siswi</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mini Progress Bar Reguler -->
            @if ($pStats['reguler_all'] > 0)
                <div class="mt-4 pt-3 border-t border-blue-100">
                    <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden flex">
                        <div class="bg-blue-600 h-full transition-all duration-500" style="width: {{ $pStats['reguler_laki_pct'] }}%" title="Laki-laki: {{ $pStats['reguler_laki_pct'] }}%"></div>
                        <div class="bg-pink-500 h-full transition-all duration-500" style="width: {{ $pStats['reguler_perempuan_pct'] }}%" title="Perempuan: {{ $pStats['reguler_perempuan_pct'] }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
