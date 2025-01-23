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
        Schema::create('program_studi', function (Blueprint $table) {
            $table->string('prodi_id' ,50)->primary();
            $table->string('nama_prodi', 150);
            $table->string('singkatan_prodi', 10);
            $table->string('id_fakultas', 50);
            $table->foreign('id_fakultas')->references('id_fakultas')->on('fakultasn')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_studi', function (Blueprint $table) {
            // Hapus foreign key sebelum tabel di-drop
            $table->dropForeign(['id_fakultas']);
        });
    
        // Hapus tabel setelah foreign key dihapus
        Schema::dropIfExists('program_studi');
        
    }
};
