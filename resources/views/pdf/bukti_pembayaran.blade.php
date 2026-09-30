@extends('pdf.layout', ['title' => 'Kwitansi Pembayaran - ' . ($calonSiswa->nomor_pendaftaran ?? 'SPMB')])

@section('content')
    <div class="doc-title">BUKTI RESMI PEMBAYARAN</div>
    <div class="doc-number">No. Referensi: <strong>{{ $pembayaran->nomor_referensi ?? ('TRX-' . str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT)) }}</strong></div>

    <table class="info-table">
        <tr>
            <td class="label">Telah Diterima Dari</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nomor Pendaftaran / NISN</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->nomor_pendaftaran }} / {{ $calonSiswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Kompetensi Keahlian</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }} ({{ $calonSiswa->program?->nama_program ?? 'Reguler' }})</td>
        </tr>
        <tr>
            <td class="label">Jenis Pembayaran</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $jenisPembayaran }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tanggal Pembayaran</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar ?? $pembayaran->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Metode / Bank Pengirim</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($pembayaran->metode_bayar) }} {{ $pembayaran->bank_pengirim ? '('.$pembayaran->bank_pengirim.')' : '' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pemilik Rekening</td>
            <td class="colon">:</td>
            <td class="value">{{ $pembayaran->nama_pengirim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Verifikasi</td>
            <td class="colon">:</td>
            <td class="value">
                <span class="badge badge-success">{{ $pembayaran->status }}</span>
                @if($pembayaran->verified_at)
                    <small style="color: #64748b;">(Diverifikasi pada {{ \Carbon\Carbon::parse($pembayaran->verified_at)->translatedFormat('d F Y H:i') }})</small>
                @endif
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 60%;">Uraian Pembayaran</th>
                <th style="width: 30%; text-align: right;">Jumlah Dibayar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td>{{ $jenisPembayaran }} - {{ $calonSiswa->nama_lengkap }} ({{ $calonSiswa->nomor_pendaftaran }})</td>
                <td class="right"><strong>Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="right">TOTAL DITERIMA:</td>
                <td class="right" style="font-size: 11pt; color: #047857;">Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="background-color: #f8fafc; border-left: 3px solid #0284c7; padding: 8px 12px; margin-top: 10px; font-size: 9pt;">
        <em>Kwitansi ini adalah bukti pembayaran sah yang dikeluarkan oleh Sistem SPMB Nampi SMK Wikrama 1 Garut. Simpan bukti ini dengan baik.</em>
    </div>

    <div class="signature-box">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    Penyetor / Calon Murid,
                    <div class="signature-space" style="height: 70px;"></div>
                    <strong>( {{ strtoupper($pembayaran->nama_pengirim ?? $calonSiswa->nama_lengkap) }} )</strong>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    Garut, {{ \Carbon\Carbon::parse($tte['signedAt'] ?? now())->translatedFormat('d F Y') }}<br>
                    {{ $tte['penandatanganJabatan'] ?? 'Bendahara Penerimaan Sekolah' }},
                    <div style="margin: 4px 0; text-align: center;">
                        @if(!empty($tte['qrCodeBase64']))
                            <img src="{{ $tte['qrCodeBase64'] }}" alt="QR Code TTE" style="width: 68px; height: 68px; display: inline-block;">
                            <div style="font-size: 5pt; color: #475569; margin-top: 1px; line-height: 1.2;">
                                Ditandatangani secara elektronik<br>
                                <em>Scan QR untuk verifikasi keaslian</em>
                            </div>
                        @else
                            <div class="signature-space" style="height: 70px;"></div>
                        @endif
                    </div>
                    <strong>( {{ $tte['penandatanganNama'] ?? ($pembayaran->verifikator?->name ?? 'Fitria Amalia, S.Pd.') }} )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
