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
        Schema::table('ik_baseline_tahun', function (Blueprint $table) {
            $table->string('th_tahun')->nullable()->after('th_id');
        });
    }

    public function down()
    {
        Schema::table('ik_baseline_tahun', function (Blueprint $table) {
            $table->dropColumn('th_tahun');
        });
    }
};
