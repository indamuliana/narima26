<x-layouts.guest>
    <x-slot name="title">SPMB SMK Wikrama 1 Garut — Lulus Wikrama Siap Membangun Negeri</x-slot>

    @php
        $gelombangAktif = $gelombangAktif ?? null;
        if ($gelombangAktif === null) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('master_gelombang')) {
                    $gelombangAktif = \App\Models\MasterGelombang::aktif()->first() ?? \App\Models\MasterGelombang::first();
                }
            } catch (\Throwable $e) {
                $gelombangAktif = null;
            }
        }
        $targetSelesai = $gelombangAktif?->periode_selesai ? $gelombangAktif->periode_selesai->copy()->endOfDay() : null;
        $now = now();
        $isEnded = $targetSelesai ? $targetSelesai->isPast() : false;
        $diffDays = ($targetSelesai && !$isEnded) ? (int)$now->diffInDays($targetSelesai) : 0;
        // Sumber gambar default (tersimpan di public/images/)
        // Untuk mengganti gambar utama/default, cukup upload file gambar ke public/images/ dan ganti nama filenya di sini
        $defaultImage = 'Lab-TJKT.jpg';
        $dummyImg = asset('images/' . $defaultImage);
    @endphp

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden py-10 bg-gradient-to-b from-amber-50/60 via-white to-slate-50 pt-10 pb-16 lg:pt-16 lg:pb-24 border-b border-slate-200/60">
        <!-- Background subtle decorative shapes -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full overflow-hidden pointer-events-none -z-10">
            <div class="absolute top-12 left-10 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl"></div>
            <div class="absolute top-24 right-10 w-96 h-96 bg-cyan-400/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Column: Hero Text & Badges -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <!-- Slogan & Tagline Pill Badges -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-nampi-orange/10 border border-nampi-orange/25 text-xs font-bold text-nampi-orange uppercase tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-nampi-orange animate-pulse"></span>
                            Nampi
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900 text-white text-xs font-medium">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Akreditasi A Unggul
                        </span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Sistem Penerimaan Murid Baru <span class="text-transparent bg-clip-text bg-gradient-to-r from-nampi-orange via-amber-500 to-amber-600"><br>SMK Wikrama 1 Garut</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Ilmu yang Amaliah,  Amal yang Ilmiah,  Akhlakul Karimah<br>
                        <i>Solusi pendidikan akhlak berkualitas di zaman modern</i><br> 
                    </p>

                    <!-- Call-to-Action Buttons including Download Brosur -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                        <a href="{{ url('/register') }}" class="relative group w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl font-bold bg-nampi-orange hover:bg-nampi-orange-hover text-white text-sm shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                            <span class="absolute -inset-0.5 rounded-xl bg-nampi-orange opacity-40 animate-ping pointer-events-none"></span>
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                            </span>
                            <span class="relative">Daftar Sekarang</span>
                            <svg class="relative w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>

                        <a href="{{ url('/login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3.5 rounded-xl font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 text-sm border border-slate-200 transition-colors bg-blue-500 text-white">
                            Masuk Akun
                        </a>

                        <a href="https://brosur.smkwikrama1garut.sch.id" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-sm shadow-xs transition-all group">
                            <svg class="w-4 h-4 text-amber-600 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Brosur</span>
                            <svg class="w-3.5 h-3.5 text-amber-500 opacity-70 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>

                    <!-- Trust Metric Counters -->
                    <div class="pt-6 grid grid-cols-4 gap-3 border-t border-slate-200/80 max-w-lg mx-auto lg:mx-0 text-center">
                        <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                            <div class="text-xl sm:text-2xl font-black text-slate-900">4</div>
                            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Jurusan Unggulan</div>
                        </div>
                        <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                            <div class="text-xl sm:text-2xl font-black text-nampi-orange">2</div>
                            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Program Pilihan</div>
                        </div>
                        <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                            <div class="text-xl sm:text-2xl font-black text-emerald-600">98%</div>
                            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Terserap Kerja</div>
                        </div>
                        <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                            <div class="text-xl sm:text-2xl font-black text-cyan-600">100+</div>
                            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Mitra Industri</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Card Highlight Gelombang Aktif -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xl relative overflow-hidden">
                            <!-- Header Card with Wikrama Logo -->
                            <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center p-1.5 shrink-0 shadow-2xs">
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Wikrama 1 Garut" class="w-full h-full object-contain" onerror="this.src='{{ $dummyImg }}'">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-extrabold text-slate-900 text-base">SMK Wikrama 1 Garut</h3>
                                    </div>
                                    <p class="text-xs text-slate-500">Lulus Wikrama Siap Membangun Negeri</p>
                                </div>
                            </div>

                            <!-- Gelombang Live Badge & Info -->
                            <div class="mt-5 space-y-4">
                                <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-white border border-amber-300/80 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                            Gelombang Aktif
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            {{ $gelombangAktif?->nama ?? 'Gelombang 1 (Terbuka)' }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-slate-600 leading-relaxed">
                                        Segera daftar di gelombang ini untuk mengamankan kuota jurusan pilihan serta memperoleh <strong>Potongan biaya</strong>.
                                    </div>

                                    <!-- Countdown Box -->
                                    <div class="pt-2 border-t border-amber-200/80">
                                        <div class="text-[11px] font-semibold text-slate-600 mb-2 flex items-center justify-between">
                                            <span>Sisa Waktu Pendaftaran:</span>
                                            <span class="text-nampi-orange font-bold">{{ $gelombangAktif?->periode_selesai ? $gelombangAktif->periode_selesai->format('d M Y') : 'Periode Berjalan' }}</span>
                                        </div>

                                        <div id="countdown-container"
                                            data-target="{{ $targetSelesai ? $targetSelesai->toISOString() : '' }}"
                                            class="grid grid-cols-4 gap-2 text-center">
                                            <div class="bg-white rounded-xl border border-amber-200 p-2 shadow-2xs">
                                                <div id="cd-days" class="font-black text-nampi-orange text-lg font-mono leading-none">{{ $diffDays }}</div>
                                                <div class="text-[9px] uppercase font-bold text-slate-400 mt-1">Hari</div>
                                            </div>
                                            <div class="bg-white rounded-xl border border-amber-200 p-2 shadow-2xs">
                                                <div id="cd-hours" class="font-black text-nampi-orange text-lg font-mono leading-none">{{ $diffHours }}</div>
                                                <div class="text-[9px] uppercase font-bold text-slate-400 mt-1">Jam</div>
                                            </div>
                                            <div class="bg-white rounded-xl border border-amber-200 p-2 shadow-2xs">
                                                <div id="cd-mins" class="font-black text-nampi-orange text-lg font-mono leading-none">00</div>
                                                <div class="text-[9px] uppercase font-bold text-slate-400 mt-1">Menit</div>
                                            </div>
                                            <div class="bg-white rounded-xl border border-amber-200 p-2 shadow-2xs">
                                                <div id="cd-secs" class="font-black text-nampi-orange text-lg font-mono leading-none">00</div>
                                                <div class="text-[9px] uppercase font-bold text-slate-400 mt-1">Detik</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons on Hero Card -->
                                <div class="space-y-2 pt-1">
                                    <a href="{{ url('/register') }}" class="relative group w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold bg-nampi-orange hover:bg-nampi-orange-hover text-white text-xs shadow-md transition-all">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                        </span>
                                        <span>Amankan Kuota Gelombang Ini</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>

                                    <a href="https://wa.me/6281323314430" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl font-semibold bg-emerald-600 hover:bg-emerald-700 text-white text-xs shadow-2xs transition-all">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        <span>Konsultasi Panitia via WhatsApp</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION HIGHLIGHT GELOMBANG AKTIF (Desain & Warna Serasi dengan 2 Jalur Pembinaan) -->
    <section id="gelombang-highlight" class="py-12 lg:py-16 bg-slate-900 text-white shadow-xl relative overflow-hidden border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-800 border border-slate-700 text-xs font-bold uppercase tracking-wider text-nampi-orange shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-nampi-orange opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-nampi-orange"></span>
                        </span>
                        <span>Prioritas Pendaftaran Aktif</span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-white leading-tight">
                        Daftar di {{ $gelombangAktif?->nama ?? 'Gelombang 1' }} Sekarang: Hemat Biaya &amp; Jaminan Kuota
                    </h2>
                    <p class="text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed">
                        Dapatkan potongan biaya Dana Sumbangan Pendidikan (DSP) hingga <strong class="text-nampi-orange underline decoration-nampi-orange/60 decoration-2 underline-offset-4">Rp 2.500.000</strong> dan jaminan alokasi jurusan pilihan (PPLG, TJKT, Pemasaran, Perhotelan) sebelum kuota kelas penuh.
                    </p>

                    <!-- Key Benefits Cards (Desain sama persis dengan kartu 2 Jalur Pembinaan) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-800 border border-nampi-orange/50 hover:border-nampi-orange  transition-colors">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xl">💰</span>
                                <div>
                                    <h4 class="font-bold text-nampi-orange text-sm">Hemat</h4>
                                    <p class="text-xs text-white mt-0.5">Hingga 2.5 Juta</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 hover:border-slate-600 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xl">🎯</span>
                                <div>
                                    <h4 class="font-bold text-white text-sm">Prioritas Kuota</h4>
                                    <p class="text-xs text-white mt-0.5">Amankan Kelas Favorit</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 hover:border-slate-600 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xl">📄</span>
                                <div>
                                    <h4 class="font-bold text-white text-sm">Brosur Resmi</h4>
                                    <p class="text-xs text-white mt-0.5">Download Gratis (PDF)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-3 text-center sm:text-left">
                    <a href="{{ url('/register') }}" class="relative group w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-xl font-bold bg-nampi-orange hover:bg-nampi-orange-hover text-white text-sm shadow-xl hover:shadow-2xl transition-all text-center transform hover:-translate-y-0.5">
                        <!-- Outer Ping Animation Ring -->
                        <span class="absolute -inset-0.5 rounded-xl bg-nampi-orange opacity-60 animate-ping pointer-events-none"></span>
                        
                        <!-- Inner ping indicator -->
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                        </span>
                        <span class="relative">Daftar Sekarang Juga</span>
                        <svg class="relative w-5 h-5 text-white group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="https://brosur.smkwikrama1garut.sch.id" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 text-xs shadow-xs transition-all text-center">
                        <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Brosur Lengkap (PDF)</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION GALERI FASILITAS SEKOLAH (1 Baris 4 Gambar, Tinggi ~200px, Hover Zoom) -->
    <section id="fasilitas" class="py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange bg-nampi-orange/10 px-3.5 py-1 rounded-full">Sarana & Prasarana Modern</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Galeri Fasilitas SMK Wikrama 1 Garut
                </h2>
                <p class="text-slate-600 text-sm sm:text-base">
                    Menyediakan lingkungan belajar yang representatif, modern, dan kondusif untuk mendukung pembelajaran vokasi berstandar industri dan pembinaan akhlak santri.
                </p>
            </div>

            <!-- 12 Facilities Grid: 1 Baris 4 Gambar di Desktop -->
            @php
                $fasilitasItems = [
                    [
                        'kategori' => 'Lab Praktik PPLG',
                        'judul' => 'Laboratorium Rekayasa Perangkat Lunak',
                        'deskripsi' => 'Workstation spesifikasi tinggi dengan dual-monitor, koneksi gigabit super cepat, dan lingkungan coding modern untuk pengembangan web, mobile, dan AI.',
                    ],
                    [
                        'kategori' => 'Lab Praktik TJKT',
                        'judul' => 'Laboratorium Jaringan Komputer & Server',
                        'deskripsi' => 'Dilengkapi rack server data center, router Mikrotik, switch Cisco manageable, dan peralatan perakitan fiber optic terkini.',
                        'gambar' => 'Lab-TJKT.jpg',
                    ],
                    [
                        'kategori' => 'Lab Praktik Perhotelan',
                        'judul' => 'Hotel Training Room & Front Office Mockup',
                        'deskripsi' => 'Kamar simulasi standar hotel berbintang 4 dan meja resepsionis modern untuk pelatihan hospitality, reservasi, dan housekeeping nyata.',
                    ],
                    [
                        'kategori' => 'Lab Praktik Pemasaran',
                        'judul' => 'Wikrama Digital Marketing & Business Lab',
                        'deskripsi' => 'Mini studio live streaming e-commerce, podcast room, dan workstation analitik iklan media sosial untuk praktik wirausaha digital.',
                    ],
                    [
                        'kategori' => 'Spiritual & Karakter',
                        'judul' => 'Masjid & Islamic Character Center',
                        'deskripsi' => 'Pusat ibadah harian, shalat berjamaah 5 waktu, kajian adab Islam, serta halaqah tahfidz Al-Qur\'an santri program unggulan.',
                    ],
                    [
                        'kategori' => 'Sumber Belajar',
                        'judul' => 'Perpustakaan Digital & Literacy Lounge',
                        'deskripsi' => 'Ribuan koleksi e-book teknologi, buku referensi kurikulum merdeka, ruang baca berpendingin udara (AC), dan area diskusi kolaboratif.',
                    ],
                    [
                        'kategori' => 'Akomodasi Santri',
                        'judul' => 'Asrama Putra & Putri Berstandar Nyaman',
                        'deskripsi' => 'Hunian santri asri dengan tempat tidur bertingkat ergonomis, lemari pribadi, sanitasi bersih, dan pengawasan musyrif 24 jam.',
                    ],
                    [
                        'kategori' => 'Ruang Teori',
                        'judul' => 'Ruang Kelas Ergonomis Ber-AC & Smart Screen',
                        'deskripsi' => 'Kelas representatif dengan smart projector interaktif, pencahayaan alami optimal, dan pendingin udara untuk kenyamanan belajar.',
                    ],
                    [
                        'kategori' => 'Olahraga & Kesehatan',
                        'judul' => 'Sport Center & Lapangan Multifungsi',
                        'deskripsi' => 'Fasilitas lapangan outdoor dan indoor untuk futsal, bola basket, bola voli, bulutangkis, serta kegiatan kebugaran jasmani santri.',
                    ],
                    [
                        'kategori' => 'Auditorium',
                        'judul' => 'Convention Hall & Amphitheatre Presentasi',
                        'deskripsi' => 'Gedung pertemuan representatif berkapasitas besar untuk wisuda, seminar nasional, sidang tugas akhir siswa, dan pameran teknologi.',
                    ],
                    [
                        'kategori' => 'Gizi & Nutrisi',
                        'judul' => 'Kantin Sehat Higienis & Dining Hall',
                        'deskripsi' => 'Layanan konsumsi higienis bersertifikasi sehat dengan menu bergizi seimbang bebas bahan pengawet untuk seluruh siswa dan santri.',
                    ],
                    [
                        'kategori' => 'Lingkungan Asri',
                        'judul' => 'Green Eco-School Park & Taman Terbuka',
                        'deskripsi' => 'Kawasan hijau lestari dengan pepohonan rindang, gazebo diskusi terbuka, dan taman ramah lingkungan untuk relaksasi belajar.',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($fasilitasItems as $index => $item)
                    <div class="group bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden">
                        <!-- Image Container: Tinggi 200px dengan Hover Zoom -->
                        <div class="relative h-[200px] w-full overflow-hidden bg-slate-100">
                            <img src="{{ !empty($item['gambar']) ? asset('images/' . $item['gambar']) : $dummyImg }}" alt="{{ $item['judul'] }}" class="w-full h-[200px] object-cover group-hover:scale-110 transition-transform duration-500 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/90 backdrop-blur-sm text-slate-800 border border-slate-200 shadow-2xs">
                                {{ $item['kategori'] }}
                            </span>
                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur-sm">
                                0{{ $index + 1 }}
                            </span>
                        </div>

                        <!-- Card Content / Keterangan Teks -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-nampi-orange transition-colors leading-snug">
                                    {{ $item['judul'] }}
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                    {{ $item['deskripsi'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION GALERI PROSES PEMBELAJARAN (1 Baris 4 Gambar, Tinggi ~200px, Hover Zoom) -->
    <section id="pembelajaran" class="py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-cyan bg-cyan-50 text-cyan-800 px-3.5 py-1 rounded-full border border-cyan-200">Aktivitas & Kurikulum Vokasi</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Galeri Proses Pembelajaran Nyata
                </h2>
                <p class="text-slate-600 text-sm sm:text-base">
                    Menerapkan pembelajaran kontekstual berbasis industri (Teaching Factory) yang dipadukan dengan pembiasaan adab dan karakter Qur'ani setiap hari.
                </p>
            </div>

            <!-- 12 Pembelajaran Grid: 1 Baris 4 Gambar di Desktop -->
            @php
                $pembelajaranItems = [
                    [
                        'kategori' => 'Project Based Learning',
                        'judul' => 'Penyelesaian Proyek Perangkat Lunak Industri',
                        'deskripsi' => 'Siswa PPLG menggarap aplikasi nyata sesuai pesanan klien industri, mulai dari analisis kebutuhan, rancang basis data, hingga rilis produksi.',
                    ],
                    [
                        'kategori' => 'Pendidikan Karakter',
                        'judul' => 'Pembiasaan Shalat Dhuha & Kultum Pagi',
                        'deskripsi' => 'Memulai hari dengan ibadah sunnah, tadarus bersama, dan penyampaian motivasi akhlak untuk menanamkan ketenangan batin sebelum KBM.',
                    ],
                    [
                        'kategori' => 'Coding & Software',
                        'judul' => 'Sesi Coding Sprint & Mentoring Praktisi',
                        'deskripsi' => 'Praktik pemrograman intensif menggunakan Laravel, Vue, React, dan Flutter didampingi langsung oleh senior software engineer mitra industri.',
                    ],
                    [
                        'kategori' => 'Infrastruktur Jaringan',
                        'judul' => 'Praktik Fiber Optic Splicing & Server Setup',
                        'deskripsi' => 'Siswa TJKT melakukan pengelasan kabel serat optik dengan fusi presisi dan konfigurasi routing antar-jaringan berskala enterprise.',
                    ],
                    [
                        'kategori' => 'Etika Profesional',
                        'judul' => 'Table Manner & Hospitality Standards',
                        'deskripsi' => 'Pelatihan jamuan makan resmi bertaraf internasional, teknik penyajian hidangan mewah, dan simulasi etika diplomasi perhotelan bintang 5.',
                    ],
                    [
                        'kategori' => 'Bisnis Digital',
                        'judul' => 'Live Commerce & Strategi Kampanye Medsos',
                        'deskripsi' => 'Siswa Pemasaran mempraktikkan penjualan langsung (live selling) multi-channel dan riset tren konten digital berkonversi tinggi.',
                    ],
                    [
                        'kategori' => 'Program Unggulan',
                        'judul' => 'Halaqah Tahsin & Setoran Hafalan Qur\'an',
                        'deskripsi' => 'Metode talaqqi intensif bersama asatidz bersanad untuk memperbaiki makharijul huruf dan menambah hafalan santri boarding.',
                    ],
                    [
                        'kategori' => 'Sertifikasi Kompetensi',
                        'judul' => 'Uji Kompetensi Keahlian (UKK) Bersama BNSP',
                        'deskripsi' => 'Pengujian kemampuan teknis siswa di hadapan asesor profesional independen dari Badan Nasional Sertifikasi Profesi (BNSP).',
                    ],
                    [
                        'kategori' => 'Budaya Industri',
                        'judul' => 'Penerapan Disiplin Kerja 5R (5S)',
                        'deskripsi' => 'Membiasakan Ringkas, Rapi, Resik, Rawat, dan Rajin pada setiap meja kerja dan laboratorium sebagai cerminan kesiapan mental kerja.',
                    ],
                    [
                        'kategori' => 'Kunjungan Industri',
                        'judul' => 'Tech-Tour ke Unicorn & Perusahaan Multinasional',
                        'deskripsi' => 'Melihat langsung ekosistem kerja nyata di markas perusahaan teknologi, perbankan, dan jaringan hotel terkemuka di Indonesia.',
                    ],
                    [
                        'kategori' => 'Pengembangan Bakat',
                        'judul' => 'Klub Robotika, IoT & English Active Club',
                        'deskripsi' => 'Wadah eksplorasi mikrokontroler Arduino/ESP32, otomasi cerdas, serta latihan debat bahasa Inggris untuk memperluas wawasan global.',
                    ],
                    [
                        'kategori' => 'Apresiasi Karya',
                        'judul' => 'Gelar Karya & Wikrama Innovation Expo',
                        'deskripsi' => 'Pameran tahunan hasil karya aplikasi, produk wirausaha, dan demonstrasi keahlian siswa yang disaksikan orang tua dan publik.',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($pembelajaranItems as $index => $item)
                    <div class="group bg-slate-50 hover:bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden">
                        <!-- Image Container: Tinggi 200px dengan Hover Zoom -->
                        <div class="relative h-[200px] w-full overflow-hidden bg-slate-200">
                            <img src="{{ !empty($item['gambar']) ? asset('images/' . $item['gambar']) : $dummyImg }}" alt="{{ $item['judul'] }}" class="w-full h-[200px] object-cover group-hover:scale-110 transition-transform duration-500 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-nampi-cyan text-white shadow-2xs">
                                {{ $item['kategori'] }}
                            </span>
                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur-sm">
                                #{{ $index + 1 }}
                            </span>
                        </div>

                        <!-- Card Content / Keterangan Teks -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-nampi-cyan transition-colors leading-snug">
                                    {{ $item['judul'] }}
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed mt-2">
                                    {{ $item['deskripsi'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION TESTIMONI & PROFIL LENGKAP -->
    <section id="testimoni" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">

            <!-- 1. PROFIL ALUMNI (Gambar Kecil, 1 Baris 6 Alumni di Desktop) -->
            <div>
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
                    <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange bg-nampi-orange/10 px-3.5 py-1 rounded-full">Kiprah & Kesuksesan Lulusan</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Profil 12 Alumni Berprestasi
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base">
                        Bukti nyata filosofi <em>"Lulus Wikrama Siap Membangun Negeri"</em>: Lulusan kami dipercaya berkarya di perusahaan teknologi multinasional, BUMN, perhotelan bintang lima, dan merintis startup mandiri.
                    </p>
                </div>

                @php
                    $alumniList = [
                        [
                            'nama' => 'Fajar Pratama, S.Kom.',
                            'jurusan' => 'PPLG 2018',
                            'karier' => 'Senior Fullstack Engineer',
                            'kantor' => 'Bukalapak',
                            'kutipan' => 'Pondasi logika dan kedisiplinan kerja di Wikrama membentuk mentalitas rekayasa saya.',
                        ],
                        [
                            'nama' => 'Nurul Aisyah, S.Tr.Kom.',
                            'jurusan' => 'TJKT 2019',
                            'karier' => 'Cloud Specialist',
                            'kantor' => 'Telkom Indonesia',
                            'kutipan' => 'Sertifikasi jaringan dan jam terbang lab di Wikrama membuat saya langsung lolos seleksi industri.',
                        ],
                        [
                            'nama' => 'Rizky Aditya Permana',
                            'jurusan' => 'Pemasaran 2020',
                            'karier' => 'Digital Marketing Lead',
                            'kantor' => 'Shopee Indonesia',
                            'kutipan' => 'Di Wikrama kami langsung praktik jualan riil dan ads. Menjadi modal utama karier saya.',
                        ],
                        [
                            'nama' => 'Siti Rahmawati',
                            'jurusan' => 'Perhotelan 2021',
                            'karier' => 'Guest Relation VVIP',
                            'kantor' => 'The Ritz-Carlton',
                            'kutipan' => 'Pendidikan adab dan tata krama Wikrama selaras dengan standar pelayanan hotel bintang 5.',
                        ],
                        [
                            'nama' => 'Dimas Anggara',
                            'jurusan' => 'PPLG 2020',
                            'karier' => 'Mobile Developer',
                            'kantor' => 'Tokopedia',
                            'kutipan' => 'Budaya kerja 5R dan ketepatan deadline membiasakan saya kerja lincah di startup unicorn.',
                        ],
                        [
                            'nama' => 'Anisa Fitriani, A.Md.',
                            'jurusan' => 'TJKT 2022',
                            'karier' => 'Security Analyst',
                            'kantor' => 'Bank Mandiri',
                            'kutipan' => 'Praktik keamanan server di Wikrama sangat presisi dengan standar audit perbankan.',
                        ],
                        [
                            'nama' => 'M. Yusuf Al-Farizi',
                            'jurusan' => 'PPLG 2021',
                            'karier' => 'Founder & CEO',
                            'kantor' => 'Garut Software Studio',
                            'kutipan' => 'Jiwa wirausaha dan akhlak mandiri dari Wikrama mendorong saya mendirikan software house sendiri.',
                        ],
                        [
                            'nama' => 'Dian Lestari',
                            'jurusan' => 'Pemasaran 2022',
                            'karier' => 'E-Commerce Lead',
                            'kantor' => 'Paragon Tech',
                            'kutipan' => 'Manajemen katalog digital dan analitik yang dipelajari di sekolah langsung terpakai di industri.',
                        ],
                        [
                            'nama' => 'Bagas Tri Wibowo',
                            'jurusan' => 'Perhotelan 2023',
                            'karier' => 'Pastry Assistant',
                            'kantor' => 'Hotel Tentrem',
                            'kutipan' => 'Standar higienitas dan disiplin dapur hotel di Wikrama membuat adaptasi saya sangat mulus.',
                        ],
                        [
                            'nama' => 'Zahra Safira',
                            'jurusan' => 'PPLG Unggulan 2023',
                            'karier' => 'Frontend Engineer',
                            'kantor' => 'Midtrans (GoTo)',
                            'kutipan' => 'Perpaduan hafalan Qur\'an dan ngoding di Program Unggulan memberi ketenangan dan fokus tinggi.',
                        ],
                        [
                            'nama' => 'Rian Hidayatullah',
                            'jurusan' => 'TJKT 2024',
                            'karier' => 'Data Center Ops',
                            'kantor' => 'PT DCI Indonesia',
                            'kutipan' => 'Lab server Wikrama membuat saya siap mengoperasikan fasilitas Tier IV terbesar di Asia Tenggara.',
                        ],
                        [
                            'nama' => 'Mutia Lutfiah',
                            'jurusan' => 'Pemasaran 2024',
                            'karier' => 'Social Media Lead',
                            'kantor' => 'Narasi Media',
                            'kutipan' => 'Riset audiens dan copywriting yang diajarkan membawa karya saya ditonton jutaan orang.',
                        ],
                    ];
                @endphp

                <!-- 1 Baris 6 Alumni di Desktop (grid-cols-2 sm:grid-cols-3 lg:grid-cols-6) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($alumniList as $alumni)
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:shadow-lg transition-all flex flex-col justify-between text-center group">
                            <div>
                                <!-- Gambar Kecil Bulat -->
                                <div class="w-14 h-14 rounded-full overflow-hidden bg-slate-100 border-2 border-amber-400 mx-auto mb-3 shadow-2xs">
                                    <img src="{{ !empty($alumni['gambar']) ? asset('images/' . $alumni['gambar']) : $dummyImg }}" alt="{{ $alumni['nama'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </div>
                                <h3 class="font-bold text-slate-900 text-xs sm:text-sm leading-tight">{{ $alumni['nama'] }}</h3>
                                <span class="text-[10px] font-bold text-nampi-orange block mt-1 uppercase">{{ $alumni['jurusan'] }}</span>
                                <div class="mt-2 py-1 px-1.5 rounded-lg bg-slate-50 border border-slate-100 text-[11px]">
                                    <span class="font-bold text-slate-800 block text-[10px]">{{ $alumni['karier'] }}</span>
                                    <span class="text-slate-500 text-[9px] block">{{ $alumni['kantor'] }}</span>
                                </div>
                                <p class="text-[10px] text-slate-600 italic leading-relaxed mt-2.5">
                                    "{{ $alumni['kutipan'] }}"
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. TESTIMONI MITRA INDUSTRI (8 Orang) -->
            <div class="pt-8 border-t border-slate-200">
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-3.5 py-1 rounded-full border border-emerald-300">DUDI & Dunia Kerja</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Testimoni 8 Pimpinan Industri Mitra
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base">
                        Apresiasi dari para pimpinan perusahaan teknologi, perhotelan, dan manufaktur nasional terhadap kompetensi serta integritas lulusan SMK Wikrama 1 Garut.
                    </p>
                </div>

                @php
                    $industriList = [
                        [
                            'nama' => 'Hendra Kusuma, M.Kom.',
                            'jabatan' => 'VP of Engineering',
                            'perusahaan' => 'PT Nusantara Teknologi Mandiri',
                            'testimoni' => 'Lulusan SMK Wikrama 1 Garut memiliki etos kerja yang langka: keterampilan teknis coding-nya matang, mudah beradaptasi, dan yang paling penting integritas kejujurannya luar biasa.',
                        ],
                        [
                            'nama' => 'Maya Anggraeni, M.Psi.',
                            'jabatan' => 'Head of People & Culture',
                            'perusahaan' => 'PT Astra International Tbk (Digital)',
                            'testimoni' => 'Setiap kali proses rekrutmen magang dan kerja, siswa Wikrama selalu menonjol dalam hal kedisiplinan waktu, kesantunan bertutur, serta inisiatif pemecahan masalah.',
                        ],
                        [
                            'nama' => 'Ir. Bambang Sugiarto',
                            'jabatan' => 'Head of Network Infrastructure',
                            'perusahaan' => 'PT Indosat Ooredoo Hutchison',
                            'testimoni' => 'Siswa TJKT Wikrama Garut sudah menguasai arsitektur serat optik dan konfigurasi routing dinamis. Masa onboarding dan adaptasi mereka sangat singkat.',
                        ],
                        [
                            'nama' => 'Dewi Sartika, CHA',
                            'jabatan' => 'General Manager',
                            'perusahaan' => 'Santika Premiere Hotel & Resort',
                            'testimoni' => 'Lulusan Perhotelan Wikrama Garut adalah aset berharga. Senyum tulus, standar kebersihan tinggi, dan kesopanan mereka mencerminkan didikan akhlak yang kokoh.',
                        ],
                        [
                            'nama' => 'Andreas Pratama, S.E.',
                            'jabatan' => 'Chief Marketing Officer',
                            'perusahaan' => 'PT Global Digital Niaga (Blibli)',
                            'testimoni' => 'Anak-anak Pemasaran Wikrama memiliki pemikiran analitis tajam terhadap tren media sosial serta kemampuan eksekusi campaign promosi yang rapi dan terukur.',
                        ],
                        [
                            'nama' => 'Rahmat Hidayat, S.T.',
                            'jabatan' => 'Managing Director',
                            'perusahaan' => 'Cybertech Solusindo Network',
                            'testimoni' => 'Kami telah bermitra lebih dari 5 tahun dengan SMK Wikrama 1 Garut. Sinkronisasi kurikulum sekolah ini benar-benar selaras dengan kebutuhan proyek riil di lapangan.',
                        ],
                        [
                            'nama' => 'Siska Handayani, S.Psi.',
                            'jabatan' => 'Talent Acquisition Manager',
                            'perusahaan' => 'PT Medion Farma Jaya',
                            'testimoni' => 'Kepatuhan terhadap SOP kerja dan ketelitian lulusan Wikrama sangat mengagumkan. Mereka cepat dipercaya memegang tanggung jawab sistem operasional penting.',
                        ],
                        [
                            'nama' => 'Farhan Gunawan, M.Sc.',
                            'jabatan' => 'Tech Lead & Co-Founder',
                            'perusahaan' => 'Algoritma Data Academy Jakarta',
                            'testimoni' => 'Komitmen Wikrama dalam mengajarkan logika komputasi modern dan akhlak budi pekerti patut dicontoh oleh seluruh lembaga pendidikan vokasi di Indonesia.',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($industriList as $item)
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs hover:shadow-lg transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border-2 border-emerald-400 shrink-0">
                                        <img src="{{ !empty($item['gambar']) ? asset('images/' . $item['gambar']) : $dummyImg }}" alt="{{ $item['nama'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $item['nama'] }}</h3>
                                        <p class="text-[11px] text-emerald-700 font-medium">{{ $item['jabatan'] }}</p>
                                    </div>
                                </div>
                                <div class="mb-3 px-2 py-1 rounded bg-emerald-50 text-[11px] font-bold text-emerald-900 border border-emerald-100">
                                    {{ $item['perusahaan'] }}
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed italic">
                                    "{{ $item['testimoni'] }}"
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. TESTIMONI TOKOH MASYARAKAT & PENDIDIKAN (6 Orang - Mengikuti Format Mitra Industri) -->
            <div class="pt-8 border-t border-slate-200">
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
                    <span class="text-xs font-bold uppercase tracking-wider text-cyan-800 bg-cyan-100 px-3.5 py-1 rounded-full border border-cyan-300">Pandangan Para Ahli</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Testimoni 6 Tokoh Masyarakat & Pendidikan
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base">
                        Pengakuan dari jajaran pengawas pendidikan, alim ulama, akademisi perguruan tinggi, dan perwakilan KADIN terhadap kontribusi nyata SMK Wikrama 1 Garut.
                    </p>
                </div>

                @php
                    $tokohList = [
                        [
                            'nama' => 'Drs. H. Mamat Rohimat, M.M.',
                            'jabatan' => 'Pengawas Pembina SMK',
                            'institusi' => 'Dinas Pendidikan Provinsi Jawa Barat',
                            'testimoni' => 'SMK Wikrama 1 Garut adalah rujukan sekolah vokasi unggul di Jawa Barat. Perpaduan kurikulum industri dan penanaman akhlak mulianya adalah model pendidikan masa depan yang ideal.',
                        ],
                        [
                            'nama' => 'K.H. Ahmad Syarifudin, M.Ag.',
                            'jabatan' => 'Ketua MUI & Pimpinan Ponpes',
                            'institusi' => 'MUI & Forum Pondok Pesantren',
                            'testimoni' => 'Saya bersaksi bahwa SMK Wikrama 1 Garut mendidik santri menjadi generasi yang tidak hanya tajam akalnya di bidang teknologi, tapi lembut hatinya dan rajin ibadahnya.',
                        ],
                        [
                            'nama' => 'Dr. Ir. H. Dedi Supriyadi, M.T.',
                            'jabatan' => 'Ketua Forum Vokasi Daerah',
                            'institusi' => 'Forum Kemitraan Vokasi & DUDI',
                            'testimoni' => 'Kemitraan sekolah ini dengan dunia industri bukan sekadar di atas kertas MoU formalitas, melainkan menghasilkan keterserapan kerja yang nyata dan berkelanjutan bagi putra daerah.',
                        ],
                        [
                            'nama' => 'Hj. Siti Nurjanah, S.Pd., M.Si.',
                            'jabatan' => 'Tokoh Pendidikan & Literasi',
                            'institusi' => 'Dewan Pendidikan Kabupaten Garut',
                            'testimoni' => 'Budaya literasi, kebersihan lingkungan, dan ketertiban di Wikrama Garut adalah teladan terbaik bagaimana sekolah menciptakan atmosfer belajar yang sangat memanusiakan siswa.',
                        ],
                        [
                            'nama' => 'Budi Santoso, S.E.',
                            'jabatan' => 'Ketua Bidang Ketenagakerjaan',
                            'institusi' => 'Kamar Dagang & Industri (KADIN) Daerah',
                            'testimoni' => 'Dunia usaha membutuhkan tenaga kerja yang siap pakai dan berintegritas tinggi. Wikrama 1 Garut secara konsisten menjadi pemasok talenta muda terbaik bagi industri.',
                        ],
                        [
                            'nama' => 'Prof. Dr. Hendra Gunawan, M.Sc.',
                            'jabatan' => 'Akademisi & Guru Besar',
                            'institusi' => 'Institut Teknologi Bandung (ITB)',
                            'testimoni' => 'Slogan "Lulus Wikrama Siap Membangun Negeri" terbukti dalam kompetensi riset dan etika para alumninya yang mampu bersaing di kancah nasional maupun global.',
                        ],
                    ];
                @endphp

                <!-- Mengikuti Card Format Mitra Industri -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tokohList as $item)
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs hover:shadow-lg transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border-2 border-cyan-400 shrink-0">
                                        <img src="{{ $dummyImg }}" alt="{{ $item['nama'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $item['nama'] }}</h3>
                                        <p class="text-[11px] text-cyan-700 font-medium">{{ $item['jabatan'] }}</p>
                                    </div>
                                </div>
                                <div class="mb-3 px-2 py-1 rounded bg-cyan-50 text-[11px] font-bold text-cyan-900 border border-cyan-100">
                                    {{ $item['institusi'] }}
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed italic">
                                    "{{ $item['testimoni'] }}"
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 4. TESTIMONI ORANG TUA SISWA (6 Orang - Mengikuti Format Mitra Industri) -->
            <div class="pt-8 border-t border-slate-200">
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3.5 py-1 rounded-full border border-amber-300">Kepercayaan Keluarga</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Testimoni 6 Orang Tua Siswa & Santri
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base">
                        Kisah haru dan rasa syukur para orang tua murid melihat transformasi karakter, kemandirian, dan masa depan putra-putrinya di SMK Wikrama 1 Garut.
                    </p>
                </div>

                @php
                    $ortuList = [
                        [
                            'nama' => 'H. Deden Suparman',
                            'relasi' => 'Orang Tua dari M. Farhan',
                            'jalur' => 'Program Unggulan (PPLG Boarding)',
                            'testimoni' => 'Alhamdulillah, sejak mondok di Wikrama anak saya jadi sangat mandiri. Shalat subuh selalu tepat waktu di masjid, dan kini sudah bisa membuat aplikasi pesanan orang. Bangga sekali sebagai orang tua.',
                        ],
                        [
                            'nama' => 'Ibu Rina Herawati',
                            'relasi' => 'Orang Tua dari Salsa Nabila',
                            'jalur' => 'Program Reguler (Perhotelan)',
                            'testimoni' => 'Tata krama dan kesantunan anak saya meningkat luar biasa. Di rumah pun bicaranya santun, hormat pada orang tua, dan rajin merapikan rumah. Sekolah ini benar-benar membentuk adab anak.',
                        ],
                        [
                            'nama' => 'Bapak Yudi Kurniawan',
                            'relasi' => 'Orang Tua dari Aditya Pratama',
                            'jalur' => 'Program Reguler (TJKT)',
                            'testimoni' => 'Biayanya sangat transparan tanpa pungutan liar. Di semester 4 anak saya sudah magang di perusahaan telekomunikasi besar dan langsung ditawari kontrak kerja setelah wisuda.',
                        ],
                        [
                            'nama' => 'Ibu Eni Rohaeni',
                            'relasi' => 'Orang Tua dari Fatimah Zahra',
                            'jalur' => 'Program Unggulan (PPLG Boarding)',
                            'testimoni' => 'Awalnya cemas melepas anak ke asrama. Ternyata pengasuhan musyrifah di Wikrama sangat penuh kasih sayang. Hafalan juznya bertambah dan prestasinya di sekolah gemilang.',
                        ],
                        [
                            'nama' => 'Bapak Agus Sugiarto',
                            'relasi' => 'Orang Tua dari Rizki Fauzi',
                            'jalur' => 'Program Reguler (Pemasaran)',
                            'testimoni' => 'Kemandirian anak saya terbukti saat dia mampu membiayai sebagian kebutuhan belajarnya sendiri dari hasil praktik jualan online yang dibimbing secara profesional oleh guru Wikrama.',
                        ],
                        [
                            'nama' => 'Ibu Maya Rosdiana',
                            'relasi' => 'Orang Tua dari Dinda Lestari',
                            'jalur' => 'Program Unggulan (TJKT Boarding)',
                            'testimoni' => 'Wikrama bukan sekadar tempat sekolah, melainkan rumah kedua yang menanamkan kebaikan, kedisiplinan, dan persiapan masa depan anak kami dengan penuh ketulusan.',
                        ],
                    ];
                @endphp

                <!-- Mengikuti Card Format Mitra Industri -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($ortuList as $item)
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs hover:shadow-lg transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border-2 border-amber-400 shrink-0">
                                        <img src="{{ $dummyImg }}" alt="{{ $item['nama'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $item['nama'] }}</h3>
                                        <p class="text-[11px] text-amber-800 font-medium">{{ $item['relasi'] }}</p>
                                    </div>
                                </div>
                                <div class="mb-3 px-2 py-1 rounded bg-amber-50 text-[11px] font-bold text-amber-900 border border-amber-100">
                                    {{ $item['jalur'] }}
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed italic">
                                    "{{ $item['testimoni'] }}"
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION PROGRAM KEAHLIAN & JURUSAN -->
    <section id="program" class="py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange bg-nampi-orange/10 px-3 py-1 rounded-full">Kompetensi Kejuruan</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    4 Program Keahlian Berstandar Industri
                </h2>
                <p class="text-slate-600 text-sm sm:text-base">
                    Dirancang dengan kurikulum berbasis kompetensi DUDI dan bersertifikasi BNSP untuk mencetak tenaga kerja profesional yang siap diserap dunia kerja.
                </p>
            </div>

            <!-- Jurusan Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- PPLG -->
                <div class="p-6 rounded-2xl border border-slate-200/90 bg-slate-50 hover:bg-white hover:border-nampi-orange/70 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-nampi-orange flex items-center justify-center font-bold text-2xl mb-4 group-hover:scale-110 transition-transform">
                            ⚙️
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">PPLG</h3>
                        <p class="text-xs font-bold text-nampi-orange uppercase tracking-wider mb-3">Pengembangan Perangkat Lunak & Gim</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Fokus pada rekayasa perangkat lunak modern, pengembangan web (fullstack), aplikasi mobile Android/iOS, API services, dan logika komputasi.
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-200/70 text-[11px] font-semibold text-slate-500">
                        Karier: Web Developer, Mobile Dev, UI/UX, QA Tester
                    </div>
                </div>

                <!-- TJKT -->
                <div class="p-6 rounded-2xl border border-slate-200/90 bg-slate-50 hover:bg-white hover:border-nampi-cyan/70 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-cyan-100 text-nampi-cyan flex items-center justify-center font-bold text-2xl mb-4 group-hover:scale-110 transition-transform">
                            💻
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">TJKT</h3>
                        <p class="text-xs font-bold text-nampi-cyan uppercase tracking-wider mb-3">Teknik Jaringan Komputer & Telekomunikasi</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Keahlian instalasi infrastruktur fiber optic, routing BGP/OSPF, administrasi server Linux/Cloud, serta keamanan siber (cyber security).
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-200/70 text-[11px] font-semibold text-slate-500">
                        Karier: Network Engineer, SysAdmin, Cloud Ops, NOC
                    </div>
                </div>

                <!-- PEMASARAN -->
                <div class="p-6 rounded-2xl border border-slate-200/90 bg-slate-50 hover:bg-white hover:border-emerald-500/70 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-2xl mb-4 group-hover:scale-110 transition-transform">
                            📈
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Pemasaran</h3>
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-3">Bisnis Digital & Digital Marketing</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mempelajari optimasi search engine (SEO), manajemen kampanye iklan berbayar (Ads), content creation, live commerce, dan kewirausahaan.
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-200/70 text-[11px] font-semibold text-slate-500">
                        Karier: Digital Marketer, Content Creator, E-Commerce Ops
                    </div>
                </div>

                <!-- PERHOTELAN -->
                <div class="p-6 rounded-2xl border border-slate-200/90 bg-slate-50 hover:bg-white hover:border-amber-400 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-2xl mb-4 group-hover:scale-110 transition-transform">
                            🏨
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Perhotelan</h3>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-3">Hospitality & Tourism Industry</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Keahlian tata hidang berstandar internasional (Food & Beverage Service), divisi kamar (Housekeeping), resepsionis (Front Office), dan event management.
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-200/70 text-[11px] font-semibold text-slate-500">
                        Karier: Hotelier, Front Desk VVIP, Event Planner
                    </div>
                </div>
            </div>

            <!-- Program Selector (Reguler / Unggulan) -->
            <div class="mt-12 p-8 rounded-3xl bg-slate-900 text-white shadow-xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange">2 Jalur Pembinaan</span>
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 mb-3">Program Reguler & Program Unggulan</h3>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Calon peserta didik dapat menentukan jalur program yang selaras dengan cita-cita akademik, kesiapan mandiri, serta pembinaan akhlak komprehensif.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700">
                            <h4 class="font-bold text-white text-base">Program Reguler</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Kurikulum vokasi standar industri dengan pembelajaran teori dan praktik seimbang (non-asrama).</p>
                        </div>
                        <div class="p-5 rounded-2xl bg-slate-800 border border-nampi-orange/50">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-nampi-orange text-base">Program Unggulan</h4>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-nampi-orange text-white">Boarding</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Wajib tinggal di asrama pondok, bimbingan Tahfidz Al-Qur'an, pembiasaan akhlak 24 jam, dan pendalaman IT.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION TRANSPARANSI BIAYA PENDIDIKAN (Minimalis, Ringkas, & Proporsional) -->
    <section id="biaya" class="py-16 sm:py-20 bg-slate-50/70 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto space-y-2 mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange bg-nampi-orange/10 px-3.5 py-1 rounded-full">Investasi Pendidikan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Rincian Biaya Pendidikan TA 2027/2028
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                    Investasi terbaik keluarga adalah pendidikan anak, bekal berharga untuk masa depannya.
                </p>
            </div>

            <!-- Ringkasan Cepat 3 Kartu Minimalis -->
            <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-9 h-9 rounded-xl bg-orange-50 text-nampi-orange flex items-center justify-center text-base shrink-0 font-bold">📋</span>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pendaftaran Awal</span>
                            <h4 class="font-bold text-slate-800 text-sm">Biaya Seleksi</h4>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-2">Rp 200.000</div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Sekali bayar untuk semua gelombang &amp; program pendaftaran.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-9 h-9 rounded-xl bg-cyan-50 text-nampi-cyan flex items-center justify-center text-base shrink-0 font-bold">💰</span>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Daftar Ulang</span>
                            <h4 class="font-bold text-slate-800 text-sm">DSP Gelombang 1</h4>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-nampi-cyan mt-2">Mulai Rp 3.500.000</div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Hemat s.d Rp 2.500.000 dengan daftar ulang di Gelombang 1.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-xs transition-shadow">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0 font-bold">🗓️</span>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rutin Bulanan</span>
                            <h4 class="font-bold text-slate-800 text-sm">SPP Sekolah</h4>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 mt-2">Mulai Rp 450.000<span class="text-xs font-normal text-slate-500">/bln</span></div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Tersedia opsi SPP Pondok (Boarding) untuk Program Unggulan.</p>
                </div>
            </div> -->

            <!-- Tabel Komparasi Biaya Minimalis -->
            <div class="overflow-hidden rounded-2xl border border-slate-200/90 shadow-2xs bg-white">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Tabel Komparasi Biaya Pendidikan</h3>
                        <p class="text-xs text-slate-500">Program Unggulan (Boarding) vs Program Reguler (Non-Boarding)</p>
                    </div>
                    <span class="text-[11px] font-semibold bg-white border border-slate-200 px-3 py-1 rounded-full text-slate-600 self-start sm:self-auto">
                        Tahun Ajaran 2027/2028
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse min-w-[600px]">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="py-3 px-5 font-bold text-slate-700 text-xs uppercase tracking-wider w-5/12">Komponen Pembiayaan</th>
                                <th class="py-3 px-5 font-bold text-nampi-orange text-xs uppercase tracking-wider w-7/24 bg-amber-50/40 border-l border-r border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <span>Program Unggulan</span>
                                        <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-nampi-orange text-white">Boarding</span>
                                    </div>
                                </th>
                                <th class="py-3 px-5 font-bold text-slate-700 text-xs uppercase tracking-wider w-7/24">
                                    <div class="flex items-center justify-between">
                                        <span>Program Reguler</span>
                                        <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Reguler</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <!-- Kategori 1: Uang Pendaftaran -->
                            <tr class="bg-slate-50/70">
                                <td colspan="3" class="py-2 px-5 font-bold text-[11px] uppercase tracking-wider text-slate-600">
                                    📋 1. Biaya Pendaftaran / Seleksi Masuk (Sekali Bayar)
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="font-medium text-slate-800 text-xs sm:text-sm">Biaya Seleksi Awal</div>
                                    <div class="text-[11px] text-slate-400">Administrasi akun, pemetaan minat &amp; wawancara</div>
                                </td>
                                <td class="py-3 px-5 font-bold text-slate-900 bg-amber-50/20 border-l border-r border-slate-200 text-xs sm:text-sm">
                                    Rp 200.000
                                </td>
                                <td class="py-3 px-5 font-bold text-slate-900 text-xs sm:text-sm">
                                    Rp 200.000
                                </td>
                            </tr>

                            <!-- Kategori 2: DSP -->
                            <tr class="bg-slate-50/70">
                                <td colspan="3" class="py-2 px-5 font-bold text-[11px] uppercase tracking-wider text-slate-600">
                                    💰 2. Dana Sumbangan Pendidikan (DSP) - Sekali Saat Daftar Ulang
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="font-semibold text-slate-800 text-xs sm:text-sm flex items-center gap-2">
                                        <span>Gelombang 1</span>
                                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded-full">Hemat Terbesar</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">Periode awal pendaftaran</div>
                                </td>
                                <td class="py-3 px-5 font-bold text-nampi-orange bg-amber-50/20 border-l border-r border-slate-200 text-xs sm:text-sm">
                                    Rp 7.500.000
                                    <span class="block text-[10px] font-medium text-emerald-600">Hemat Rp 2.000.000</span>
                                </td>
                                <td class="py-3 px-5 font-bold text-emerald-600 text-xs sm:text-sm">
                                    Rp 3.500.000
                                    <span class="block text-[10px] font-medium text-emerald-600">Hemat Rp 2.500.000</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="font-medium text-slate-800 text-xs sm:text-sm">Gelombang 2</div>
                                    <div class="text-[11px] text-slate-400">Periode lanjutan</div>
                                </td>
                                <td class="py-3 px-5 font-semibold text-slate-800 bg-amber-50/20 border-l border-r border-slate-200 text-xs sm:text-sm">
                                    Rp 8.500.000
                                </td>
                                <td class="py-3 px-5 font-semibold text-slate-800 text-xs sm:text-sm">
                                    Rp 4.500.000
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="font-medium text-slate-800 text-xs sm:text-sm">Gelombang 3</div>
                                    <div class="text-[11px] text-slate-400">Periode akhir</div>
                                </td>
                                <td class="py-3 px-5 font-semibold text-slate-800 bg-amber-50/20 border-l border-r border-slate-200 text-xs sm:text-sm">
                                    Rp 9.500.000
                                </td>
                                <td class="py-3 px-5 font-semibold text-slate-800 text-xs sm:text-sm">
                                    Rp 6.000.000
                                </td>
                            </tr>

                            <!-- Kategori 3: SPP -->
                            <tr class="bg-slate-50/70">
                                <td colspan="3" class="py-2 px-5 font-bold text-[11px] uppercase tracking-wider text-slate-600">
                                    🗓️ 3. Sumbangan Pembinaan Pendidikan (SPP) Bulanan
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="font-semibold text-slate-800 text-xs sm:text-sm">SPP Sekolah</div>
                                    <div class="text-[11px] text-slate-400">Operasional KBM &amp; praktikum lab</div>
                                </td>
                                <td class="py-3 px-5 font-bold text-slate-900 bg-amber-50/20 border-l border-r border-slate-200 text-xs sm:text-sm">
                                    Rp 650.000 <span class="text-[11px] font-normal text-slate-500">/ bulan</span>
                                </td>
                                <td class="py-3 px-5 font-bold text-slate-900 text-xs sm:text-sm">
                                    Rp 450.000 <span class="text-[11px] font-normal text-slate-500">/ bulan</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="font-semibold text-slate-800 text-xs sm:text-sm flex items-center gap-1.5">
                                        <span>SPP Pondok (Asrama)</span>
                                        <span class="text-[9px] font-bold text-cyan-800 bg-cyan-100 px-1.5 py-0.5 rounded-full">Boarding</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">Akomodasi kamar, makan, laundry &amp; tahfidz</div>
                                </td>
                                <td class="py-3 px-5 font-bold text-nampi-cyan bg-amber-50/20 border-l border-r border-slate-200 text-xs sm:text-sm">
                                    Rp 1.450.000 <span class="text-[11px] font-normal text-slate-500">/ bulan</span>
                                </td>
                                <td class="py-3 px-5 text-slate-400 text-xs sm:text-sm">
                                    <span class="text-xs italic text-slate-400">— (Khusus Non-Pondok)</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footnote Fasilitas Pondok Minimalis -->
                <div class="px-5 py-3 bg-cyan-50/50 border-t border-cyan-100 text-xs text-slate-600 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-nampi-cyan">ℹ️ Keterangan SPP Pondok:</span>
                        <span>Fasilitas lemari, tempat tidur, makan 2x weekday &amp; 3x weekend, laundry, serta pembelajaran agama &amp; tahfidz.</span>
                    </div>
                </div>
            </div>

            <!-- Download Brosur Banner Minimalis -->
            <div class="mt-6 p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 text-center sm:text-left">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-lg shrink-0">
                        📄
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Unduh Brosur Resmi SPMB Wikrama Garut</h4>
                        <p class="text-xs text-slate-500">Informasi lengkap rincian program, kurikulum, dan tata cara pendaftaran (PDF).</p>
                    </div>
                </div>
                <a href="https://brosur.smkwikrama1garut.sch.id" target="_blank" rel="noopener noreferrer" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold bg-slate-900 hover:bg-slate-800 text-white text-xs shadow-xs transition-colors">
                    <span>Download Brosur (PDF)</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION ALUR SPMB 5 LANGKAH -->
    <section id="alur" class="py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-cyan bg-cyan-50 text-cyan-800 px-3.5 py-1 rounded-full border border-cyan-200">Tahapan Praktis</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    5 Langkah Mudah Pendaftaran di SPMB NAMPI
                </h2>
                <p class="text-slate-600 text-sm sm:text-base">
                    Proses terintegrasi dan transparan dari pengisian data awal hingga resmi menjadi siswa baru.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/90 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-nampi-orange text-white font-bold flex items-center justify-center text-sm mb-4 shadow-2xs">1</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1.5">Registrasi Akun</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Isi formulir NISN, nama lengkap, dan nomor WhatsApp aktif. Akun dibuat seketika.</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/90 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-nampi-cyan text-white font-bold flex items-center justify-center text-sm mb-4 shadow-2xs">2</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1.5">Bayar Biaya Seleksi (Rp 200.000)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Transfer biaya seleksi Rp 200.000 dan unggah bukti transfer ke portal untuk verifikasi panitia.</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/90 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white font-bold flex items-center justify-center text-sm mb-4 shadow-2xs">3</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1.5">Isi Biodata</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Lengkapi data pribadi, identitas orang tua, riwayat akademik rapor, ukuran seragam, dan berkas.</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/90 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-sm mb-4 shadow-2xs">4</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1.5">Wawancara</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Ikuti sesi wawancara bakat minat calon siswa serta pemetaan kesepahaman bersama orang tua.</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/90 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold flex items-center justify-center text-sm mb-4 shadow-2xs">5</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1.5">Pengumuman</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Cek status kelulusan di dashboard, unduh rincian tagihan daftar ulang, lalu selesaikan pembayaran.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION FAQ (10 Pertanyaan Lengkap) -->
    <section id="faq" class="py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600 bg-slate-200 px-3.5 py-1 rounded-full">Informasi Penting</span>
                <h2 class="text-3xl font-extrabold text-slate-900">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                <p class="text-slate-500 text-xs sm:text-sm">Temukan jawaban atas pertanyaan umum mengenai proses pendaftaran, kurikulum, asrama, dan pembiayaan di SMK Wikrama 1 Garut.</p>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">1. Kapan batas akhir pendaftaran di gelombang yang sedang aktif?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Pendaftaran gelombang aktif akan ditutup sesuai target tanggal yang tercantum pada hitung mundur atau saat kuota kelas masing-masing jurusan telah terpenuhi. Kami menyarankan calon murid untuk mendaftar lebih awal guna mengamankan jurusan pilihan.
                    </p>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">2. Apa perbedaan mendasar Program Unggulan dan Program Reguler?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Program Unggulan mewajibkan santri tinggal di asrama pondok dengan penguatan tahfidz Al-Qur'an, adab Islam 24 jam, serta jam praktik kejuruan intensif. Sedangkan Program Reguler berfokus pada kurikulum vokasi industri harian tanpa kewajiban tinggal di asrama.
                    </p>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">3. Apakah pembayaran biaya daftar ulang (DSP) bisa dicicil?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Ya. Sekolah menyediakan skema pembayaran bertahap untuk Dana Sumbangan Pendidikan (DSP). Rincian skema termin cicilan dapat dikonsultasikan langsung saat sesi wawancara keuangan bersama bendahara sekolah.
                    </p>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">4. Di mana saya bisa mengunduh brosur resmi SPMB SMK Wikrama 1 Garut?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Brosur resmi dapat diunduh gratis melalui tombol download brosur di menu navigasi atas atau dengan membuka tautan resmi <a href="https://brosur.smkwikrama1garut.sch.id" target="_blank" rel="noopener noreferrer" class="text-nampi-orange font-bold hover:underline">brosur.smkwikrama1garut.sch.id</a>.
                    </p>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">5. Apa saja materi yang diujikan dalam tes pemetaan dan wawancara?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Tes pemetaan mencakup observasi minat bakat, dasar logika komputasi/literasi kejuruan, tes membaca Al-Qur'an (untuk pemetaan tahsin/tahfidz), serta wawancara kesepahaman komitmen belajar bersama orang tua.
                    </p>
                </div>

                <!-- FAQ 6 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">6. Apakah lulusan SMK Wikrama 1 Garut disalurkan kerja atau bisa melanjutkan kuliah?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Lulusan Wikrama memiliki fleksibilitas tinggi (BMW: Bekerja, Melanjutkan Kuliah, Wirausaha). Bursa Kerja Khusus (BKK) Wikrama bekerja sama dengan 100+ mitra industri nasional, dan banyak pula alumni yang diterima di PTN ternama serta perguruan tinggi kedinasan.
                    </p>
                </div>

                <!-- FAQ 7 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">7. Fasilitas apa saja yang diperoleh santri yang tinggal di asrama (Boarding)?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        SPP Pondok sudah mencakup fasilitas tempat tidur dan lemari pribadi, konsumsi makan bergizi teratur (2x weekday, 3x weekend), layanan cuci seragam & pakaian (laundry), serta bimbingan tahfidz dan ibadah oleh musyrif/musyrifah 24 jam.
                    </p>
                </div>

                <!-- FAQ 8 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">8. Kapan seragam sekolah dibagikan dan bagaimana proses pengukurannya?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Penginputan ukuran seragam dilakukan secara online pada portal calon siswa setelah data diri lengkap. Pengambilan seragam fisik dijadwalkan menjelang Masa Pengenalan Lingkungan Sekolah (MPLS) setelah daftar ulang diselesaikan.
                    </p>
                </div>

                <!-- FAQ 9 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">9. Apakah calon siswa dari luar Kabupaten Garut atau luar provinsi bisa mendaftar?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Sangat bisa. Sistem SPMB NAMPI berbasis online penuh sehingga calon siswa dari seluruh Indonesia dapat mendaftar, mengunggah dokumen, dan mengikuti wawancara secara daring, khususnya bagi yang memilih Program Unggulan Berasrama.
                    </p>
                </div>

                <!-- FAQ 10 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">10. Kemana saya menghubungi panitia jika mengalami kendala teknis pendaftaran online?</h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                        Panitia SPMB siap membantu Anda melalui WhatsApp Helpdesk di nomor <a href="https://wa.me/6281323314430" class="text-emerald-600 font-bold hover:underline">+62 813-2331-4430</a>, atau Anda dapat berkunjung langsung ke Sekretariat SPMB di Kampus SMK Wikrama 1 Garut setiap hari kerja pukul 08.00 - 15.00 WIB.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CTA BANNER -->
    <section class="py-16 bg-gradient-to-r from-nampi-orange via-amber-500 to-amber-600 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                🎓 Lulus Wikrama Siap Membangun Negeri
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">
                Siap Menjadi Generasi Unggul Berakhlak Mulia?
            </h2>
            <p class="text-base sm:text-lg text-amber-100 max-w-2xl mx-auto leading-relaxed">
                Solusi pendidikan akhlak berkualitas di zaman modern. Daftarkan diri Anda sekarang dan bergabunglah bersama keluarga besar SMK Wikrama 1 Garut.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <a href="{{ url('/register') }}" class="relative group w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl font-extrabold bg-white text-orange-700 hover:bg-orange-50 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-0.5 text-base">
                    <span class="absolute -inset-0.5 rounded-xl bg-white opacity-40 animate-ping pointer-events-none"></span>
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-600 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-600"></span>
                    </span>
                    <span class="relative">Mulai Pendaftaran Online</span>
                    <svg class="relative w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="https://brosur.smkwikrama1garut.sch.id" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl font-bold bg-amber-900/30 hover:bg-amber-900/50 text-white border border-white/30 backdrop-blur-sm transition-all text-sm">
                    <svg class="w-4 h-4 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download Brosur (PDF)</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Real-time Countdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('countdown-container');
            if (!container) return;

            const targetStr = container.getAttribute('data-target');
            if (!targetStr) return;

            const targetDate = new Date(targetStr).getTime();
            const daysEl = document.getElementById('cd-days');
            const hoursEl = document.getElementById('cd-hours');
            const minsEl = document.getElementById('cd-mins');
            const secsEl = document.getElementById('cd-secs');

            function updateTimer() {
                const now = new Date().getTime();
                const diff = targetDate - now;

                if (isNaN(diff) || diff <= 0) {
                    if (daysEl) daysEl.textContent = '0';
                    if (hoursEl) hoursEl.textContent = '00';
                    if (minsEl) minsEl.textContent = '00';
                    if (secsEl) secsEl.textContent = '00';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (daysEl) daysEl.textContent = days;
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                if (minsEl) minsEl.textContent = String(minutes).padStart(2, '0');
                if (secsEl) secsEl.textContent = String(seconds).padStart(2, '0');
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    </script>
</x-layouts.guest>
