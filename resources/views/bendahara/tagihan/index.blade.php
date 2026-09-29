<x-layouts.app>
    <x-slot name="title">Tagihan Daftar Ulang</x-slot>

    <x-slot name="sidebar">
        @include('bendahara.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Tagihan Biaya Daftar Ulang</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar snapshot tagihan beku resmi calon siswa baru SMK Wikrama 1 Garut.</p>
            </div>
            <a href="{{ route('bendahara.tagihan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                <span>➕ Terbitkan Tagihan Baru</span>
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

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('bendahara.tagihan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari nomor tagihan, nama siswa, NISN, atau no pendaftaran..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-3">
                    <select name="jenis_tagihan" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange bg-white">
                        <option value="">Semua Jenis Tagihan</option>
                        <option value="DAFTAR_ULANG" {{ ($jenisTagihan ?? '') === 'DAFTAR_ULANG' ? 'selected' : '' }}>Daftar Ulang (DSP & SPP)</option>
                        <option value="SERAGAM" {{ ($jenisTagihan ?? '') === 'SERAGAM' ? 'selected' : '' }}>Paket Seragam & Atribut</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange bg-white">
                        <option value="">Semua Status</option>
                        <option value="BELUM_LUNAS" {{ $status === 'BELUM_LUNAS' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="CICILAN" {{ $status === 'CICILAN' ? 'selected' : '' }}>Cicilan</option>
                        <option value="LUNAS" {{ $status === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if ($search || $status || !empty($jenisTagihan))
                        <a href="{{ route('bendahara.tagihan.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Nomor Tagihan</th>
                            <th class="px-4 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Jenis Tagihan</th>
                            <th class="px-4 py-3.5">Program / Gelombang</th>
                            <th class="px-4 py-3.5 text-right">Total Netto</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tagihanList as $t)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-slate-900 text-xs block">
                                        {{ $t->nomor_tagihan }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        {{ $t->created_at?->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-800">{{ $t->calonSiswa?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $t->calonSiswa?->nomor_pendaftaran }} • NISN: {{ $t->calonSiswa?->nisn }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">
                                        {{ $t->calonSiswa?->jurusan?->nama ?? $t->calonSiswa?->jurusan?->nama_jurusan ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    @if($t->jenis_tagihan === 'SERAGAM')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            Seragam & Atribut
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            DSP & SPP Bulan-1
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains(strtolower($t->program_snapshot ?? ''), 'unggul') ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $t->program_snapshot ?: 'Reguler' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ $t->gelombang_snapshot }}</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-slate-900 block">
                                        Rp {{ number_format($t->total_netto, 0, ',', '.') }}
                                    </span>
                                    @if ($t->total_diskon > 0)
                                        <span class="text-[10px] text-emerald-600 font-semibold">
                                            (Diskon: Rp {{ number_format($t->total_diskon, 0, ',', '.') }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($t->status === 'LUNAS')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            LUNAS
                                        </span>
                                    @elseif ($t->status === 'CICILAN')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            CICILAN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($t->calonSiswa)
                                            <x-whatsapp-contact-dropdown :calonSiswa="$t->calonSiswa" />
                                        @endif
                                        <a href="{{ route('bendahara.tagihan.show', $t) }}"
                                           class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors">
                                            Rincian
                                        </a>
                                        <a href="{{ route('bendahara.tagihan.cetak', $t) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors">
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada tagihan daftar ulang</p>
                                    <p class="text-xs text-slate-400 mt-1">Terbitkan tagihan baru untuk calon siswa yang berhak.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tagihanList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $tagihanList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
