<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            $table->string('ik_baseline')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            $table->string('ik_baseline')->nullable(false)->change();
        });
    }
};

