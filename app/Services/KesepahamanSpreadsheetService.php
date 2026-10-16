<?php

namespace App\Services;

use App\Models\KesepahamanKelompok;
use App\Models\KesepahamanPoin;
use App\Models\KesepahamanProgram;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kesepahaman');

        $this->applyHeaderStyles($sheet);

        // Contoh baris pengisian
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

        $rowNum = 2;
        foreach ($sampleRows as $row) {
            $sheet->fromArray($row, null, 'A' . $rowNum);
            $this->applyDataRowStyles($sheet, $rowNum);
            $rowNum++;
        }

        $this->autoFitColumns($sheet);

        $filename = "Template_Kesepahaman_{$program->kode}." . ($format === 'csv' ? 'csv' : 'xlsx');
        return $this->streamSpreadsheet($spreadsheet, $filename, $format);
    }

    /**
     * Export data klausul program aktif ke Excel (.xlsx) atau CSV (.csv).
     */
    public function exportProgram(KesepahamanProgram $program, string $format = 'xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($program->nama, 0, 30));

        $this->applyHeaderStyles($sheet);

        $kelompoks = $program->kelompoks()
            ->with(['poins' => fn($q) => $q->orderBy('urutan')])
            ->orderBy('urutan')
            ->get();

        $rowNum = 2;
        foreach ($kelompoks as $kelompok) {
            foreach ($kelompok->poins as $poin) {
                $rowData = [
                    $kelompok->kode,
                    $kelompok->judul,
                    $kelompok->urutan,
                    $poin->nomor,
                    $poin->uraian,
                    $poin->kode_poin,
                    $poin->urutan,
                ];

                $sheet->fromArray($rowData, null, 'A' . $rowNum);
                $this->applyDataRowStyles($sheet, $rowNum);
                $rowNum++;
            }
        }

        $this->autoFitColumns($sheet);

        $timestamp = date('Ymd_His');
        $filename = "Export_Kesepahaman_{$program->kode}_{$timestamp}." . ($format === 'csv' ? 'csv' : 'xlsx');
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

        if ($extension === 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            // Deteksi otomatis delimiter CSV (, atau ;)
            $firstLine = fgets(fopen($filePath, 'r'));
            if ($firstLine !== false && substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $reader->setDelimiter(';');
            }
            $spreadsheet = $reader->load($filePath);
        } else {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        if ($highestRow < 2) {
            throw new \InvalidArgumentException('File kosong atau hanya berisi baris header.');
        }

        $rows = [];
        $errors = [];

        // Baca data dari baris ke-2 (asumsi baris 1 adalah header)
        for ($row = 2; $row <= $highestRow; $row++) {
            $kodeKelompok = trim((string) $sheet->getCell('A' . $row)->getValue());
            $judulKelompok = trim((string) $sheet->getCell('B' . $row)->getValue());
            $urutanKelompok = trim((string) $sheet->getCell('C' . $row)->getValue());
            $nomorPoin = trim((string) $sheet->getCell('D' . $row)->getValue());
            $uraianPoin = trim((string) $sheet->getCell('E' . $row)->getValue());
            $kodePoin = trim((string) $sheet->getCell('F' . $row)->getValue());
            $urutanPoin = trim((string) $sheet->getCell('G' . $row)->getValue());

            // Lewati baris jika benar-benar kosong
            if (empty($kodeKelompok) && empty($judulKelompok) && empty($nomorPoin) && empty($uraianPoin)) {
                continue;
            }

            // Validasi kolom wajib
            if (empty($kodeKelompok) || empty($judulKelompok) || empty($nomorPoin) || empty($uraianPoin)) {
                $errors[] = "Baris {$row}: Kode Kelompok, Judul Kelompok, Nomor Poin, dan Uraian Poin wajib diisi.";
                continue;
            }

            // Auto-generate kode_poin jika kosong
            if (empty($kodePoin)) {
                $slugKelompok = Str::slug($kodeKelompok, '_');
                $slugNomor = Str::slug($nomorPoin, '_');
                $kodePoin = strtolower($program->kode . '_' . $slugKelompok . '_' . $slugNomor);
            }

            $rows[] = [
                'row' => $row,
                'kode_kelompok' => strtoupper($kodeKelompok),
                'judul_kelompok' => $judulKelompok,
                'urutan_kelompok' => is_numeric($urutanKelompok) ? (int) $urutanKelompok : null,
                'nomor_poin' => $nomorPoin,
                'uraian_poin' => $uraianPoin,
                'kode_poin' => $kodePoin,
                'urutan_poin' => is_numeric($urutanPoin) ? (int) $urutanPoin : null,
            ];
        }

        if (empty($rows)) {
            throw new \InvalidArgumentException('Tidak ada data valid yang dapat diimpor dari file tersebut.');
        }

        // Jalankan import dalam database transaction
        return DB::transaction(function () use ($program, $rows, $mode, $errors) {
            if ($mode === 'replace') {
                // Hapus semua poin dan kelompok yang ada pada program ini
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
                    // Mode upsert: cek berdasarkan kode_poin atau kombinasi kelompok_id + nomor
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
     * Terapkan styling visual untuk header baris ke-1.
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
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
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
                'vertical' => Alignment::VERTICAL_TOP,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        // Center alignment untuk kolom kode kelompok, urutan, nomor, ID
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Wrap text untuk judul kelompok dan uraian poin
        $sheet->getStyle("B{$rowNum}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("E{$rowNum}")->getAlignment()->setWrapText(true);
    }

    /**
     * Set lebar kolom yang proporsional.
     */
    protected function autoFitColumns($sheet): void
    {
        $sheet->getColumnDimension('A')->setWidth(16); // Kode Kelompok
        $sheet->getColumnDimension('B')->setWidth(40); // Judul Kelompok
        $sheet->getColumnDimension('C')->setWidth(16); // Urutan Kelompok
        $sheet->getColumnDimension('D')->setWidth(14); // Nomor Poin
        $sheet->getColumnDimension('E')->setWidth(75); // Uraian Poin
        $sheet->getColumnDimension('F')->setWidth(20); // Kode Poin (ID)
        $sheet->getColumnDimension('G')->setWidth(14); // Urutan Poin
    }

    /**
     * Stream spreadsheet langsung ke browser.
     */
    protected function streamSpreadsheet(Spreadsheet $spreadsheet, string $filename, string $format): StreamedResponse
    {
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'max-age=0',
            ];

            return response()->stream(function () use ($spreadsheet) {
                $writer = new Csv($spreadsheet);
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
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }
}
