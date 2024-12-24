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
        Schema::create('periode_monitoring', function (Blueprint $table) {
            $table->string('pmo_id', 50)->primary();  
            $table->string('th_id', 50);
            
            $table->datetime('pmo_tanggal_mulai');
            $table->datetime('pmo_tanggal_selesai');
            $table->foreign('th_id')->references('th_id')->on('tahun_kerja')->onDelete('cascade')->onUpdate('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('periode_monitoring', function (Blueprint $table) {
            $table->dropForeign(['th_id']);
            
        });
        Schema::dropIfExists('periode_monitoring');
    }
};
