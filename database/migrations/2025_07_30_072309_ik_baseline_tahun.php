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
       Schema::create('ik_baseline_tahun', function (Blueprint $table) {
        $table->id();
        $table->string('ik_id');     // indikator
        $table->string('th_id');     // tahun kerja
        $table->float('baseline')->nullable();
        $table->timestamps();

        // $table->foreign('ik_id')->references('ik_id')->on('indikator_kinerja')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
