<x-layouts.app>
    <x-slot name="title">Manajemen Pembayaran Seleksi — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Manajemen Pembayaran Seleksi</h1>
                <p class="text-xs text-slate-400 mt-0.5">Monitoring dan verifikasi transaksi biaya tes seleksi calon peserta didik baru</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pembayaran.daftar-ulang') }}"
                   class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors">
                    Lihat Kas Daftar Ulang &rarr;
                </a>
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

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-amber-600 uppercase">Menunggu Verifikasi</span>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-emerald-600 uppercase">Terverifikasi</span>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['diverifikasi'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-rose-500 uppercase">Ditolak</span>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $stats['ditolak'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Total Kas Masuk</span>
                <p class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($stats['total_masuk'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.pembayaran.seleksi') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex-1 w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NISN, no pendaftaran..."
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Status</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                        <option value="DIVERIFIKASI" {{ request('status') === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-5 py-3.5">Calon Murid</th>
                            <th class="px-4 py-3.5">Jurusan</th>
                            <th class="px-4 py-3.5 text-right">Nominal Tagihan</th>
                            <th class="px-4 py-3.5 text-right">Nominal Bayar</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-center">Bukti Transfer</th>
                            <th class="px-5 py-3.5 text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pembayarans as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 block text-sm">{{ $p->calonSiswa?->nama_lengkap ?? 'Calon Murid' }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $p->calonSiswa?->nomor_pendaftaran }} &bull; NISN: {{ $p->calonSiswa?->nisn }}</span>
                                </td>
                                <td class="px-4 py-4 font-semibold text-slate-700">
                                    {{ $p->calonSiswa?->jurusan?->nama ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-right font-medium text-slate-600">
                                    Rp {{ number_format($p->nominal_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right font-black text-slate-900">
                                    Rp {{ number_format($p->nominal_dibayar ?? $p->nominal_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $p->status === 'DIVERIFIKASI' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                           ($p->status === 'DITOLAK' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($p->bukti_transfer_path)
                                        <a href="{{ asset('storage/' . $p->bukti_transfer_path) }}" target="_blank"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 text-[11px]">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum upload</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($p->status === 'PENDING')
                                        <div class="inline-flex items-center gap-1.5">
                                            <form method="POST" action="{{ route('admin.pembayaran.seleksi.verify', $p) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 text-xs">
                                                    Verifikasi
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.pembayaran.seleksi.reject', $p) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 font-bold hover:bg-rose-100 text-xs">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif ($p->status === 'DIVERIFIKASI')
                                        <span class="text-[11px] text-slate-400 font-mono">
                                            Oleh: {{ $p->verifikator?->name ?? 'Admin' }}
                                        </span>
                                    @else
                                        <span class="text-xs text-rose-500 font-medium">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada transaksi pembayaran seleksi sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pembayarans->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
