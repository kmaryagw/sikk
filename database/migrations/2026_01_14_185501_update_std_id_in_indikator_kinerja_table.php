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
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            $table->dropForeign(['std_id']);
            $table->string('std_id', 50)->nullable()->change();
            $table->foreign('std_id')
                  ->references('std_id')
                  ->on('standar')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_kinerja', function (Blueprint $table) {
            $table->dropForeign(['std_id']);
            $table->string('std_id', 50)->nullable(false)->change();
            $table->foreign('std_id')
                  ->references('std_id')
                  ->on('standar')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }
};