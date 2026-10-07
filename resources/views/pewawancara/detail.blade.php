<x-layouts.app>
    <x-slot name="title">Hasil Wawancara — {{ $calonSiswa->nama_lengkap }}</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Nav -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('pewawancara.riwayat') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    ← Kembali ke Riwayat
                </a>
                <h1 class="text-2xl font-black text-slate-800">Hasil Penilaian Wawancara</h1>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan evaluasi seleksi calon siswa dan orang tua.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pewawancara.wawancara.hub', $calonSiswa) }}"
                   class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                    ✏️ Edit Penilaian
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Candidate & Interview Overview -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($calonSiswa->dokumenPendaftaran?->pas_foto_path && file_exists(public_path('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path)))
                        <img src="{{ asset('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path) }}"
                             alt="Pas Foto"
                             class="w-16 h-20 object-cover rounded-xl border-2 border-white/30 shadow-md shrink-0">
                    @else
                        <div class="w-16 h-20 rounded-xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-slate-400 shrink-0">
                            <span class="text-2xl">👤</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-nampi-cyan font-bold">
                            {{ $calonSiswa->nomor_pendaftaran }}
                        </span>
                        <h2 class="text-xl font-black text-white mt-0.5">{{ $calonSiswa->nama_lengkap }}</h2>
                        <p class="text-xs text-slate-300 mt-0.5 font-mono">NISN: {{ $calonSiswa->nisn }} • Asal: {{ $calonSiswa->sekolah_asal_text }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-400/30">
                                Pil 1: {{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}
                            </span>
                            @if($calonSiswa->jurusan2)
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-500/20 text-slate-300 border border-slate-400/30">
                                Pil 2: {{ $calonSiswa->jurusan2?->nama_jurusan ?? '-' }}
                            </span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-500/20 text-purple-300 border border-purple-400/30">
                                Program: {{ $calonSiswa->programBelajar?->nama_program ?? '-' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                                Beasiswa: {{ $calonSiswa->tag_beasiswa ?? 'Normal' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-pink-500/20 text-pink-300 border border-pink-400/30">
                                Jalur: {{ $calonSiswa->tag_jalur ?? 'Normal' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Status: {{ $calonSiswa->status_spmb instanceof \BackedEnum ? $calonSiswa->status_spmb->label() : $calonSiswa->status_spmb }}
                    </span>
                </div>
            </div>

            <!-- Scoring Breakdown Table -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ringkasan Wawancara Siswa -->
                    <div class="border rounded-xl p-4 bg-slate-50">
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-3">
                            Wawancara Siswa
                            @if($wawancaraSiswa?->status === 'SELESAI')
                                <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Selesai</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded-full">Draft/Belum</span>
                            @endif
                        </h3>
                        
                        @if($wawancaraSiswa)
                        <dl class="space-y-2 text-xs">
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Pewawancara:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraSiswa->pewawancara?->name ?? $wawancaraSiswa->nama_petugas }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Tanggal:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraSiswa->tanggal_wawancara?->format('d M Y') }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-4 pt-2 border-t">
                                <dt class="text-slate-500">Baca Al-Quran:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraSiswa->baca_quran }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Kesehatan:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraSiswa->kondisi_kesehatan }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Observasi (Rambut/Seragam/Pendengaran):</dt>
                                <dd class="col-span-2 font-medium text-slate-800">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-white {{ $wawancaraSiswa->kerapihan_rambut == 'HIJAU' ? 'bg-emerald-500' : ($wawancaraSiswa->kerapihan_rambut == 'OREN' ? 'bg-amber-500' : 'bg-red-500') }}">{{ $wawancaraSiswa->kerapihan_rambut }}</span> / 
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-white {{ $wawancaraSiswa->kerapihan_seragam == 'HIJAU' ? 'bg-emerald-500' : ($wawancaraSiswa->kerapihan_seragam == 'OREN' ? 'bg-amber-500' : 'bg-red-500') }}">{{ $wawancaraSiswa->kerapihan_seragam }}</span> / 
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-white {{ $wawancaraSiswa->status_pendengaran == 'HIJAU' ? 'bg-emerald-500' : ($wawancaraSiswa->status_pendengaran == 'OREN' ? 'bg-amber-500' : 'bg-red-500') }}">{{ $wawancaraSiswa->status_pendengaran }}</span>
                                </dd>
                            </div>
                            
                            <div class="mt-4 pt-2 border-t">
                                <dt class="text-slate-500 font-bold mb-1">Rekomendasi Pewawancara:</dt>
                                <dd class="font-bold text-slate-800 text-sm">
                                    @if($wawancaraSiswa->rekomendasi == 'TERIMA')
                                        <span class="text-emerald-600">TERIMA</span>
                                    @elseif($wawancaraSiswa->rekomendasi == 'PERTIMBANGKAN')
                                        <span class="text-amber-600">PERTIMBANGKAN</span>
                                    @else
                                        <span class="text-red-600">TOLAK</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="mt-2">
                                <dt class="text-slate-500 mb-1">Catatan Rahasia:</dt>
                                <dd class="text-slate-700 bg-yellow-50 p-2 border rounded border-yellow-200">
                                    {{ $wawancaraSiswa->catatan_pewawancara ?: '-' }}
                                </dd>
                            </div>
                        </dl>
                        @else
                        <p class="text-xs text-slate-500 italic">Belum ada data wawancara siswa.</p>
                        @endif
                    </div>

                    <!-- Ringkasan Wawancara Orang Tua -->
                    <div class="border rounded-xl p-4 bg-slate-50">
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-3">
                            Wawancara Orang Tua
                            @if($wawancaraOrangTua?->status === 'SELESAI')
                                <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Selesai</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded-full">Draft/Belum</span>
                            @endif
                        </h3>
                        
                        @if($wawancaraOrangTua)
                        <dl class="space-y-2 text-xs">
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Pewawancara:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraOrangTua->pewawancara?->name ?? $wawancaraOrangTua->nama_petugas }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Tanggal:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraOrangTua->tanggal_wawancara?->format('d M Y') }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-4 pt-2 border-t">
                                <dt class="text-slate-500">Narasumber:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraOrangTua->nama_diwawancarai }} ({{ $wawancaraOrangTua->hubungan_dengan_siswa }})</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Penanggung Jwb Belajar:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraOrangTua->penanggung_jawab_belajar }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Info Wikrama dari:</dt>
                                <dd class="col-span-2 font-medium text-slate-800">{{ $wawancaraOrangTua->info_wikrama_dari }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <dt class="text-slate-500">Infaq Rutin Bulanan:</dt>
                                <dd class="col-span-2 font-bold text-emerald-700">
                                    {{ $wawancaraOrangTua->infaq_rutin_bulanan !== null ? 'Rp ' . number_format($wawancaraOrangTua->infaq_rutin_bulanan, 0, ',', '.') : '-' }}
                                </dd>
                            </div>

                            <div class="mt-4 pt-2 border-t">
                                <dt class="text-slate-500 mb-1">Catatan Khusus (dari Ortu):</dt>
                                <dd class="text-slate-700 p-2 border rounded bg-white">
                                    {{ $wawancaraOrangTua->hal_perhatian_ortu ?: '-' }}
                                </dd>
                            </div>
                            <div class="mt-2">
                                <dt class="text-slate-500 mb-1">Kesan Pewawancara (Rahasia):</dt>
                                <dd class="text-slate-700 bg-yellow-50 p-2 border rounded border-yellow-200">
                                    {{ $wawancaraOrangTua->kesan_pewawancara ?: '-' }}
                                </dd>
                            </div>
                        </dl>
                        @else
                        <p class="text-xs text-slate-500 italic">Belum ada data wawancara orang tua.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
