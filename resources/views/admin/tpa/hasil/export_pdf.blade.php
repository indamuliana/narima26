@extends('pdf.layout', ['title' => 'Berita Acara Hasil Tes Potensi Akademik (TPA) - SPMB SMK Wikrama 1 Garut'])

@section('custom_styles')
    @page {
        margin: 0.8cm 1.0cm 0.8cm 1.0cm;
    }
    .doc-title {
        font-size: 12pt;
        font-weight: bold;
        text-align: center;
        margin-top: 4px;
        margin-bottom: 2px;
        color: #1e293b;
    }
    .doc-number {
        font-size: 8pt;
        text-align: center;
        margin-bottom: 10px;
        color: #64748b;
    }
    .data-table {
        margin-top: 6px;
        margin-bottom: 12px;
        font-size: 7pt;
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
        padding: 5px 6px;
        border: 1px solid #cbd5e1;
    }
    .data-table th {
        background-color: #f1f5f9;
        color: #0f172a;
        font-weight: bold;
    }
    .signature-box {
        margin-top: 20px;
        page-break-inside: avoid;
    }
    .signature-space {
        height: 45px;
    }
@endsection

@section('content')
    <div class="doc-title">BERITA ACARA REKAPITULASI HASIL TES POTENSI AKADEMIK (TPA)</div>
    <div class="doc-number">Tahun Ajaran 2027/2028 &bull; Dicetak pada: {{ $printedAt->translatedFormat('d F Y H:i') }} WIB</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 14%; text-align: left;">No. Reg & NISN</th>
                <th style="width: 22%; text-align: left;">Nama Calon Peserta Didik</th>
                <th style="width: 14%; text-align: left;">Kompetensi Keahlian</th>
                <th style="width: 10%; text-align: center;">Status Ujian</th>
                <th style="width: 11%; text-align: center;">Skor Literasi</th>
                <th style="width: 11%; text-align: center;">Skor Numerasi</th>
                <th style="width: 14%; text-align: center;">Total Skor (Poin)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($candidates as $index => $cs)
                @php
                    $sesi = $cs->tpaSesi;
                @endphp
                <tr style="{{ $index % 2 === 1 ? 'background-color: #f8fafc;' : '' }}">
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">
                        <strong style="color: #ea580c;">{{ $cs->nomor_pendaftaran }}</strong><br>
                        <span style="color: #64748b;">{{ $cs->nisn ?? '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ strtoupper($cs->nama_lengkap) }}</strong>
                    </td>
                    <td>
                        {{ $cs->jurusan?->nama_jurusan ?? '-' }}
                    </td>
                    <td style="text-align: center;">
                        @if (! $sesi || $sesi->status === 'belum_mulai')
                            <span style="color: #64748b;">Belum Mulai</span>
                        @elseif ($sesi->status === 'selesai')
                            <strong style="color: #15803d;">TUNTAS</strong>
                        @elseif ($sesi->status === 'didiskualifikasi')
                            <strong style="color: #b91c1c;">DISKUALIFIKASI</strong>
                        @else
                            <span style="color: #d97706;">PROSES</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if ($sesi && in_array($sesi->status, ['transisi', 'babak_2', 'selesai', 'didiskualifikasi']))
                            <strong>{{ number_format($sesi->skor_literasi, 0) }}</strong><br>
                            <span style="font-size: 6pt; color: #16a34a;">({{ $sesi->benar_literasi }} B / {{ $sesi->salah_literasi }} S)</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if ($sesi && in_array($sesi->status, ['selesai', 'didiskualifikasi']))
                            <strong>{{ number_format($sesi->skor_numerasi, 0) }}</strong><br>
                            <span style="font-size: 6pt; color: #2563eb;">({{ $sesi->benar_numerasi }} B / {{ $sesi->salah_numerasi }} S)</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center; font-size: 8pt;">
                        @if ($sesi && in_array($sesi->status, ['selesai', 'didiskualifikasi']))
                            <strong style="color: #ea580c;">{{ number_format($sesi->total_skor, 0) }}</strong>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Tidak ada data calon siswa untuk ditampilkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 15px; font-size: 8pt; border: none;" class="signature-box">
        <tr>
            <td style="width: 60%; border: none;"></td>
            <td style="width: 40%; text-align: center; border: none;">
                Garut, {{ $printedAt->translatedFormat('d F Y') }}<br>
                <strong>Panitia Pelaksana TPA SPMB</strong>
                <div class="signature-space"></div>
                <strong><u>Panitia SPMB Wikrama 1 Garut</u></strong>
            </td>
        </tr>
    </table>
@endsection
