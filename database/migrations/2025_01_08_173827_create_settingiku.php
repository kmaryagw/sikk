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
        Schema::create('settingiku', function (Blueprint $table) {
            $table->string('id_setting', 50)->primary();  
            $table->string('th_id', 50);  
            $table->string('ik_id', 50);

            $table->foreign('th_id')->references('th_id')->on('tahun_kerja')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ik_id')->references('ik_id')->on('indikator_kinerja')->onDelete('cascade')->onUpdate('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settingiku', function (Blueprint $table) {
            // Menghapus foreign key dan kolom
            $table->dropForeign(['th_id']);
            $table->dropColumn(['th_id']);
    });
}
};
