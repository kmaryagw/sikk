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
        Schema::create('surat_klasifikasi_lingkup', function (Blueprint $table) {
            $table->string('skl_id', 50)->primary();  
            $table->string('skl_nama', 100);
            $table->string('skl_kode', 50)->nullable();
            $table->enum('skl_aktif', ['y', 'n']);
            $table->string('skp_id', 50);
            $table->timestamps();

            $table->foreign('skp_id')->references('skp_id')->on('surat_klasifikasi_perihal')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_klasifikasi_lingkup', function (Blueprint $table) {
            $table->dropForeign(['skp_id']);
        });

        Schema::dropIfExists('surat_klasifikasi_lingkup');
    }
};
