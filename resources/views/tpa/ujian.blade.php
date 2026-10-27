<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian TPA — {{ ucfirst($babakAktif) }} — SPMB SMK Wikrama 1 Garut</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Proteksi anti seleksi teks & copy */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Watermark sangat samar anti kebocoran soal tanpa mengaburkan teks */
        .watermark-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 20;
            opacity: 0.022;
            background-image: repeating-linear-gradient(
                -45deg,
                transparent,
                transparent 160px,
                rgba(0, 0, 0, 0.7) 160px,
                rgba(0, 0, 0, 0.7) 320px
            );
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .watermark-text {
            transform: rotate(-30deg);
            font-size: 1.5rem;
            font-weight: 900;
            color: #000;
            white-space: pre-wrap;
            text-align: center;
            line-height: 5rem;
            user-select: none;
        }

        /* Mode Ukuran Font Dinamis Bertingkat (Step 1 s/d 5) */
        body.font-step-1 .soal-pertanyaan-text { font-size: 0.8125rem !important; line-height: 1.45rem !important; }
        body.font-step-1 .opsi-jawaban-text { font-size: 0.75rem !important; }

        body.font-step-2 .soal-pertanyaan-text { font-size: 0.9375rem !important; line-height: 1.6rem !important; }
        body.font-step-2 .opsi-jawaban-text { font-size: 0.875rem !important; }

        body.font-step-3 .soal-pertanyaan-text { font-size: 1.0625rem !important; line-height: 1.75rem !important; }
        body.font-step-3 .opsi-jawaban-text { font-size: 0.9375rem !important; }

        body.font-step-4 .soal-pertanyaan-text { font-size: 1.25rem !important; line-height: 1.95rem !important; }
        body.font-step-4 .opsi-jawaban-text { font-size: 1.0625rem !important; }

        body.font-step-5 .soal-pertanyaan-text { font-size: 1.45rem !important; line-height: 2.25rem !important; }
        body.font-step-5 .opsi-jawaban-text { font-size: 1.2rem !important; }

        /* ============================================================== */
        /* LAYOUT FLEXBOX VIEWPORT & SCROLLING SOAL TPA                   */
        /* ============================================================== */
        body {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }

        main.exam-main-layout {
            display: flex !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            height: 100% !important;
            max-height: 100% !important;
            overflow: hidden !important;
            position: relative !important;
        }

        section.exam-section-layout {
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            height: 100% !important;
            max-height: 100% !important;
            overflow: hidden !important;
            position: relative !important;
        }

        /* Wadah Soal: Wajib Scroll Internal & Menampung Seluruh Opsi Jawaban */
        #soal-scroll-container {
            display: block !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            height: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: thin !important;
            scrollbar-color: #cbd5e1 #f8fafc !important;
            padding-bottom: 7rem !important; /* Ruang scroll bawah sangat lega */
        }

        #soal-scroll-container::-webkit-scrollbar {
            width: 8px !important;
        }
        #soal-scroll-container::-webkit-scrollbar-track {
            background: #f8fafc !important;
        }
        #soal-scroll-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1 !important;
            border-radius: 9999px !important;
            border: 2px solid #f8fafc !important;
        }
        #soal-scroll-container::-webkit-scrollbar-thumb:hover {
            background-color: #f97316 !important;
        }

        /* Bilah Navigasi Bawah Selalu Terkunci di Posisi Bawah Layar */
        .exam-bottom-bar {
            flex-shrink: 0 !important;
            background-color: #ffffff !important;
            border-top: 1px solid #e2e8f0 !important;
            z-index: 30 !important;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Gambar Soal: Ukuran Maksimal Terkendali Agar Tidak Menutupi Seluruh Layar */
        .exam-image-box {
            max-height: 280px !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #f8fafc !important;
            border-radius: 0.5rem !important;
        }

        .exam-img-element {
            max-height: 260px !important;
            max-width: 100% !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
            border-radius: 0.5rem !important;
            transition: transform 0.2s ease !important;
            cursor: zoom-in !important;
        }

        /* Custom Scrollbar Ramping Global */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 h-screen w-screen overflow-hidden flex flex-col no-select font-step-2" oncontextmenu="return false;">

    @include('tpa.partials.simulasi-banner')

    <!-- Watermark Anti-Leak Overlay -->
    <div class="watermark-overlay">
        <div class="watermark-text">
            {{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
        </div>
    </div>

    <!-- Banner Peringatan Keluar Fullscreen (Jika layar keluar fullscreen) -->
    <div id="banner-fullscreen-warning" class="bg-rose-600 text-white px-4 py-2 text-xs font-bold flex items-center justify-between sticky top-0 z-50 shadow-md hidden shrink-0">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-white animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>PERINGATAN: Ujian wajib dikerjakan dalam mode Layar Penuh (Fullscreen).</span>
        </div>
        <button type="button" onclick="requestFullscreenMode()" class="px-3 py-1 bg-white text-rose-700 rounded-lg text-xs font-black shadow-xs hover:bg-slate-100 cursor-pointer">
            Kembalikan Layar Penuh &rarr;
        </button>
    </div>

    <!-- Top Navigation Header Ramping (h-14 / 56px) -->
    <header class="bg-slate-900 text-white h-14 shrink-0 shadow-sm border-b border-slate-800 z-30">
        <div class="h-full px-4 sm:px-6 flex items-center justify-between gap-3">
            <!-- Sisi Kiri: Toggle Lembar Jawaban & Siswa Info -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <!-- Tombol Toggle Hide / Unhide Lembar Jawaban Kiri -->
                <button type="button" onclick="toggleSidebarLembarJawaban()"
                        id="btn-sidebar-toggle-header"
                        class="p-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors cursor-pointer shrink-0"
                        title="Sembunyikan / Tampilkan Lembar Jawaban">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>

                <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                    TPA
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="font-bold text-xs sm:text-sm text-white truncate">{{ $calonSiswa->nama_lengkap }}</span>
                        <span class="hidden sm:inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 font-mono">
                            {{ $calonSiswa->nomor_pendaftaran }}
                        </span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-orange-400 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full {{ $babakAktif === 'literasi' ? 'bg-blue-400' : 'bg-emerald-400' }} animate-pulse"></span>
                        <span class="truncate">{{ $babakAktif === 'literasi' ? 'Babak 1: Literasi Membaca' : 'Babak 2: Penalaran Numerasi' }}</span>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Kontrol Font, Timer, Fullscreen -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Toolbar Ukuran Font Soal Bertingkat (A- / A / A+) -->
                <div class="flex items-center gap-0.5 bg-slate-800 p-0.5 rounded-lg border border-slate-700">
                    <button type="button" onclick="adjustFontSize(-1)" id="btn-font-dec"
                            class="px-2 py-0.5 rounded text-[11px] font-bold text-slate-400 hover:text-white cursor-pointer transition-colors"
                            title="Perkecil Ukuran Teks (A-)">
                        A-
                    </button>
                    <button type="button" onclick="resetFontSize()" id="btn-font-reset"
                            class="px-2 py-0.5 rounded text-[11px] font-bold bg-white text-slate-900 shadow-2xs cursor-pointer transition-colors"
                            title="Reset Ukuran Normal (A)">
                        A
                    </button>
                    <button type="button" onclick="adjustFontSize(1)" id="btn-font-inc"
                            class="px-2 py-0.5 rounded text-[11px] font-bold text-slate-400 hover:text-white cursor-pointer transition-colors"
                            title="Perbesar Ukuran Teks (A+)">
                        A+
                    </button>
                </div>

                <!-- Sisa Waktu (Countdown Timer) -->
                <div class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 rounded-xl bg-slate-800/95 border border-slate-700 shadow-inner">
                    <svg class="w-3.5 h-3.5 text-orange-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-right">
                        <span class="hidden sm:block text-[8px] uppercase font-bold text-slate-400 leading-none">Sisa Waktu</span>
                        <span id="timer-display" class="font-mono text-xs sm:text-sm font-black text-white tracking-wider block leading-tight">
                            --:--:--
                        </span>
                    </div>
                </div>

                <!-- Tombol Fullscreen Manual -->
                <button type="button" onclick="requestFullscreenMode()" id="btn-fullscreen-toggle"
                        class="p-1.5 sm:p-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer"
                        title="Mode Layar Penuh (Fullscreen)">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container 1 Layar Penuh (Tinggi = 100vh - 56px) -->
    <main class="exam-main-layout flex-1 min-h-0 flex overflow-hidden relative">

        <!-- Mobile Backdrop untuk Drawer Lembar Jawaban -->
        <div id="lembar-jawaban-backdrop" onclick="toggleSidebarLembarJawaban()"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden hidden"></div>

        <!-- ============================================================== -->
        <!-- PANEL LEMBAR JAWABAN (SISI KIRI) - Kotak-kotak Kecil Minimalis -->
        <!-- ============================================================== -->
        <aside id="sidebar-lembar-jawaban"
               class="fixed inset-y-0 left-0 z-40 w-64 sm:w-72 bg-white border-r border-slate-200/90 flex flex-col justify-between shadow-2xl transition-all duration-300 -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:shadow-none shrink-0">

            <!-- Bagian Atas Lembar Jawaban -->
            <div class="p-3.5 space-y-2.5 overflow-hidden flex flex-col flex-1">
                <!-- Header Panel dengan tombol collapse -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div>
                        <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">Lembar Jawaban</h2>
                        <span id="label-progress-stat" class="text-[11px] font-semibold text-slate-500 font-mono">0/{{ count($soalSorted) }} Terjawab (0%)</span>
                    </div>
                    <button type="button" onclick="toggleSidebarLembarJawaban()"
                            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Sembunyikan Lembar Jawaban">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar Tipis -->
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div id="bar-progress-stat" class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>

                <!-- Filter Tab Cepat (Semua, Belum, Ragu) -->
                <div class="grid grid-cols-3 gap-1 p-0.5 bg-slate-100 rounded-lg text-[10px] font-bold">
                    <button type="button" onclick="setGridFilter('semua')" id="tab-filter-semua"
                            class="py-1 rounded bg-white text-slate-900 shadow-2xs text-center cursor-pointer transition-all">
                        Semua (<span id="count-filter-semua">{{ count($soalSorted) }}</span>)
                    </button>
                    <button type="button" onclick="setGridFilter('belum')" id="tab-filter-belum"
                            class="py-1 rounded text-slate-500 hover:text-slate-900 text-center cursor-pointer transition-all">
                        Belum (<span id="count-filter-belum">0</span>)
                    </button>
                    <button type="button" onclick="setGridFilter('ragu')" id="tab-filter-ragu"
                            class="py-1 rounded text-slate-500 hover:text-slate-900 text-center cursor-pointer transition-all">
                        Ragu (<span id="count-filter-ragu">0</span>)
                    </button>
                </div>

                <!-- Legend Ringkas -->
                <div class="flex items-center justify-between text-[10px] font-semibold text-slate-500 px-1 pt-0.5">
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded bg-emerald-600"></span>
                        <span>Dijawab</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded bg-amber-400"></span>
                        <span>Ragu</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded bg-slate-200"></span>
                        <span>Belum</span>
                    </div>
                </div>

                <!-- Grid Kotak-kotak Kecil Nomor Soal (5 Kolom, Compact) -->
                <div id="grid-nomor-container" class="grid grid-cols-5 gap-1.5 overflow-y-auto pr-1 flex-1 py-1">
                    @foreach ($soalSorted as $idx => $soal)
                        @php
                            $jawaban = $jawabanMap->get($soal->id);
                            $dipilih = $jawaban?->jawaban_dipilih;
                            $isRagu = $jawaban?->is_ragu ?? false;

                            $btnClass = 'bg-slate-100 text-slate-700 border-slate-200/90 hover:bg-slate-200';
                            if ($isRagu) {
                                $btnClass = 'bg-amber-400 text-amber-950 font-black border-amber-500 shadow-2xs';
                            } elseif (!empty($dipilih)) {
                                $btnClass = 'bg-emerald-600 text-white font-bold border-emerald-700 shadow-2xs';
                            }
                        @endphp
                        <button type="button" onclick="bukaSoalNomor({{ $idx }})"
                                id="btn-grid-{{ $idx }}"
                                data-index="{{ $idx }}"
                                class="grid-number-btn relative h-9 rounded-lg border flex flex-col items-center justify-center text-xs font-bold transition-all cursor-pointer {{ $btnClass }} {{ $idx === 0 ? 'ring-2 ring-orange-500 ring-offset-1 z-10' : '' }}">
                            <span class="leading-none text-[11px]">{{ $idx + 1 }}</span>
                            <span id="grid-key-{{ $idx }}" class="text-[8px] font-mono leading-none mt-0.5 {{ empty($dipilih) ? 'hidden' : '' }}">
                                {{ $dipilih ?? '' }}
                            </span>
                            @if ($isRagu)
                                <span class="absolute top-0.5 right-0.5 w-1.5 h-1.5 rounded-full bg-amber-900"></span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tombol Selesaikan Babak di Bawah Lembar Jawaban -->
            <div class="p-3 border-t border-slate-100 bg-slate-50/70">
                <button type="button" onclick="konfirmasiSelesaiBabak()"
                        class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Selesaikan {{ $babakAktif === 'literasi' ? 'Babak 1' : 'Ujian TPA' }}</span>
                </button>
            </div>
        </aside>

        <!-- Tombol Mengambang (Floating Handle) saat Lembar Jawaban di-Hide -->
        <button type="button" id="btn-floating-unhide" onclick="toggleSidebarLembarJawaban()"
                class="hidden absolute left-0 top-1/3 z-30 bg-slate-900 hover:bg-orange-600 text-white px-1.5 py-3 rounded-r-xl shadow-lg border border-l-0 border-slate-700 transition-colors cursor-pointer"
                title="Buka Lembar Jawaban">
            <div class="flex flex-col items-center gap-1 text-[9px] font-black uppercase tracking-tighter">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="writing-vertical [writing-mode:vertical-lr] rotate-180">LEMBAR SOAL</span>
            </div>
        </button>

        <!-- ============================================================== -->
        <!-- AREA PENGERJAAN SOAL (TENGAH / KANAN) - 1 Layar Proporsional   -->
        <!-- ============================================================== -->
        <section class="exam-section-layout flex-1 min-h-0 flex flex-col h-full overflow-hidden bg-slate-50/60 relative">

            <!-- Subheader Area Soal (Fixed, No Scroll) -->
            <div class="px-4 sm:px-6 py-2.5 bg-white border-b border-slate-200/80 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span id="current-question-badge" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-black text-xs shadow-2xs">
                        Soal No. 1
                    </span>
                    <span class="text-xs text-slate-400 font-medium">dari {{ count($soalSorted) }} butir</span>
                    <span class="text-slate-300 hidden sm:inline">&bull;</span>
                    <span id="current-bobot-text" class="text-xs font-semibold text-slate-500 hidden sm:inline">Bobot: {{ $soalSorted[0]->bobot ?? 1 }} Poin</span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Status Tersimpan AJAX -->
                    <span id="save-status-indicator" class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 opacity-0 transition-opacity">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Jawaban tersimpan</span>
                    </span>

                    <!-- Tombol Cepat Buka Lembar Jawaban (Untuk Mobile) -->
                    <button type="button" onclick="toggleSidebarLembarJawaban()"
                            class="lg:hidden text-xs font-bold text-orange-600 px-2 py-1 rounded-lg bg-orange-50 border border-orange-200">
                        Nomor Soal &rarr;
                    </button>
                </div>
            </div>

            <!-- Konten Soal & Opsi (Scrollable jika teks sangat panjang, tapi proporsional) -->
            <div id="soal-scroll-container" class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-5 lg:p-6 space-y-4 max-w-4xl w-full mx-auto pb-24">
                @foreach ($soalSorted as $idx => $soal)
                    @php
                        $jawaban = $jawabanMap->get($soal->id);
                        $dipilih = $jawaban?->jawaban_dipilih;
                        $isRagu = $jawaban?->is_ragu ?? false;
                    @endphp
                    <div class="soal-item space-y-4 {{ $idx === 0 ? '' : 'hidden' }}"
                         id="soal-card-{{ $idx }}"
                         data-index="{{ $idx }}"
                         data-soal-id="{{ $soal->id }}"
                         data-nomor="{{ $idx + 1 }}"
                         data-bobot="{{ $soal->bobot }}">

                        <!-- Teks Wacana / Pertanyaan (Kompak & Bersih) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                            <div class="soal-pertanyaan-text font-normal leading-relaxed text-slate-800 select-none">
                                {!! nl2br(e($soal->pertanyaan)) !!}
                            </div>

                            <!-- Gambar Soal: Kompak Thumbnail dengan Tombol Zoom + dan - -->
                            @if ($soal->gambar_path)
                                @php
                                    $imgUrl = asset('storage/' . $soal->gambar_path);
                                @endphp
                                <div class="pt-2 border-t border-slate-100 flex flex-col items-center">
                                    <div class="relative inline-block rounded-xl border border-slate-200 bg-slate-50 p-2 overflow-hidden shadow-2xs group max-w-full">
                                        <!-- Container gambar dengan max-height terkendali dan overflow untuk zoom in/out di tempat -->
                                        <div class="exam-image-box overflow-hidden flex items-center justify-center">
                                            <img id="img-soal-{{ $idx }}"
                                                 src="{{ $imgUrl }}"
                                                 alt="Gambar Soal {{ $idx + 1 }}"
                                                 data-scale="1"
                                                 class="exam-img-element object-contain rounded-lg transition-transform duration-200 cursor-zoom-in"
                                                 onclick="openImageLightbox('{{ $imgUrl }}', 'Gambar Soal No. {{ $idx + 1 }}')"
                                                 loading="lazy">
                                        </div>

                                        <!-- Toolbar Kontrol Zoom Gambar: Zoom In (+), Zoom Out (-), Reset & Modal Layar Penuh -->
                                        <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1 bg-slate-900/90 px-2 py-1 rounded-lg text-white shadow-md border border-slate-700" style="background: rgba(15, 23, 42, 0.9);">
                                            <button type="button" onclick="zoomImage({{ $idx }}, 0.25)" class="w-6 h-6 flex items-center justify-center rounded hover:bg-white/20 font-black text-xs cursor-pointer text-white" title="Perbesar (+)">
                                                +
                                            </button>
                                            <button type="button" onclick="zoomImage({{ $idx }}, -0.25)" class="w-6 h-6 flex items-center justify-center rounded hover:bg-white/20 font-black text-xs cursor-pointer text-white" title="Perkecil (-)">
                                                &minus;
                                            </button>
                                            <button type="button" onclick="resetImageZoom({{ $idx }})" class="px-1.5 h-6 flex items-center justify-center rounded hover:bg-white/20 text-[10px] font-bold cursor-pointer text-white" title="Reset Ukuran Normal">
                                                1x
                                            </button>
                                            <span class="text-slate-400">|</span>
                                            <button type="button" onclick="openImageLightbox('{{ $imgUrl }}', 'Gambar Soal No. {{ $idx + 1 }}')" class="px-1.5 h-6 flex items-center justify-center rounded hover:bg-white/20 text-[10px] font-bold cursor-pointer text-white" title="Buka Gambar Layar Penuh">
                                                Layar Penuh
                                            </button>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-1">*Klik gambar atau tombol untuk memperbesar/memperkecil</span>
                                </div>
                            @endif
                        </div>

                        <!-- Opsi Jawaban Radio Cards (Kompak & Elegan) -->
                        <div class="space-y-2 pt-1">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">PILIHAN JAWABAN:</p>
                            @foreach ($soal->opsi_jawaban ?? [] as $opsi)
                                @php
                                    $isSelected = ($dipilih === $opsi['key']);
                                @endphp
                                <label class="opsi-label flex items-center justify-between p-2.5 sm:p-3 rounded-xl border transition-all cursor-pointer select-none group {{ $isSelected ? 'bg-orange-50/90 border-orange-500 shadow-2xs ring-1 ring-orange-500' : 'bg-white border-slate-200/90 hover:border-orange-300 hover:bg-orange-50/30' }}">
                                    <div class="flex items-start gap-2.5 sm:gap-3 flex-1 min-w-0">
                                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg flex items-center justify-center font-black text-xs shrink-0 transition-colors {{ $isSelected ? 'bg-orange-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 group-hover:bg-slate-200' }}">
                                            {{ $opsi['key'] }}
                                        </span>
                                        <span class="opsi-jawaban-text text-slate-800 leading-snug font-medium pt-0.5 select-none">
                                            {{ $opsi['text'] }}
                                        </span>
                                    </div>
                                    <input type="radio" name="jawaban_{{ $soal->id }}" value="{{ $opsi['key'] }}"
                                           {{ $isSelected ? 'checked' : '' }}
                                           onchange="pilihJawaban({{ $idx }}, {{ $soal->id }}, '{{ $opsi['key'] }}')"
                                           class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-slate-300 shrink-0 ml-2">
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bilah Navigasi Bawah Tetap (Fixed Bottom Nav Bar) -->
            <div class="exam-bottom-bar bg-white border-t border-slate-200/90 px-4 sm:px-6 py-2.5 flex items-center justify-between gap-3 shrink-0 shadow-xs z-20">
                <button type="button" id="btn-prev" onclick="geserSoal(-1)"
                        class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Sebelumnya</span>
                </button>

                <!-- Tombol Simpan Sementara / Ragu-Ragu -->
                <button type="button" id="btn-ragu" onclick="toggleRaguRagu()"
                        class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl border border-amber-300 bg-amber-50 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="label-ragu">Ragu-ragu</span>
                </button>

                <button type="button" id="btn-next" onclick="geserSoal(1)"
                        class="inline-flex items-center gap-1.5 px-4 sm:px-5 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold transition-all shadow-sm shadow-orange-600/20 cursor-pointer">
                    <span>Berikutnya</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>
    </main>

    <!-- ============================================================== -->
    <!-- MODAL LIGHTBOX ZOOM GAMBAR                                     -->
    <!-- ============================================================== -->
    <div id="modal-image-lightbox" class="fixed inset-0 z-50 bg-slate-900/90 backdrop-blur-sm flex items-center justify-center p-4 hidden" onclick="closeImageLightbox()">
        <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-2xl p-3 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <span id="lightbox-title" class="text-xs font-bold text-slate-700">Perbesar Gambar Soal</span>
                <button type="button" onclick="closeImageLightbox()" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-2 overflow-auto max-h-[80vh] flex items-center justify-center">
                <img id="lightbox-img" src="" alt="Gambar Perbesar" class="max-h-[75vh] w-auto object-contain rounded-lg">
            </div>
        </div>
    </div>

    <!-- Modal Peringatan Kecurangan / Tab Switch / Blur -->
    <div id="modal-pelanggaran" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-rose-200 shadow-2xl max-w-md w-full p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-800">PERINGATAN INTEGRITAS UJIAN!</h3>
                <p class="text-xs text-rose-600 font-bold mt-1" id="label-violation-reason">Anda terdeteksi beralih dari jendela/layar ujian.</p>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Pelanggaran telah dicatat pada log panitia: <br>
                    <strong class="text-slate-800 text-sm" id="label-violation-count">1</strong> dari batas toleransi <strong>{{ $pengaturan->max_pelanggaran }} kali</strong>.
                    Jika kuota pelanggaran terlampaui, ujian Anda akan otomatis dikunci dan didiskualifikasi!
                </p>
            </div>
            <button type="button" onclick="tutupModalPelanggaran()"
                    class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition-colors cursor-pointer">
                Saya Mengerti & Kembali Mengerjakan
            </button>
        </div>
    </div>

    <!-- Modal Konfirmasi Selesai Babak (Dengan Indikator Soal Belum Diisi) -->
    <div id="modal-konfirmasi-selesai" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-900">
                    Konfirmasi Selesai {{ $babakAktif === 'literasi' ? 'Babak 1 (Literasi)' : 'Seluruh Tes' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1">Periksa kembali ringkasan jawaban Anda sebelum mengunci hasil ujian.</p>
            </div>

            <!-- 3 Kartu Ringkasan Status Pengerjaan -->
            <div class="grid grid-cols-3 gap-2.5 text-center">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200/80">
                    <span class="text-xs text-emerald-800 font-bold block">Terjawab</span>
                    <span id="modal-stat-terjawab" class="text-xl font-black text-emerald-700 block mt-0.5">0</span>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80">
                    <span class="text-xs text-amber-800 font-bold block">Ragu-ragu</span>
                    <span id="modal-stat-ragu" class="text-xl font-black text-amber-700 block mt-0.5">0</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-100 border border-slate-200">
                    <span class="text-xs text-slate-700 font-bold block">Belum Diisi</span>
                    <span id="modal-stat-kosong" class="text-xl font-black text-slate-800 block mt-0.5">0</span>
                </div>
            </div>

            <!-- Banner Peringatan Soal Kosong / Ragu-ragu -->
            <div id="modal-warning-empty-banner" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-left flex items-start gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-xs">
                    <p class="font-bold text-rose-800" id="modal-warning-empty-text">Perhatian: Masih ada butir soal yang belum Anda jawab!</p>
                    <p class="text-rose-600 mt-0.5">Semua butir soal yang kosong tidak akan mendapat poin.</p>
                </div>
            </div>

            <p class="text-[11px] text-slate-500 text-left bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                {{ $babakAktif === 'literasi' ? 'Catatan: Setelah disubmit, Babak 1 akan dikunci dan Anda akan diarahkan ke petunjuk Babak 2.' : 'Catatan: Setelah disubmit, seluruh sesi TPA Anda akan tuntas dan nilai akan diproses oleh sistem.' }}
            </p>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="periksaSoalBelumTerjawab()"
                        class="w-1/2 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors cursor-pointer">
                    Periksa Kembali
                </button>
                <form action="{{ $babakAktif === 'literasi' ? route('tpa.submit-babak-1') : route('tpa.submit-babak-2') }}"
                      method="POST" id="form-submit-babak" class="w-1/2">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer">
                        Ya, Selesaikan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Logika Ujian, Timer, Fullscreen, Image Zoom & Anti-Cheat -->
    <script>
        // State Ujian
        let currentIndex = 0;
        const totalSoal = {{ count($soalSorted) }};
        const maxPelanggaran = {{ $pengaturan->max_pelanggaran }};
        let sisaDetik = {{ $sisaWaktuDetik }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentGridFilter = 'semua';
        let isSidebarCollapsed = false;

        // Peta state jawaban lokal
        const examState = {
            @foreach ($soalSorted as $idx => $s)
                @php
                    $ans = $jawabanMap->get($s->id);
                @endphp
                {{ $idx }}: {
                    soalId: {{ $s->id }},
                    jawaban: "{{ $ans?->jawaban_dipilih ?? '' }}",
                    isRagu: {{ $ans?->is_ragu ? 'true' : 'false' }}
                },
            @endforeach
        };

        // 1. Inisialisasi Tampilan & Fullscreen
        document.addEventListener('DOMContentLoaded', function () {
            updateSoalView();
            updateTimerDisplay();
            startCountdown();
            initAntiCheat();
            updateProgressSummary();

            // Coba aktifkan fullscreen saat halaman dimuat
            requestFullscreenMode();

            // Fullscreen fallback pada klik pertama murid (karena browser policies)
            document.addEventListener('click', function autoEnterOnFirstClick() {
                if (!document.fullscreenElement) {
                    requestFullscreenMode();
                }
                document.removeEventListener('click', autoEnterOnFirstClick);
            }, { once: true });
        });

        // 2. Fullscreen API Helper & Monitor
        function requestFullscreenMode() {
            const el = document.documentElement;
            if (el.requestFullscreen) {
                el.requestFullscreen().catch(() => {});
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', function () {
            const warningBanner = document.getElementById('banner-fullscreen-warning');
            if (!document.fullscreenElement) {
                if (warningBanner) warningBanner.classList.remove('hidden');
                laporkanPelanggaran('Keluar dari mode layar penuh (fullscreen)');
            } else {
                if (warningBanner) warningBanner.classList.add('hidden');
            }
        });

        // 3. Toggle Sidebar Lembar Jawaban di Kiri (Hide / Unhide)
        function toggleSidebarLembarJawaban() {
            const sidebar = document.getElementById('sidebar-lembar-jawaban');
            const floatingBtn = document.getElementById('btn-floating-unhide');
            const backdrop = document.getElementById('lembar-jawaban-backdrop');
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                // Di HP: Buka tutup drawer dengan backdrop
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            } else {
                // Di Desktop: Collapse kiri dengan margin minus
                isSidebarCollapsed = !isSidebarCollapsed;
                if (isSidebarCollapsed) {
                    sidebar.classList.add('lg:-ml-72');
                    floatingBtn.classList.remove('hidden');
                } else {
                    sidebar.classList.remove('lg:-ml-72');
                    floatingBtn.classList.add('hidden');
                }
            }
        }

        // 4. Zoom Gambar Soal (+, -, Reset, Lightbox)
        function zoomImage(index, delta) {
            const img = document.getElementById(`img-soal-${index}`);
            if (!img) return;

            let currentScale = parseFloat(img.getAttribute('data-scale') || '1');
            currentScale += delta;
            if (currentScale < 0.75) currentScale = 0.75;
            if (currentScale > 2.5) currentScale = 2.5;

            img.setAttribute('data-scale', currentScale);
            img.style.transform = `scale(${currentScale})`;
        }

        function resetImageZoom(index) {
            const img = document.getElementById(`img-soal-${index}`);
            if (!img) return;
            img.setAttribute('data-scale', '1');
            img.style.transform = 'scale(1)';
        }

        function openImageLightbox(url, title) {
            document.getElementById('lightbox-img').src = url;
            document.getElementById('lightbox-title').innerText = title || 'Perbesar Gambar Soal';
            document.getElementById('modal-image-lightbox').classList.remove('hidden');
        }

        function closeImageLightbox() {
            document.getElementById('modal-image-lightbox').classList.add('hidden');
        }

        // 5. Kontrol Ukuran Font Soal Bertingkat (Step 1 s/d 5)
        let currentFontStep = 2; // Default normal (Step 2)

        function applyFontStep(step) {
            currentFontStep = Math.max(1, Math.min(5, step));

            for (let i = 1; i <= 5; i++) {
                document.body.classList.remove(`font-step-${i}`);
            }
            document.body.classList.add(`font-step-${currentFontStep}`);

            const btnDec = document.getElementById('btn-font-dec');
            const btnReset = document.getElementById('btn-font-reset');
            const btnInc = document.getElementById('btn-font-inc');

            if (btnDec) {
                btnDec.disabled = (currentFontStep <= 1);
                btnDec.classList.toggle('opacity-40', currentFontStep <= 1);
                btnDec.classList.toggle('cursor-not-allowed', currentFontStep <= 1);
            }
            if (btnInc) {
                btnInc.disabled = (currentFontStep >= 5);
                btnInc.classList.toggle('opacity-40', currentFontStep >= 5);
                btnInc.classList.toggle('cursor-not-allowed', currentFontStep >= 5);
            }
            if (btnReset) {
                if (currentFontStep === 2) {
                    btnReset.className = 'px-2 py-0.5 rounded text-[11px] font-bold bg-white text-slate-900 shadow-2xs cursor-pointer transition-colors';
                } else {
                    btnReset.className = 'px-2 py-0.5 rounded text-[11px] font-bold text-slate-400 hover:text-white cursor-pointer transition-colors';
                }
            }

            try {
                localStorage.setItem('tpa_font_step', currentFontStep.toString());
            } catch (e) {}
        }

        function adjustFontSize(delta) {
            applyFontStep(currentFontStep + delta);
        }

        function resetFontSize() {
            applyFontStep(2);
        }

        try {
            const savedStep = parseInt(localStorage.getItem('tpa_font_step') || '2', 10);
            if (!isNaN(savedStep) && savedStep >= 1 && savedStep <= 5) {
                applyFontStep(savedStep);
            } else {
                applyFontStep(2);
            }
        } catch (e) {
            applyFontStep(2);
        }

        // 6. Navigasi Soal
        function bukaSoalNomor(index) {
            if (index < 0 || index >= totalSoal) return;
            currentIndex = index;
            updateSoalView();

            // Kembalikan posisi scroll ke atas saat beralih butir soal
            const scrollContainer = document.getElementById('soal-scroll-container');
            if (scrollContainer) {
                scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // Sembunyikan drawer mobile jika di layar HP
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar-lembar-jawaban');
                const backdrop = document.getElementById('lembar-jawaban-backdrop');
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            }
        }

        function geserSoal(offset) {
            const target = currentIndex + offset;
            if (target >= 0 && target < totalSoal) {
                currentIndex = target;
                updateSoalView();

                // Kembalikan posisi scroll ke atas saat beralih butir soal
                const scrollContainer = document.getElementById('soal-scroll-container');
                if (scrollContainer) {
                    scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }

        function updateSoalView() {
            // Sembunyikan semua card soal, tampilkan yang aktif
            for (let i = 0; i < totalSoal; i++) {
                const el = document.getElementById(`soal-card-${i}`);
                if (el) {
                    if (i === currentIndex) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }

                // Update highlight nomor aktif di kisi-kisi
                const btnGrid = document.getElementById(`btn-grid-${i}`);
                if (btnGrid) {
                    if (i === currentIndex) {
                        btnGrid.classList.add('ring-2', 'ring-orange-500', 'ring-offset-1', 'z-10');
                    } else {
                        btnGrid.classList.remove('ring-2', 'ring-orange-500', 'ring-offset-1', 'z-10');
                    }
                }
            }

            // Update badge nomor di header soal
            const badgeNo = document.getElementById('current-question-badge');
            if (badgeNo) badgeNo.innerText = `Soal No. ${currentIndex + 1}`;

            const activeCard = document.getElementById(`soal-card-${currentIndex}`);
            const bobotText = document.getElementById('current-bobot-text');
            if (activeCard && bobotText) {
                bobotText.innerText = `Bobot: ${activeCard.getAttribute('data-bobot')} Poin`;
            }

            // Atur tombol prev & next
            const btnPrev = document.getElementById('btn-prev');
            if (btnPrev) btnPrev.disabled = (currentIndex === 0);

            const btnNext = document.getElementById('btn-next');
            if (btnNext) {
                if (currentIndex === totalSoal - 1) {
                    btnNext.innerHTML = `<span>Selesai</span><svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
                    btnNext.onclick = konfirmasiSelesaiBabak;
                } else {
                    btnNext.innerHTML = `<span>Berikutnya</span><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>`;
                    btnNext.onclick = () => geserSoal(1);
                }
            }

            // Update label tombol ragu-ragu
            const currentState = examState[currentIndex];
            const btnRagu = document.getElementById('btn-ragu');
            const labelRagu = document.getElementById('label-ragu');
            if (currentState && currentState.isRagu) {
                if (btnRagu) btnRagu.className = 'inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl border border-amber-500 bg-amber-400 text-xs font-bold text-amber-950 shadow-xs cursor-pointer';
                if (labelRagu) labelRagu.innerText = 'Ragu-ragu (Aktif)';
            } else {
                if (btnRagu) btnRagu.className = 'inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl border border-amber-300 bg-amber-50 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors shadow-2xs cursor-pointer';
                if (labelRagu) labelRagu.innerText = 'Ragu-ragu';
            }
        }

        // 7. Pemilihan & Penyimpanan Jawaban Real-time AJAX
        function pilihJawaban(index, soalId, opsiKey) {
            examState[index].jawaban = opsiKey;
            syncJawabanKeServer(soalId, opsiKey, examState[index].isRagu);
            updateGridButton(index);
            updateProgressSummary();
            filterGridButtons();
            showSaveFeedback();
        }

        function toggleRaguRagu() {
            const current = examState[currentIndex];
            current.isRagu = !current.isRagu;
            syncJawabanKeServer(current.soalId, current.jawaban, current.isRagu);
            updateGridButton(currentIndex);
            updateSoalView();
            updateProgressSummary();
            filterGridButtons();
            showSaveFeedback();
        }

        function showSaveFeedback() {
            const indicator = document.getElementById('save-status-indicator');
            if (!indicator) return;
            indicator.classList.remove('opacity-0');
            indicator.classList.add('opacity-100');
            setTimeout(() => {
                indicator.classList.remove('opacity-100');
                indicator.classList.add('opacity-0');
            }, 1800);
        }

        function updateGridButton(index) {
            const current = examState[index];
            const btn = document.getElementById(`btn-grid-${index}`);
            const keySpan = document.getElementById(`grid-key-${index}`);

            if (!btn) return;

            btn.className = 'grid-number-btn relative h-9 rounded-lg border flex flex-col items-center justify-center text-xs font-bold transition-all cursor-pointer';

            if (current.isRagu) {
                btn.classList.add('bg-amber-400', 'text-amber-950', 'border-amber-500', 'shadow-2xs');
            } else if (current.jawaban) {
                btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-700', 'shadow-2xs');
            } else {
                btn.classList.add('bg-slate-100', 'text-slate-700', 'border-slate-200/90', 'hover:bg-slate-200');
            }

            if (index === currentIndex) {
                btn.classList.add('ring-2', 'ring-orange-500', 'ring-offset-1', 'z-10');
            }

            if (keySpan) {
                if (current.jawaban) {
                    keySpan.innerText = current.jawaban;
                    keySpan.classList.remove('hidden');
                } else {
                    keySpan.innerText = '';
                    keySpan.classList.add('hidden');
                }
            }
        }

        function updateProgressSummary() {
            let terjawab = 0;
            let ragu = 0;
            let kosong = 0;

            for (let i = 0; i < totalSoal; i++) {
                const s = examState[i];
                if (s.isRagu) ragu++;
                if (s.jawaban) terjawab++;
                else kosong++;
            }

            const pct = totalSoal > 0 ? Math.round((terjawab / totalSoal) * 100) : 0;

            const labelStat = document.getElementById('label-progress-stat');
            const barStat = document.getElementById('bar-progress-stat');
            if (labelStat) labelStat.innerText = `${terjawab}/${totalSoal} Terjawab (${pct}%)`;
            if (barStat) barStat.style.width = `${pct}%`;

            const filterSemua = document.getElementById('count-filter-semua');
            const filterBelum = document.getElementById('count-filter-belum');
            const filterRagu = document.getElementById('count-filter-ragu');
            if (filterSemua) filterSemua.innerText = totalSoal;
            if (filterBelum) filterBelum.innerText = kosong;
            if (filterRagu) filterRagu.innerText = ragu;
        }

        // 8. Filter Tab Kisi Nomor (Semua, Belum, Ragu)
        function setGridFilter(filter) {
            currentGridFilter = filter;
            const tabs = ['semua', 'belum', 'ragu'];

            tabs.forEach(t => {
                const btn = document.getElementById('tab-filter-' + t);
                if (btn) {
                    if (t === filter) {
                        btn.className = 'py-1 rounded bg-white text-slate-900 shadow-2xs text-center cursor-pointer transition-all';
                    } else {
                        btn.className = 'py-1 rounded text-slate-500 hover:text-slate-900 text-center cursor-pointer transition-all';
                    }
                }
            });

            filterGridButtons();
        }

        function filterGridButtons() {
            for (let i = 0; i < totalSoal; i++) {
                const btn = document.getElementById(`btn-grid-${i}`);
                if (!btn) continue;

                const s = examState[i];
                if (currentGridFilter === 'semua') {
                    btn.classList.remove('hidden');
                } else if (currentGridFilter === 'belum') {
                    if (!s.jawaban) {
                        btn.classList.remove('hidden');
                    } else {
                        btn.classList.add('hidden');
                    }
                } else if (currentGridFilter === 'ragu') {
                    if (s.isRagu) {
                        btn.classList.remove('hidden');
                    } else {
                        btn.classList.add('hidden');
                    }
                }
            }
        }

        function syncJawabanKeServer(soalId, jawaban, isRagu) {
            fetch("{{ route('tpa.jawaban') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    soal_id: soalId,
                    jawaban: jawaban || null,
                    is_ragu: isRagu ? 1 : 0
                })
            }).catch(err => console.error('Gagal sync jawaban:', err));
        }

        // 9. Countdown Timer Berjalan
        function startCountdown() {
            const timer = setInterval(() => {
                sisaDetik--;
                if (sisaDetik <= 0) {
                    clearInterval(timer);
                    sisaDetik = 0;
                    updateTimerDisplay();
                    alert('Waktu pengerjaan babak ini telah habis! Seluruh jawaban Anda akan otomatis disimpan.');
                    document.getElementById('form-submit-babak').submit();
                } else {
                    updateTimerDisplay();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const jam = Math.floor(sisaDetik / 3600);
            const menit = Math.floor((sisaDetik % 3600) / 60);
            const detik = sisaDetik % 60;

            const format = (n) => String(n).padStart(2, '0');
            const display = document.getElementById('timer-display');
            if (display) {
                display.innerText = `${format(jam)}:${format(menit)}:${format(detik)}`;
                if (sisaDetik < 300) {
                    display.classList.add('text-rose-400', 'animate-pulse');
                } else {
                    display.classList.remove('text-rose-400', 'animate-pulse');
                }
            }
        }

        // 10. Modal Konfirmasi Penyelesaian
        function konfirmasiSelesaiBabak() {
            let terjawab = 0;
            let ragu = 0;
            let kosong = 0;

            for (let i = 0; i < totalSoal; i++) {
                const s = examState[i];
                if (s.isRagu) ragu++;
                if (s.jawaban) terjawab++;
                else kosong++;
            }

            document.getElementById('modal-stat-terjawab').innerText = terjawab;
            document.getElementById('modal-stat-ragu').innerText = ragu;
            document.getElementById('modal-stat-kosong').innerText = kosong;

            const warningBanner = document.getElementById('modal-warning-empty-banner');
            const warningText = document.getElementById('modal-warning-empty-text');

            if (kosong > 0) {
                warningBanner.classList.remove('hidden');
                warningText.innerText = `Perhatian: Masih ada ${kosong} butir soal yang belum Anda jawab!`;
            } else if (ragu > 0) {
                warningBanner.classList.remove('hidden');
                warningText.innerText = `Catatan: Anda masih memiliki ${ragu} butir soal dengan status ragu-ragu.`;
            } else {
                warningBanner.classList.add('hidden');
            }

            document.getElementById('modal-konfirmasi-selesai').classList.remove('hidden');
        }

        function periksaSoalBelumTerjawab() {
            tutupModalKonfirmasi();

            // Cari nomor soal pertama yang belum terjawab atau ragu
            for (let i = 0; i < totalSoal; i++) {
                const s = examState[i];
                if (!s.jawaban || s.isRagu) {
                    bukaSoalNomor(i);
                    return;
                }
            }
        }

        function tutupModalKonfirmasi() {
            document.getElementById('modal-konfirmasi-selesai').classList.add('hidden');
        }

        // 11. Anti-Cheat: Proteksi Keyboard & Deteksi Tab Switch / Minimize
        function initAntiCheat() {
            document.addEventListener('keydown', function (e) {
                // Shortcut Ctrl+B untuk toggle Lembar Jawaban
                if (e.ctrlKey && e.key.toLowerCase() === 'b') {
                    e.preventDefault();
                    toggleSidebarLembarJawaban();
                    return false;
                }

                // Blokir shortcut keyboard mencurigakan
                if (
                    (e.ctrlKey && ['c', 'v', 'x', 'u', 'p', 's', 'a'].includes(e.key.toLowerCase())) ||
                    e.key === 'F12' ||
                    e.key === 'PrintScreen'
                ) {
                    e.preventDefault();
                    return false;
                }
            });

            // Deteksi perubahan visibilitas layar (Tab Switch atau Minimize)
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    laporkanPelanggaran('Beralih tab browser / aplikasi');
                }
            });

            window.addEventListener('blur', function () {
                laporkanPelanggaran('Meninggalkan fokus jendela ujian (blur)');
            });
        }

        let isReportingViolation = false;
        function laporkanPelanggaran(alasan) {
            if (isReportingViolation) return;
            isReportingViolation = true;

            fetch("{{ route('tpa.pelanggaran') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ alasan: alasan })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.is_disqualified) {
                        alert('Anda telah didiskualifikasi karena melebihi batas toleransi pelanggaran.');
                        window.location.href = "{{ route('tpa.selesai') }}";
                    } else {
                        const reasonEl = document.getElementById('label-violation-reason');
                        if (reasonEl) reasonEl.innerText = alasan;
                        const countEl = document.getElementById('label-violation-count');
                        if (countEl) countEl.innerText = data.violations;
                        document.getElementById('modal-pelanggaran').classList.remove('hidden');
                    }
                }
            })
            .finally(() => {
                setTimeout(() => { isReportingViolation = false; }, 2000);
            });
        }

        function tutupModalPelanggaran() {
            document.getElementById('modal-pelanggaran').classList.add('hidden');
            requestFullscreenMode();
        }
    </script>
</body>
</html>
