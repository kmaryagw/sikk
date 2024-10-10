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
        Schema::create('renstra', function (Blueprint $table) {
            $table->string('ren_id', 10)->primary();
            $table->string('ren_nama', 100);
            $table->string('ren_pimpinan', 100);
            $table->year('ren_periode_awal');
            $table->year('ren_periode_akhir');
            $table->enum('ren_is_aktif', ['y', 'n']);
            $table->dateTime('create_date');
            $table->dateTime('update_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
