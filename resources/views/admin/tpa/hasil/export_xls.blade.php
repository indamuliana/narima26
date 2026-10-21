<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Rekap Nilai TPA</x:Name>
                    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        th { background-color: #ea580c; color: #ffffff; font-weight: bold; border: 1px solid #c2410c; padding: 8px 10px; text-align: left; }
        td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-number { mso-number-format: "\#\,\#\#0"; text-align: right; }
        .text-str { mso-number-format: "\@"; }
    </style>
</head>
<body>
    <h2>REKAPITULASI HASIL TES POTENSI AKADEMIK (TPA)</h2>
    <p>SPMB SMK WIKRAMA 1 GARUT — TANGGAL CETAK: {{ now()->translatedFormat('d F Y H:i') }}</p>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>No. Pendaftaran</th>
                <th>NISN</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Kompetensi Keahlian</th>
                <th>Gelombang</th>
                <th>Status Ujian</th>
                <th>Skor Literasi</th>
                <th>Benar Lit</th>
                <th>Salah Lit</th>
                <th>Kosong Lit</th>
                <th>Skor Numerasi</th>
                <th>Benar Num</th>
                <th>Salah Num</th>
                <th>Kosong Num</th>
                <th>Total Skor (Poin)</th>
                <th>Pelanggaran (x)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($candidates as $idx => $cs)
                @php
                    $sesi = $cs->tpaSesi;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-str">{{ $cs->nomor_pendaftaran }}</td>
                    <td class="text-str">{{ $cs->nisn ?? '-' }}</td>
                    <td>{{ $cs->nama_lengkap }}</td>
                    <td class="text-center">{{ $cs->jenis_kelamin ?? '-' }}</td>
                    <td>{{ $cs->jurusan?->nama_jurusan ?? '-' }}</td>
                    <td>{{ $cs->gelombang?->nama_gelombang ?? '-' }}</td>
                    <td class="text-center">{{ $sesi ? strtoupper($sesi->status) : 'BELUM MULAI' }}</td>
                    <td class="text-number">{{ $sesi ? $sesi->skor_literasi : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->benar_literasi : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->salah_literasi : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->kosong_literasi : 0 }}</td>
                    <td class="text-number">{{ $sesi ? $sesi->skor_numerasi : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->benar_numerasi : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->salah_numerasi : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->kosong_numerasi : 0 }}</td>
                    <td class="text-number font-bold">{{ $sesi ? $sesi->total_skor : 0 }}</td>
                    <td class="text-center">{{ $sesi ? $sesi->jumlah_pelanggaran : 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
