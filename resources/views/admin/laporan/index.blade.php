<x-layouts.app>
    <x-slot name="title">Laporan & Rekapitulasi Eksekutif — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Laporan & Analitik SPMB 2026/2027</h1>
                <p class="text-xs text-slate-400 mt-0.5">Ringkasan funnel konversi, rekapitulasi kuota kompetensi keahlian, dan arus kas penerimaan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.rekap.export.pdf') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Cetak Rekapitulasi Resmi (PDF)</span>
                </a>
            </div>
        </div>

        <!-- 1. Funnel Konversi Alur SPMB -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-base font-black text-slate-800">Funnel Konversi Alur Pendaftaran</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block">1. Registrasi</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $funnel['total_registrasi'] }}</span>
                    <span class="text-[10px] text-slate-500">100% Pendaftar</span>
                </div>
                <div class="p-4 rounded-xl bg-cyan-50/50 border border-cyan-100">
                    <span class="text-[11px] font-bold text-cyan-700 uppercase block">2. Bayar Seleksi</span>
                    <span class="text-2xl font-black text-cyan-900 mt-1 block">{{ $funnel['seleksi_terbayar'] }}</span>
                    <span class="text-[10px] text-cyan-600">
                        {{ $funnel['total_registrasi'] > 0 ? round(($funnel['seleksi_terbayar'] / $funnel['total_registrasi']) * 100) : 0 }}% konversi
                    </span>
                </div>
                <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-100">
                    <span class="text-[11px] font-bold text-blue-700 uppercase block">3. Berkas Lengkap</span>
                    <span class="text-2xl font-black text-blue-900 mt-1 block">{{ $funnel['biodata_selesai'] }}</span>
                    <span class="text-[10px] text-blue-600">Siap tes</span>
                </div>
                <div class="p-4 rounded-xl bg-indigo-50/50 border border-indigo-100">
                    <span class="text-[11px] font-bold text-indigo-700 uppercase block">4. Wawancara</span>
                    <span class="text-2xl font-black text-indigo-900 mt-1 block">{{ $funnel['telah_wawancara'] }}</span>
                    <span class="text-[10px] text-indigo-600">Telah dievaluasi</span>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-100">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase block">5. Diterima</span>
                    <span class="text-2xl font-black text-emerald-900 mt-1 block">{{ $funnel['diterima'] }}</span>
                    <span class="text-[10px] text-emerald-600">Lulus seleksi</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-900 text-white border border-slate-900">
                    <span class="text-[11px] font-bold text-nampi-orange uppercase block">6. Resmi Terdaftar</span>
                    <span class="text-2xl font-black text-white mt-1 block">{{ $funnel['resmi_terdaftar'] }}</span>
                    <span class="text-[10px] text-slate-300">Siswa definitif</span>
                </div>
            </div>
        </div>

        <!-- 2. Rekapitulasi per Kompetensi Keahlian -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-black text-slate-800">Rekapitulasi Keterisian Kuota Kompetensi Keahlian</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kapasitas rombongan belajar: 72 siswa (2 kelas @ 36 siswa)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Kompetensi Keahlian</th>
                            <th class="px-4 py-3 text-center">Kuota Rombel</th>
                            <th class="px-4 py-3 text-center">Pendaftar</th>
                            <th class="px-4 py-3 text-center">Lulus (Diterima)</th>
                            <th class="px-4 py-3 text-center">Ditolak</th>
                            <th class="px-4 py-3 text-center">Mundur</th>
                            <th class="px-4 py-3 text-center">Resmi Terdaftar</th>
                            <th class="px-4 py-3 text-center">Sisa Kursi</th>
                            <th class="px-5 py-3 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($rekapJurusan as $rj)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-900">{{ $rj['nama'] }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">({{ $rj['kode'] }})</span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-slate-700">{{ $rj['kuota'] }}</td>
                                <td class="px-4 py-3.5 text-center font-bold text-slate-800">{{ $rj['pendaftar'] }}</td>
                                <td class="px-4 py-3.5 text-center font-bold text-emerald-600">{{ $rj['diterima'] }}</td>
                                <td class="px-4 py-3.5 text-center text-rose-600">{{ $rj['ditolak'] }}</td>
                                <td class="px-4 py-3.5 text-center text-slate-400">{{ $rj['mengundurkan_diri'] }}</td>
                                <td class="px-4 py-3.5 text-center font-black text-slate-900">{{ $rj['resmi'] }}</td>
                                <td class="px-4 py-3.5 text-center font-bold {{ $rj['sisa'] <= 10 ? 'text-rose-600' : 'text-slate-700' }}">{{ $rj['sisa'] }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black
                                        {{ $rj['persentase'] >= 90 ? 'bg-rose-50 text-rose-700' : ($rj['persentase'] >= 50 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $rj['persentase'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Rekap Keuangan & Asal Sekolah Feeder -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Keuangan SPMB (7 Cols) -->
            <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-black text-slate-800">Rekapitulasi Keuangan & Arus Kas</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbandingan potensi tagihan, diskon, dan realisasi penerimaan</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block">Total Potensi Tagihan Seleksi</span>
                        <span class="font-black text-slate-800 text-base mt-1 block">Rp {{ number_format($keuangan['tagihan_seleksi'], 0, ',', '.') }}</span>
                        <span class="text-[11px] text-emerald-600">Realisasi: Rp {{ number_format($keuangan['kas_seleksi'], 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block">Total Tagihan Daftar Ulang</span>
                        <span class="font-black text-slate-800 text-base mt-1 block">Rp {{ number_format($keuangan['tagihan_daftar_ulang'], 0, ',', '.') }}</span>
                        <span class="text-[11px] text-emerald-600">Realisasi: Rp {{ number_format($keuangan['kas_daftar_ulang'], 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-900 text-white col-span-2 flex items-center justify-between">
                        <div>
                            <span class="text-slate-300 block text-xs">Total Kas Riil Masuk (Seleksi + Daftar Ulang)</span>
                            <span class="text-xl font-black text-nampi-orange mt-0.5 block">Rp {{ number_format($keuangan['total_kas_masuk'], 0, ',', '.') }}</span>
                        </div>
                        <div class="text-right text-xs">
                            <span class="text-slate-400 block">Total Diskon Diberikan:</span>
                            <span class="font-bold text-amber-400">Rp {{ number_format($keuangan['total_diskon'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Feeder Schools (5 Cols) -->
            <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-black text-slate-800">Top Sekolah Asal (Feeder)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">SMP/MTs dengan kontribusi pendaftar terbanyak</p>
                </div>

                <div class="space-y-2.5">
                    @forelse ($topSchools as $idx => $sch)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-[11px]">
                                    {{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $sch->nama_sekolah }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $sch->kabupaten ?? 'Garut' }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-nampi-orange/10 text-nampi-orange">
                                {{ $sch->calon_siswa_count }} Siswa
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada data sekolah asal pendaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
