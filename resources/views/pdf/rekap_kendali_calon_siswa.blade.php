@extends('pdf.layout', ['title' => 'Rekapitulasi Kendali Calon Murid - SPMB SMK Wikrama 1 Garut'])

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
        padding: 4px 5px;
    }
    .signature-box {
        margin-top: 15px;
        page-break-inside: avoid;
    }
    .signature-space {
        height: 40px;
    }
    .footer-note {
        margin-top: 10px;
        padding-top: 4px;
    }
@endsection

@section('content')
    <div class="doc-title">REKAPITULASI KENDALI CALON MURID BARU</div>
    <div class="doc-number">Tahun Ajaran 2027/2028 &bull; Dicetak pada: {{ $printedAt->translatedFormat('d F Y H:i') }} WIB</div>

    @if (!empty($filters['jurusan_nama']) || !empty($filters['status_nama']) || !empty($filters['gelombang_nama']) || !empty($filters['program_nama']) || !empty($filters['gender_nama']))
        <div style="font-size: 7.5pt; color: #475569; margin-bottom: 8px; background-color: #f8fafc; padding: 5px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
            <strong>Filter Laporan:</strong>
            @if (!empty($filters['program_nama'])) Program: <strong>{{ $filters['program_nama'] }}</strong> &bull; @endif
            @if (!empty($filters['jurusan_nama'])) Jurusan: <strong>{{ $filters['jurusan_nama'] }}</strong> &bull; @endif
            @if (!empty($filters['gelombang_nama'])) Gelombang: <strong>{{ $filters['gelombang_nama'] }}</strong> &bull; @endif
            @if (!empty($filters['status_nama'])) Status SPMB: <strong>{{ $filters['status_nama'] }}</strong> &bull; @endif
            @if (!empty($filters['gender_nama'])) Gender: <strong>{{ $filters['gender_nama'] }}</strong> &bull; @endif
            Total Data: <strong>{{ count($calonSiswaList) }} calon murid</strong>
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr style="background-color: #f1f5f9; color: #1e293b;">
                <th style="width: 3%; text-align: center;">No</th>
                <th style="width: 10%; text-align: left;">No. Reg & NISN</th>
                <th style="width: 15%; text-align: left;">Nama Calon Murid</th>
                <th style="width: 3%; text-align: center;">L/P</th>
                <th style="width: 8%; text-align: center;">Prog & Jurusan</th>
                <th style="width: 9%; text-align: center;">Gelombang</th>
                <th style="width: 9%; text-align: center;">Status SPMB</th>
                <th style="width: 11%; text-align: left;">Rekomendasi</th>
                <th style="width: 16%; text-align: left; word-wrap: break-word;">Catatan Khusus CS</th>
                <th style="width: 16%; text-align: left; word-wrap: break-word;">Catatan Pewawancara</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($calonSiswaList as $index => $cs)
                @php
                    $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                @endphp
                <tr style="{{ $index % 2 === 1 ? 'background-color: #f8fafc;' : '' }}">
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">
                        <strong style="color: #ea580c;">{{ $cs->nomor_pendaftaran }}</strong><br>
                        <span style="color: #64748b;">{{ $cs->nisn }}</span>
                    </td>
                    <td>
                        <strong>{{ strtoupper($cs->nama_lengkap) }}</strong><br>
                        <span style="font-size: 6pt; color: #64748b;">{{ $cs->sekolah_asal_text }}</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $cs->jenis_kelamin === 'PEREMPUAN' || $cs->jenis_kelamin === 'P' ? 'P' : 'L' }}
                    </td>
                    <td style="text-align: center; font-size: 6.5pt;">
                        <span style="color: #0369a1; font-weight: bold;">{{ $cs->program?->nama ?? $cs->program?->nama_program ?? 'Reguler' }}</span> &bull; 
                        <strong style="color: #0f172a;">{{ $cs->jurusan?->kode ?? '-' }}</strong>
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        {{ $cs->gelombang?->nama_gelombang ?? '-' }}
                    </td>
                    <td style="text-align: center; font-size: 6.5pt; font-weight: bold;">
                        {{ str_replace('_', ' ', $statusStr) }}
                    </td>
                    <td style="font-size: 6.5pt;">
                        @if($cs->referensi_nama)
                            <strong>{{ $cs->referensi_nama }}</strong><br>
                            <span style="color: #64748b;">{{ str_replace('_', ' ', $cs->referensi_jenis ?? '') }}</span>
                            @if($cs->referensi_rayon) (Rayon: {{ $cs->referensi_rayon }}) @endif
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Mandiri</span>
                        @endif
                    </td>
                    <td style="font-size: 6.5pt; color: #334155; word-wrap: break-word; word-break: break-word; white-space: normal;">
                        @if($cs->catatan_admin)
                            <strong style="color: {{ $cs->is_catatan_admin_done ? '#166534' : '#b45309' }};">
                                {{ $cs->is_catatan_admin_done ? '✓ [SELESAI]' : '⏳ [PENDING]' }}
                            </strong><br>
                            {!! nl2br(e($cs->catatan_admin)) !!}
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="font-size: 6.5pt; color: #334155; word-wrap: break-word; word-break: break-word; white-space: normal;">
                        @if($cs->catatan_untuk_pewawancara)
                            <strong style="color: {{ $cs->is_catatan_pewawancara_done ? '#166534' : '#4338ca' }};">
                                {{ $cs->is_catatan_pewawancara_done ? '✓ [SELESAI]' : '⏳ [PENDING]' }}
                            </strong><br>
                            {!! nl2br(e($cs->catatan_untuk_pewawancara)) !!}
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94a3b8; padding: 15px;">
                        Tidak ada data calon murid baru sesuai kriteria pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    <strong>Kepala SMK Wikrama 1 Garut</strong>
                    <div class="signature-space"></div>
                    <strong>( Kunedi, S.Si., Gr. )</strong>
                </td>
                <td style="width: 50%;">
                    Garut, {{ $printedAt->translatedFormat('d F Y') }}<br>
                    <strong>Ketua Panitia SPMB 2027/2028</strong>
                    <div class="signature-space"></div>
                    <strong>( Panitia SPMB )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
