<div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200/80 text-[11px] font-bold text-nampi-orange">
                <span class="w-1.5 h-1.5 rounded-full bg-nampi-orange animate-ping"></span>
                <span id="timelineBadgeText">Timeline Tren Mingguan</span>
            </div>
            <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-1">
                Grafik Timeline Pendaftar (September 2026 — Juni 2027)
            </h2>
            <p class="text-xs text-slate-500 mt-0.5" id="timelineDescText">
                Pemantauan volume calon siswa baru masuk dan konversi kelulusan per minggu sepanjang siklus SPMB T.A. 2027/2028.
            </p>
        </div>
        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl self-start sm:self-auto shrink-0" id="cycleBadgeText">
            Siklus 10 Bulan (49 Minggu)
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
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 block" id="kpiPeakTitle">Minggu Puncak</span>
                <span class="text-base font-black text-amber-800 mt-0.5 block truncate max-w-[140px]" id="kpiPeakValue">{{ $timeline['mingguan']['puncak_label'] ?? $timeline['puncak_label'] }}</span>
                <span class="text-[10px] text-amber-600" id="kpiPeakSub">Volume Tertinggi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white border border-amber-200 text-amber-600 flex items-center justify-center text-lg shadow-xs">
                📈
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/70 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block" id="kpiAvgTitle">Rata-Rata Masuk</span>
                <span class="text-2xl font-black text-blue-800 mt-0.5 block" id="kpiAvgValue">{{ $timeline['mingguan']['rata_rata'] ?? $timeline['rata_rata'] }}</span>
                <span class="text-[10px] text-blue-600" id="kpiAvgSub">Siswa / Minggu</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white border border-blue-200 text-blue-600 flex items-center justify-center text-lg shadow-xs">
                📊
            </div>
        </div>
    </div>

    <!-- Chart Toolbar / Filter Mode -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-1 text-xs">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Granularity Switcher: Mingguan vs Bulanan -->
            <div class="flex items-center gap-1.5">
                <span class="text-slate-500 font-semibold text-[11px]">Granularitas:</span>
                <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 shadow-2xs" id="granularityToggle">
                    <button type="button" onclick="setGranularity('mingguan')" id="btnGranularityMingguan"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs transition-all bg-white text-slate-900 shadow-xs cursor-pointer">
                        Mingguan
                    </button>
                    <button type="button" onclick="setGranularity('bulanan')" id="btnGranularityBulanan"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                        Bulanan
                    </button>
                </div>
            </div>

            <!-- View Mode Switcher -->
            <div class="flex items-center gap-1.5">
                <span class="text-slate-500 font-semibold text-[11px]">Tampilan:</span>
                <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 shadow-2xs" id="chartViewToggle">
                    <button type="button" onclick="setChartMode('pendaftar')" id="btnModePendaftar"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs transition-all bg-white text-slate-900 shadow-xs cursor-pointer">
                        Pendaftar Baru
                    </button>
                    <button type="button" onclick="setChartMode('kumulatif')" id="btnModeKumulatif"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                        Akumulasi Kumulatif
                    </button>
                    <button type="button" onclick="setChartMode('komparasi')" id="btnModeKomparasi"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                        Pendaftar vs Diterima
                    </button>
                </div>
            </div>
        </div>

        <!-- Legend Indicator -->
        <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
            <div class="flex items-center gap-1.5" id="legendPendaftar">
                <span class="w-3 h-3 rounded-full bg-nampi-orange shadow-xs"></span>
                <span>Pendaftar Baru</span>
            </div>
            <div class="flex items-center gap-1.5" id="legendDiterima" style="display: none;">
                <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
                <span>Diterima / Lulus</span>
            </div>
        </div>
    </div>

    <!-- Canvas Container -->
    <div class="relative w-full h-80 sm:h-96">
        <canvas id="spmbTimelineChart"></canvas>
    </div>

    <!-- Static Milestones Guide Strip -->
    <div class="pt-3 border-t border-slate-100 space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Milestone Gelombang SPMB (Penanda Vertikal)</span>
            <span class="text-[10px] text-slate-400 font-medium">Garis putus-putus pada grafik menandai tanggal kunci seleksi</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
            @foreach ($timeline['milestones'] as $m)
                <div class="p-2.5 rounded-xl bg-slate-50/80 border border-slate-200/70 flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full shrink-0 shadow-2xs" style="background-color: {{ $m['color'] }};"></span>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 text-xs truncate">{{ $m['title'] }}</p>
                        <p class="text-[11px] text-slate-500 font-medium">{{ $m['date_label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('spmbTimelineChart');
        if (!ctx) return;

        // Raw datasets from backend
        const timelineData = {
            mingguan: @json($timeline['mingguan']),
            bulanan: @json($timeline['bulanan']),
            milestones: @json($timeline['milestones']),
        };

        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }

        // Gradients
        const canvasCtx = ctx.getContext('2d');
        const gradientOrange = canvasCtx.createLinearGradient(0, 0, 0, 320);
        gradientOrange.addColorStop(0, 'rgba(234, 88, 12, 0.28)');
        gradientOrange.addColorStop(1, 'rgba(234, 88, 12, 0.01)');

        const gradientEmerald = canvasCtx.createLinearGradient(0, 0, 0, 320);
        gradientEmerald.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        gradientEmerald.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const gradientBlue = canvasCtx.createLinearGradient(0, 0, 0, 320);
        gradientBlue.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
        gradientBlue.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

        let activeChart = null;
        let currentGranularity = 'mingguan'; // 'mingguan' or 'bulanan'
        let currentMode = 'pendaftar';        // 'pendaftar', 'kumulatif', 'komparasi'

        // Chart.js Plugin for Static Vertical Milestone Marker Lines
        const milestoneLinesPlugin = {
            id: 'milestoneLines',
            afterDraw: function (chart) {
                const chartCtx = chart.ctx;
                const xAxis = chart.scales.x;
                const yAxis = chart.scales.y;
                if (!xAxis || !yAxis) return;

                const isWeekly = (currentGranularity === 'mingguan');
                const milestones = timelineData.milestones || [];

                milestones.forEach(function (m) {
                    const targetIdx = isWeekly ? m.weekly_index : m.monthly_index;
                    if (targetIdx === undefined || targetIdx === null) return;

                    const xPos = xAxis.getPixelForTick(targetIdx);
                    if (isNaN(xPos) || xPos < xAxis.left || xPos > xAxis.right) return;

                    chartCtx.save();

                    // Draw vertical dashed line
                    chartCtx.beginPath();
                    chartCtx.setLineDash([4, 4]);
                    chartCtx.strokeStyle = m.color || '#94a3b8';
                    chartCtx.lineWidth = 1.5;
                    chartCtx.moveTo(xPos, yAxis.top);
                    chartCtx.lineTo(xPos, yAxis.bottom);
                    chartCtx.stroke();

                    // Draw pill badge at top of line
                    chartCtx.setLineDash([]);
                    chartCtx.font = 'bold 9px "Plus Jakarta Sans", sans-serif';
                    const text = m.title;
                    const textWidth = chartCtx.measureText(text).width;
                    const badgeWidth = textWidth + 12;
                    const badgeHeight = 18;
                    const badgeX = Math.min(Math.max(xPos - badgeWidth / 2, xAxis.left + 2), xAxis.right - badgeWidth - 2);
                    const badgeY = yAxis.top + 4;

                    // Badge background
                    chartCtx.fillStyle = m.color;
                    chartCtx.beginPath();
                    if (typeof chartCtx.roundRect === 'function') {
                        chartCtx.roundRect(badgeX, badgeY, badgeWidth, badgeHeight, 5);
                    } else {
                        chartCtx.rect(badgeX, badgeY, badgeWidth, badgeHeight);
                    }
                    chartCtx.fill();

                    // Badge text
                    chartCtx.fillStyle = '#ffffff';
                    chartCtx.textAlign = 'center';
                    chartCtx.textBaseline = 'middle';
                    chartCtx.fillText(text, badgeX + badgeWidth / 2, badgeY + badgeHeight / 2);

                    chartCtx.restore();
                });
            }
        };

        function getBaseOptions() {
            const isWeekly = (currentGranularity === 'mingguan');
            const currentDataset = timelineData[currentGranularity];

            return {
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
                            title: function (tooltipItems) {
                                if (!tooltipItems.length) return '';
                                const idx = tooltipItems[0].dataIndex;
                                if (isWeekly && currentDataset.ranges && currentDataset.ranges[idx]) {
                                    return currentDataset.labels[idx] + ' (' + currentDataset.ranges[idx] + ')';
                                }
                                return currentDataset.labels[idx] || '';
                            },
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
                            autoSkip: isWeekly,
                            maxTicksLimit: isWeekly ? 17 : 10,
                            autoSkipPadding: 10,
                            maxRotation: 0,
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: isWeekly ? 10 : 11,
                                weight: '600',
                            },
                            color: '#64748b',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: isWeekly ? 8 : 15,
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
        }

        function renderChart() {
            if (activeChart) {
                activeChart.destroy();
            }

            const activeDataset = timelineData[currentGranularity];
            const isWeekly = (currentGranularity === 'mingguan');
            const ptRadius = isWeekly ? 3 : 5;
            const ptHoverRadius = isWeekly ? 6 : 8;
            const borderWidth = isWeekly ? 2.5 : 3;

            let datasets = [];

            if (currentMode === 'pendaftar') {
                datasets = [{
                    label: 'Pendaftar Baru',
                    data: activeDataset.pendaftar,
                    borderColor: '#ea580c',
                    backgroundColor: gradientOrange,
                    fill: true,
                    tension: 0.3,
                    borderWidth: borderWidth,
                    pointBackgroundColor: '#ea580c',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: ptRadius,
                    pointHoverRadius: ptHoverRadius,
                }];
                document.getElementById('legendPendaftar').style.display = 'flex';
                document.getElementById('legendDiterima').style.display = 'none';
            } else if (currentMode === 'kumulatif') {
                datasets = [{
                    label: 'Total Kumulatif Siswa',
                    data: activeDataset.kumulatif,
                    borderColor: '#2563eb',
                    backgroundColor: gradientBlue,
                    fill: true,
                    tension: 0.3,
                    borderWidth: borderWidth,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: ptRadius,
                    pointHoverRadius: ptHoverRadius,
                }];
                document.getElementById('legendPendaftar').style.display = 'flex';
                document.getElementById('legendDiterima').style.display = 'none';
            } else if (currentMode === 'komparasi') {
                datasets = [
                    {
                        label: 'Pendaftar Baru',
                        data: activeDataset.pendaftar,
                        borderColor: '#ea580c',
                        backgroundColor: gradientOrange,
                        fill: false,
                        tension: 0.3,
                        borderWidth: borderWidth,
                        pointBackgroundColor: '#ea580c',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: ptRadius,
                        pointHoverRadius: ptHoverRadius,
                    },
                    {
                        label: 'Diterima / Lulus',
                        data: activeDataset.diterima,
                        borderColor: '#10b981',
                        backgroundColor: gradientEmerald,
                        fill: false,
                        tension: 0.3,
                        borderWidth: borderWidth,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: ptRadius,
                        pointHoverRadius: ptHoverRadius,
                    }
                ];
                document.getElementById('legendPendaftar').style.display = 'flex';
                document.getElementById('legendDiterima').style.display = 'flex';
            }

            activeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: activeDataset.labels,
                    datasets: datasets,
                },
                options: getBaseOptions(),
                plugins: [milestoneLinesPlugin],
            });
        }

        // Granularity switcher: Mingguan vs Bulanan
        window.setGranularity = function (type) {
            currentGranularity = type;

            const btnMingguan = document.getElementById('btnGranularityMingguan');
            const btnBulanan  = document.getElementById('btnGranularityBulanan');

            const activeClass = ['bg-white', 'text-slate-900', 'shadow-xs'];
            const inactiveClass = ['text-slate-600', 'hover:text-slate-900'];

            if (type === 'mingguan') {
                btnMingguan.classList.add(...activeClass);
                btnMingguan.classList.remove(...inactiveClass);
                btnBulanan.classList.remove(...activeClass);
                btnBulanan.classList.add(...inactiveClass);

                // Update text header
                document.getElementById('timelineBadgeText').innerText = 'Timeline Tren Mingguan';
                document.getElementById('timelineDescText').innerText = 'Pemantauan volume calon siswa baru masuk dan konversi kelulusan per minggu sepanjang siklus SPMB T.A. 2027/2028.';
                document.getElementById('cycleBadgeText').innerText = 'Siklus 10 Bulan (49 Minggu)';

                // Update KPI Cards
                document.getElementById('kpiPeakTitle').innerText = 'Minggu Puncak';
                document.getElementById('kpiPeakValue').innerText = timelineData.mingguan.puncak_label;
                document.getElementById('kpiPeakSub').innerText = 'Volume Tertinggi';

                document.getElementById('kpiAvgTitle').innerText = 'Rata-Rata Masuk';
                document.getElementById('kpiAvgValue').innerText = timelineData.mingguan.rata_rata;
                document.getElementById('kpiAvgSub').innerText = 'Siswa / Minggu';
            } else {
                btnBulanan.classList.add(...activeClass);
                btnBulanan.classList.remove(...inactiveClass);
                btnMingguan.classList.remove(...activeClass);
                btnMingguan.classList.add(...inactiveClass);

                // Update text header
                document.getElementById('timelineBadgeText').innerText = 'Timeline Tren Bulanan';
                document.getElementById('timelineDescText').innerText = 'Pemantauan volume calon siswa baru masuk dan konversi kelulusan per bulan sepanjang siklus SPMB T.A. 2027/2028.';
                document.getElementById('cycleBadgeText').innerText = 'Siklus 10 Bulan';

                // Update KPI Cards
                document.getElementById('kpiPeakTitle').innerText = 'Bulan Tertinggi';
                document.getElementById('kpiPeakValue').innerText = timelineData.bulanan.bulan_tertinggi;
                document.getElementById('kpiPeakSub').innerText = 'Volume Tertinggi';

                document.getElementById('kpiAvgTitle').innerText = 'Rata-Rata Masuk';
                document.getElementById('kpiAvgValue').innerText = timelineData.bulanan.rata_rata;
                document.getElementById('kpiAvgSub').innerText = 'Siswa / Bulan';
            }

            renderChart();
        };

        // Chart mode switcher: Pendaftar vs Kumulatif vs Komparasi
        window.setChartMode = function (mode) {
            currentMode = mode;

            const btnPendaftar = document.getElementById('btnModePendaftar');
            const btnKumulatif = document.getElementById('btnModeKumulatif');
            const btnKomparasi = document.getElementById('btnModeKomparasi');

            const activeClass = ['bg-white', 'text-slate-900', 'shadow-xs'];
            const inactiveClass = ['text-slate-600', 'hover:text-slate-900'];

            [btnPendaftar, btnKumulatif, btnKomparasi].forEach(btn => {
                if (btn) {
                    btn.classList.remove(...activeClass);
                    btn.classList.add(...inactiveClass);
                }
            });

            if (mode === 'pendaftar' && btnPendaftar) {
                btnPendaftar.classList.add(...activeClass);
                btnPendaftar.classList.remove(...inactiveClass);
            } else if (mode === 'kumulatif' && btnKumulatif) {
                btnKumulatif.classList.add(...activeClass);
                btnKumulatif.classList.remove(...inactiveClass);
            } else if (mode === 'komparasi' && btnKomparasi) {
                btnKomparasi.classList.add(...activeClass);
                btnKomparasi.classList.remove(...inactiveClass);
            }

            renderChart();
        };

        // Initial render (Mingguan by default)
        renderChart();
    });
</script>
@endpush
