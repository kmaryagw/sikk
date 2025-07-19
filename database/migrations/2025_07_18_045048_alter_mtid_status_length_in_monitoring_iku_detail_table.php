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
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            $table->string('mtid_status', 50)->change(); // perbesar jadi 50 karakter
        });
    }

    public function down()
    {
        Schema::table('monitoring_iku_detail', function (Blueprint $table) {
            $table->string('mtid_status', 10)->change(); // sesuaikan dengan nilai lama (jika tahu)
        });
    }
};
