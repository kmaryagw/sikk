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
        Schema::create('rencana_kerja_target_indikator', function (Blueprint $table) {
            $table->increments('rkti_id');  // ID untuk tabel pivot
            $table->unsignedBigInteger('rk_id');  // Pastikan tipe data string sesuai dengan rk_id di rencana_kerja
            $table->unsignedBigInteger('ti_id');  // Menyimpan referensi ke target_indikator
            $table->string('ik_id', 50);
            // Menambahkan foreign key constraints
            $table->foreign('ik_id')->references('ik_id')->on('indikator_kinerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rk_id')->references('rk_id')->on('rencana_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ti_id')->references('ti_id')->on('target_indikator')->onDelete('cascade')->onUpdate('cascade');
            
            // Kolom timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rencana_kerja_target_indikator', function (Blueprint $table) {
            // Hapus foreign key sebelum tabel di-drop
            $table->dropForeign(['ik_id']);
            $table->dropForeign(['rk_id']);
            $table->dropForeign(['ti_id']);
        });
    
        // Hapus tabel setelah foreign key dihapus
        Schema::dropIfExists('rencana_kerja_target_indikator');
    }
};
