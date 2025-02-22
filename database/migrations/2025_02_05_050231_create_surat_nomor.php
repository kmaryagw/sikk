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
        Schema::create('surat_nomor', function (Blueprint $table) {
            $table->string('sn_id', 50)->primary();
            $table->string('unit_id', 50);
            $table->string('skl_id', 50);
            $table->string('oj_id', 50);
            $table->string('sn_nomor', 50)->nullable();
            $table->date('sn_tanggal');
            $table->text('sn_perihal');
            $table->text('sn_keterangan');
            $table->enum('sn_status', ['draft', 'ajukan', 'validasi', 'revisi']);
            $table->timestamps();

            $table->foreign('unit_id')->references('unit_id')->on('unit_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('skl_id')->references('skl_id')->on('surat_klasifikasi_lingkup')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('oj_id')->references('oj_id')->on('organisasi_jabatan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_klasifikasi_perihal', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['skl_id']);
            $table->dropForeign(['oj_id']);
        });
        
        Schema::dropIfExists('surat_nomor');
    }
};
