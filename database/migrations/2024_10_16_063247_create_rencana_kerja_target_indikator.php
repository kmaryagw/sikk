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
            $table->string('rkti_id', 50)->primary();  
            $table->string('rk_id', 50);
            $table->string('ti_id', 50);  
            
            $table->foreign('rk_id')->references('rk_id')->on('rencana_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ti_id')->references('ti_id')->on('target_indikator')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('rencana_kerja_target_indikator', function (Blueprint $table) {
        //     // Hapus foreign key sebelum tabel di-drop
        //     $table->dropForeign(['rk_id']);
        //     $table->dropForeign(['ti_id']);
            
        // });
    
        // Hapus tabel setelah foreign key dihapus
        
        Schema::dropIfExists('rencana_kerja_target_indikator');
    }
};
