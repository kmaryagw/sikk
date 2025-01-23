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
        Schema::create('history_monitoring_iku', function (Blueprint $table) {
            $table->string('hmi_id', 50)->primary();  
            $table->string('mtid_id', 50);  
            $table->string('ti_id', 50);
            $table->string('hmi_target', 100);
            $table->string('hmi_capaian', 100);
            $table->string('hmi_keterangan');
            $table->enum('hmi_status', ['tercapai', 'tidak tercapai', 'tidak terlaksana']);
            $table->text('hmi_url');
            $table->foreign('mtid_id')->references('mtid_id')->on('monitoring_iku_detail')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ti_id')->references('ti_id')->on('target_indikator')->onDelete('cascade')->onUpdate('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_monitoring_iku', function (Blueprint $table) {
            
            $table->dropForeign(['mtid_id']);
            $table->dropForeign(['ti_id']);
            
        });
        
        Schema::dropIfExists('history_monitoring_iku');
    }
};
