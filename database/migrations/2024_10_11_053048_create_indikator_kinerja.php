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
            $table->string('ik_id', 50)->primary(); // Wajib diisi dalam insert
            $table->string('ik_kode', 255);
            $table->string('ik_nama', 255);
            $table->enum('ik_jenis', ['IKU', 'IKT']);
            $table->enum('ik_ketercapaian', ['nilai', 'persentase', 'ketersediaan']);
            $table->string('ik_baseline', 100);
            $table->enum('ik_is_aktif', ['y', 'n'])->default('y'); // Tambahkan default 'y'
            $table->string('std_id', 50);
            $table->foreign('std_id')->references('std_id')->on('standar')->cascadeOnDelete()->cascadeOnUpdate();
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
