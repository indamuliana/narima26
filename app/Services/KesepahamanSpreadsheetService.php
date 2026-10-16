<?php

namespace App\Services;

use App\Models\KesepahamanKelompok;
use App\Models\KesepahamanPoin;
use App\Models\KesepahamanProgram;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KesepahamanSpreadsheetService
{
    /**
     * Header kolom standar template dan export.
     */
    public const HEADERS = [
        'A' => 'Kode Kelompok',
        'B' => 'Judul Kelompok',
        'C' => 'Urutan Kelompok',
        'D' => 'Nomor Poin',
        'E' => 'Uraian Poin',
        'F' => 'Kode Poin (ID)',
        'G' => 'Urutan Poin',
    ];

    /**
     * Download template Excel (.xlsx) atau CSV (.csv) dengan baris panduan / contoh.
     */
    public function downloadTemplate(KesepahamanProgram $program, string $format = 'xlsx'): StreamedResponse
    {
        $sampleRows = [
            [
                'A',
                'Dalam kaitannya dengan Sistem Penerimaan Murid Baru (SPMB), murid bersedia :',
                1,
                '1',
                'Melaporkan kepada SMK WIKRAMA 1 GARUT jika tidak lulus SMP/MTs sederajat paling lambat dua hari setelah pengumuman kelulusan, dengan menyerahkan surat keterangan tidak lulus.',
                $program->kode . '_a_1',
                1,
            ],
            [
                'A',
                'Dalam kaitannya dengan Sistem Penerimaan Murid Baru (SPMB), murid bersedia :',
                1,
                '2',
                'Memenuhi persyaratan terbebas dari permasalahan hukum.',
                $program->kode . '_a_2',
                2,
            ],
            [
                'B',
                'Dalam kaitannya dengan mengikuti pendidikan di SMK Wikrama 1 Garut, murid dan orang tua bersedia mengikuti skema pembelajaran sebagai berikut :',
                2,
                '1',
                'Menerima keputusan penjurusan yang telah ditetapkan oleh pihak sekolah berdasarkan kuota dan hasil penelusuran minat dan bakat.',
                $program->kode . '_b_1',
                1,
            ],
        ];

        $filename = "Template_Kesepahaman_{$program->kode}." . ($format === 'csv' ? 'csv' : 'xlsx');

        // Jika format CSV atau PhpSpreadsheet belum ada di hosting, gunakan native CSV
        if ($format === 'csv' || !class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->streamNativeCsv($filename, $sampleRows);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kesepahaman');

        $this->applyHeaderStyles($sheet);

        $rowNum = 2;
        foreach ($sampleRows as $row) {
            $sheet->fromArray($row, null, 'A' . $rowNum);
            $this->applyDataRowStyles($sheet, $rowNum);
            $rowNum++;
        }

        $this->autoFitColumns($sheet);

        return $this->streamSpreadsheet($spreadsheet, $filename, $format);
    }

    /**
     * Export data klausul program aktif ke Excel (.xlsx) atau CSV (.csv).
     */
    public function exportProgram(KesepahamanProgram $program, string $format = 'xlsx'): StreamedResponse
    {
        $kelompoks = $program->kelompoks()
            ->with(['poins' => fn($q) => $q->orderBy('urutan')])
            ->orderBy('urutan')
            ->get();

        $rowsData = [];
        foreach ($kelompoks as $kelompok) {
            foreach ($kelompok->poins as $poin) {
                $rowsData[] = [
                    $kelompok->kode,
                    $kelompok->judul,
                    $kelompok->urutan,
                    $poin->nomor,
                    $poin->uraian,
                    $poin->kode_poin,
                    $poin->urutan,
                ];
            }
        }

        $timestamp = date('Ymd_His');
        $filename = "Export_Kesepahaman_{$program->kode}_{$timestamp}." . ($format === 'csv' ? 'csv' : 'xlsx');

        // Jika format CSV atau PhpSpreadsheet belum terinstal di server hosting, gunakan native CSV
        if ($format === 'csv' || !class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->streamNativeCsv($filename, $rowsData);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($program->nama, 0, 30));

        $this->applyHeaderStyles($sheet);

        $rowNum = 2;
        foreach ($rowsData as $rowData) {
            $sheet->fromArray($rowData, null, 'A' . $rowNum);
            $this->applyDataRowStyles($sheet, $rowNum);
            $rowNum++;
        }

        $this->autoFitColumns($sheet);

        return $this->streamSpreadsheet($spreadsheet, $filename, $format);
    }

    /**
     * Import data dari file Excel (.xlsx / .xls) atau CSV (.csv).
     *
     * @param KesepahamanProgram $program
     * @param UploadedFile $file
     * @param string $mode 'replace' atau 'upsert'
     * @return array
     */
    public function importProgram(KesepahamanProgram $program, UploadedFile $file, string $mode = 'replace'): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            throw new \InvalidArgumentException('Format file tidak didukung. Harap unggah file .xlsx, .xls, atau .csv');
        }

        $filePath = $file->getRealPath();
        $rawRows = [];

        if ($extension === 'csv') {
            // Gunakan native PHP CSV parser (tidak memerlukan library eksternal)
            $rawRows = $this->parseNativeCsv($filePath);
        } else {
            // Untuk XLSX / XLS, periksa ketersediaan library PhpSpreadsheet
            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                throw new \InvalidArgumentException(
                    'Library PhpSpreadsheet belum terpasang di hosting. Silakan jalankan `composer install` di terminal cPanel hosting Anda, atau gunakan file berformat CSV (.csv).'
                );
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
                ];
            }
        }

        if (empty($rawRows)) {
            throw new \InvalidArgumentException('File kosong atau tidak memuat data butir kesepahaman.');
        }

        $rows = [];
        $errors = [];
        $lineNum = 2;

        foreach ($rawRows as $item) {
            $kodeKelompok = trim($item[0] ?? '');
            $judulKelompok = trim($item[1] ?? '');
            $urutanKelompok = trim($item[2] ?? '');
            $nomorPoin = trim($item[3] ?? '');
            $uraianPoin = trim($item[4] ?? '');
            $kodePoin = trim($item[5] ?? '');
            $urutanPoin = trim($item[6] ?? '');

            // Lewati baris jika benar-benar kosong
            if (empty($kodeKelompok) && empty($judulKelompok) && empty($nomorPoin) && empty($uraianPoin)) {
                $lineNum++;
                continue;
            }

            // Validasi kolom wajib
            if (empty($kodeKelompok) || empty($judulKelompok) || empty($nomorPoin) || empty($uraianPoin)) {
                $errors[] = "Baris {$lineNum}: Kode Kelompok, Judul Kelompok, Nomor Poin, dan Uraian Poin wajib diisi.";
                $lineNum++;
                continue;
            }

            // Auto-generate kode_poin jika kosong
            if (empty($kodePoin)) {
                $slugKelompok = Str::slug($kodeKelompok, '_');
                $slugNomor = Str::slug($nomorPoin, '_');
                $kodePoin = strtolower($program->kode . '_' . $slugKelompok . '_' . $slugNomor);
            }

            $rows[] = [
                'row' => $lineNum,
                'kode_kelompok' => strtoupper($kodeKelompok),
                'judul_kelompok' => $judulKelompok,
                'urutan_kelompok' => is_numeric($urutanKelompok) ? (int) $urutanKelompok : null,
                'nomor_poin' => $nomorPoin,
                'uraian_poin' => $uraianPoin,
                'kode_poin' => $kodePoin,
                'urutan_poin' => is_numeric($urutanPoin) ? (int) $urutanPoin : null,
            ];

            $lineNum++;
        }

        if (empty($rows)) {
            throw new \InvalidArgumentException('Tidak ada data valid yang dapat diimpor dari file tersebut.');
        }

        // Jalankan import dalam database transaction
        return DB::transaction(function () use ($program, $rows, $mode, $errors) {
            if ($mode === 'replace') {
                foreach ($program->kelompoks as $k) {
                    $k->poins()->delete();
                    $k->delete();
                }
            }

            $kelompokCache = [];
            $kelompokCounter = 1;
            $poinCounter = 1;
            $totalPoin = 0;
            $totalKelompok = 0;

            foreach ($rows as $item) {
                $kodeK = $item['kode_kelompok'];

                if (!isset($kelompokCache[$kodeK])) {
                    if ($mode === 'replace') {
                        $urutanK = $item['urutan_kelompok'] ?? $kelompokCounter++;
                        $kelompok = KesepahamanKelompok::create([
                            'program_id' => $program->id,
                            'kode' => $kodeK,
                            'judul' => $item['judul_kelompok'],
                            'urutan' => $urutanK,
                        ]);
                        $totalKelompok++;
                    } else {
                        // Mode upsert
                        $urutanK = $item['urutan_kelompok'] ?? $kelompokCounter++;
                        $kelompok = KesepahamanKelompok::firstOrCreate(
                            ['program_id' => $program->id, 'kode' => $kodeK],
                            [
                                'judul' => $item['judul_kelompok'],
                                'urutan' => $urutanK,
                            ]
                        );
                        if (!$kelompok->wasRecentlyCreated) {
                            $kelompok->update([
                                'judul' => $item['judul_kelompok'],
                                'urutan' => $item['urutan_kelompok'] ?? $kelompok->urutan,
                            ]);
                        } else {
                            $totalKelompok++;
                        }
                    }
                    $kelompokCache[$kodeK] = $kelompok;
                } else {
                    $kelompok = $kelompokCache[$kodeK];
                }

                $urutanP = $item['urutan_poin'] ?? $poinCounter++;

                if ($mode === 'replace') {
                    KesepahamanPoin::create([
                        'kelompok_id' => $kelompok->id,
                        'kode_poin' => $item['kode_poin'],
                        'nomor' => $item['nomor_poin'],
                        'uraian' => $item['uraian_poin'],
                        'urutan' => $urutanP,
                    ]);
                    $totalPoin++;
                } else {
                    // Mode upsert
                    $poin = KesepahamanPoin::where('kode_poin', $item['kode_poin'])->first();
                    if (!$poin) {
                        $poin = KesepahamanPoin::where('kelompok_id', $kelompok->id)
                            ->where('nomor', $item['nomor_poin'])
                            ->first();
                    }

                    if ($poin) {
                        $poin->update([
                            'kelompok_id' => $kelompok->id,
                            'kode_poin' => $item['kode_poin'],
                            'nomor' => $item['nomor_poin'],
                            'uraian' => $item['uraian_poin'],
                            'urutan' => $item['urutan_poin'] ?? $poin->urutan,
                        ]);
                    } else {
                        KesepahamanPoin::create([
                            'kelompok_id' => $kelompok->id,
                            'kode_poin' => $item['kode_poin'],
                            'nomor' => $item['nomor_poin'],
                            'uraian' => $item['uraian_poin'],
                            'urutan' => $urutanP,
                        ]);
                    }
                    $totalPoin++;
                }
            }

            return [
                'success' => true,
                'mode' => $mode,
                'total_kelompok' => count($kelompokCache),
                'total_poin' => $totalPoin,
                'errors' => $errors,
            ];
        });
    }

    /**
     * Parser CSV native bawaan PHP (tanpa perlu library tambahan).
     */
    protected function parseNativeCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \InvalidArgumentException('Tidak dapat membaca file CSV.');
        }

        // Baca baris pertama untuk deteksi delimiter (, atau ;)
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new \InvalidArgumentException('File CSV kosong.');
        }
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }

        $rows = [];
        $isHeader = true;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue; // Lewati baris header
            }

            // Bersihkan BOM jika ada pada sel pertama
            if (isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }

            $rows[] = $data;
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Stream CSV native langsung ke browser dengan UTF-8 BOM.
     */
    protected function streamNativeCsv(string $filename, array $rows): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($rows) {
            $output = fopen('php://output', 'w');
            // Tulis UTF-8 BOM agar Microsoft Excel membuka karakter dengan benar
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, array_values(self::HEADERS));
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, $headers);
    }

    /**
     * Terapkan styling visual untuk header baris ke-1 (jika PhpSpreadsheet tersedia).
     */
    protected function applyHeaderStyles($sheet): void
    {
        foreach (self::HEADERS as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '3730A3'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);
    }

    /**
     * Terapkan styling visual untuk baris data.
     */
    protected function applyDataRowStyles($sheet, int $rowNum): void
    {
        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray([
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("B{$rowNum}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("E{$rowNum}")->getAlignment()->setWrapText(true);
    }

    /**
     * Set lebar kolom yang proporsional.
     */
    protected function autoFitColumns($sheet): void
    {
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(75);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(14);
    }

    /**
     * Stream spreadsheet langsung ke browser.
     */
    protected function streamSpreadsheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet, string $filename, string $format): StreamedResponse
    {
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'max-age=0',
            ];

            return response()->stream(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                $writer->setDelimiter(',');
                $writer->setEnclosure('"');
                $writer->setLineEnding("\r\n");
                $writer->setUseBOM(true);
                $writer->save('php://output');
            }, 200, $headers);
        }

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
