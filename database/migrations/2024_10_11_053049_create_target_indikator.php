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
        Schema::create('target_indikator', function (Blueprint $table) {
            $table->string('ti_id', 50)->primary();
            $table->string('ik_id', 50);
            $table->string('ti_target', 100)->nullable();
            $table->text('ti_keterangan')->nullable();
            $table->string('prodi_id', 50);
            $table->string('th_id', 50);
            $table->timestamps();

            $table->foreign('ik_id')->references('ik_id')->on('indikator_kinerja')->onDelete('cascade');
            $table->foreign('prodi_id')->references('prodi_id')->on('program_studi')->onDelete('cascade');
            $table->foreign('th_id')->references('th_id')->on('tahun_kerja')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_indikator', function (Blueprint $table) {
            // Hapus foreign key sebelum tabel di-drop
            $table->dropForeign(['ik_id']);
            $table->dropForeign(['prodi_id']);
            $table->dropForeign(['th_id']);
        });
    
        // Hapus tabel setelah foreign key dihapus
        Schema::dropIfExists('target_indikator');
}
};
 