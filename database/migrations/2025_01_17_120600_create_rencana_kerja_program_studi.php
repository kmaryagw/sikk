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
        Schema::create('rencana_kerja_program_studi', function (Blueprint $table) {
            $table->string('rkps_id', 50)->primary();  
            $table->string('rk_id', 50);
            $table->string('prodi_id', 50);  
            
            $table->foreign('rk_id')->references('rk_id')->on('rencana_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('prodi_id')->references('prodi_id')->on('program_studi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kerja_program_studi');
    }
};
