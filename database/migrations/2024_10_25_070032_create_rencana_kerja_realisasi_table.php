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
        Schema::create('rencana_kerja_realisasi', function (Blueprint $table) {
            $table->uuid('rkr_id')->primary();  // Menggunakan UUID sebagai primary key
            $table->unsignedBigInteger('rk_id'); // Tipe data sesuai dengan panjang yang digunakan di tabel 'rencana_kerja'
            $table->string('pm_id', 255)->nullable();
            $table->string('rkr_url', 255)->nullable();
            $table->string('rkr_file', 255)->nullable();
            $table->text('rkr_deskripsi'); // Menggunakan text untuk deskripsi yang lebih panjang
            $table->integer('rkr_capaian');
            $table->datetime('rkr_tanggal');
            
            // Menambahkan foreign key
            $table->foreign('pm_id')->references('pm_id')->on('periode_monev')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rk_id')->references('rk_id')->on('rencana_kerja')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rencana_kerja_realisasi', function (Blueprint $table) {
            $table->dropForeign(['pm_id']);
            $table->dropForeign(['rk_id']);
        });
        Schema::dropIfExists('rencana_kerja_realisasi');
    }
};
