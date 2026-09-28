<x-layouts.app>
    <x-slot name="title">Audit Trail & Log Aktivitas — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Audit Trail Sistem SPMB</h1>
                <p class="text-xs text-slate-400 mt-0.5">Catatan riwayat aktivitas transaksi, verifikasi keuangan, penilaian, dan perubahan status pengguna</p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.audit-trail.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Deskripsi / Pengguna</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari aktivitas, nama pengguna, IP..."
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Modul / Jenis Log</label>
                    <select name="log_name" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="SEMUA">Semua Modul</option>
                        @foreach ($availableLogNames as $name)
                            <option value="{{ $name }}" {{ request('log_name') === $name ? 'selected' : '' }}>
                                {{ strtoupper($name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if (request()->hasAny(['q', 'log_name']))
                        <a href="{{ route('admin.audit-trail.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Activity Log Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-5 py-3.5" style="width: 18%;">Waktu & Tanggal</th>
                            <th class="px-4 py-3.5" style="width: 15%;">Pelaku (User)</th>
                            <th class="px-4 py-3.5 text-center" style="width: 12%;">Modul</th>
                            <th class="px-5 py-3.5" style="width: 40%;">Aktivitas / Deskripsi</th>
                            <th class="px-4 py-3.5 text-right" style="width: 15%;">Detail Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-800 block">{{ $log->created_at?->translatedFormat('d F Y') }}</span>
                                    <span class="text-slate-400 text-[11px] font-mono">{{ $log->created_at?->format('H:i:s') }} WIB &bull; {{ $log->created_at?->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-900 block">{{ $log->causer?->name ?? 'Sistem Otomatis' }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-mono">{{ $log->causer?->role ?? 'System' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono
                                        {{ in_array($log->log_name, ['auth', 'login']) ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                           (in_array($log->log_name, ['pembayaran', 'keuangan']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                           (in_array($log->log_name, ['wawancara', 'kelulusan']) ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700')) }}">
                                        {{ $log->log_name }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-800 leading-snug">{{ $log->description }}</p>
                                    @if ($log->properties && $log->properties->count() > 0)
                                        <details class="mt-1 text-[11px] text-slate-400 cursor-pointer">
                                            <summary class="hover:text-slate-600">Rincian parameter</summary>
                                            <pre class="mt-1 p-2 rounded-lg bg-slate-50 text-[10px] text-slate-600 overflow-x-auto font-mono">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </details>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-[11px] text-slate-500">
                                    {{ class_basename($log->subject_type ?? '') }} #{{ $log->subject_id ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center text-slate-400">
                                    Tidak ada catatan log aktivitas yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
