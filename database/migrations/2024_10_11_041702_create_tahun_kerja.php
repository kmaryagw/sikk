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
        Schema::create('tahun_kerja', function (Blueprint $table) {
            $table->string('th_id', 50)->primary();
            $table->year('th_tahun');
            $table->enum('ren_is_aktif', ['y', 'n']);
            $table->string('ren_id', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_kerja');
    }
};
