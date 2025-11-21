<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            // Jadikan kolom nullable dulu
            $table->string('mtid_keterangan')->nullable()->change();
        });

        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            // Baru ubah menjadi TEXT
            $table->text('mtid_keterangan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            // Kembalikan ke VARCHAR(255) atau ukuran sebelumnya
            $table->string('mtid_keterangan', 255)->change();
        });
    }
};
