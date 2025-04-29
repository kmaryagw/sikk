<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_nomor', function (Blueprint $table) {
            $table->text('sn_revisi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_nomor', function (Blueprint $table) {
            $table->text('sn_revisi')->default('')->change();
        });
    }

};
