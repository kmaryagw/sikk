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
        Schema::table('rencana_kerja', function (Blueprint $table) {
            $table->decimal('anggaran', 15, 2)->nullable()->after('rk_nama');
        });
    }

    public function down()
    {
        Schema::table('rencana_kerja', function (Blueprint $table) {
            $table->dropColumn('anggaran');
        });
    }

};
