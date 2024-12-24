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
        Schema::create('evaluasi_detail', function (Blueprint $table) {
            $table->string('evald_id', 50)->primary();  
            $table->string('eval_id', 50);  
            $table->string('ti_id', 50);
            $table->string('evald_target', 100);
            $table->string('evald_capaian', 100);
            $table->string('evald_keterangan');
            $table->enum('evald_status', ['tercapai', 'tidak tercapai', 'tidak terlaksana']);
            $table->foreign('eval_id')->references('eval_id')->on('evaluasi')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ti_id')->references('ti_id')->on('target_indikator')->onDelete('cascade')->onUpdate('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi_detail', function (Blueprint $table) {
            
            $table->dropForeign(['eval_id']);
            $table->dropForeign(['ti_id']);
            
        });

        Schema::dropIfExists('evaluasi_detail');
    }
};
