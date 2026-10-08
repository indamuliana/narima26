<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $kriteria = [
            'KRP' => 'Bagaimana pendapatmu tentang pentingnya berpakaian rapi di lingkungan sekolah dan dunia kerja?',
            'SKP' => 'Ceritakan bagaimana caramu mengendalikan emosi ketika sedang marah atau berbeda pendapat dengan teman?',
            'KOM' => 'Coba perkenalkan dirimu, apa saja kelebihanmu dan hal apa yang masih perlu kamu perbaiki?',
            'DIS' => 'Pernahkah kamu datang terlambat atau melanggar aturan sekolah? Jika iya, apa yang kamu lakukan setelahnya?',
            'MOT' => 'Apa alasan terbesar kamu memilih SMK Wikrama 1 Garut dibandingkan sekolah lain?',
            'KSP' => 'Jadwal belajar di SMK cukup padat dan banyak praktik, apakah kamu siap? Bagaimana caramu mengatur waktu?',
            'PPH' => 'Apa yang kamu ketahui tentang jurusan yang kamu pilih ini? Apa cita-citamu setelah lulus nanti?',
            'DUK' => 'Bagaimana Bapak/Ibu memantau perkembangan dan pergaulan anak di rumah? Sejauh mana dukungan Bapak/Ibu terhadap pilihan jurusan anak?',
            'FIN' => 'Terkait dengan administrasi dan pembiayaan selama pendidikan, apakah Bapak/Ibu sudah memahaminya dan bersedia berkomitmen penuh terhadap penyelesaiannya secara disiplin?',
        ];

        foreach ($kriteria as $kode => $keterangan) {
            DB::table('master_kriteria_wawancara')
                ->where('kode', $kode)
                ->update(['keterangan' => $keterangan]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('master_kriteria_wawancara')
            ->update(['keterangan' => null]);
    }
};
