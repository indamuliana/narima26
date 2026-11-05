@extends('pdf.layout', ['title' => 'Laporan Keuangan & Pembukuan SPMB - SMK Wikrama 1 Garut'])

@section('custom_styles')
    @page {
        margin: 0.8cm 1.0cm 0.8cm 1.0cm;
    }
    .doc-title {
        font-size: 13pt;
        margin-top: 4px;
        margin-bottom: 2px;
    }
    .doc-number {
        font-size: 8.5pt;
        margin-bottom: 8px;
    }
    .data-table {
        margin-top: 6px;
        margin-bottom: 10px;
        font-size: 6.5pt;
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead {
        display: table-header-group;
    }
    .data-table tr {
        page-break-inside: avoid;
    }
    .data-table th, .data-table td {
        padding: 4px 4px;
        border: 0.5px solid #94a3b8;
    }
    .data-table th {
        background-color: #0f172a;
        color: #ffffff;
        font-weight: bold;
        text-align: center;
    }
    .data-table th.bg-sub {
        background-color: #1e293b;
    }
    .signature-box {
        margin-top: 15px;
        page-break-inside: avoid;
    }
    .signature-space {
        height: 45px;
    }
@endsection

@section('content')
    <div class="doc-title">LAPORAN KEUANGAN & PEMBUKUAN KAS SPMB</div>
    <div class="doc-number">Tahun Ajaran 2027/2028 &bull; Dicetak pada: {{ $tanggalCetak }} WIB</div>

    <div style="font-size: 7.5pt; color: #334155; margin-bottom: 8px; background-color: #f8fafc; padding: 5px 8px; border-radius: 4px; border: 1px solid #cbd5e1;">
        <strong>Parameter Laporan:</strong>
        Gelombang: <strong>{{ $filterInfo['gelombang'] }}</strong> &bull;
        Jurusan: <strong>{{ $filterInfo['jurusan'] }}</strong> &bull;
        Status: <strong>{{ $filterInfo['status'] }}</strong> &bull;
        Total Siswa: <strong>{{ $summary['total_siswa'] }}</strong> &bull;
        Total Kewajiban: <strong>Rp {{ number_format($summary['total_kewajiban'], 0, ',', '.') }}</strong> &bull;
        Kas Masuk: <strong style="color: #059669;">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }} ({{ $summary['tingkat_pelunasan'] }}%)</strong> &bull;
        Sisa Piutang: <strong style="color: #dc2626;">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</strong>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2.5%;">No</th>
                <th rowspan="2" style="width: 7%;">No. Reg</th>
                <th rowspan="2" style="width: 14%; text-align: left;">Nama Calon Murid</th>
                <th rowspan="2" style="width: 10%; text-align: left;">Jurusan</th>
                <th rowspan="2" style="width: 6.5%;">Gelombang</th>
                <th colspan="3" class="bg-sub" style="width: 17%;">Biaya Seleksi</th>
                <th colspan="3" class="bg-sub" style="width: 21%;">Tagihan #1 (Pendidikan)</th>
                <th colspan="3" class="bg-sub" style="width: 20%;">Tagihan #2 (Seragam)</th>
                <th colspan="3" class="bg-sub" style="width: 23%;">Rekapitulasi Total</th>
                <th rowspan="2" style="width: 9%;">Status</th>
            </tr>
            <tr>
                <!-- Seleksi -->
                <th style="width: 5.5%;">Tagihan</th>
                <th style="width: 5.5%;">Terbayar</th>
                <th style="width: 6%;">Status</th>
                <!-- Pendidikan -->
                <th style="width: 7%;">Tagihan</th>
                <th style="width: 7%;">Terbayar</th>
                <th style="width: 7%;">Sisa</th>
                <!-- Seragam -->
                <th style="width: 6.5%;">Tagihan</th>
                <th style="width: 6.5%;">Terbayar</th>
                <th style="width: 7%;">Sisa</th>
                <!-- Rekap Total -->
                <th style="width: 7.5%;">Kewajiban</th>
                <th style="width: 7.5%;">Terbayar</th>
                <th style="width: 8%;">Sisa Piutang</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totSel = 0; $totSelBayar = 0;
                $totEdu = 0; $totEduBayar = 0; $totEduSisa = 0;
                $totSrg = 0; $totSrgBayar = 0; $totSrgSisa = 0;
                $totKewajiban = 0; $totTerbayar = 0; $totPiutang = 0;
            @endphp
            @forelse ($rows as $index => $r)
                @php
                    $s = $r['calon_siswa'];
                    $totSel += $r['tagihan_sel'];
                    $totSelBayar += $r['bayar_sel'];
                    $totEdu += $r['netto_edu'];
                    $totEduBayar += $r['bayar_edu'];
                    $totEduSisa += $r['sisa_edu'];
                    $totSrg += $r['netto_srg'];
                    $totSrgBayar += $r['bayar_srg'];
                    $totSrgSisa += $r['sisa_srg'];
                    $totKewajiban += $r['total_kewajiban'];
                    $totTerbayar += $r['total_terbayar'];
                    $totPiutang += $r['sisa_piutang'];
                @endphp
                <tr style="{{ $index % 2 == 1 ? 'background-color: #f8fafc;' : '' }}">
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $s->nomor_pendaftaran }}</td>
                    <td style="text-align: left; font-weight: bold;">{{ $s->nama_lengkap }}</td>
                    <td style="text-align: left;">{{ $s->jurusan?->nama_jurusan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $s->gelombang?->nama_gelombang ?? '-' }}</td>

                    <!-- Seleksi -->
                    <td style="text-align: right;">{{ number_format($r['tagihan_sel'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($r['bayar_sel'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $r['status_sel'] }}</td>

                    <!-- Pendidikan -->
                    <td style="text-align: right;">{{ number_format($r['netto_edu'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($r['bayar_edu'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($r['sisa_edu'], 0, ',', '.') }}</td>

                    <!-- Seragam -->
                    <td style="text-align: right;">{{ number_format($r['netto_srg'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($r['bayar_srg'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($r['sisa_srg'], 0, ',', '.') }}</td>

                    <!-- Total -->
                    <td style="text-align: right; font-weight: bold;">{{ number_format($r['total_kewajiban'], 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: #047857;">{{ number_format($r['total_terbayar'], 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: #b91c1c;">{{ number_format($r['sisa_piutang'], 0, ',', '.') }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($r['status_kelunasan'] === 'LUNAS')
                            <span style="color: #047857;">LUNAS</span>
                        @elseif($r['status_kelunasan'] === 'CICILAN')
                            <span style="color: #b45309;">CICILAN</span>
                        @elseif($r['status_kelunasan'] === 'SELEKSI_LUNAS')
                            <span style="color: #2563eb;">SELEKSI OK</span>
                        @else
                            <span style="color: #64748b;">BELUM BAYAR</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" style="text-align: center; padding: 12px; color: #64748b;">
                        Tidak ada data catatan keuangan yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" style="text-align: center;">TOTAL KESELURUHAN</td>
                <td style="text-align: right;">{{ number_format($totSel, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totSelBayar, 0, ',', '.') }}</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: right;">{{ number_format($totEdu, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totEduBayar, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totEduSisa, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totSrg, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totSrgBayar, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totSrgSisa, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($totKewajiban, 0, ',', '.') }}</td>
                <td style="text-align: right; color: #047857;">{{ number_format($totTerbayar, 0, ',', '.') }}</td>
                <td style="text-align: right; color: #b91c1c;">{{ number_format($totPiutang, 0, ',', '.') }}</td>
                <td style="text-align: center;">-</td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-box" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none; font-size: 8pt;">
                Mengetahui,<br>
                <strong>Kepala SMK Wikrama 1 Garut</strong>
                <div class="signature-space"></div>
                <u><strong>Dra. Hj. Yayuk Suryani, M.Pd.</strong></u><br>
                <span>NIP. -</span>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none; font-size: 8pt;">
                Garut, {{ now()->translatedFormat('d F Y') }}<br>
                <strong>Bendahara Panitia SPMB</strong>
                <div class="signature-space"></div>
                <u><strong>{{ auth()->user()->name ?? 'Bendahara Sekolah' }}</strong></u><br>
                <span>NIP/NUPTK. -</span>
            </td>
        </tr>
    </table>
@endsection
