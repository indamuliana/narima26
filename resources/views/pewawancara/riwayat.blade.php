<x-layouts.app>
    <x-slot name="title">Riwayat Wawancara</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Riwayat Sesi Wawancara</h1>
                <p class="text-xs text-slate-500 mt-1">Daftar calon siswa yang telah selesai diwawancara dan penilaian telah tersimpan.</p>
            </div>
            <a href="{{ route('pewawancara.antrian') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                <span>➕ Antrian Wawancara</span>
            </a>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('pewawancara.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-7">
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari nama siswa, nomor pendaftaran, atau NISN..."
                           class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                </div>

                <div class="sm:col-span-3">
                    <select name="jurusan_id" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30 focus:border-nampi-orange">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ $jurusanId == $j->id ? 'selected' : '' }}>
                                {{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if ($search || $jurusanId)
                        <a href="{{ route('pewawancara.riwayat') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm flex items-center justify-center">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Jurusan</th>
                            <th class="px-4 py-3.5">Tanggal Uji</th>
                            <th class="px-4 py-3.5">Pewawancara</th>
                            <th class="px-4 py-3.5 text-center">Rata-rata Skor</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($riwayatList as $cs)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($cs->nama_lengkap ?? 'CS', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $cs->nama_lengkap }}</p>
                                            <p class="text-xs text-slate-400 font-mono mt-0.5">
                                                {{ $cs->nomor_pendaftaran }} • NISN: {{ $cs->nisn }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-cyan-50 text-cyan-800 border border-cyan-200 mb-1">
                                        1. {{ $cs->jurusan?->nama_jurusan ?? '-' }}
                                    </span>
                                    @if($cs->jurusan2)
                                        <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-50 text-slate-600 border border-slate-200 block mb-1">
                                            2. {{ $cs->jurusan2?->nama_jurusan ?? '-' }}
                                        </span>
                                    @endif
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Beasiswa: {{ $cs->tag_beasiswa ?? 'Normal' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Jalur: {{ $cs->tag_jalur ?? 'Normal' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-medium text-slate-700">
                                        {{ $cs->wawancaraSiswa?->tanggal_wawancara?->format('d/m/Y') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-xs font-medium text-slate-700">{{ $cs->wawancaraSiswa?->pewawancara?->name ?? $cs->wawancaraSiswa?->nama_petugas ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $cs->wawancaraSiswa?->rekomendasi == 'TERIMA' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($cs->wawancaraSiswa?->rekomendasi == 'PERTIMBANGKAN' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                        {{ $cs->wawancaraSiswa?->rekomendasi ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-whatsapp-contact-dropdown :calonSiswa="$cs" />
                                        <a href="{{ route('pewawancara.wawancara.show', $cs) }}"
                                           class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors">
                                            Detail
                                        </a>
                                        <a href="{{ route('pewawancara.wawancara.hub', $cs) }}"
                                           class="px-2.5 py-1.5 rounded-lg border border-blue-200 text-blue-700 text-xs font-semibold hover:bg-blue-50 transition-colors">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    <p class="text-base font-bold text-slate-600">Belum ada riwayat wawancara</p>
                                    <p class="text-xs text-slate-400 mt-1">Selesaikan penilaian pada menu Antrian Wawancara.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($riwayatList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $riwayatList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
