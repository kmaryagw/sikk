<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            $table->text('mtid_evaluasi')->nullable()->after('mtid_keterangan');
            $table->text('mtid_tindaklanjut')->nullable()->after('mtid_evaluasi');
            $table->text('mtid_peningkatan')->nullable()->after('mtid_tindaklanjut');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            $table->dropColumn(['mtid_evaluasi', 'mtid_tindaklanjut', 'mtid_peningkatan']);
        });
    }
};
