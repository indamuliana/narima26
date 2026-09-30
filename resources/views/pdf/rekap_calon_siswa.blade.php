@extends('pdf.layout', ['title' => 'Rekapitulasi Calon Murid Baru - SPMB SMK Wikrama 1 Garut'])

@section('content')
    <div class="doc-title" style="font-size: 13pt;">REKAPITULASI PENDAFTARAN CALON MURID BARU</div>
    <div class="doc-number">Tahun Ajaran 2027/2028 &bull; Dicetak pada: {{ $printedAt->translatedFormat('d F Y H:i') }} WIB</div>

    @if (!empty($filters['jurusan_nama']) || !empty($filters['status_nama']) || !empty($filters['gelombang_nama']))
        <div style="font-size: 8.5pt; color: #475569; margin-bottom: 10px; background-color: #f8fafc; padding: 6px 10px; border-radius: 4px; border: 1px solid #e2e8f0;">
            <strong>Filter Laporan:</strong>
            @if (!empty($filters['jurusan_nama'])) Jurusan: <strong>{{ $filters['jurusan_nama'] }}</strong> &bull; @endif
            @if (!empty($filters['gelombang_nama'])) Gelombang: <strong>{{ $filters['gelombang_nama'] }}</strong> &bull; @endif
            @if (!empty($filters['status_nama'])) Status SPMB: <strong>{{ $filters['status_nama'] }}</strong> &bull; @endif
            Total Data: <strong>{{ count($calonSiswaList) }} calon siswa</strong>
        </div>
    @endif

    <table class="data-table" style="font-size: 8pt; width: 100%;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 14%; text-align: left;">No. Pendaftaran</th>
                <th style="width: 10%; text-align: center;">NISN</th>
                <th style="width: 22%; text-align: left;">Nama Lengkap</th>
                <th style="width: 5%; text-align: center;">L/P</th>
                <th style="width: 14%; text-align: left;">Pilihan Jurusan</th>
                <th style="width: 13%; text-align: left;">Gelombang</th>
                <th style="width: 18%; text-align: center;">Status Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($calonSiswaList as $index => $cs)
                @php
                    $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                    $statusLabel = str_replace('_', ' ', $statusStr);
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #ea580c;">{{ $cs->nomor_pendaftaran }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $cs->nisn }}</td>
                    <td><strong>{{ strtoupper($cs->nama_lengkap) }}</strong></td>
                    <td style="text-align: center;">{{ $cs->jenis_kelamin === 'PEREMPUAN' ? 'P' : 'L' }}</td>
                    <td>{{ $cs->jurusan_pilihan_text }}</td>
                    <td>{{ $cs->gelombang?->nama_gelombang ?? '-' }}</td>
                    <td style="text-align: center;">
                        <span style="font-size: 7.5pt; font-weight: bold; text-transform: uppercase;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">
                        Tidak ada data calon murid baru sesuai kriteria pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-box" style="margin-top: 25px;">
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
