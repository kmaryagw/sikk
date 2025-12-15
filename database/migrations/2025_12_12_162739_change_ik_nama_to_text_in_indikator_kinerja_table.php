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
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            $table->text('ik_nama')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            $table->string('ik_nama', 255)->change();
        });
    }
};