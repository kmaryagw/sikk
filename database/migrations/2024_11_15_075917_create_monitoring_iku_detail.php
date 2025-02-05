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
        Schema::create('monitoring_iku_detail', function (Blueprint $table) {
            $table->string('mtid_id', 50)->primary();  
            $table->string('mti_id', 50);  
            $table->string('ti_id', 50);
            $table->string('mtid_target', 100);
            $table->string('mtid_capaian', 100);
            $table->string('mtid_keterangan');
            $table->enum('mtid_status', ['tercapai', 'tidak tercapai', 'tidak terlaksana']);
            $table->string('mtid_url', 255)->nullable();
            $table->foreign('mti_id')->references('mti_id')->on('monitoring_iku')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ti_id')->references('ti_id')->on('target_indikator')->onDelete('cascade')->onUpdate('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            
            $table->dropForeign(['mti_id']);
            $table->dropForeign(['ti_id']);
            
        });

        Schema::dropIfExists('monitoring_iku_detail');
    }
};
