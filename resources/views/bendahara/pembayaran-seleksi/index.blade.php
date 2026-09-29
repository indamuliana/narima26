<x-layouts.app>
    <x-slot name="title">Verifikasi Pembayaran Seleksi — Bendahara</x-slot>

    <x-slot name="sidebar">
        <a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('bendahara.pembayaran-seleksi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-nampi-orange text-white shadow-xs">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Pembayaran Seleksi</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Tagihan Daftar Ulang</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </x-slot>

    <div class="space-y-6">

        <!-- Top Header & Flash Alerts -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Verifikasi Pembayaran Seleksi</h1>
                <p class="text-xs text-slate-500 mt-1">Periksa bukti transfer pendaftar, validasi mutasi kas, dan terbitkan kwitansi resmi.</p>
            </div>
        </div>

        @if(session('success'))
            <x-alert type="success" title="Sukses">{{ session('success') }}</x-alert>
        @endif

        @if(session('warning'))
            <x-alert type="warning" title="Perhatian">{{ session('warning') }}</x-alert>
        @endif

        <!-- Metrik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 block">Menunggu Verifikasi</span>
                    <span class="text-2xl font-black text-amber-500 mt-1 block">{{ $countPending }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 block">Telah Diverifikasi</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $countDiverifikasi }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 block">Bukti Ditolak</span>
                    <span class="text-2xl font-black text-rose-600 mt-1 block">{{ $countDitolak }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 block">Total Dana Terverifikasi</span>
                    <span class="text-xl font-black text-slate-900 mt-1 block">Rp {{ number_format($totalDana, 0, ',', '.') }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Filter Tabs & Search Bar -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <!-- Status Tabs -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('bendahara.pembayaran-seleksi.index') }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ empty($status) ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('bendahara.pembayaran-seleksi.index', ['status' => 'PENDING', 'q' => $search]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'PENDING' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Menunggu ({{ $countPending }})
                    </a>
                    <a href="{{ route('bendahara.pembayaran-seleksi.index', ['status' => 'DIVERIFIKASI', 'q' => $search]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'DIVERIFIKASI' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Diverifikasi ({{ $countDiverifikasi }})
                    </a>
                    <a href="{{ route('bendahara.pembayaran-seleksi.index', ['status' => 'DITOLAK', 'q' => $search]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'DITOLAK' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                        Ditolak ({{ $countDitolak }})
                    </a>
                </div>

                <!-- Search Input -->
                <form action="{{ route('bendahara.pembayaran-seleksi.index') }}" method="GET" class="flex gap-2">
                    @if(!empty($status))
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, NISN, atau nomor..."
                        class="rounded-xl border border-slate-300 px-3.5 py-1.5 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-200 outline-none w-56">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 cursor-pointer">
                        Cari
                    </button>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-3.5">Calon Murid</th>
                            <th class="p-3.5">Jurusan / Program</th>
                            <th class="p-3.5">Kanal / Pengirim</th>
                            <th class="p-3.5 text-right">Nominal</th>
                            <th class="p-3.5">Tgl Bayar</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pembayarans as $pembayaran)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">{{ $pembayaran->calonSiswa?->nama_lengkap ?? '-' }}</div>
                                    <div class="text-[11px] font-mono text-slate-500">
                                        {{ $pembayaran->calonSiswa?->nomor_pendaftaran }} &bull; NISN: {{ $pembayaran->calonSiswa?->nisn }}
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-semibold text-slate-800">{{ $pembayaran->calonSiswa?->jurusan?->nama ?? $pembayaran->calonSiswa?->jurusan?->nama_jurusan ?? '-' }}</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains(strtolower($pembayaran->calonSiswa?->program?->nama ?? $pembayaran->calonSiswa?->program?->nama_program ?? ''), 'unggul') ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $pembayaran->calonSiswa?->program?->nama ?? $pembayaran->calonSiswa?->program?->nama_program ?? 'Reguler' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800">{{ $pembayaran->bank_pengirim ?: 'Transfer Bank' }}</div>
                                    <div class="text-[11px] text-slate-500">a.n. {{ $pembayaran->nama_pengirim ?: '-' }}</div>
                                </td>
                                <td class="p-3.5 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($pembayaran->nominal_dibayar ?: $pembayaran->nominal_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-slate-600">
                                    {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($pembayaran->status === 'DIVERIFIKASI')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800">
                                            DIVERIFIKASI
                                        </span>
                                    @elseif($pembayaran->status === 'DITOLAK')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800">
                                            DITOLAK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800">
                                            PENDING
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($pembayaran->calonSiswa)
                                            <x-whatsapp-contact-dropdown :calonSiswa="$pembayaran->calonSiswa" />
                                        @endif
                                        <a href="{{ route('bendahara.pembayaran-seleksi.show', $pembayaran->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold transition text-xs">
                                            Tinjau
                                        </a>

                                        @if($pembayaran->status === 'DIVERIFIKASI')
                                            <a href="{{ route('bendahara.pembayaran-seleksi.cetak', $pembayaran->id) }}"
                                                title="Cetak Kwitansi PDF"
                                                class="inline-flex items-center p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    Belum ada data pembayaran seleksi yang sesuai filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-3">
                {{ $pembayarans->links() }}
            </div>
        </div>

    </div>
</x-layouts.app>
