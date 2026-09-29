<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Dokumen Resmi SPMB - SMK Wikrama 1 Garut' }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #0A1128;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            width: 100%;
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
        }
        .kop-image {
            width: 100%;
            max-height: 125px;
            object-fit: contain;
        }
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
            margin-bottom: 3px;
            color: #0f172a;
        }
        .doc-number {
            text-align: center;
            font-size: 9.5pt;
            color: #64748b;
            margin-bottom: 16px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10pt;
        }
        .info-table td.label {
            width: 28%;
            font-weight: bold;
            color: #334155;
        }
        .info-table td.colon {
            width: 2%;
        }
        .info-table td.value {
            width: 70%;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 14px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            font-size: 9.5pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            text-align: left;
            font-weight: bold;
        }
        .data-table td.center {
            text-align: center;
        }
        .data-table td.right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8.5pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .signature-box {
            width: 100%;
            margin-top: 25px;
        }
        .signature-box table {
            width: 100%;
        }
        .signature-box td {
            text-align: center;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .signature-space {
            height: 65px;
        }
        .footer-note {
            margin-top: 20px;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    @hasSection('custom_footer')
        @yield('custom_footer')
    @endif

    @unless($hideKop ?? false)
    <div class="header-kop">
        @if (!empty($kopSuratBase64))
            <img src="{{ $kopSuratBase64 }}" class="kop-image" alt="Kop Surat SMK Wikrama 1 Garut">
        @else
            <h2 style="margin: 0; font-size: 16pt;">SMK WIKRAMA 1 GARUT</h2>
            <p style="margin: 2px 0; font-size: 9pt;">Jl. Otto Iskandardinata, Kp. Tanjung Kidul, RT.003/RW.013, Pasawahan, Kec. Tarogong Kaler, Kabupaten Garut, Jawa Barat 44151</p>
        @endif
    </div>
    @endunless

    @yield('content')

    @unless(View::hasSection('custom_footer'))
        <div class="footer-note">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: left;">Sistem SPMB Nampi — SMK Wikrama 1 Garut</td>
                    <td style="text-align: right;">Dicetak otomatis pada: {{ now()->translatedFormat('d F Y H:i:s') }}</td>
                </tr>
            </table>
        </div>
    @endunless
</body>
</html>
