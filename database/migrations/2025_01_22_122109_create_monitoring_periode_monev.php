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
        Schema::create('monitoring_periode_monev', function (Blueprint $table) {
            $table->string('mpm_id', 50)->primary();  
            $table->string('mtg_id', 50);
            $table->string('pm_id', 50);  
            
            $table->foreign('mtg_id')->references('mtg_id')->on('monitoring')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pm_id')->references('pm_id')->on('periode_monev')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_periode_monev');
    }
};
