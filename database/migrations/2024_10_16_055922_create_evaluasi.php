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
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->string('eval_id', 50)->primary();  
            $table->string('th_id', 50);  
            $table->string('prodi_id', 50);
            $table->string('status', 1);
            $table->foreign('th_id')->references('th_id')->on('tahun_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('prodi_id')->references('prodi_id')->on('program_studi')->onDelete('cascade')->onUpdate('cascade');  
            $table->timestamps();  
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            // Menghapus foreign key dan kolom
            $table->dropForeign(['th_id']);
            $table->dropColumn(['th_id']);
    });
    }
};
