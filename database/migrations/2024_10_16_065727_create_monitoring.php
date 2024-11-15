<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitoring', function (Blueprint $table) {
            $table->string('mtg_id', 50)->primary()->default(DB::raw('(UUID())'));  
            $table->string('pmo_id', 50);
           
            
            $table->string('mtg_capaian', 255);
            $table->string('mtg_kondisi', 255);
            $table->string('mtg_kendala', 255);
            $table->string('mtg_tindak_lanjut', 255);
            $table->date('mtg_tindak_lanjut_tanggal');  
            $table->string('mtg_bukti', 255);
            $table->string('rk_id', 50);
            $table->foreign('pmo_id')->references('pmo_id')->on('periode_monitoring')->onDelete('cascade')->onUpdate('cascade');
            
            
            $table->foreign('rk_id')->references('rk_id')->on('rencana_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('monitoring', function (Blueprint $table) {
            $table->dropForeign(['pmo_id']);
            
            $table->dropForeign(['rk_id']);
            
        });
        Schema::dropIfExists('monitoring');
    }
};
