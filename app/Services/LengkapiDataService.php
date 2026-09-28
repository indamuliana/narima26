<?php

namespace App\Services;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\DataAkademik;
use App\Models\DataOrangtua;
use App\Models\DokumenPendaftaran;
use App\Models\MasterSeragam;
use App\Models\Prestasi;
use App\Models\UkuranSeragam;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LengkapiDataService
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected SpmbStatusService $spmbStatusService
    ) {}

    /**
     * Hitung persentase dan status kelengkapan data pendaftaran.
     */
    public function calculateCompletion(CalonSiswa $calonSiswa): array
    {
        $calonSiswa->loadMissing(['dataOrangtua', 'dataAkademik', 'prestasi', 'ukuranSeragam', 'dokumenPendaftaran']);

        // 1. Biodata (Pribadi & Wilayah)
        $biodataFields = [
            'nik', 'no_kk', 'agama', 'tempat_lahir', 'tanggal_lahir',
            'alamat_lengkap', 'rt', 'rw', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id'
        ];
        $filledBiodata = 0;
        foreach ($biodataFields as $f) {
            if (!empty($calonSiswa->$f)) {
                $filledBiodata++;
            }
        }
        $biodataPercent = (int) round(($filledBiodata / count($biodataFields)) * 100);

        // 2. Data Orang Tua
        $orangTua = $calonSiswa->dataOrangtua;
        $ortuFields = [
            'nama_ayah', 'pekerjaan_ayah_id', 'no_hp_ayah',
            'nama_ibu', 'pekerjaan_ibu_id', 'no_hp_ibu'
        ];
        $filledOrtu = 0;
        if ($orangTua) {
            foreach ($ortuFields as $f) {
                if (!empty($orangTua->$f)) {
                    $filledOrtu++;
                }
            }
        }
        $ortuPercent = (int) round(($filledOrtu / count($ortuFields)) * 100);

        // 3. Data Akademik
        $akademik = $calonSiswa->dataAkademik;
        $akademikFields = [
            'nilai_rata_rata', 'nilai_bahasa_indonesia', 'nilai_matematika',
            'nilai_bahasa_inggris', 'nilai_ipa'
        ];
        $filledAkademik = 0;
        if ($akademik) {
            foreach ($akademikFields as $f) {
                if ($akademik->$f !== null && $akademik->$f !== '') {
                    $filledAkademik++;
                }
            }
        }
        $akademikPercent = (int) round(($filledAkademik / count($akademikFields)) * 100);

        // 4. Ukuran Seragam
        $distinctSeragamCount = MasterSeragam::aktif()->distinct('nama_jenis')->count('nama_jenis');
        if ($distinctSeragamCount === 0) {
            $distinctSeragamCount = 1;
        }
        $chosenSeragamCount = $calonSiswa->ukuranSeragam()->count();
        $seragamPercent = min(100, (int) round(($chosenSeragamCount / $distinctSeragamCount) * 100));

        // 5. Dokumen Persyaratan
        $dokumen = $calonSiswa->dokumenPendaftaran;
        $mandatoryDocs = ['kk_path', 'akta_path', 'ijazah_skl_path', 'pas_foto_path'];
        $filledDocs = 0;
        if ($dokumen) {
            foreach ($mandatoryDocs as $doc) {
                if (!empty($dokumen->$doc)) {
                    $filledDocs++;
                }
            }
        }
        $dokumenPercent = (int) round(($filledDocs / count($mandatoryDocs)) * 100);

        $totalPercent = (int) round(
            ($biodataPercent + $ortuPercent + $akademikPercent + $seragamPercent + $dokumenPercent) / 5
        );

        $isAllComplete = ($biodataPercent === 100 &&
            $ortuPercent === 100 &&
            $akademikPercent === 100 &&
            $seragamPercent === 100 &&
            $dokumenPercent === 100);

        return [
            'biodata' => [
                'percent' => $biodataPercent,
                'is_complete' => $biodataPercent === 100,
                'filled' => $filledBiodata,
                'total' => count($biodataFields),
            ],
            'orang_tua' => [
                'percent' => $ortuPercent,
                'is_complete' => $ortuPercent === 100,
                'filled' => $filledOrtu,
                'total' => count($ortuFields),
            ],
            'akademik' => [
                'percent' => $akademikPercent,
                'is_complete' => $akademikPercent === 100,
                'filled' => $filledAkademik,
                'total' => count($akademikFields),
            ],
            'seragam' => [
                'percent' => $seragamPercent,
                'is_complete' => $seragamPercent === 100,
                'filled' => $chosenSeragamCount,
                'total' => $distinctSeragamCount,
            ],
            'dokumen' => [
                'percent' => $dokumenPercent,
                'is_complete' => $dokumenPercent === 100,
                'filled' => $filledDocs,
                'total' => count($mandatoryDocs),
            ],
            'prestasi_count' => $calonSiswa->prestasi()->count(),
            'total_percent' => $totalPercent,
            'is_all_complete' => $isAllComplete,
        ];
    }

    /**
     * Simpan update biodata calon siswa & alamat wilayah.
     */
    public function saveBiodata(CalonSiswa $calonSiswa, array $data): CalonSiswa
    {
        $allowedFields = [
            'nama_lengkap', 'nama_panggilan', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
            'nik', 'no_kk', 'agama', 'alamat_lengkap', 'rt', 'rw', 'kode_pos',
            'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id',
            'no_hp_siswa', 'email',
        ];

        $payload = array_intersect_key($data, array_flip($allowedFields));
        $calonSiswa->update($payload);

        // Jika status masih PEMBAYARAN_SELEKSI_DIVERIFIKASI, transisi ke MELENGKAPI_DATA
        if ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
            $this->spmbStatusService->changeStatus(
                $calonSiswa,
                SpmbStatus::MELENGKAPI_DATA,
                'Mulai melengkapi biodata pendaftaran.'
            );
        }

        return $calonSiswa->fresh();
    }

    /**
     * Simpan data orang tua dan wali.
     */
    public function saveOrangTua(CalonSiswa $calonSiswa, array $data): DataOrangtua
    {
        $allowedFields = [
            'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pekerjaan_ayah_id', 'penghasilan_ayah',
            'pendidikan_ayah', 'no_hp_ayah', 'alamat_ayah',
            'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pekerjaan_ibu_id', 'penghasilan_ibu',
            'pendidikan_ibu', 'no_hp_ibu', 'alamat_ibu',
            'nama_wali', 'hubungan_wali', 'pekerjaan_wali_id', 'penghasilan_wali',
            'no_hp_wali', 'alamat_wali',
        ];

        $payload = array_intersect_key($data, array_flip($allowedFields));

        $ortu = DataOrangtua::updateOrCreate(
            ['calon_siswa_id' => $calonSiswa->id],
            $payload
        );

        // Sinkronisasi nomor telepon orang tua ke calon_siswa jika belum ada
        $updateHp = [];
        if (!empty($payload['no_hp_ayah'])) {
            $updateHp['no_hp_ayah'] = $payload['no_hp_ayah'];
        }
        if (!empty($payload['no_hp_ibu'])) {
            $updateHp['no_hp_ibu'] = $payload['no_hp_ibu'];
        }
        if (!empty($updateHp)) {
            $calonSiswa->update($updateHp);
        }

        if ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
            $this->spmbStatusService->changeStatus(
                $calonSiswa,
                SpmbStatus::MELENGKAPI_DATA,
                'Mengisi data orang tua calon siswa.'
            );
        }

        return $ortu;
    }

    /**
     * Simpan data nilai akademik dan prestasi siswa.
     */
    public function saveAkademik(CalonSiswa $calonSiswa, array $data, ?array $prestasiList = null): DataAkademik
    {
        $allowedAkademik = [
            'nama_sekolah', 'npsn', 'nisn', 'nilai_rata_rata',
            'nilai_bahasa_indonesia', 'nilai_matematika', 'nilai_bahasa_inggris',
            'nilai_ipa', 'nilai_lainnya', 'catatan',
        ];

        $payload = array_intersect_key($data, array_flip($allowedAkademik));

        $akademik = DataAkademik::updateOrCreate(
            ['calon_siswa_id' => $calonSiswa->id],
            $payload
        );

        // Jika prestasi diinputkan
        if ($prestasiList !== null) {
            // Hapus prestasi lama atau perbarui
            $calonSiswa->prestasi()->delete();
            foreach ($prestasiList as $pres) {
                if (!empty($pres['nama_prestasi'])) {
                    Prestasi::create([
                        'calon_siswa_id' => $calonSiswa->id,
                        'jenis_prestasi' => $pres['jenis_prestasi'] ?? 'non-akademik',
                        'tingkat' => $pres['tingkat'] ?? 'kabupaten',
                        'nama_prestasi' => $pres['nama_prestasi'],
                        'tahun' => $pres['tahun'] ?? date('Y'),
                        'peringkat' => $pres['peringkat'] ?? null,
                        'keterangan' => $pres['keterangan'] ?? null,
                    ]);
                }
            }
        }

        if ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
            $this->spmbStatusService->changeStatus(
                $calonSiswa,
                SpmbStatus::MELENGKAPI_DATA,
                'Mengisi data akademik & prestasi.'
            );
        }

        return $akademik;
    }

    /**
     * Simpan pilihan ukuran seragam siswa.
     */
    public function saveSeragam(CalonSiswa $calonSiswa, array $seragamEntries): void
    {
        DB::transaction(function () use ($calonSiswa, $seragamEntries) {
            $calonSiswa->ukuranSeragam()->delete();

            foreach ($seragamEntries as $entry) {
                if (!empty($entry['jenis_seragam_id']) && !empty($entry['ukuran'])) {
                    UkuranSeragam::create([
                        'calon_siswa_id' => $calonSiswa->id,
                        'jenis_seragam_id' => $entry['jenis_seragam_id'],
                        'ukuran' => $entry['ukuran'],
                        'jumlah' => $entry['jumlah'] ?? 1,
                        'keterangan' => $entry['keterangan'] ?? null,
                    ]);
                }
            }

            if ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
                $this->spmbStatusService->changeStatus(
                    $calonSiswa,
                    SpmbStatus::MELENGKAPI_DATA,
                    'Mengisi data ukuran seragam.'
                );
            }
        });
    }

    /**
     * Unggah berkas dokumen persyaratan calon siswa.
     */
    public function uploadDokumen(CalonSiswa $calonSiswa, array $files): DokumenPendaftaran
    {
        $dokumen = DokumenPendaftaran::firstOrNew(['calon_siswa_id' => $calonSiswa->id]);

        $allowedFileKeys = [
            'file_kartu_keluarga' => 'kk_path',
            'file_akta_kelahiran' => 'akta_path',
            'file_ijazah_atau_skl' => 'ijazah_skl_path',
            'file_pas_foto' => 'pas_foto_path',
            'file_dokumen_pendukung' => 'dokumen_pendukung_path',
        ];

        foreach ($allowedFileKeys as $inputKey => $columnName) {
            if (isset($files[$inputKey]) && $files[$inputKey] instanceof UploadedFile) {
                $file = $files[$inputKey];
                // Hapus berkas lama jika ada
                if ($dokumen->$columnName) {
                    $this->fileUploadService->deleteFile($dokumen->$columnName);
                }

                $docCategory = str_replace('_path', '', $columnName);
                $savedPath = $this->fileUploadService->uploadStudentDocument($file, $calonSiswa->id, $docCategory);
                $dokumen->$columnName = $savedPath;
            }
        }

        $dokumen->save();

        if ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
            $this->spmbStatusService->changeStatus(
                $calonSiswa,
                SpmbStatus::MELENGKAPI_DATA,
                'Mengunggah berkas persyaratan pendaftaran.'
            );
        }

        return $dokumen;
    }

    /**
     * Finalisasi data pendaftaran: validasi kelengkapan, ubah status_data = LENGKAP
     * dan transisikan status_spmb ke DATA_LENGKAP.
     *
     * @throws InvalidArgumentException
     */
    public function finalize(CalonSiswa $calonSiswa, ?User $actor = null): bool
    {
        $completion = $this->calculateCompletion($calonSiswa);

        if (!$completion['is_all_complete']) {
            $missing = [];
            if (!$completion['biodata']['is_complete']) $missing[] = 'Biodata Siswa';
            if (!$completion['orang_tua']['is_complete']) $missing[] = 'Data Orang Tua';
            if (!$completion['akademik']['is_complete']) $missing[] = 'Data Akademik / Nilai';
            if (!$completion['seragam']['is_complete']) $missing[] = 'Ukuran Seragam';
            if (!$completion['dokumen']['is_complete']) $missing[] = 'Dokumen Persyaratan (KK, Akta, Ijazah/SKL, Pas Foto)';

            throw new InvalidArgumentException(
                'Data belum lengkap! Bagian yang belum terisi penuh: ' . implode(', ', $missing) . '.'
            );
        }

        DB::transaction(function () use ($calonSiswa, $actor) {
            // Update status_data
            $calonSiswa->status_data = 'LENGKAP';
            $calonSiswa->save();

            // Transisi status SPMB
            if ($calonSiswa->status_spmb === SpmbStatus::MELENGKAPI_DATA) {
                $this->spmbStatusService->changeStatus(
                    $calonSiswa,
                    SpmbStatus::DATA_LENGKAP,
                    'Seluruh data pendaftaran dan berkas persyaratan telah lengkap.',
                    null,
                    $actor
                );
            } elseif ($calonSiswa->status_spmb === SpmbStatus::PEMBAYARAN_SELEKSI_DIVERIFIKASI) {
                $this->spmbStatusService->changeStatus(
                    $calonSiswa,
                    SpmbStatus::DATA_LENGKAP,
                    'Seluruh data pendaftaran dan berkas persyaratan telah lengkap.',
                    null,
                    $actor
                );
            }

            if (function_exists('activity')) {
                activity('spmb_data')
                    ->performedOn($calonSiswa)
                    ->causedBy($actor ?? auth()->user())
                    ->withProperties([
                        'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                        'status_data' => 'LENGKAP',
                    ])
                    ->log("Calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}) menyelesaikan kelengkapan data & berkas.");
            }
        });

        return true;
    }
}
