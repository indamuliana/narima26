<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan status_ayah dan status_ibu ke tabel data_orangtua
        Schema::table('data_orangtua', function (Blueprint $table) {
            $table->enum('status_ayah', ['MASIH_HIDUP', 'WAFAT'])->default('MASIH_HIDUP')->after('calon_siswa_id');
            $table->enum('status_ibu', ['MASIH_HIDUP', 'WAFAT'])->default('MASIH_HIDUP')->after('alamat_ayah');
        });

        // Tabel matrix nilai rapor per semester (4 mapel x 5 semester)
        Schema::create('nilai_rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswa')->cascadeOnDelete();

            // Matematika semester 1-5
            $table->decimal('mtk_sem1', 5, 2)->nullable();
            $table->decimal('mtk_sem2', 5, 2)->nullable();
            $table->decimal('mtk_sem3', 5, 2)->nullable();
            $table->decimal('mtk_sem4', 5, 2)->nullable();
            $table->decimal('mtk_sem5', 5, 2)->nullable();

            // Bahasa Indonesia semester 1-5
            $table->decimal('ind_sem1', 5, 2)->nullable();
            $table->decimal('ind_sem2', 5, 2)->nullable();
            $table->decimal('ind_sem3', 5, 2)->nullable();
            $table->decimal('ind_sem4', 5, 2)->nullable();
            $table->decimal('ind_sem5', 5, 2)->nullable();

            // Bahasa Inggris semester 1-5
            $table->decimal('eng_sem1', 5, 2)->nullable();
            $table->decimal('eng_sem2', 5, 2)->nullable();
            $table->decimal('eng_sem3', 5, 2)->nullable();
            $table->decimal('eng_sem4', 5, 2)->nullable();
            $table->decimal('eng_sem5', 5, 2)->nullable();

            // Pendidikan Agama semester 1-5
            $table->decimal('pai_sem1', 5, 2)->nullable();
            $table->decimal('pai_sem2', 5, 2)->nullable();
            $table->decimal('pai_sem3', 5, 2)->nullable();
            $table->decimal('pai_sem4', 5, 2)->nullable();
            $table->decimal('pai_sem5', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_rapor');

        Schema::table('data_orangtua', function (Blueprint $table) {
            $table->dropColumn(['status_ayah', 'status_ibu']);
        });
    }
};
