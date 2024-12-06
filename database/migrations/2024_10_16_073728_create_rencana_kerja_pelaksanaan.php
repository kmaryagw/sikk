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
        Schema::create('rencana_kerja_pelaksanaan', function (Blueprint $table) {
            $table->string('rkp_id', 50)->primary();  
            $table->string('rk_id', 150);
            $table->string('pm_id', 50);  
            
            $table->foreign('rk_id')->references('rk_id')->on('rencana_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pm_id')->references('pm_id')->on('periode_monev')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('rencana_kerja_pelaksanaan', function (Blueprint $table) {
            
            $table->dropForeign(['rk_id']);
            $table->dropForeign(['pm_id']);
            
        });
        Schema::dropIfExists('rencana_kerja_pelaksanaan');
    }
};
