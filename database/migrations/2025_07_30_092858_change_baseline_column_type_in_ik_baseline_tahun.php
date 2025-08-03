<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ik_baseline_tahun', function (Blueprint $table) {
            // Mengubah tipe kolom baseline menjadi string
            $table->string('baseline')->change();
        });
    }

    public function down(): void
    {
        Schema::table('ik_baseline_tahun', function (Blueprint $table) {
            // Kembalikan baseline menjadi double (jika sebelumnya double)
            $table->double('baseline')->change();
        });
    }
};
