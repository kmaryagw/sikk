<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStdKategoriToStandarTable extends Migration
{
    public function up()
    {
        Schema::table('standar', function (Blueprint $table) {
            $table->string('std_kategori', 50)->after('std_id');
        });
    }

    public function down()
    {
        Schema::table('standar', function (Blueprint $table) {
            $table->dropColumn('std_kategori');
        });
    }
}
