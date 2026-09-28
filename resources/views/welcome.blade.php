<x-layouts.guest>
    <x-slot name="title">Nampi — SPMB SMK Wikrama 1 Garut TA 2026/2027</x-slot>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-nampi-cream/40 via-white to-slate-50 pt-12 pb-20 lg:pt-20 lg:pb-28 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Hero Text -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-nampi-orange/10 border border-nampi-orange/20 text-xs font-semibold text-nampi-orange">
                        <span class="w-2 h-2 rounded-full bg-nampi-orange animate-pulse"></span>
                        Penerimaan Murid Baru Tahun Ajaran 2026/2027 Telah Dibuka
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Wujudkan Masa Depan Gemilang di <span class="text-transparent bg-clip-text bg-gradient-to-r from-nampi-orange via-amber-500 to-nampi-cyan">SMK Wikrama 1 Garut</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Aplikasi resmi <strong>NAMPI</strong> memudahkan pendaftaran calon peserta didik secara transparan, praktis, dan terintegrasi dari pengisian data hingga daftar ulang.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <x-button variant="primary" size="lg" href="{{ url('/register') }}" class="w-full sm:w-auto shadow-md hover:shadow-lg">
                            Daftar Sekarang (Calon Siswa)
                            <svg class="w-5 h-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </x-button>
                        <x-button variant="outline" size="lg" href="{{ url('/login') }}" class="w-full sm:w-auto">
                            Sudah Punya Akun? Masuk
                        </x-button>
                    </div>

                    <!-- Trust indicators -->
                    <div class="pt-6 grid grid-cols-3 gap-4 border-t border-slate-200/80 max-w-lg mx-auto lg:mx-0 text-center">
                        <div>
                            <div class="text-2xl font-extrabold text-slate-900">4</div>
                            <div class="text-xs text-slate-500 font-medium">Program Keahlian</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold text-nampi-orange">2</div>
                            <div class="text-xs text-slate-500 font-medium">Jalur Program</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold text-nampi-green">100%</div>
                            <div class="text-xs text-slate-500 font-medium">Online & Terpadu</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual Card with Wikrama Logo & Highlights -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <!-- Decorative glow -->
                        <div class="absolute -top-6 -left-6 w-72 h-72 bg-nampi-orange/20 rounded-full blur-3xl -z-10"></div>
                        <div class="absolute -bottom-6 -right-6 w-72 h-72 bg-nampi-cyan/20 rounded-full blur-3xl -z-10"></div>

                        <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl relative">
                            <div class="flex items-center justify-center pb-6 border-b border-slate-100">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Wikrama 1 Garut" class="h-28 w-auto object-contain drop-shadow-sm">
                            </div>

                            <div class="mt-6 space-y-4">
                                <div class="text-center">
                                    <h3 class="font-bold text-slate-900 text-lg">SMK Wikrama 1 Garut</h3>
                                    <p class="text-xs text-slate-500">Disiplin, Berakhlak Mulia, dan Siap Kerja</p>
                                </div>

                                <div class="p-4 rounded-xl bg-nampi-cream/50 border border-amber-200/70 space-y-2">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                                        <span>Status Pendaftaran:</span>
                                        <x-badge variant="green">DIBUKA</x-badge>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-slate-600">
                                        <span>Gelombang Aktif:</span>
                                        <span class="font-medium text-slate-800">Gelombang 1</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-slate-600">
                                        <span>Biaya Seleksi:</span>
                                        <span class="font-bold text-nampi-orange">Rp 250.000</span>
                                    </div>
                                </div>

                                <div class="text-center pt-2">
                                    <p class="text-xs text-slate-500 mb-3">Butuh panduan langsung dari panitia?</p>
                                    <a href="https://wa.me/6281323314430" target="_blank" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        Chat WhatsApp Helpdesk
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jurusan & Program Section -->
    <section id="program" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange bg-nampi-orange/10 px-3 py-1 rounded-full">Pilihan Program & Keahlian</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Kompetensi Unggulan Berstandar Industri
                </h2>
                <p class="text-slate-600 text-sm sm:text-base">
                    SMK Wikrama 1 Garut menyediakan program studi terakreditasi dan relevan dengan kebutuhan dunia usaha dan dunia industri (DUDI).
                </p>
            </div>

            <!-- Jurusan Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- TJKT -->
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-nampi-cyan/60 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 text-nampi-cyan flex items-center justify-center font-bold text-lg mb-4 group-hover:scale-110 transition-transform">
                        💻
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">TJKT</h3>
                    <p class="text-xs font-semibold text-nampi-cyan uppercase mb-2">Teknik Jaringan Komputer & Telekomunikasi</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Keahlian infrastruktur server, jaringan kabel dan nirkabel, cyber security, dan cloud administration.
                    </p>
                </div>

                <!-- PPLG -->
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-nampi-orange/60 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-nampi-orange flex items-center justify-center font-bold text-lg mb-4 group-hover:scale-110 transition-transform">
                        ⚙️
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">PPLG</h3>
                    <p class="text-xs font-semibold text-nampi-orange uppercase mb-2">Pengembangan Perangkat Lunak & Gim</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Fokus pada pembuatan aplikasi web modern, mobile development, backend API, dan perancangan perangkat lunak.
                    </p>
                </div>

                <!-- PEMASARAN -->
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-nampi-green/60 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-nampi-green flex items-center justify-center font-bold text-lg mb-4 group-hover:scale-110 transition-transform">
                        📈
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Pemasaran</h3>
                    <p class="text-xs font-semibold text-nampi-green uppercase mb-2">Bisnis Digital & Marketing</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Mempelajari digital marketing, e-commerce management, content strategy, copy writing, dan wirausaha modern.
                    </p>
                </div>

                <!-- PERHOTELAN -->
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-amber-400 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg mb-4 group-hover:scale-110 transition-transform">
                        🏨
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Perhotelan</h3>
                    <p class="text-xs font-semibold text-amber-600 uppercase mb-2">Hospitality & Tourism</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Keterampilan tata hidang, front office, housekeeping berstandar hotel berbintang nasional dan internasional.
                    </p>
                </div>
            </div>

            <!-- Program Selector (Reguler / Unggulan) -->
            <div class="mt-12 p-8 rounded-3xl bg-slate-900 text-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange">Jalur Pilihan</span>
                        <h3 class="text-2xl font-bold mt-1 mb-3">Program Reguler & Program Unggulan</h3>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Calon peserta didik dapat memilih jalur program yang sesuai dengan minat, target akademik, dan pembinaan karakter komprehensif.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700">
                            <h4 class="font-bold text-white text-base">Program Reguler</h4>
                            <p class="text-xs text-slate-400 mt-1">Kurikulum vokasi standar industri dengan pembelajaran teori dan praktik seimbang.</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-800/80 border border-nampi-orange/50">
                            <h4 class="font-bold text-nampi-orange text-base">Program Unggulan</h4>
                            <p class="text-xs text-slate-400 mt-1">Pendalaman intensif kejuruan, Hafalan Qur'an, Kemandirian dan pembinaan Akhlak dan Adab.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur SPMB Timeline Section -->
    <section id="alur" class="py-20 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-nampi-cyan bg-nampi-cyan/10 px-3 py-1 rounded-full">Alur Pendaftaran</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Langkah Mudah Bergabung di SPMB Nampi
                </h2>
                <p class="text-slate-600 text-sm sm:text-base">
                    Ikuti tahapan alur penerimaan murid baru dari pendaftaran awal hingga resmi terdaftar.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/80 relative">
                    <div class="w-10 h-10 rounded-full bg-nampi-orange text-white font-bold flex items-center justify-center text-sm mb-4">
                        1
                    </div>
                    <h4 class="font-bold text-slate-800 text-base mb-1">Registrasi Akun</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Isi form pendaftaran singkat menggunakan NISN dan nomor HP aktif. Nomor Pendaftaran & akun dibuat otomatis.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/80 relative">
                    <div class="w-10 h-10 rounded-full bg-nampi-cyan text-white font-bold flex items-center justify-center text-sm mb-4">
                        2
                    </div>
                    <h4 class="font-bold text-slate-800 text-base mb-1">Bayar Biaya Seleksi</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lakukan transfer biaya seleksi dan unggah bukti transfer di dashboard untuk diverifikasi oleh Bendahara.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/80 relative">
                    <div class="w-10 h-10 rounded-full bg-amber-500 text-white font-bold flex items-center justify-center text-sm mb-4">
                        3
                    </div>
                    <h4 class="font-bold text-slate-800 text-base mb-1">Lengkapi Biodata & Wawancara</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lengkapi data diri, orang tua, akademik, ukuran seragam, persetujuan kesepahaman, serta ikuti wawancara.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/80 relative">
                    <div class="w-10 h-10 rounded-full bg-nampi-green text-white font-bold flex items-center justify-center text-sm mb-4">
                        4
                    </div>
                    <h4 class="font-bold text-slate-800 text-base mb-1">Keputusan & Daftar Ulang</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Cek pengumuman kelulusan, unduh tagihan daftar ulang, lakukan pembayaran, dan resmi menjadi siswa Wikrama!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Biaya & Gelombang Section -->
    <section id="biaya" class="py-20 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <span class="text-xs font-bold uppercase tracking-wider text-nampi-orange bg-nampi-orange/10 px-3 py-1 rounded-full">Transparansi Biaya</span>
                    <h2 class="text-3xl font-extrabold text-slate-900">
                        Investasi Pendidikan Transparan & Terjangkau
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Seluruh rincian pembiayaan pendidikan disusun secara transparan dengan skema tagihan snapshot yang aman dan jelas. Komponen biaya mencakup seleksi, dana sarana, SPP, dan seragam.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-700">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">✓</span>
                            <span>Biaya pendaftaran dan seleksi terjangkau</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">✓</span>
                            <span>Tersedia beasiswa dan skema diskon prestasi</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">✓</span>
                            <span>Verifikasi pembayaran langsung oleh tim Bendahara sekolah</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-lg border-b border-slate-200 pb-3">Simulasi Komponen Biaya</h3>
                    <div class="divide-y divide-slate-200/80 text-sm">
                        <div class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-600">Biaya Seleksi Awal</span>
                            <span class="font-bold text-slate-900">Rp 250.000</span>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-600">Dana Sarana Prasarana (DSP)*</span>
                            <span class="font-semibold text-slate-700">Rp 3.000.000</span>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-600">SPP Bulanan*</span>
                            <span class="font-semibold text-slate-700">Rp 450.000</span>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-600">Paket Seragam Sekolah*</span>
                            <span class="font-semibold text-slate-700">Rp 1.000.000</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 italic pt-1">
                        *Estimasi komponen biaya daftar ulang dapat disesuaikan pada saat penetapan gelombang dan program pendaftaran.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600 bg-slate-200 px-3 py-1 rounded-full">FAQ</span>
                <h2 class="text-3xl font-extrabold text-slate-900">Pertanyaan yang Sering Diajukan</h2>
            </div>

            <div class="space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80">
                    <h4 class="font-bold text-slate-800 text-sm">Bagaimana cara mendapatkan akun calon siswa?</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Akun calon siswa akan dibuat secara otomatis saat Anda menyelesaikan formulir registrasi awal. Username berupa NISN dan password awal berupa nomor HP siswa.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80">
                    <h4 class="font-bold text-slate-800 text-sm">Apakah saya bisa mengubah pilihan jurusan setelah mendaftar?</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Pilihan jurusan dapat dikonsultasikan kembali saat sesi wawancara dengan persetujuan pewawancara dan tim admin SPMB.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80">
                    <h4 class="font-bold text-slate-800 text-sm">Kemana saya menghubungi panitia jika mengalami kendala?</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Anda dapat menekan tombol WhatsApp Helpdesk di pojok kanan bawah layar untuk langsung terhubung dengan panitia SPMB SMK Wikrama 1 Garut di <a href="https://wa.me/6281323314430" class="text-nampi-orange font-semibold hover:underline">0813-2331-4430</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="py-16 bg-gradient-to-r from-nampi-orange via-amber-500 to-amber-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                Siap Menjadi Bagian dari SMK Wikrama 1 Garut?
            </h2>
            <p class="text-base text-amber-100 max-w-2xl mx-auto">
                Daftarkan diri Anda sekarang sebelum kuota gelombang pendaftaran terpenuhi.
            </p>
            <div class="pt-2">
                <a href="{{ url('/register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl font-bold bg-white text-orange-700 hover:bg-orange-50 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 text-base">
                    Mulai Pendaftaran Sekarang
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
</x-layouts.guest>
