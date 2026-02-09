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
        Schema::table('history_monitoring_iku', function (Blueprint $table) {
            $table->text('hmi_keterangan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_monitoring_iku', function (Blueprint $table) {
            $table->string('hmi_keterangan', 255)->nullable()->change();
        });
    }
};