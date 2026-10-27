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
        'A' => 'Nomor Urut',
        'B' => 'Pertanyaan',
        'C' => 'Opsi A',
        'D' => 'Opsi B',
        'E' => 'Opsi C',
        'F' => 'Opsi D',
        'E_ALT' => 'Opsi E', // Kolom G
        'KUNCI' => 'Kunci Jawaban', // Kolom H
        'BOBOT' => 'Bobot', // Kolom I
    ];

    /**
     * Download template Excel (.xlsx) dengan panduan contoh soal sesuai babak.
     */
    public function downloadTemplate(string $babak = 'literasi'): StreamedResponse
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
                    1,
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
                    1,
                ],
            ];
        } else {
            $sampleRows = [
                [
                    1,
                    'Bacalah teks berikut: "Pendidikan vokasi berfokus pada penguasaan keahlian terapan untuk memasuki dunia kerja." Gagasan pokok dari kutipan tersebut adalah?',
                    'Fokus utama pendidikan vokasi',
                    'Tantangan dunia kerja masa kini',
                    'Kurikulum sekolah menengah',
                    'Peran teknologi dalam belajar',
                    'Kebutuhan industri manufaktur',
                    'A',
                    1,
                ],
                [
                    2,
                    'Sinonim kata "akurat" dalam konteks data penilaian adalah?',
                    'Cepat',
                    'Tepat',
                    'Banyak',
                    'Rinci',
                    'Sederhana',
                    'B',
                    1,
                ],
            ];
        }

        $filename = "Template_Soal_TPA_" . ucfirst($babak) . ".xlsx";

        return $this->generateSpreadsheetFile($sampleRows, $filename, $babak);
    }

    /**
     * Export seluruh soal TPA pada babak tertentu ke format Excel (.xlsx).
     */
    public function exportSoal(string $babak = 'literasi'): StreamedResponse
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
        $filename = "Bank_Soal_TPA_" . ucfirst($babak) . "_{$timestamp}.xlsx";

        return $this->generateSpreadsheetFile($rowsData, $filename, $babak);
    }

    /**
     * Import butir soal dari file Excel atau CSV ke dalam babak tertentu.
     */
    public function importSoal(string $babak, UploadedFile $file, string $mode = 'upsert'): array
    {
        $babak = in_array(strtolower($babak), ['literasi', 'numerasi']) ? strtolower($babak) : 'literasi';
        $mode = $mode === 'replace' ? 'replace' : 'upsert';

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            throw new \InvalidArgumentException('Format file tidak didukung. Harap unggah file .xlsx, .xls, atau .csv');
        }

        $filePath = $file->getRealPath();
        $rawRows = [];

        if ($extension === 'csv') {
            $rawRows = $this->parseNativeCsv($filePath);
        } else {
            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                throw new \InvalidArgumentException('Library PhpSpreadsheet belum terpasang. Harap jalankan composer install atau gunakan file .csv.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

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
        }

        if (empty($rawRows)) {
            throw new \InvalidArgumentException('File kosong atau tidak memuat data baris soal TPA.');
        }

        $processedRows = [];
        $errors = [];
        $rowNumber = 2;

        foreach ($rawRows as $item) {
            $nomorUrut = trim($item[0] ?? '');
            $pertanyaan = trim($item[1] ?? '');
            $opsiA = trim($item[2] ?? '');
            $opsiB = trim($item[3] ?? '');
            $opsiC = trim($item[4] ?? '');
            $opsiD = trim($item[5] ?? '');
            $opsiE = trim($item[6] ?? '');
            $kunci = strtoupper(trim($item[7] ?? ''));
            $bobot = trim($item[8] ?? '');

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

            // Susun array opsi jawaban
            $opsiJawaban = [
                ['key' => 'A', 'text' => $opsiA],
                ['key' => 'B', 'text' => $opsiB],
            ];
            if ($opsiC !== '') $opsiJawaban[] = ['key' => 'C', 'text' => $opsiC];
            if ($opsiD !== '') $opsiJawaban[] = ['key' => 'D', 'text' => $opsiD];
            if ($opsiE !== '') $opsiJawaban[] = ['key' => 'E', 'text' => $opsiE];

            // Validasi kunci ada di antara opsi yang disediakan
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
            $msg = 'Tidak ada data soal valid yang dapat diimpor.';
            if (!empty($errors)) {
                $msg .= ' ' . implode(' ', array_slice($errors, 0, 3));
            }
            throw new \InvalidArgumentException($msg);
        }

        return DB::transaction(function () use ($babak, $processedRows, $mode, $errors) {
            $totalInserted = 0;
            $totalUpdated = 0;

            if ($mode === 'replace') {
                // Hapus berkas gambar soal lama
                $oldSoal = TpaSoal::where('babak', $babak)->get();
                foreach ($oldSoal as $os) {
                    if ($os->gambar_path) {
                        Storage::disk('public')->delete($os->gambar_path);
                    }
                    $os->delete();
                }

                $counter = 1;
                foreach ($processedRows as $row) {
                    TpaSoal::create([
                        'babak' => $babak,
                        'nomor_urut' => $row['nomor_urut'] ?? $counter++,
                        'pertanyaan' => $row['pertanyaan'],
                        'opsi_jawaban' => $row['opsi_jawaban'],
                        'kunci_jawaban' => $row['kunci_jawaban'],
                        'bobot' => $row['bobot'],
                        'is_active' => true,
                    ]);
                    $totalInserted++;
                }
            } else {
                // Mode Upsert
                $maxUrut = (TpaSoal::where('babak', $babak)->max('nomor_urut') ?? 0);

                foreach ($processedRows as $row) {
                    $targetUrut = $row['nomor_urut'];

                    if ($targetUrut !== null) {
                        $existing = TpaSoal::where('babak', $babak)
                            ->where('nomor_urut', $targetUrut)
                            ->first();

                        if ($existing) {
                            $existing->update([
                                'pertanyaan' => $row['pertanyaan'],
                                'opsi_jawaban' => $row['opsi_jawaban'],
                                'kunci_jawaban' => $row['kunci_jawaban'],
                                'bobot' => $row['bobot'],
                            ]);
                            $totalUpdated++;
                            continue;
                        }
                    }

                    // Buat baru jika belum ada
                    $nextUrut = $targetUrut ?? (++$maxUrut);
                    if ($nextUrut > $maxUrut) {
                        $maxUrut = $nextUrut;
                    }

                    TpaSoal::create([
                        'babak' => $babak,
                        'nomor_urut' => $nextUrut,
                        'pertanyaan' => $row['pertanyaan'],
                        'opsi_jawaban' => $row['opsi_jawaban'],
                        'kunci_jawaban' => $row['kunci_jawaban'],
                        'bobot' => $row['bobot'],
                        'is_active' => true,
                    ]);
                    $totalInserted++;
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
     * Generator berkas Spreadsheet (PhpSpreadsheet / native CSV).
     */
    protected function generateSpreadsheetFile(array $rowsData, string $filename, string $babak): StreamedResponse
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->streamNativeCsv($filename, $rowsData);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Soal ' . ucfirst($babak));

        // Tulis Header
        $headers = [
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
        $sheet->fromArray($headers, null, 'A1');

        // Style Header
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

        // Tulis Data
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

        // Auto width
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

    /**
     * Fallback parsing CSV native PHP tanpa modul tambahan.
     */
    protected function parseNativeCsv(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $first = true;
            while (($data = fgetcsv($handle, 4096, ',')) !== false) {
                if ($first) {
                    $first = false;
                    continue; // Skip header
                }
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Fallback stream CSV native.
     */
    protected function streamNativeCsv(string $filename, array $rowsData): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"" . str_replace('.xlsx', '.csv', $filename) . "\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($rowsData) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Nomor Urut', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'Kunci Jawaban', 'Bobot']);

            foreach ($rowsData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
