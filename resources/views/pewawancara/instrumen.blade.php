<x-layouts.app>
    <x-slot name="title">Instrumen & Rubrik Wawancara</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Instrumen & Rubrik Penilaian</h1>
                <p class="text-xs text-slate-500 mt-1">Pedoman indikator dan kriteria penilaian wawancara calon siswa & orang tua.</p>
            </div>
            <a href="{{ route('pewawancara.antrian') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs">
                <span>Antrian Calon Siswa →</span>
            </a>
        </div>

        <!-- Rubrik Information Alert -->
        <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-sm font-bold">Panduan Klasifikasi Indikator Warna (Section 21.2)</h3>
                <p class="text-xs text-indigo-700">
                    Sistem menggunakan 3 skala representasi warna indikator untuk memudahkan penarikan kesimpulan seleksi:
                </p>
                <div class="flex flex-wrap gap-3 pt-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold border border-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> HIJAU (Skor 75 - 100): Memenuhi syarat / Sangat Baik / Mendukung penuh
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold border border-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> ORANYE (Skor 60 - 74): Cukup / Perlu pembinaan khusus
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 font-bold border border-rose-300">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> MERAH (Skor 0 - 59): Kurang / Kurang memadai / Catatan kritis
                    </span>
                </div>
            </div>
        </div>

        <!-- Criteria Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Urutan & Kode</th>
                            <th class="px-4 py-3.5">Nama Kriteria</th>
                            <th class="px-4 py-3.5">Sasaran Uji</th>
                            <th class="px-4 py-3.5">Pedoman / Keterangan Penilaian</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($kriteriaList as $k)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="inline-block w-6 h-6 rounded-lg bg-slate-100 text-center font-bold text-xs leading-6 text-slate-600 mr-2">
                                        {{ $k->urutan }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-800 text-xs px-2 py-0.5 rounded bg-slate-100">
                                        {{ $k->kode }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-800">{{ $k->nama_kriteria }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($k->jenis_penilaian === 'orang_tua')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                            Orang Tua / Wali
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Calon Siswa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs text-slate-600">
                                    {!! $k->keterangan ? nl2br(e($k->keterangan)) : '<i>Observasi menyeluruh terhadap aspek ini selama sesi interaksi langsung.</i>' !!}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($k->aktif)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-400">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
