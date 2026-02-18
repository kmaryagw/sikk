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
        Schema::table('program_studi', function (Blueprint $table) {
            $table->string('unit_id_pengelola', 50)->nullable()->after('id_fakultas');

            $table->foreign('unit_id_pengelola')
                  ->references('unit_id')
                  ->on('unit_kerja')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_studi', function (Blueprint $table) {
            $table->dropForeign(['unit_id_pengelola']);
            $table->dropColumn('unit_id_pengelola');
        });
    }
};