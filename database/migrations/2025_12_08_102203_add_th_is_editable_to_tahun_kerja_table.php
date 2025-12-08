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
        Schema::table('tahun_kerja', function (Blueprint $table) {
            $table->boolean('th_is_editable')->default(1)->after('th_is_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_kerja', function (Blueprint $table) {
            $table->dropColumn('th_is_editable');
        });
    }
};