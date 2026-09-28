<?php

namespace App\Services;

use App\Enums\SpmbStatus;
use App\Enums\UserRole;
use App\Models\CalonSiswa;
use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\PembayaranSeleksi;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RegistrationService
{
    public function __construct(
        protected RegistrationNumberService $numberService,
        protected PhoneNumberService $phoneService,
        protected SpmbStatusService $statusService
    ) {}

    /**
     * Daftarkan calon siswa baru secara atomik di dalam Database Transaction.
     * Membuat nomor pendaftaran, user login akun siswa, data calon siswa,
     * status awal SPMB, dan tagihan biaya seleksi.
     *
     * @param array $data
     * @return array [calon_siswa, user, pembayaran_seleksi, password_plain]
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. Tentukan gelombang pendaftaran aktif
            $gelombangId = $data['gelombang_id'] ?? null;
            if (!$gelombangId) {
                $activeGelombang = MasterGelombang::aktif()->first() ?? MasterGelombang::first();
                if (!$activeGelombang) {
                    throw new RuntimeException("Tidak ada gelombang pendaftaran yang aktif saat ini.");
                }
                $gelombangId = $activeGelombang->id;
            }

            // 2. Generate nomor registrasi berurutan & anti-duplikasi (Section 13)
            $nomorPendaftaran = $this->numberService->generate();

            // 3. Normalisasi nomor telepon ke standar internasional 62xxx (Section 14)
            $phoneSiswa = $this->phoneService->normalize($data['no_hp_siswa'] ?? null);
            $phoneAyah = $this->phoneService->normalize($data['no_hp_ayah'] ?? null);
            $phoneIbu = $this->phoneService->normalize($data['no_hp_ibu'] ?? null);

            // 4. Buat password awal = Nomor Pendaftaran (di-generate otomatis oleh sistem)
            $passwordPlain = $nomorPendaftaran;

            // Tentukan email akun
            $email = !empty($data['email']) ? trim($data['email']) : ($data['nisn'] . '@siswa.wikrama.sch.id');

            // 5. Buat entitas User akun Calon Siswa (Section 3 & 4)
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'username' => $data['nisn'],
                'email' => $email,
                'password' => Hash::make($passwordPlain),
                'phone' => $phoneSiswa,
                'role' => UserRole::CALON_SISWA->value,
                'is_active' => true,
            ]);

            // 6. Buat entitas CalonSiswa
            $calonSiswa = CalonSiswa::create([
                'nomor_pendaftaran' => $nomorPendaftaran,
                'user_id' => $user->id,
                'nisn' => $data['nisn'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'nama_lengkap' => $data['nama_lengkap'],
                'nama_panggilan' => $data['nama_panggilan'] ?? null,
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'no_hp_siswa' => $phoneSiswa,
                'no_hp_ayah' => $phoneAyah,
                'no_hp_ibu' => $phoneIbu,
                'email' => $email,
                'asal_sekolah_id' => $data['asal_sekolah_id'] ?? null,
                'asal_sekolah_lainnya' => $data['asal_sekolah_lainnya'] ?? null,
                'program_id' => $data['program_id'],
                'jurusan_id' => $data['jurusan_id'],
                'gelombang_id' => $gelombangId,
                'status_spmb' => SpmbStatus::REGISTRASI,
                'status_data' => 'BELUM_LENGKAP',
            ]);

            // 7. Transisi status dari REGISTRASI ke MENUNGGU_PEMBAYARAN_SELEKSI (Section 5)
            $this->statusService->transition(
                calonSiswa: $calonSiswa,
                newStatus: SpmbStatus::MENUNGGU_PEMBAYARAN_SELEKSI,
                catatan: 'Pendaftaran akun baru berhasil. Menunggu pembayaran biaya seleksi.',
                actor: $user
            );

            // 8. Tentukan nominal biaya seleksi dari master biaya atau default
            $biayaSeleksiMaster = MasterBiaya::aktif()
                ->where('kategori', 'seleksi')
                ->first();
            $nominalBiayaSeleksi = $biayaSeleksiMaster ? (float) $biayaSeleksiMaster->nominal : 250000;

            // Buat record tagihan awal pembayaran seleksi
            $pembayaranSeleksi = PembayaranSeleksi::create([
                'calon_siswa_id' => $calonSiswa->id,
                'nominal_tagihan' => $nominalBiayaSeleksi,
                'nominal_dibayar' => 0,
                'status' => 'PENDING',
                'metode_bayar' => 'transfer_bank',
            ]);

            // 9. Audit Trail Pendaftaran
            activity('registration')
                ->performedOn($calonSiswa)
                ->causedBy($user)
                ->withProperties([
                    'nomor_pendaftaran' => $nomorPendaftaran,
                    'nisn' => $calonSiswa->nisn,
                    'nama_lengkap' => $calonSiswa->nama_lengkap,
                    'program_id' => $calonSiswa->program_id,
                    'jurusan_id' => $calonSiswa->jurusan_id,
                    'gelombang_id' => $calonSiswa->gelombang_id,
                ])
                ->log("Pendaftaran mandiri calon peserta didik baru #{$nomorPendaftaran} ({$calonSiswa->nama_lengkap})");

            return [
                'calon_siswa' => $calonSiswa->fresh(['program', 'jurusan', 'gelombang', 'sekolahAsal']),
                'user' => $user,
                'pembayaran_seleksi' => $pembayaranSeleksi,
                'password_plain' => $passwordPlain,
            ];
        });
    }
}
