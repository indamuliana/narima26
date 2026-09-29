@extends('pdf.layout', ['title' => 'Naskah Persetujuan Kesepahaman - ' . $calonSiswa->nomor_pendaftaran, 'hideKop' => true])

@section('content')
    <style>
        .doc-title-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 14px;
            border: none;
        }
        .doc-title-main {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.35;
            color: #0f172a;
            margin: 0 auto;
            text-transform: uppercase;
        }
        .intro-p {
            font-size: 9.5pt;
            line-height: 1.4;
            margin-top: 8px;
            margin-bottom: 6px;
            color: #0f172a;
        }
        .identitas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .identitas-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.35;
        }
        .identitas-table td.label-col {
            width: 24%;
            color: #1e293b;
        }
        .identitas-table td.colon-col {
            width: 2%;
            text-align: center;
        }
        .identitas-table td.val-col {
            width: 74%;
            color: #0f172a;
        }
        .table-klausul {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 16px;
            page-break-inside: auto;
        }
        .table-klausul tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .table-klausul th {
            border: 1px solid #94a3b8;
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 6px 8px;
            text-align: center;
        }
        .table-klausul td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            font-size: 8pt;
            line-height: 1.35;
            vertical-align: top;
        }
        .table-klausul tr.group-header-row td {
            background-color: #f8fafc;
            border-top: 1.5px solid #64748b;
            border-bottom: 1px solid #94a3b8;
            font-weight: bold;
            color: #0f172a;
            font-size: 8.5pt;
            padding: 5px 8px;
        }
        .table-klausul td.col-no {
            width: 6%;
            text-align: center;
            font-weight: bold;
        }
        .table-klausul td.col-uraian {
            width: 79%;
            text-align: justify;
        }
        .table-klausul td.col-ceklis {
            width: 15%;
            text-align: center;
            font-weight: bold;
            color: #15803d;
            font-size: 8pt;
        }
        .signature-section {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .fixed-footer-paraf {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            height: 38px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
        }
    </style>

    <!-- Header Dokumen Sesuai Narasi Resmi (Tepat di Tengah Kertas A4) -->
    <table class="doc-title-container" align="center">
        <tr>
            <td align="center" style="text-align: center; border: none; padding: 0;">
                <div class="doc-title-main" align="center">NASKAH PERSETUJUAN<br>SISWA SMK WIKRAMA 1 GARUT DAN ORANG TUA<br>TENTANG<br>KETENTUAN UMUM SMK WIKRAMA 1 GARUT<br>PROGRAM {{ strtoupper($programNama ?? 'REGULER') }}</div>
            </td>
        </tr>
    </table>

    <!-- Biodata Pihak Bertandatangan -->
    <p class="intro-p">Kami yang bertanda tangan di bawah ini :</p>
    @php
        $ortu = $calonSiswa->dataOrangtua ?: $calonSiswa->orangTua;
        $alamatSiswa = $calonSiswa->alamat_lengkap ?: '-';
        $alamatAyah = $ortu?->alamat_ayah ?: $alamatSiswa;
        $alamatIbu = $ortu?->alamat_ibu ?: $alamatSiswa;
        $namaWali = $ortu?->nama_wali;
        $alamatWali = $namaWali ? ($ortu?->alamat_wali ?: $alamatSiswa) : '';
    @endphp

    <table class="identitas-table">
        <tr>
            <td class="label-col">Nomor Seleksi</td>
            <td class="colon-col">:</td>
            <td class="val-col"><strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Nama Siswa</td>
            <td class="colon-col">:</td>
            <td class="val-col"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Nama Orang Ayah</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $ortu?->nama_ayah ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $alamatAyah }}</td>
        </tr>
        <tr>
            <td class="label-col">Nama Orang Ibu</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $ortu?->nama_ibu ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $alamatIbu }}</td>
        </tr>
        <tr>
            <td class="label-col">Nama Wali</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $namaWali ?: '' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $alamatWali }}</td>
        </tr>
    </table>

    <p class="intro-p">
        Bersedia mengikuti ketentuan SMK Wikrama 1 Garut tahun pelajaran {{ $tahunPelajaran ?? '2027/2028' }} sebagai berikut :
    </p>

    <!-- Tabel Klausul (Kelompok, Poin, dan Checklist) -->
    <table class="table-klausul">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 79%; text-align: left;">Uraian Ketentuan & Kesepahaman</th>
                <th style="width: 15%;">Checklis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelompokList as $kelompok)
                <tr class="group-header-row">
                    <td colspan="3">{{ $kelompok['judul'] ?? $kelompok['nama_kelompok'] ?? '' }}</td>
                </tr>
                @foreach($kelompok['poin'] ?? [] as $poin)
                    <tr>
                        <td class="col-no">{{ $poin['nomor'] ?? $loop->iteration }}</td>
                        <td class="col-uraian">{!! nl2br(e($poin['uraian'])) !!}</td>
                        <td class="col-ceklis">
                            <span style="font-family: DejaVu Sans, sans-serif;">[ &#10003; ]</span>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan Dekat (1 Materai) dan Tanda Tangan Kepala Sekolah -->
<div class="signature-section">
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <!-- Tanda Tangan Orang Tua / Wali -->
            <td style="width: 42%; text-align: right; vertical-align: top; font-size: 9pt; padding-right: 5px;">
                <br>
                Orang Tua / Wali Siswa,
                <div style="height: 130px;"></div>

                <div style="border-bottom: 1px dotted #334155; width: 85%; margin-left: auto;"></div>

                <span style="display: block; font-size: 7.5pt; color: #64748b; font-style: italic; text-align: right;">
                    (Nama jelas)
                </span>
            </td>

            <!-- Kotak Materai -->
            <td style="width: 16%; text-align: center; vertical-align: middle;">
                <div style="
                    border: 1px dashed #64748b;
                    width: 75px;
                    height: 50px;
                    margin: 0 auto;
                    text-align: center;
                    padding-top: 12px;
                    font-size: 6.5pt;
                    color: #64748b;
                    line-height: 1.2;
                ">
                    MATERAI<br>
                    Rp 10.000
                </div>
            </td>

            <!-- Tanda Tangan Calon Siswa -->
            <td style="width: 42%; text-align: left; vertical-align: top; font-size: 9pt; padding-left: 5px;">
                Garut, {{ now()->translatedFormat('d F Y') }}<br>
                Calon Peserta Didik,
                <div style="height: 130px;"></div>

                <strong>( {{ strtoupper($calonSiswa->nama_lengkap) }} )</strong>
            </td>
        </tr>
    </table>


        <!-- Tanda Tangan Kepala Sekolah (TTE) -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 100%; text-align: center; vertical-align: top; font-size: 9pt;">
                    Mengetahui,<br>
                    {{ $tte['penandatanganJabatan'] ?? 'Kepala SMK Wikrama 1 Garut' }}
                    <div style="margin: 4px 0; text-align: center;">
                        @if(!empty($tte['qrCodeBase64']))
                            <img src="{{ $tte['qrCodeBase64'] }}" alt="QR Code TTE" style="width: 68px; height: 68px; display: inline-block;">
                            <div style="font-size: 6.5pt; color: #475569; margin-top: 1px; line-height: 1.2;">
                                Ditandatangani secara elektronik<br>
                                <em>Scan QR untuk verifikasi keaslian</em>
                            </div>
                        @else
                            <div style="height: 60px;"></div>
                        @endif
                    </div>
                    <strong>{{ $tte['penandatanganNama'] ?? 'Kunedi, S.Si., Gr.' }}</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection

<!-- Custom Footer: 3 Kotak Paraf Kecil di Setiap Lembar -->
@section('custom_footer')
    <div class="fixed-footer-paraf">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="font-size: 7pt; color: #64748b; vertical-align: middle; text-align: left; border: none;">
                    SPMB Wikrama 1 Garut TP {{ $tahunPelajaran ?? '2027/2028' }} &bull; {{ $calonSiswa->nomor_pendaftaran }} &bull; {{ $calonSiswa->nama_lengkap }} ({{ strtoupper($programNama ?? 'REGULER') }})
                </td>
                <td style="text-align: right; vertical-align: middle; width: 220px; border: none;" align="right">
                    <table style="border-collapse: collapse; margin-left: auto; margin-right: 0;" align="right">
                        <tr>
                            <td style="font-size: 6.5pt; color: #475569; padding-right: 4px; font-weight: bold; vertical-align: middle; border: none;">Paraf:</td>
                            <td style="border: 1px solid #475569; width: 34px; height: 24px; text-align: center; vertical-align: bottom; font-size: 5.5pt; color: #64748b; padding-bottom: 2px;">Siswa</td>
                            <td style="width: 4px; border: none;"></td>
                            <td style="border: 1px solid #475569; width: 34px; height: 24px; text-align: center; vertical-align: bottom; font-size: 5.5pt; color: #64748b; padding-bottom: 2px;">Ortu</td>
                            <td style="width: 4px; border: none;"></td>
                            <td style="border: 1px solid #475569; width: 34px; height: 24px; text-align: center; vertical-align: bottom; font-size: 5.5pt; color: #64748b; padding-bottom: 2px;">Sekolah</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection
