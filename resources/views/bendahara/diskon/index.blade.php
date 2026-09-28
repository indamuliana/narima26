<x-layouts.app>
    <x-slot name="title">Kelola Diskon & Keringanan</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Diskon & Keringanan Biaya</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar riwayat potongan biaya dan beasiswa yang telah disetujui untuk calon siswa.</p>
            </div>
            <a href="{{ route('bendahara.tagihan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                <span>Lihat Tagihan Daftar Ulang &rarr;</span>
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Diskon Diberikan</span>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_discounts']) }} Siswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-nampi-orange flex items-center justify-center text-xl font-bold">
                        🏷️
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Nominal Keringanan</span>
                        <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        💰
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.diskon.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-10">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari jenis diskon, alasan, nama siswa, atau nomor pendaftaran..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('bendahara.diskon.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Jenis Diskon</th>
                            <th class="px-4 py-3.5">Skema Potongan</th>
                            <th class="px-4 py-3.5 text-right">Nominal Potongan</th>
                            <th class="px-4 py-3.5">Alasan / Catatan</th>
                            <th class="px-5 py-3.5">Diberikan Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($diskonList as $d)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $d->calonSiswa?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $d->calonSiswa?->nomor_pendaftaran }} • {{ $d->calonSiswa?->jurusan?->nama_jurusan }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $d->jenis_diskon }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($d->metode_diskon === 'persentase')
                                        <span class="font-semibold text-slate-800">{{ $d->nilai_diskon }}%</span>
                                        <span class="text-xs text-slate-400 block">dari bruto tagihan</span>
                                    @else
                                        <span class="font-semibold text-slate-800">Nominal Tetap</span>
                                        <span class="text-xs text-slate-400 block">Rp {{ number_format($d->nilai_diskon, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-emerald-700 block">
                                        Rp {{ number_format($d->nominal_potongan, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 max-w-xs">
                                    <p class="text-xs text-slate-700 font-medium line-clamp-2">{{ $d->alasan }}</p>
                                    @if ($d->keterangan)
                                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 italic">{{ $d->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-xs font-semibold text-slate-800 block">{{ $d->diberikanOleh?->name ?? 'Sistem' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $d->created_at?->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada data diskon</p>
                                    <p class="text-xs text-slate-400 mt-1">Diskon dapat diterapkan langsung melalui menu Rincian Tagihan Daftar Ulang.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($diskonList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $diskonList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
