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
        Schema::table('ik_baseline_tahun', function (Blueprint $table) {
            // Tambah kolom prodi_id
            $table->string('prodi_id', 100)->after('th_id');

            // Jika ingin ada relasi ke tabel program_studi
            // $table->foreign('prodi_id')->references('id')->on('program_studi')->onDelete('cascade');

            // Pastikan kombinasi unik per indikator, tahun, prodi
            $table->unique(['ik_id', 'th_id', 'prodi_id'], 'uniq_ik_th_prodi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ik_baseline_tahun', function (Blueprint $table) {
            // Hapus constraint unik
            $table->dropUnique('uniq_ik_th_prodi');

            // Hapus foreign key jika tadi ditambahkan
            // $table->dropForeign(['prodi_id']);

            // Hapus kolom prodi_id
            $table->dropColumn('prodi_id');
        });
    }
};
