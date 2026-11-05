@extends('pdf.layout', ['title' => 'Laporan Keuangan & Pembukuan SPMB - SMK Wikrama 1 Garut'])

@section('custom_styles')
    @page {
        margin: 0.8cm 1.0cm 0.8cm 1.0cm;
    }
    .doc-title {
        font-size: 12pt;
        margin-top: 4px;
        margin-bottom: 2px;
    }
    .doc-number {
        font-size: 8pt;
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
    @php
        $titles = [
            'piutang_siswa' => 'REKAPITULASI PIUTANG & STATUS CALON MURID',
            'buku_kas' => 'JURNAL BUKU KAS PENERIMAAN DANA MASUK',
            'matrix_jurusan' => 'REKAPITULASI CAPAIAN PER KOMPETENSI KEAHLIAN & POS BIAYA',
            'diskon' => 'REKAPITULASI PEMBERIAN DISKON & BEASISWA SPMB',
        ];
    @endphp

    <div class="doc-title">{{ $titles[$activeTab] ?? 'LAPORAN KEUANGAN SPMB' }}</div>
    <div class="doc-number">Tahun Ajaran 2027/2028 &bull; Dicetak pada: {{ $tanggalCetak }} WIB</div>

    <div style="font-size: 7.5pt; color: #334155; margin-bottom: 8px; background-color: #f8fafc; padding: 5px 8px; border-radius: 4px; border: 1px solid #cbd5e1;">
        <strong>Parameter Laporan:</strong>
        Gelombang: <strong>{{ $filterInfo['gelombang'] }}</strong> &bull;
        Jurusan: <strong>{{ $filterInfo['jurusan'] }}</strong> &bull;
        Periode: <strong>{{ $filterInfo['periode'] }}</strong> &bull;
        Status: <strong>{{ $filterInfo['status'] }}</strong>
        @if ($activeTab === 'piutang_siswa')
            &bull; Siswa: <strong>{{ $summary['total_siswa'] }}</strong> &bull; Total Kewajiban: <strong>Rp {{ number_format($summary['total_kewajiban'], 0, ',', '.') }}</strong> &bull; Kas Masuk: <strong style="color: #059669;">Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }} ({{ $summary['tingkat_pelunasan'] }}%)</strong> &bull; Sisa Piutang: <strong style="color: #dc2626;">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</strong>
        @elseif ($activeTab === 'buku_kas')
            &bull; Transaksi: <strong>{{ $summary['total_transaksi'] }}</strong> &bull; Total Kas Masuk: <strong style="color: #059669;">Rp {{ number_format($summary['total_kas_masuk'], 0, ',', '.') }}</strong> (Seleksi: Rp {{ number_format($summary['kas_seleksi'], 0, ',', '.') }} &bull; DU: Rp {{ number_format($summary['kas_daftar_ulang'], 0, ',', '.') }})
        @elseif ($activeTab === 'matrix_jurusan')
            &bull; Target Sekolah: <strong>Rp {{ number_format($summary['total_target'], 0, ',', '.') }}</strong> &bull; Realisasi: <strong style="color: #059669;">Rp {{ number_format($summary['total_kas_masuk'], 0, ',', '.') }} ({{ $summary['rata_capaian'] }}%)</strong> &bull; Piutang: <strong style="color: #dc2626;">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</strong>
        @elseif ($activeTab === 'diskon')
            &bull; Penerima: <strong>{{ $summary['total_penerima'] }} calon murid</strong> &bull; Total Subsidi Diskon: <strong style="color: #059669;">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</strong>
        @endif
    </div>

    <!-- TAB 1: REKAP PIUTANG SISWA -->
    @if ($activeTab === 'piutang_siswa')
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
                    <th style="width: 5.5%;">Tagihan</th>
                    <th style="width: 5.5%;">Terbayar</th>
                    <th style="width: 6%;">Status</th>
                    <th style="width: 7%;">Tagihan</th>
                    <th style="width: 7%;">Terbayar</th>
                    <th style="width: 7%;">Sisa</th>
                    <th style="width: 6.5%;">Tagihan</th>
                    <th style="width: 6.5%;">Terbayar</th>
                    <th style="width: 7%;">Sisa</th>
                    <th style="width: 7.5%;">Kewajiban</th>
                    <th style="width: 7.5%;">Terbayar</th>
                    <th style="width: 8%;">Sisa Piutang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totSel = 0; $totSelBayar = 0; $totEdu = 0; $totEduBayar = 0; $totEduSisa = 0;
                    $totSrg = 0; $totSrgBayar = 0; $totSrgSisa = 0; $totKewajiban = 0; $totTerbayar = 0; $totPiutang = 0;
                @endphp
                @forelse ($rows as $index => $r)
                    @php
                        $s = $r['calon_siswa'];
                        $totSel += $r['tagihan_sel']; $totSelBayar += $r['bayar_sel'];
                        $totEdu += $r['netto_edu']; $totEduBayar += $r['bayar_edu']; $totEduSisa += $r['sisa_edu'];
                        $totSrg += $r['netto_srg']; $totSrgBayar += $r['bayar_srg']; $totSrgSisa += $r['sisa_srg'];
                        $totKewajiban += $r['total_kewajiban']; $totTerbayar += $r['total_terbayar']; $totPiutang += $r['sisa_piutang'];
                    @endphp
                    <tr style="{{ $index % 2 == 1 ? 'background-color: #f8fafc;' : '' }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $s->nomor_pendaftaran }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ $s->nama_lengkap }}</td>
                        <td style="text-align: left;">{{ $s->jurusan?->nama_jurusan ?? '-' }}</td>
                        <td style="text-align: center;">{{ $s->gelombang?->nama_gelombang ?? '-' }}</td>
                        <td style="text-align: right;">{{ number_format($r['tagihan_sel'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($r['bayar_sel'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $r['status_sel'] }}</td>
                        <td style="text-align: right;">{{ number_format($r['netto_edu'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($r['bayar_edu'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($r['sisa_edu'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($r['netto_srg'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($r['bayar_srg'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($r['sisa_srg'], 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($r['total_kewajiban'], 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold; color: #047857;">{{ number_format($r['total_terbayar'], 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold; color: #b91c1c;">{{ number_format($r['sisa_piutang'], 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $r['status_kelunasan'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="18" style="text-align: center; padding: 10px; color: #64748b;">Tidak ada data.</td></tr>
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

    <!-- TAB 2: JURNAL BUKU KAS MASUK -->
    @elseif ($activeTab === 'buku_kas')
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 7.5%;">Tgl Bayar</th>
                    <th style="width: 9.5%;">Waktu Verif</th>
                    <th style="width: 11%;">No. Ref / Kwitansi</th>
                    <th style="width: 7%;">No. Reg</th>
                    <th style="width: 15%; text-align: left;">Nama Calon Murid</th>
                    <th style="width: 10%; text-align: left;">Jurusan</th>
                    <th style="width: 12%;">Pos Penerimaan</th>
                    <th style="width: 8%;">Metode</th>
                    <th style="width: 9%;">Nominal Masuk</th>
                    <th style="width: 8%;">Verifikator</th>
                </tr>
            </thead>
            <tbody>
                @php $totKas = 0; @endphp
                @forelse ($rows as $index => $t)
                    @php
                        $s = $t['calon_siswa'];
                        $totKas += $t['nominal'];
                    @endphp
                    <tr style="{{ $index % 2 == 1 ? 'background-color: #f8fafc;' : '' }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ $t['tanggal_bayar'] ? \Carbon\Carbon::parse($t['tanggal_bayar'])->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: center;">{{ $t['waktu'] ? \Carbon\Carbon::parse($t['waktu'])->format('d/m/Y H:i') : '-' }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $t['nomor_transaksi'] }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $s?->nomor_pendaftaran ?? '-' }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ $s?->nama_lengkap ?? '-' }}</td>
                        <td style="text-align: left;">{{ $s?->jurusan?->nama_jurusan ?? '-' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $t['pos'] }}</td>
                        <td style="text-align: center;">{{ $t['metode_bayar'] }}</td>
                        <td style="text-align: right; font-weight: bold; color: #047857;">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $t['verifikator'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" style="text-align: center; padding: 10px; color: #64748b;">Belum ada transaksi dana masuk.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="9" style="text-align: center;">TOTAL KAS MASUK</td>
                    <td style="text-align: right; color: #047857;">Rp {{ number_format($totKas, 0, ',', '.') }}</td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>

    <!-- TAB 3: REKAP PER JURUSAN & POS BIAYA -->
    @elseif ($activeTab === 'matrix_jurusan')
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">No</th>
                    <th rowspan="2" style="width: 17%; text-align: left;">Kompetensi Keahlian</th>
                    <th rowspan="2" style="width: 5%;">Pendaftar</th>
                    <th colspan="2" class="bg-sub" style="width: 14%;">Seleksi</th>
                    <th colspan="2" class="bg-sub" style="width: 16%;">Pendidikan (DSP/SPP)</th>
                    <th colspan="2" class="bg-sub" style="width: 15%;">Seragam</th>
                    <th rowspan="2" style="width: 7%;">Diskon</th>
                    <th rowspan="2" style="width: 8%;">Target Netto</th>
                    <th rowspan="2" style="width: 8%;">Kas Masuk</th>
                    <th rowspan="2" style="width: 8%;">Sisa Piutang</th>
                    <th rowspan="2" style="width: 6%;">% Capaian</th>
                </tr>
                <tr>
                    <th>Target</th><th>Kas</th><th>Target</th><th>Kas</th><th>Target</th><th>Kas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($matrixRows as $index => $row)
                    <tr style="{{ $index % 2 == 1 ? 'background-color: #f8fafc;' : '' }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ $row['jurusan']->nama_jurusan }} ({{ $row['jurusan']->kode }})</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row['pendaftar_count'] }}</td>
                        <td style="text-align: right;">{{ number_format($row['target_seleksi'], 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #047857;">{{ number_format($row['kas_seleksi'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($row['target_edu'], 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #047857;">{{ number_format($row['kas_edu'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($row['target_srg'], 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #047857;">{{ number_format($row['kas_srg'], 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #6b21a8;">{{ number_format($row['diskon'], 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($row['total_target'], 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold; color: #047857;">{{ number_format($row['total_kas'], 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold; color: #b91c1c;">{{ number_format($row['sisa_piutang'], 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row['persentase'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="2" style="text-align: center;">TOTAL KESELURUHAN SEKOLAH</td>
                    <td style="text-align: center;">{{ $matrixRows->sum('pendaftar_count') }}</td>
                    <td style="text-align: right;">{{ number_format($matrixRows->sum('target_seleksi'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #047857;">{{ number_format($matrixRows->sum('kas_seleksi'), 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($matrixRows->sum('target_edu'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #047857;">{{ number_format($matrixRows->sum('kas_edu'), 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($matrixRows->sum('target_srg'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #047857;">{{ number_format($matrixRows->sum('kas_srg'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #6b21a8;">{{ number_format($matrixRows->sum('diskon'), 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($matrixRows->sum('total_target'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #047857;">{{ number_format($matrixRows->sum('total_kas'), 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #b91c1c;">{{ number_format($matrixRows->sum('sisa_piutang'), 0, ',', '.') }}</td>
                    <td style="text-align: center; color: #047857; font-black;">{{ $summary['rata_capaian'] }}%</td>
                </tr>
            </tfoot>
        </table>

    <!-- TAB 4: REKAP DISKON & BEASISWA -->
    @elseif ($activeTab === 'diskon')
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Tanggal SK</th>
                    <th style="width: 8%;">No. Reg</th>
                    <th style="width: 17%; text-align: left;">Nama Calon Murid</th>
                    <th style="width: 12%; text-align: left;">Jurusan</th>
                    <th style="width: 8%;">Gelombang</th>
                    <th style="width: 14%;">Jenis Beasiswa / Diskon</th>
                    <th style="width: 6%;">Skema</th>
                    <th style="width: 9%;">Nominal Diskon</th>
                    <th style="width: 15%; text-align: left;">Keterangan / Alasan</th>
                </tr>
            </thead>
            <tbody>
                @php $totDiskon = 0; @endphp
                @forelse ($rows as $index => $d)
                    @php
                        $s = $d->calonSiswa;
                        $totDiskon += (float) $d->nominal_potongan;
                    @endphp
                    <tr style="{{ $index % 2 == 1 ? 'background-color: #f8fafc;' : '' }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ $d->diberikan_at ? \Carbon\Carbon::parse($d->diberikan_at)->format('d/m/Y') : ($d->created_at ? \Carbon\Carbon::parse($d->created_at)->format('d/m/Y') : '-') }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $s?->nomor_pendaftaran ?? '-' }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ $s?->nama_lengkap ?? '-' }}</td>
                        <td style="text-align: left;">{{ $s?->jurusan?->nama_jurusan ?? '-' }}</td>
                        <td style="text-align: center;">{{ $s?->gelombang?->nama_gelombang ?? '-' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $d->jenis_diskon ?? 'Diskon Tagihan' }}</td>
                        <td style="text-align: center; text-transform: uppercase;">{{ $d->metode_diskon ?? 'Nominal' }}</td>
                        <td style="text-align: right; font-weight: bold; color: #047857;">Rp {{ number_format($d->nominal_potongan, 0, ',', '.') }}</td>
                        <td style="text-align: left;">{{ $d->alasan ?? ($d->keterangan ?? '-') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" style="text-align: center; padding: 10px; color: #64748b;">Belum ada catatan beasiswa atau diskon.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="8" style="text-align: center;">TOTAL ALOKASI DISKON & BEASISWA</td>
                    <td style="text-align: right; color: #047857;">Rp {{ number_format($totDiskon, 0, ',', '.') }}</td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>
    @endif

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
