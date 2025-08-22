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
        Schema::table('target_indikator', function (Blueprint $table) {
            $table->string('unit_id')->nullable()->after('th_id');

            // Tambahkan foreign key constraint
            $table->foreign('unit_id')
                  ->references('unit_id')
                  ->on('unit_kerja')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_indikator', function (Blueprint $table) {
            // Drop foreign key terlebih dahulu
            $table->dropForeign(['unit_id']);

            // Baru drop kolomnya
            $table->dropColumn('unit_id');
        });
    }
};
