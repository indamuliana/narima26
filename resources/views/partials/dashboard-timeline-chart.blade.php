<div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200/80 text-[11px] font-bold text-nampi-orange">
                <span class="w-1.5 h-1.5 rounded-full bg-nampi-orange animate-ping"></span>
                <span>Timeline Tren Bulanan</span>
            </div>
            <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-1">
                Grafik Timeline Pendaftar (September 2026 — Juni 2027)
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Pemantauan volume calon siswa baru masuk dan konversi kelulusan per bulan sepanjang siklus SPMB T.A. 2027/2028.
            </p>
        </div>
        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl self-start sm:self-auto shrink-0">
            Siklus 10 Bulan
        </span>
    </div>

    <!-- Quick Summary 4 Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Pendaftar</span>
                <span class="text-2xl font-black text-slate-900 mt-0.5 block">{{ number_format($timeline['total_periode']) }}</span>
                <span class="text-[10px] text-slate-400">Sep '26 - Jun '27</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center text-lg shadow-xs">
                👥
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/70 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 block">Lulus / Diterima</span>
                <span class="text-2xl font-black text-emerald-700 mt-0.5 block">{{ number_format($timeline['total_diterima']) }}</span>
                <span class="text-[10px] text-emerald-600">Terverifikasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white border border-emerald-200 text-emerald-600 flex items-center justify-center text-lg shadow-xs">
                🎓
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 block">Puncak Masuk</span>
                <span class="text-base font-black text-amber-800 mt-0.5 block truncate max-w-[120px]">{{ $timeline['bulan_tertinggi'] }}</span>
                <span class="text-[10px] text-amber-600">Volume Tertinggi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white border border-amber-200 text-amber-600 flex items-center justify-center text-lg shadow-xs">
                📈
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/70 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block">Rata-Rata Masuk</span>
                <span class="text-2xl font-black text-blue-800 mt-0.5 block">{{ $timeline['rata_rata'] }}</span>
                <span class="text-[10px] text-blue-600">Siswa / Bulan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white border border-blue-200 text-blue-600 flex items-center justify-center text-lg shadow-xs">
                📊
            </div>
        </div>
    </div>

    <!-- Chart Toolbar / Filter Mode -->
    <div class="flex flex-wrap items-center justify-between gap-3 pt-1 text-xs">
        <div class="flex items-center gap-2">
            <span class="text-slate-500 font-semibold text-[11px]">Mode Grafik:</span>
            <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200" id="chartViewToggle">
                <button type="button" onclick="setChartMode('bulanan')" id="btnModeBulanan"
                        class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all bg-white text-slate-900 shadow-xs cursor-pointer">
                    Pendaftar Bulanan
                </button>
                <button type="button" onclick="setChartMode('kumulatif')" id="btnModeKumulatif"
                        class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                    Akumulasi Kumulatif
                </button>
                <button type="button" onclick="setChartMode('komparasi')" id="btnModeKomparasi"
                        class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                    Pendaftar vs Diterima
                </button>
            </div>
        </div>

        <!-- Legend Indicator -->
        <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
            <div class="flex items-center gap-1.5" id="legendPendaftar">
                <span class="w-3 h-3 rounded-full bg-nampi-orange shadow-xs"></span>
                <span>Pendaftar Baru</span>
            </div>
            <div class="flex items-center gap-1.5" id="legendDiterima">
                <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
                <span>Diterima / Lulus</span>
            </div>
        </div>
    </div>

    <!-- Canvas Container -->
    <div class="relative w-full h-72 sm:h-84">
        <canvas id="spmbTimelineChart"></canvas>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('spmbTimelineChart');
        if (!ctx) return;

        const timelineLabels = @json($timeline['labels']);
        const dataPendaftar  = @json($timeline['pendaftar']);
        const dataDiterima   = @json($timeline['diterima']);
        const dataKumulatif  = @json($timeline['kumulatif']);

        // Check if Chart is available
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }

        // Gradients
        const canvasCtx = ctx.getContext('2d');
        const gradientOrange = canvasCtx.createLinearGradient(0, 0, 0, 300);
        gradientOrange.addColorStop(0, 'rgba(234, 88, 12, 0.28)');
        gradientOrange.addColorStop(1, 'rgba(234, 88, 12, 0.01)');

        const gradientEmerald = canvasCtx.createLinearGradient(0, 0, 0, 300);
        gradientEmerald.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        gradientEmerald.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const gradientBlue = canvasCtx.createLinearGradient(0, 0, 0, 300);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.01)');

        let activeChart = null;

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#f1f5f9',
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: {
                        family: 'Plus Jakarta Sans',
                        size: 13,
                        weight: 'bold',
                    },
                    bodyFont: {
                        family: 'Plus Jakarta Sans',
                        size: 12,
                    },
                    callbacks: {
                        label: function (context) {
                            return ' ' + context.dataset.label + ': ' + context.parsed.y + ' Siswa';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(241, 245, 249, 1)',
                        borderColor: 'rgba(226, 232, 240, 1)',
                    },
                    ticks: {
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11,
                            weight: '600',
                        },
                        color: '#64748b',
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: 10,
                    grid: {
                        color: 'rgba(226, 232, 240, 0.8)',
                        borderDash: [4, 4],
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11,
                        },
                        color: '#94a3b8',
                    }
                }
            }
        };

        function renderChart(mode) {
            if (activeChart) {
                activeChart.destroy();
            }

            let datasets = [];

            if (mode === 'bulanan') {
                datasets = [{
                    label: 'Pendaftar Baru',
                    data: dataPendaftar,
                    borderColor: '#ea580c',
                    backgroundColor: gradientOrange,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: '#ea580c',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }];
                document.getElementById('legendPendaftar').style.display = 'flex';
                document.getElementById('legendDiterima').style.display = 'none';
            } else if (mode === 'kumulatif') {
                datasets = [{
                    label: 'Total Kumulatif Siswa',
                    data: dataKumulatif,
                    borderColor: '#2563eb',
                    backgroundColor: gradientBlue,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }];
                document.getElementById('legendPendaftar').style.display = 'flex';
                document.getElementById('legendDiterima').style.display = 'none';
            } else if (mode === 'komparasi') {
                datasets = [
                    {
                        label: 'Pendaftar Baru',
                        data: dataPendaftar,
                        borderColor: '#ea580c',
                        backgroundColor: gradientOrange,
                        fill: false,
                        tension: 0.35,
                        borderWidth: 3,
                        pointBackgroundColor: '#ea580c',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                    },
                    {
                        label: 'Diterima / Lulus',
                        data: dataDiterima,
                        borderColor: '#10b981',
                        backgroundColor: gradientEmerald,
                        fill: false,
                        tension: 0.35,
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                    }
                ];
                document.getElementById('legendPendaftar').style.display = 'flex';
                document.getElementById('legendDiterima').style.display = 'flex';
            }

            activeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: timelineLabels,
                    datasets: datasets,
                },
                options: baseOptions,
            });
        }

        // Toggle buttons style handler
        window.setChartMode = function (mode) {
            const btnBulanan = document.getElementById('btnModeBulanan');
            const btnKumulatif = document.getElementById('btnModeKumulatif');
            const btnKomparasi = document.getElementById('btnModeKomparasi');

            const activeClass = ['bg-white', 'text-slate-900', 'shadow-xs'];
            const inactiveClass = ['text-slate-600', 'hover:text-slate-900'];

            [btnBulanan, btnKumulatif, btnKomparasi].forEach(btn => {
                btn.classList.remove(...activeClass);
                btn.classList.add(...inactiveClass);
            });

            if (mode === 'bulanan') {
                btnBulanan.classList.add(...activeClass);
                btnBulanan.classList.remove(...inactiveClass);
            } else if (mode === 'kumulatif') {
                btnKumulatif.classList.add(...activeClass);
                btnKumulatif.classList.remove(...inactiveClass);
            } else if (mode === 'komparasi') {
                btnKomparasi.classList.add(...activeClass);
                btnKomparasi.classList.remove(...inactiveClass);
            }

            renderChart(mode);
        };

        // Initial render
        renderChart('bulanan');
    });
</script>
@endpush
