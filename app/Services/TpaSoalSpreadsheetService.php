<?php

namespace App\Services;

use App\Models\TpaSoal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TpaSoalSpreadsheetService
{
    public const HEADERS = [
        'Nomor Urut',
        'Pertanyaan',
        'Opsi A',
        'Opsi B',
        'Opsi C',
        'Opsi D',
        'Opsi E',
        'Kunci Jawaban',
        'Bobot',
    ];

    /**
     * Download template panduan contoh soal (format default: CSV).
     */
    public function downloadTemplate(string $babak = 'literasi', string $format = 'csv'): StreamedResponse
    {
        $babak = in_array(strtolower($babak), ['literasi', 'numerasi']) ? strtolower($babak) : 'literasi';

        if ($babak === 'numerasi') {
            $sampleRows = [
                [
                    1,
                    'Sebuah toko memberikan diskon 20% untuk semua barang. Jika harga sebuah tas sebelum diskon adalah Rp 150.000, berapakah harga tas setelah diskon?',
                    'Rp 110.000',
                    'Rp 120.000',
                    'Rp 125.000',
                    'Rp 130.000',
                    'Rp 135.000',
                    'B',
                    8,
                ],
                [
                    2,
                    'Deret bilangan: 2, 4, 8, 16, 32, ... Bilangan selanjutnya adalah?',
                    '48',
                    '60',
                    '64',
                    '72',
                    '128',
                    'C',
                    8,
                ],
            ];
        } else {
            $sampleRows = [
                [
                    1,
                    "Bacalah teks berikut!\nFenomena Urban Heat Island (Pulau Bahang Perkotaan) menyebabkan suhu di wilayah perkotaan lebih tinggi 2°C hingga 5°C dibandingkan wilayah pedesaan di sekitarnya. Hal ini dipicu oleh dominasi material konstruksi seperti aspal dan beton yang menyerap serta menahan panas matahari lebih lama.\n\nBerdasarkan teks tersebut, alasan utama berkurangnya proses pendinginan alami di wilayah perkotaan adalah ....",
                    'Penggunaan pendingin ruangan (AC) yang membuang udara panas ke luar gedung',
                    'Kenaikan suhu udara yang mencapai 2°C hingga 5°C setiap tahunnya',
                    'Dominasi material aspal dan beton yang memantulkan cahaya matahari',
                    'Minimnya ruang terbuka hijau yang menghambat proses evapotranspirasi tanaman',
                    'Kepadatan penduduk yang meningkatkan aktivitas transportasi di jalan raya',
                    'D',
                    8,
                ],
                [
                    2,
                    "Perhatikan pernyataan berikut!\nSemua siswa yang lolos seleksi klub robotika menguasai dasar pemrograman logika. Sebagian siswa yang menguasai dasar pemrograman logika juga mahir merakit rangkaian elektronik. Raka adalah siswa yang lolos seleksi klub robotika.\n\nSimpulan yang paling tepat berdasarkan pernyataan di atas adalah ....",
                    'Raka mahir merakit rangkaian elektronik dan menguasai pemrograman logika',
                    'Raka menguasai dasar pemrograman logika',
                    'Raka belum tentu menguasai dasar pemrograman logika',
                    'Semua anggota klub robotika mahir merakit rangkaian elektronik',
                    'Sebagian siswa yang lolos klub robotika tidak menguasai pemrograman logika',
                    'B',
                    8,
                ],
            ];
        }

        if (strtolower($format) === 'xlsx' && class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->generateSpreadsheetFile($sampleRows, "Template_Soal_TPA_" . ucfirst($babak) . ".xlsx", $babak);
        }

        return $this->streamNativeCsv("Template_Soal_TPA_" . ucfirst($babak) . ".csv", $sampleRows);
    }

    /**
     * Export seluruh soal TPA per babak ke berkas CSV (default) atau Excel.
     */
    public function exportSoal(string $babak = 'literasi', string $format = 'csv'): StreamedResponse
    {
        $babak = in_array(strtolower($babak), ['literasi', 'numerasi']) ? strtolower($babak) : 'literasi';

        $soalList = TpaSoal::where('babak', $babak)
            ->orderBy('nomor_urut')
            ->orderBy('id')
            ->get();

        $rowsData = [];
        foreach ($soalList as $s) {
            $opsiMap = [];
            foreach ($s->opsi_jawaban ?? [] as $op) {
                $opsiMap[strtoupper($op['key'] ?? '')] = $op['text'] ?? '';
            }

            $rowsData[] = [
                $s->nomor_urut,
                $s->pertanyaan,
                $opsiMap['A'] ?? '',
                $opsiMap['B'] ?? '',
                $opsiMap['C'] ?? '',
                $opsiMap['D'] ?? '',
                $opsiMap['E'] ?? '',
                strtoupper($s->kunci_jawaban),
                $s->bobot ?? 1,
            ];
        }

        $timestamp = date('Ymd_His');
        if (strtolower($format) === 'xlsx' && class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $filename = "Bank_Soal_TPA_" . ucfirst($babak) . "_{$timestamp}.xlsx";
            return $this->generateSpreadsheetFile($rowsData, $filename, $babak);
        }

        $filename = "Bank_Soal_TPA_" . ucfirst($babak) . "_{$timestamp}.csv";
        return $this->streamNativeCsv($filename, $rowsData);
    }

    /**
     * Import butir soal dari berkas CSV atau Excel ke babak tertentu.
     */
    public function importSoal(string $babak, UploadedFile $file, string $mode = 'upsert'): array
    {
        $babak = in_array(strtolower($babak), ['literasi', 'numerasi']) ? strtolower($babak) : 'literasi';
        $mode = $mode === 'replace' ? 'replace' : 'upsert';

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'txt', 'xlsx', 'xls'])) {
            throw new \InvalidArgumentException('Format file tidak didukung. Harap gunakan file .csv atau .xlsx');
        }

        $filePath = $file->getRealPath();
        $rawRows = [];

        if (in_array($extension, ['csv', 'txt'], true)) {
            $rawRows = $this->parseNativeCsv($filePath);
        } elseif ($extension === 'xlsx') {
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $rawRows = $this->parsePhpSpreadsheet($filePath);
            } else {
                $rawRows = $this->parseXlsxNative($filePath);
            }
        } elseif ($extension === 'xls') {
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $rawRows = $this->parsePhpSpreadsheet($filePath);
            } else {
                throw new \InvalidArgumentException('Format .xls (Excel lama) membutuhkan library tambahan di server. Harap simpan berkas sebagai .csv atau .xlsx.');
            }
        }

        if (empty($rawRows)) {
            throw new \InvalidArgumentException('File kosong atau tidak memuat baris soal yang valid.');
        }

        $processedRows = [];
        $errors = [];
        $rowNumber = 2;

        foreach ($rawRows as $item) {
            $nomorUrut = trim((string) ($item[0] ?? ''));
            $pertanyaan = trim((string) ($item[1] ?? ''));
            $opsiA = trim((string) ($item[2] ?? ''));
            $opsiB = trim((string) ($item[3] ?? ''));
            $opsiC = trim((string) ($item[4] ?? ''));
            $opsiD = trim((string) ($item[5] ?? ''));
            $opsiE = trim((string) ($item[6] ?? ''));
            $kunci = strtoupper(trim((string) ($item[7] ?? '')));
            $bobot = trim((string) ($item[8] ?? ''));

            // Abaikan baris yang seluruhnya kosong
            if ($pertanyaan === '' && $opsiA === '' && $opsiB === '') {
                $rowNumber++;
                continue;
            }

            if ($pertanyaan === '') {
                $errors[] = "Baris {$rowNumber}: Kolom pertanyaan tidak boleh kosong.";
                $rowNumber++;
                continue;
            }

            if ($opsiA === '' || $opsiB === '') {
                $errors[] = "Baris {$rowNumber}: Minimal harus menyertakan Opsi A dan Opsi B.";
                $rowNumber++;
                continue;
            }

            if (!in_array($kunci, ['A', 'B', 'C', 'D', 'E'], true)) {
                $errors[] = "Baris {$rowNumber}: Kunci jawaban harus salah satu dari A, B, C, D, atau E.";
                $rowNumber++;
                continue;
            }

            // Susun opsi jawaban
            $opsiJawaban = [
                ['key' => 'A', 'text' => $opsiA],
                ['key' => 'B', 'text' => $opsiB],
            ];
            if ($opsiC !== '') $opsiJawaban[] = ['key' => 'C', 'text' => $opsiC];
            if ($opsiD !== '') $opsiJawaban[] = ['key' => 'D', 'text' => $opsiD];
            if ($opsiE !== '') $opsiJawaban[] = ['key' => 'E', 'text' => $opsiE];

            $availableKeys = array_column($opsiJawaban, 'key');
            if (!in_array($kunci, $availableKeys, true)) {
                $errors[] = "Baris {$rowNumber}: Kunci jawaban '{$kunci}' tidak ada di daftar opsi yang terisi.";
                $rowNumber++;
                continue;
            }

            $processedRows[] = [
                'nomor_urut' => is_numeric($nomorUrut) && (int) $nomorUrut > 0 ? (int) $nomorUrut : null,
                'pertanyaan' => $pertanyaan,
                'opsi_jawaban' => $opsiJawaban,
                'kunci_jawaban' => $kunci,
                'bobot' => is_numeric($bobot) && (int) $bobot > 0 ? (int) $bobot : 1,
            ];

            $rowNumber++;
        }

        if (empty($processedRows)) {
            $msg = 'Tidak ada butir soal valid yang dapat diimpor.';
            if (!empty($errors)) {
                $msg .= ' ' . implode(' ', array_slice($errors, 0, 3));
            }
            throw new \InvalidArgumentException($msg);
        }

        return DB::transaction(function () use ($babak, $processedRows, $mode, $errors) {
            $totalInserted = 0;
            $totalUpdated = 0;

            if ($mode === 'replace') {
                $oldSoals = TpaSoal::where('babak', $babak)->get();
                foreach ($oldSoals as $old) {
                    if ($old->gambar_path && Storage::disk('public')->exists($old->gambar_path)) {
                        Storage::disk('public')->delete($old->gambar_path);
                    }
                }
                TpaSoal::where('babak', $babak)->delete();

                $currNomor = 1;
                foreach ($processedRows as $row) {
                    $nomorUrut = $row['nomor_urut'] ?? $currNomor;
                    TpaSoal::create([
                        'babak' => $babak,
                        'nomor_urut' => $nomorUrut,
                        'pertanyaan' => $row['pertanyaan'],
                        'opsi_jawaban' => $row['opsi_jawaban'],
                        'kunci_jawaban' => $row['kunci_jawaban'],
                        'bobot' => $row['bobot'],
                        'is_active' => true,
                    ]);
                    $totalInserted++;
                    $currNomor = max($currNomor + 1, $nomorUrut + 1);
                }
            } else {
                // Upsert
                $maxUrut = TpaSoal::where('babak', $babak)->max('nomor_urut') ?? 0;

                foreach ($processedRows as $row) {
                    $targetNomor = $row['nomor_urut'];

                    if ($targetNomor !== null) {
                        $existing = TpaSoal::where('babak', $babak)->where('nomor_urut', $targetNomor)->first();
                        if ($existing) {
                            $existing->update([
                                'pertanyaan' => $row['pertanyaan'],
                                'opsi_jawaban' => $row['opsi_jawaban'],
                                'kunci_jawaban' => $row['kunci_jawaban'],
                                'bobot' => $row['bobot'],
                                'is_active' => true,
                            ]);
                            $totalUpdated++;
                            continue;
                        }
                    } else {
                        $maxUrut++;
                        $targetNomor = $maxUrut;
                    }

                    TpaSoal::create([
                        'babak' => $babak,
                        'nomor_urut' => $targetNomor,
                        'pertanyaan' => $row['pertanyaan'],
                        'opsi_jawaban' => $row['opsi_jawaban'],
                        'kunci_jawaban' => $row['kunci_jawaban'],
                        'bobot' => $row['bobot'],
                        'is_active' => true,
                    ]);
                    $totalInserted++;
                    $maxUrut = max($maxUrut, $targetNomor);
                }
            }

            return [
                'total_inserted' => $totalInserted,
                'total_updated' => $totalUpdated,
                'total' => $totalInserted + $totalUpdated,
                'mode' => $mode,
                'errors' => $errors,
            ];
        });
    }

    /**
     * Parser berkas CSV native PHP dengan auto-deteksi delimiter (koma, titik koma, tab) & BOM UTF-8.
     */
    protected function parseNativeCsv(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException('File CSV tidak dapat dibaca.');
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \InvalidArgumentException('Gagal membuka file CSV.');
        }

        // Baca baris pertama untuk mendeteksi delimiter
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new \InvalidArgumentException('File CSV kosong.');
        }

        if (str_starts_with($firstLine, "\xEF\xBB\xBF")) {
            $firstLine = substr($firstLine, 3);
        }

        $countSemicolon = substr_count($firstLine, ';');
        $countComma = substr_count($firstLine, ',');
        $countTab = substr_count($firstLine, "\t");

        $delimiter = ',';
        if ($countSemicolon > $countComma && $countSemicolon > $countTab) {
            $delimiter = ';';
        } elseif ($countTab > $countComma && $countTab > $countSemicolon) {
            $delimiter = "\t";
        }

        rewind($handle);
        // Lewati BOM UTF-8 jika ada pada byte awal
        $bomCheck = fread($handle, 3);
        if ($bomCheck !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows = [];
        $isHeader = true;

        while (($data = fgetcsv($handle, 0, $delimiter, '"')) !== false) {
            if (count($data) === 1 && trim((string) $data[0]) === '') {
                continue;
            }

            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // Standarisasi tepat 9 kolom
            $cols = array_pad(array_slice($data, 0, 9), 9, '');
            $rows[] = $cols;
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parser native berkas .xlsx menggunakan ekstensi standar ZipArchive & SimpleXML tanpa PhpSpreadsheet.
     */
    protected function parseXlsxNative(string $filePath): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Ekstensi PHP ZipArchive tidak aktif pada server hosting.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \InvalidArgumentException('Gagal membuka file .xlsx.');
        }

        // 1. Ambil tabel shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->children() as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $tCombined = '';
                        foreach ($si->r as $r) {
                            $tCombined .= (string) ($r->t ?? '');
                        }
                        $sharedStrings[] = $tCombined;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Cari lembar kerja pertama
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('#^xl/worksheets/sheet\d*\.xml$#i', $name)) {
                    $sheetXml = $zip->getFromName($name);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXml === false) {
            throw new \InvalidArgumentException('Lembar kerja Excel tidak ditemukan di dalam file.');
        }

        $xml = @simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData)) {
            throw new \InvalidArgumentException('Format isi file Excel tidak valid.');
        }

        $rawRows = [];
        $isFirst = true;

        foreach ($xml->sheetData->row as $row) {
            $rowNum = isset($row['r']) ? (int) $row['r'] : null;
            if ($isFirst || $rowNum === 1) {
                $isFirst = false;
                continue;
            }

            $cols = array_fill(0, 9, '');

            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                if (!preg_match('/^([A-Z]+)(\d+)$/', $ref, $m)) {
                    continue;
                }

                $colLetter = $m[1];
                $colIndex = 0;
                $len = strlen($colLetter);
                for ($k = 0; $k < $len; $k++) {
                    $colIndex = $colIndex * 26 + (ord($colLetter[$k]) - ord('A') + 1);
                }
                $colIndex -= 1;

                if ($colIndex < 0 || $colIndex > 8) {
                    continue;
                }

                $type = (string) $cell['t'];
                $val = '';

                if ($type === 's') {
                    $sIdx = (int) $cell->v;
                    $val = $sharedStrings[$sIdx] ?? '';
                } elseif ($type === 'inlineStr') {
                    if (isset($cell->is->t)) {
                        $val = (string) $cell->is->t;
                    } elseif (isset($cell->is->r)) {
                        foreach ($cell->is->r as $r) {
                            $val .= (string) ($r->t ?? '');
                        }
                    }
                } elseif ($type === 'b') {
                    $val = ((string) $cell->v === '1') ? '1' : '0';
                } else {
                    $val = (string) $cell->v;
                }

                $cols[$colIndex] = $val;
            }

            $rawRows[] = $cols;
        }

        return $rawRows;
    }

    /**
     * Parser menggunakan PhpSpreadsheet jika library tersedia.
     */
    protected function parsePhpSpreadsheet(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $rawRows = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $rawRows[] = [
                (string) $sheet->getCell('A' . $row)->getValue(),
                (string) $sheet->getCell('B' . $row)->getValue(),
                (string) $sheet->getCell('C' . $row)->getValue(),
                (string) $sheet->getCell('D' . $row)->getValue(),
                (string) $sheet->getCell('E' . $row)->getValue(),
                (string) $sheet->getCell('F' . $row)->getValue(),
                (string) $sheet->getCell('G' . $row)->getValue(),
                (string) $sheet->getCell('H' . $row)->getValue(),
                (string) $sheet->getCell('I' . $row)->getValue(),
            ];
        }

        return $rawRows;
    }

    /**
     * Stream CSV native dengan UTF-8 BOM untuk kompatibilitas penuh dengan Excel.
     */
    protected function streamNativeCsv(string $filename, array $rowsData): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($rowsData) {
            $handle = fopen('php://output', 'w');
            // Tulis UTF-8 BOM agar Excel Windows langsung membaca dengan UTF-8 yang rapi
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, self::HEADERS);

            foreach ($rowsData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Stream file Excel menggunakan PhpSpreadsheet (jika terpasang).
     */
    protected function generateSpreadsheetFile(array $rowsData, string $filename, string $babak): StreamedResponse
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Soal ' . ucfirst($babak));

        $sheet->fromArray(self::HEADERS, null, 'A1');

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $babak === 'numerasi' ? '059669' : '1D4ED8'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        foreach ($rowsData as $row) {
            $sheet->fromArray($row, null, 'A' . $rowNum);
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setWrapText(true);

            $rowNum++;
        }

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(55);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(12);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }
}
