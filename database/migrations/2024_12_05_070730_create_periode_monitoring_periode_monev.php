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
        Schema::create('periode_monitoring_periode_monev', function (Blueprint $table) {
            $table->increments('pmpm_id');
            $table->string('pmo_id', 50);
            $table->string('pm_id', 255);
            $table->timestamps();
            $table->foreign('pmo_id')->references('pmo_id')->on('periode_monitoring')->onDelete('cascade');
            $table->foreign('pm_id')->references('pm_id')->on('periode_monev')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_monitoring_periode_monev', function (Blueprint $table) {
            
            $table->dropForeign(['pmo_id']);
            $table->dropForeign(['pm_id']);
            
        });
        Schema::dropIfExists('periode_monitoring_periode_monev');
    }
};
