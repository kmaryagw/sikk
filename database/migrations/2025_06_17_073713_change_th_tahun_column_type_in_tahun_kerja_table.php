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
        Schema::table('tahun_kerja', function (Blueprint $table) {
            $table->string('th_tahun', 20)->change(); // ubah dari int ke varchar
        });
    }

    public function down()
    {
        Schema::table('tahun_kerja', function (Blueprint $table) {
            $table->integer('th_tahun')->change(); // rollback ke integer
        });
    }
};
