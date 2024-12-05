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
        Schema::create('rencana_kerja', function (Blueprint $table) {
            $table->bigIncrements('rk_id');  
            $table->string('rk_nama', 150);
            $table->string('th_id', 50);  
            $table->string('unit_id', 50);
            $table->string('ik_id', 50)->nullable(); // Menambahkan kolom 'ik_id' yang mengarah ke IndikatorKinerja
        $table->foreign('ik_id')->references('ik_id')->on('indikator_kinerja')->onDelete('cascade');
            $table->foreign('th_id')->references('th_id')->on('tahun_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('unit_id')->references('unit_id')->on('unit_kerja')->onDelete('cascade')->onUpdate('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rencana_kerja', function (Blueprint $table) {
            
            $table->dropForeign(['th_id']);
            $table->dropForeign(['ik_id']);
            $table->dropForeign(['unit_id']);
            
            
        });

        Schema::dropIfExists('rencana_kerja');
    }
};
