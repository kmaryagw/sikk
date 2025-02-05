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
        Schema::create('surat_klasifikasi_fungsi', function (Blueprint $table) {
            $table->string('skf_id', 50)->primary();
            $table->string('skf_nama', 100);
            $table->enum('skf_aktif', ['y', 'n']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_klasifikasi_fungsi');
    }
};
