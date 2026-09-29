@extends('pdf.layout', ['title' => 'Tagihan Daftar Ulang - ' . $tagihan->nomor_tagihan])

@section('content')
    <div class="doc-title">
        {{ $tagihan->jenis_tagihan === 'SERAGAM' 
            ? 'RINCIAN TAGIHAN PAKET SERAGAM & ATRIBUT' 
            : ($tagihan->details->where('kategori_snapshot', 'ASRAMA')->isNotEmpty() ? 'RINCIAN TAGIHAN BIAYA PENDIDIKAN & ASRAMA' : 'RINCIAN TAGIHAN BIAYA PENDIDIKAN (DSP & SPP)') }}
    </div>
    <div class="doc-number">Nomor Tagihan: <strong>{{ $tagihan->nomor_tagihan }}</strong></div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Calon Siswa</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nomor Pendaftaran / NISN</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->nomor_pendaftaran }} / {{ $calonSiswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Program / Gelombang</td>
            <td class="colon">:</td>
            <td class="value">{{ $tagihan->program_snapshot }} / {{ $tagihan->gelombang_snapshot }}</td>
        </tr>
        <tr>
            <td class="label">Kompetensi Keahlian</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Tagihan</td>
            <td class="colon">:</td>
            <td class="value">
                <span class="badge {{ $tagihan->status === 'LUNAS' ? 'badge-success' : 'badge-warning' }}">
                    {{ $tagihan->status }}
                </span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 18%;">Kode Biaya</th>
                <th style="width: 44%;">Komponen Pembiayaan</th>
                <th style="width: 12%; text-align: center;">Kategori</th>
                <th style="width: 20%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihan->details as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><code>{{ $item->kode_biaya_snapshot }}</code></td>
                    <td>{{ $item->nama_biaya_snapshot }}</td>
                    <td class="center">{{ strtoupper($item->kategori_snapshot) }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="right">SUBTOTAL BRUTO:</td>
                <td class="right">Rp {{ number_format($tagihan->total_bruto, 0, ',', '.') }}</td>
            </tr>
            @if($tagihan->total_diskon > 0)
                <tr style="color: #dc2626; background-color: #fef2f2;">
                    <td colspan="4" class="right">
                        POTONGAN / DISKON 
                        @if($tagihan->diskon)
                            ({{ $tagihan->diskon->jenis_diskon }} - {{ $tagihan->diskon->metode_diskon === 'persentase' ? $tagihan->diskon->nilai_diskon.'%' : 'Nominal' }}):
                        @else
                            :
                        @endif
                    </td>
                    <td class="right">- Rp {{ number_format($tagihan->total_diskon, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="total-row" style="background-color: #f1f5f9; font-size: 10.5pt;">
                <td colspan="4" class="right"><strong>TOTAL NETTO WAJIB BAYAR:</strong></td>
                <td class="right" style="color: #0369a1;"><strong>Rp {{ number_format($tagihan->total_netto, 0, ',', '.') }}</strong></td>
            </tr>
            @if(isset($totalPaid) && $totalPaid > 0)
                <tr>
                    <td colspan="4" class="right">Total Telah Diverifikasi:</td>
                    <td class="right" style="color: #15803d;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row" style="background-color: #fffbeb;">
                    <td colspan="4" class="right"><strong>SISA PEMBAYARAN:</strong></td>
                    <td class="right" style="color: #b45309;"><strong>Rp {{ number_format($remainingBalance ?? ($tagihan->total_netto - $totalPaid), 0, ',', '.') }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 10px 12px; margin-top: 10px; font-size: 9pt;">
        <strong>Informasi Rekening Resmi Pembayaran:</strong>
        <p style="margin: 4px 0 0 0;">
            Bank BNI: <strong>082-0083-086</strong> a.n. <strong>SMK WIKRAMA 1 GARUT</strong><br>
            <em>Cantumkan berita transfer: "{{ $calonSiswa->nomor_pendaftaran }} - Daftar Ulang". Setelah transfer, unggah bukti bayar melalui portal SPMB Nampi.</em>
        </p>
    </div>

    <div class="signature-box">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    Orang Tua / Wali Calon Siswa,
                    <div class="signature-space" style="height: 70px;"></div>
                    <strong>( .................................................. )</strong>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    Garut, {{ \Carbon\Carbon::parse($tte['signedAt'] ?? now())->translatedFormat('d F Y') }}<br>
                    {{ $tte['penandatanganJabatan'] ?? 'Bendahara Penerimaan SPMB' }},
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
                    <strong>( {{ $tte['penandatanganNama'] ?? 'Fitria Amalia, S.Pd.' }} )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
