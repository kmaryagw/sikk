<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('history_monitoring_iku', function (Blueprint $table) {
        // Step 1: Ubah jadi nullable dulu
        $table->string('hmi_status', 50)->nullable()->change();
    });

    // Optional: Jika ingin langsung ubah kembali jadi NOT NULL, bisa buat step lanjutan
    DB::statement("UPDATE history_monitoring_iku SET hmi_status = '' WHERE hmi_status IS NULL");
    Schema::table('history_monitoring_iku', function (Blueprint $table) {
        $table->string('hmi_status', 50)->default('')->change();
    });
}
};
