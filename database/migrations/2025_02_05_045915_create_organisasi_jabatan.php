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
        Schema::create('organisasi_jabatan', function (Blueprint $table) {
            $table->string('oj_id', 50)->primary();
            $table->string('oj_nama', 100);
            $table->enum('oj_mengeluarkan_nomor', ['y', 'n']);
            $table->string('oj_kode', 50);
            $table->string('oj_induk', 50)->nullable(); 
            $table->timestamps();

            $table->foreign('oj_induk')->references('oj_id')->on('organisasi_jabatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisasi_jabatan', function (Blueprint $table) {
            $table->dropForeign(['oj_induk']);
        });

        Schema::dropIfExists('organisasi_jabatan');
    }
};