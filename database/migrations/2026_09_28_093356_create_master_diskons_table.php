<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_diskon', function (Blueprint $table) {
            $table->id();
            $table->string('nama_diskon');
            $table->enum('metode_diskon', ['nominal', 'persentase']);
            $table->decimal('nilai_diskon', 15, 2);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_diskon');
    }
};
