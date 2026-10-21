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

        /* Watermark samar anti screenshot / camera foto */
        .watermark-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 40;
            opacity: 0.045;
            background-image: repeating-linear-gradient(
                -45deg,
                transparent,
                transparent 150px,
                rgba(0, 0, 0, 0.8) 150px,
                rgba(0, 0, 0, 0.8) 300px
            );
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .watermark-text {
            transform: rotate(-30deg);
            font-size: 1.75rem;
            font-weight: 900;
            color: #000;
            white-space: pre-wrap;
            text-align: center;
            line-height: 4.5rem;
            user-select: none;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col no-select" oncontextmenu="return false;">

    <!-- Watermark Anti-Leak Overlay -->
    <div class="watermark-overlay">
        <div class="watermark-text">
            {{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
            &#10;{{ $calonSiswa->nama_lengkap }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ request()->ip() }}
        </div>
    </div>

    <!-- Top Navigation Header -->
    <header class="bg-slate-900 text-white sticky top-0 z-30 shadow-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <!-- Siswa & Babak Info -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-600 flex items-center justify-center font-black text-xs shrink-0">
                    TPA
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs sm:text-sm text-white">{{ $calonSiswa->nama_lengkap }}</span>
                        <span class="hidden sm:inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300">
                            {{ $calonSiswa->nomor_pendaftaran }}
                        </span>
                    </div>
                    <div class="text-[11px] text-orange-400 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $babakAktif === 'literasi' ? 'bg-blue-400' : 'bg-emerald-400' }}"></span>
                        <span>{{ $babakAktif === 'literasi' ? 'Babak 1: Literasi' : 'Babak 2: Numerasi' }}</span>
                    </div>
                </div>
            </div>

            <!-- Sisa Waktu (Countdown Timer) & Mobile Toggle -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-800 border border-slate-700">
                    <svg class="w-4 h-4 text-orange-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-right">
                        <span class="hidden sm:block text-[9px] uppercase font-bold text-slate-400">Sisa Waktu</span>
                        <span id="timer-display" class="font-mono text-sm sm:text-base font-black text-white tracking-wider">
                            --:--:--
                        </span>
                    </div>
                </div>

                <!-- Tombol Buka Lembar Jawaban (Khusus Layar HP) -->
                <button type="button" onclick="toggleLembarJawabanMobile()"
                        class="lg:hidden p-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 hover:text-white cursor-pointer"
                        title="Daftar Soal">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Exam Container (Layout 2/3 Kiri Soal, 1/3 Kanan Kisi-kisi) -->
    <main class="max-w-7xl w-full mx-auto p-4 sm:p-6 flex-1 flex flex-col lg:flex-row gap-6 relative">

        <!-- Kolom Kiri 2/3: Butir Soal & Pilihan Jawaban -->
        <section class="w-full lg:w-2/3 flex flex-col justify-between space-y-6">

            <!-- Loop Seluruh Soal (Hanya satu yang aktif/ditampilkan sekaligus via JS) -->
            <div id="soal-container" class="space-y-6">
                @foreach ($soalSorted as $idx => $soal)
                    @php
                        $jawaban = $jawabanMap->get($soal->id);
                        $dipilih = $jawaban?->jawaban_dipilih;
                        $isRagu = $jawaban?->is_ragu ?? false;
                    @endphp
                    <div class="soal-item bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6 {{ $idx === 0 ? '' : 'hidden' }}"
                         id="soal-card-{{ $idx }}"
                         data-index="{{ $idx }}"
                         data-soal-id="{{ $soal->id }}"
                         data-nomor="{{ $idx + 1 }}">

                        <!-- Card Header Soal -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-slate-900 text-white font-black text-xs sm:text-sm">
                                    Soal No. {{ $idx + 1 }}
                                </span>
                                <span class="text-xs text-slate-400">dari {{ count($soalSorted) }} butir</span>
                            </div>
                            <span class="text-xs font-bold text-slate-500">Bobot: {{ $soal->bobot }} Poin</span>
                        </div>

                        <!-- Stimulus & Pertanyaan -->
                        <div class="space-y-4">
                            <p class="text-sm sm:text-base text-slate-800 leading-relaxed font-normal select-none">
                                {!! nl2br(e($soal->pertanyaan)) !!}
                            </p>

                            @if ($soal->gambar_path)
                                <div class="p-2 bg-slate-50 rounded-2xl border border-slate-200 inline-block max-w-full">
                                    <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Gambar Soal {{ $idx + 1 }}"
                                         class="max-h-72 max-w-full object-contain rounded-xl mx-auto"
                                         loading="lazy">
                                </div>
                            @endif
                        </div>

                        <!-- Opsi Jawaban Radio Options -->
                        <div class="space-y-3 pt-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pilih Jawaban Anda:</p>
                            @foreach ($soal->opsi_jawaban ?? [] as $opsi)
                                @php
                                    $isSelected = ($dipilih === $opsi['key']);
                                @endphp
                                <label class="opsi-label flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl border border-slate-200 hover:border-orange-400 hover:bg-orange-50/40 cursor-pointer transition-all {{ $isSelected ? 'bg-orange-50 border-orange-500 font-semibold ring-1 ring-orange-500' : 'bg-white' }}">
                                    <input type="radio" name="jawaban_{{ $soal->id }}" value="{{ $opsi['key'] }}"
                                           {{ $isSelected ? 'checked' : '' }}
                                           onchange="pilihJawaban({{ $idx }}, {{ $soal->id }}, '{{ $opsi['key'] }}')"
                                           class="mt-1 w-4 h-4 text-orange-600 focus:ring-orange-500 border-slate-300">
                                    <div class="flex items-start gap-2.5 flex-1">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center font-black text-xs text-slate-700 shrink-0">
                                            {{ $opsi['key'] }}
                                        </span>
                                        <span class="text-xs sm:text-sm text-slate-800 leading-relaxed select-none">
                                            {{ $opsi['text'] }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bottom Navigation Bar (Sebelumnya, Ragu-ragu, Berikutnya) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 flex items-center justify-between gap-3">
                <button type="button" id="btn-prev" onclick="geserSoal(-1)"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Sebelumnya</span>
                </button>

                <!-- Tombol Simpan Sementara / Ragu-Ragu -->
                <button type="button" id="btn-ragu" onclick="toggleRaguRagu()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-300 bg-amber-50 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="label-ragu">Ragu-ragu</span>
                </button>

                <button type="button" id="btn-next" onclick="geserSoal(1)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold transition-all shadow-md shadow-orange-600/20 cursor-pointer">
                    <span>Berikutnya</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>

        <!-- Kolom Kanan 1/3: Kisi-kisi Lembar Jawaban & Submit Babak -->
        <aside id="lembar-jawaban-panel"
               class="fixed inset-y-0 right-0 z-40 w-80 bg-white border-l border-slate-200 p-6 flex flex-col justify-between shadow-2xl transition-transform transform translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:w-1/3 lg:shadow-xs lg:rounded-2xl lg:border">

            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Lembar Jawaban</h2>
                        <p class="text-[11px] text-slate-400">Pilih nomor untuk langsung membuka soal</p>
                    </div>
                    <button type="button" onclick="toggleLembarJawabanMobile()" class="lg:hidden text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Indikator Warna Status Soal -->
                <div class="grid grid-cols-3 gap-2 text-[10px] font-semibold text-slate-600 pb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-emerald-600 text-white flex items-center justify-center font-bold text-[8px]">&check;</span>
                        <span>Dijawab</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-amber-400 text-white flex items-center justify-center font-bold text-[8px]">?</span>
                        <span>Ragu-ragu</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-[8px]">-</span>
                        <span>Belum</span>
                    </div>
                </div>

                <!-- Grid Nomor Soal -->
                <div class="grid grid-cols-5 gap-2 max-h-96 overflow-y-auto pr-1">
                    @foreach ($soalSorted as $idx => $soal)
                        @php
                            $jawaban = $jawabanMap->get($soal->id);
                            $dipilih = $jawaban?->jawaban_dipilih;
                            $isRagu = $jawaban?->is_ragu ?? false;

                            $btnClass = 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200';
                            if ($isRagu) {
                                $btnClass = 'bg-amber-400 text-amber-950 font-bold border-amber-500 shadow-2xs';
                            } elseif (!empty($dipilih)) {
                                $btnClass = 'bg-emerald-600 text-white font-bold border-emerald-700 shadow-2xs';
                            }
                        @endphp
                        <button type="button" onclick="bukaSoalNomor({{ $idx }})"
                                id="btn-grid-{{ $idx }}"
                                data-index="{{ $idx }}"
                                class="grid-number-btn relative h-11 rounded-xl border flex flex-col items-center justify-center text-xs font-bold transition-all cursor-pointer {{ $btnClass }} {{ $idx === 0 ? 'ring-2 ring-orange-500 ring-offset-1' : '' }}">
                            <span>{{ $idx + 1 }}</span>
                            <span id="grid-key-{{ $idx }}" class="text-[9px] font-mono leading-none {{ empty($dipilih) ? 'hidden' : '' }}">
                                {{ $dipilih ?? '' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tombol Selesaikan Babak -->
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="konfirmasiSelesaiBabak()"
                        class="w-full py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Selesaikan {{ $babakAktif === 'literasi' ? 'Babak 1 (Literasi)' : 'Seluruh Ujian TPA' }}</span>
                </button>
            </div>
        </aside>
    </main>

    <!-- Modal Peringatan Kecurangan / Tab Switch -->
    <div id="modal-pelanggaran" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-rose-200 shadow-2xl max-w-md w-full p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-800">PERINGATAN INTEGRITAS UJIAN!</h3>
                <p class="text-xs text-rose-600 font-bold mt-1">Anda terdeteksi beralih dari jendela/tab ujian.</p>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Pelanggaran telah dicatat pada log panitia: <br>
                    <strong class="text-slate-800 text-sm" id="label-violation-count">1</strong> dari maksimal <strong>{{ $pengaturan->max_pelanggaran }} kali</strong>.
                    Jika kuota pelanggaran terlampaui, ujian Anda akan otomatis dikunci dan didiskualifikasi!
                </p>
            </div>
            <button type="button" onclick="tutupModalPelanggaran()"
                    class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition-colors cursor-pointer">
                Saya Mengerti & Kembali Mengerjakan
            </button>
        </div>
    </div>

    <!-- Modal Konfirmasi Selesai Babak -->
    <div id="modal-konfirmasi-selesai" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-800">
                    Konfirmasi Selesai {{ $babakAktif === 'literasi' ? 'Babak 1 (Literasi)' : 'Seluruh Tes' }}
                </h3>
                <div class="text-xs text-slate-500 mt-2 space-y-1">
                    <p id="info-status-jawaban" class="font-semibold text-slate-700"></p>
                    <p class="text-rose-600 font-medium">
                        {{ $babakAktif === 'literasi' ? 'Setelah diselesaikan, Babak 1 akan dikunci permanen dan Anda tidak dapat kembali mengubah jawaban.' : 'Setelah disubmit, seluruh pengerjaan TPA Anda akan tuntas dan disimpan.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="tutupModalKonfirmasi()"
                        class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-colors">
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

    <!-- Script Logika Ujian, Timer & Anti-Cheat -->
    <script>
        // State Ujian
        let currentIndex = 0;
        const totalSoal = {{ count($soalSorted) }};
        const maxPelanggaran = {{ $pengaturan->max_pelanggaran }};
        let sisaDetik = {{ $sisaWaktuDetik }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

        // 1. Inisialisasi Tampilan
        document.addEventListener('DOMContentLoaded', function () {
            updateSoalView();
            updateTimerDisplay();
            startCountdown();
            initAntiCheat();
        });

        function bukaSoalNomor(index) {
            if (index < 0 || index >= totalSoal) return;
            currentIndex = index;
            updateSoalView();

            // Sembunyikan drawer mobile jika sedang terbuka
            const drawer = document.getElementById('lembar-jawaban-panel');
            drawer.classList.add('translate-x-full');
        }

        function geserSoal(offset) {
            const target = currentIndex + offset;
            if (target >= 0 && target < totalSoal) {
                currentIndex = target;
                updateSoalView();
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
                        btnGrid.classList.add('ring-2', 'ring-orange-500', 'ring-offset-1');
                    } else {
                        btnGrid.classList.remove('ring-2', 'ring-orange-500', 'ring-offset-1');
                    }
                }
            }

            // Atur tombol prev & next
            const btnPrev = document.getElementById('btn-prev');
            btnPrev.disabled = (currentIndex === 0);

            const btnNext = document.getElementById('btn-next');
            if (currentIndex === totalSoal - 1) {
                btnNext.innerHTML = `<span>Selesai</span><svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
                btnNext.onclick = konfirmasiSelesaiBabak;
            } else {
                btnNext.innerHTML = `<span>Berikutnya</span><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>`;
                btnNext.onclick = () => geserSoal(1);
            }

            // Update label tombol ragu-ragu
            const currentState = examState[currentIndex];
            const btnRagu = document.getElementById('btn-ragu');
            const labelRagu = document.getElementById('label-ragu');
            if (currentState && currentState.isRagu) {
                btnRagu.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-500 bg-amber-400 text-xs font-bold text-amber-950 shadow-xs cursor-pointer';
                labelRagu.innerText = 'Ragu-ragu (Aktif)';
            } else {
                btnRagu.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-300 bg-amber-50 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors shadow-2xs cursor-pointer';
                labelRagu.innerText = 'Ragu-ragu';
            }
        }

        // 2. Pemilihan & Penyimpanan Jawaban Real-time AJAX
        function pilihJawaban(index, soalId, opsiKey) {
            examState[index].jawaban = opsiKey;
            syncJawabanKeServer(soalId, opsiKey, examState[index].isRagu);
            updateGridButton(index);
        }

        function toggleRaguRagu() {
            const current = examState[currentIndex];
            current.isRagu = !current.isRagu;
            syncJawabanKeServer(current.soalId, current.jawaban, current.isRagu);
            updateGridButton(currentIndex);
            updateSoalView();
        }

        function updateGridButton(index) {
            const current = examState[index];
            const btn = document.getElementById(`btn-grid-${index}`);
            const keySpan = document.getElementById(`grid-key-${index}`);

            if (!btn) return;

            btn.className = 'grid-number-btn relative h-11 rounded-xl border flex flex-col items-center justify-center text-xs font-bold transition-all cursor-pointer';

            if (current.isRagu) {
                btn.classList.add('bg-amber-400', 'text-amber-950', 'border-amber-500', 'shadow-2xs');
            } else if (current.jawaban) {
                btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-700', 'shadow-2xs');
            } else {
                btn.classList.add('bg-slate-100', 'text-slate-700', 'border-slate-200', 'hover:bg-slate-200');
            }

            if (index === currentIndex) {
                btn.classList.add('ring-2', 'ring-orange-500', 'ring-offset-1');
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

        // 3. Countdown Timer Berjalan
        function startCountdown() {
            const timer = setInterval(() => {
                sisaDetik--;
                if (sisaDetik <= 0) {
                    clearInterval(timer);
                    sisaDetik = 0;
                    updateTimerDisplay();
                    alert('Waktu pengerjaan babak ini telah habis! Jawaban Anda akan otomatis disimpan.');
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
                    display.classList.add('text-rose-400');
                }
            }
        }

        // 4. Modal Konfirmasi Penyelesaian
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

            const info = document.getElementById('info-status-jawaban');
            info.innerHTML = `Terjawab: <strong>${terjawab}</strong> &bull; Ragu-ragu: <strong>${ragu}</strong> &bull; Belum Dijawab: <strong>${kosong}</strong>`;

            document.getElementById('modal-konfirmasi-selesai').classList.remove('hidden');
        }

        function tutupModalKonfirmasi() {
            document.getElementById('modal-konfirmasi-selesai').classList.add('hidden');
        }

        function toggleLembarJawabanMobile() {
            const drawer = document.getElementById('lembar-jawaban-panel');
            drawer.classList.toggle('translate-x-full');
        }

        // 5. Anti-Cheat: Proteksi Keyboard & Deteksi Tab Switch / Minimize
        function initAntiCheat() {
            // Blokir shortcut keyboard berbahaya
            document.addEventListener('keydown', function (e) {
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
                    laporkanPelanggaran('Beralih tab browser');
                }
            });

            window.addEventListener('blur', function () {
                laporkanPelanggaran('Meninggalkan jendela ujian (blur)');
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
                        document.getElementById('label-violation-count').innerText = data.violations;
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
        }
    </script>
</body>
</html>
