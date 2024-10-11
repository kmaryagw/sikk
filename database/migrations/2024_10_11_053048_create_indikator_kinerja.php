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
        Schema::create('indikator_kinerja', function (Blueprint $table) {
            $table->string('ik_id', 10)->primary();
            $table->string('ik_nama', 255);
            $table->string('std_id', 10);
            $table->foreign('std_id')->references('std_id')->on('standar')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            // Hapus foreign key sebelum tabel di-drop
            $table->dropForeign(['std_id']);
        });
    
        // Hapus tabel setelah foreign key dihapus
        Schema::dropIfExists('indikator_kinerja');
    }
};
