<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\MasterKriteriaWawancara;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Soft delete old criteria to prevent breaking WawancaraDetail fk
        MasterKriteriaWawancara::query()->delete();

        // 2. Insert new criteria matched to the form sections
        $newKriteria = [
            // SISWA
            [
                'kode' => 'S-INFO',
                'nama_kriteria' => 'Informasi Dasar & Lingkungan Belajar',
                'jenis_penilaian' => 'siswa',
                'urutan' => 1,
                'aktif' => true,
                'keterangan' => 'Kondisi tempat tinggal dan penanggung jawab siswa selama bersekolah di SMK Wikrama.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'S-AGM',
                'nama_kriteria' => 'Keagamaan & Alasan Memilih',
                'jenis_penilaian' => 'siswa',
                'urutan' => 2,
                'aktif' => true,
                'keterangan' => 'Pemahaman bacaan Al-Qur\'an, hafalan, dan motivasi memilih SMK Wikrama 1 Garut.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'S-KES',
                'nama_kriteria' => 'Riwayat Kesehatan & Kebiasaan',
                'jenis_penilaian' => 'siswa',
                'urutan' => 3,
                'aktif' => true,
                'keterangan' => 'Kebugaran fisik, kebiasaan sehari-hari, dan catatan riwayat penyakit.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'S-FIS',
                'nama_kriteria' => 'Observasi Fisik (Indikator Rambu)',
                'jenis_penilaian' => 'siswa',
                'urutan' => 4,
                'aktif' => true,
                'keterangan' => 'Indikator rambu: Hijau (Sesuai Standar), Oren (Perhatian/Pembinaan), Merah (Khusus/Bermasalah) untuk Kerapihan dan Kondisi Fisik.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ORANG TUA
            [
                'kode' => 'O-ID',
                'nama_kriteria' => 'Identitas Narasumber Orang Tua / Wali',
                'jenis_penilaian' => 'orang_tua',
                'urutan' => 5,
                'aktif' => true,
                'keterangan' => 'Data orang tua atau wali yang hadir langsung mendampingi calon siswa pada sesi wawancara.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'O-LING',
                'nama_kriteria' => 'Lingkungan Tempat Tinggal, Jarak & Transportasi',
                'jenis_penilaian' => 'orang_tua',
                'urutan' => 6,
                'aktif' => true,
                'keterangan' => 'Kondisi lingkungan rumah, kepemilikan tempat tinggal, moda transportasi dan jarak ke sekolah.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'O-HOBI',
                'nama_kriteria' => 'Kebiasaan, Pergaulan, & Minat Anak di Rumah',
                'jenis_penilaian' => 'orang_tua',
                'urutan' => 7,
                'aktif' => true,
                'keterangan' => 'Hobi, cita-cita, pergaulan teman sebaya, dan pembatasan gadget/keluar malam oleh orang tua.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'O-AGM',
                'nama_kriteria' => 'Keagamaan & Rencana Infaq Bulanan',
                'jenis_penilaian' => 'orang_tua',
                'urutan' => 8,
                'aktif' => true,
                'keterangan' => 'Pengawasan ibadah anak di rumah dan komitmen infaq rutin orang tua.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'O-PLH',
                'nama_kriteria' => 'Pilihan Pendidikan & Alasan Pemilihan Jurusan',
                'jenis_penilaian' => 'orang_tua',
                'urutan' => 9,
                'aktif' => true,
                'keterangan' => 'Dukungan orang tua terhadap program/jurusan yang dipilih anak serta kesiapan finansial.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'O-KES',
                'nama_kriteria' => 'Riwayat Penyakit & Perhatian Khusus',
                'jenis_penilaian' => 'orang_tua',
                'urutan' => 10,
                'aktif' => true,
                'keterangan' => 'Riwayat penyakit serius, alergi, dan catatan sifat anak yang perlu perhatian pewawancara.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('master_kriteria_wawancara')->insert($newKriteria);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete the new criteria
        MasterKriteriaWawancara::whereIn('kode', ['S-INFO', 'S-AGM', 'S-KES', 'S-FIS', 'O-ID', 'O-LING', 'O-HOBI', 'O-AGM', 'O-PLH', 'O-KES'])->forceDelete();
        
        // Restore old criteria
        MasterKriteriaWawancara::onlyTrashed()->restore();
    }
};
