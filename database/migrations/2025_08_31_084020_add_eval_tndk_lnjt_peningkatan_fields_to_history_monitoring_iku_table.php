<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_monitoring_iku', function (Blueprint $table) {
            $table->text('hmi_evaluasi')->nullable()->after('hmi_url');
            $table->text('hmi_tindaklanjut')->nullable()->after('hmi_evaluasi');
            $table->text('hmi_peningkatan')->nullable()->after('hmi_tindaklanjut');
        });
    }

    public function down(): void
    {
        Schema::table('history_monitoring_iku', function (Blueprint $table) {
            $table->dropColumn(['hmi_evaluasi', 'hmi_tindaklanjut', 'hmi_peningkatan']);
        });
    }
};
