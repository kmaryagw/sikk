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
        Schema::create('surat_klasifikasi_perihal', function (Blueprint $table) {
            $table->string('skp_id', 50)->primary();  
            $table->string('skp_nama', 100);
            $table->string('skp_kode', 50)->nullable();
            $table->enum('skp_aktif', ['y', 'n']);
            $table->string('skf_id', 50);
            $table->timestamps();

            $table->foreign('skf_id')->references('skf_id')->on('surat_klasifikasi_fungsi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_klasifikasi_perihal', function (Blueprint $table) {
            $table->dropForeign(['skf_id']);
        });
        
        Schema::dropIfExists('surat_klasifikasi_perihal');
    }
};
