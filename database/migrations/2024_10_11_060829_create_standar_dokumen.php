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
        Schema::create('standar_dokumen', function (Blueprint $table) {
            $table->string('stdd_id', 50)->primary();
            $table->string('std_id', 50);
            $table->string('stdd_file', 255)->nullable();
            $table->foreign('std_id')->references('std_id')->on('standar')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standar_dokumen', function (Blueprint $table) {
            // Menghapus foreign key dan kolom
            $table->dropForeign(['std_id']);
            $table->dropColumn(['std_id']);



    });
    }
};
