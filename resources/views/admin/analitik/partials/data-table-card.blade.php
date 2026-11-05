@php
    $sumTotal = array_sum($counts);
    $barClass = $barColor ?? 'bg-orange-500';
    $colLabelText = $colLabel ?? 'Kategori / Nama';
    $colCountText = $colCount ?? 'Jumlah';
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col justify-between">
    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
        <div>
            <h3 class="text-xs sm:text-sm font-black text-slate-800 tracking-tight">{{ $title }}</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $subtitle }}</p>
        </div>
        <span class="text-base sm:text-lg p-2 rounded-xl bg-slate-100 text-slate-700 shrink-0">{{ $icon }}</span>
    </div>

    <div class="overflow-x-auto flex-1">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="py-2.5 px-3.5 w-10 text-center">#</th>
                    <th class="py-2.5 px-3">{{ $colLabelText }}</th>
                    <th class="py-2.5 px-3 text-right">{{ $colCountText }}</th>
                    <th class="py-2.5 px-3.5 w-36 sm:w-44 text-right">Persentase</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($labels as $i => $label)
                    @php
                        $cnt = $counts[$i] ?? 0;
                        $pct = $sumTotal > 0 ? round(($cnt / $sumTotal) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-mono text-[11px]">{{ $i + 1 }}</td>
                        <td class="py-2.5 px-3 font-semibold text-slate-800">
                            {{ $label }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                            {{ number_format($cnt, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-3.5 text-right">
                            <div class="flex items-center justify-end gap-2.5">
                                <div class="w-16 sm:w-20 bg-slate-100 rounded-full h-1.5 overflow-hidden shrink-0">
                                    <div class="{{ $barClass }} h-1.5 rounded-full transition-all duration-300" style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                                <span class="font-mono text-[11px] font-bold text-slate-600 w-11 text-right">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-400 italic text-xs">
                            Belum ada data tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($labels) > 0)
                <tfoot class="bg-slate-50/90 border-t border-slate-200 text-[11px] font-bold text-slate-700">
                    <tr>
                        <td colspan="2" class="py-2.5 px-3.5 text-right font-bold text-slate-600">Total Terdata:</td>
                        <td class="py-2.5 px-3 text-right font-mono font-black text-slate-900">{{ number_format($sumTotal, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3.5 text-right font-mono font-black text-slate-600">100%</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
